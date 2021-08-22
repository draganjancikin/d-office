<?php
/**
 * Project task edit.
 */
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['editTask']) ) {
  
  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);
  
  $project_id = htmlspecialchars($_GET["project_id"]);
  $project = $entityManager->find("\Roloffice\Entity\Project", $project_id);

  $title = htmlspecialchars($_POST["title"]);
  
  $employee_id = htmlspecialchars($_POST["employee_id"]);
  $employee = $entityManager->find("\Roloffice\Entity\Employee", $employee_id);

  $task_id = htmlspecialchars($_GET["task_id"]);
  $task = $entityManager->find("\Roloffice\Entity\ProjectTask", $task_id);

  $start = htmlspecialchars($_POST["start"]);
  $end = htmlspecialchars($_POST["end"]);
  
  // Check if $start is empty string
  if ($start <> '') {
    $start = $task->getStartDate()->format('Y-m-d H:i:s');
    // $start = '1970-01-01 00:00:00';
  } else {
  }

  // Check if $end is empty string
  if ($end <> '') {
      $end = $task->getEndDate()->format('Y-m-d H:i:s');
    // $end = '1970-01-01 00:00:00';
  } else {
  }

  if($start == '1970-01-01 00:00:00' AND $end == '1970-01-01 00:00:00') {

      // zadatak je nov i još nije setovan ni start ni end
      $status_id = 1;
      // echo 'zadatak je nov i još nije setovan ni start ni end';
      // exit();

  } elseif ( $start <> '' AND $start <> '1970-01-01 00:00:00' AND $end == '1970-01-01 00:00:00') {

      // start postoji i ne menja se, a end nije setovan
      // $result_start = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
      //   $row_start = mysqli_fetch_array($result_start);
      //   $start = $row_start['start'];
      $status_id = 2;
      // echo 'start postoji i ne menja se, a end nije setovan';
      // exit();

  } elseif ( ($start == '' OR $start <> '1970-01-01 00:00:00' ) AND $end == '1970-01-01 00:00:00') {

      //start postoji pa je brisan u formi a end nije setovan
      $start = '1970-01-01 00:00:00';
      $status_id = 1;
      // echo 'start postoji pa je brisan u formi a end nije setovan';
      // exit();

  } elseif ($start <> '1970-01-01 00:00:00' AND $start <> '' AND $end <> '' AND $start <> '1970-01-01 00:00:00') {

      // start postoji i nemenja se i end postoji i nemenja se
      // $result_start_end = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
      // $row_start_end = mysqli_fetch_array($result_start_end);
      //   $start = $row_start_end['start'];
      //   $end = $row_start_end['end'];
      $status_id = 3;

      // echo 'start je setovan i nemenja se i end je setovan i nemenja se';
      // exit();

  } elseif ($start <> '1970-01-01 00:00:00' AND $start <> '' AND $end =='') {    

      // start postoji i nemenja se a end postoji pa je brisan u formi
      // $result_start = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
      //   $row_start = mysqli_fetch_array($result_start);
      //   $start = $row_start['start'];
      $end = '1970-01-01 00:00:00';
      $status_id = 2;
      
      // echo 'start postoji i nemenja se a end postoji pa je brisan u formi';
      // exit();

  } elseif ($start == '' AND $end <> '' AND $end <> '1970-01-01 00:00:00') {

      // end postoji i ne menja se a start postoji pa je obrisan u formi
      // $result_start_end = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
      //   $row_start_end = mysqli_fetch_array($result_start_end);
      //  $start = $row_start_end['start'];
      //   $end = $row_start_end['end'];
      $status_id = 3;
      // echo 'end postoji i ne menja se a start postoji pa je obrisan u formi';
      // exit();

  } elseif ($start == '' AND $end == '') {
    // i start i end su postojali pa su brisani u formi
    $status_id = 1;
  }
  
  $status = $entityManager->find("\Roloffice\Entity\ProjectTaskStatus", $status_id);

  $task->setProject($project);
  $task->setTitle($title);
  $task->setStatus($status);
  $task->setEmployee($employee);
  $task->setStartDate(new DateTime($start));
  $task->setEndDate(new DateTime($end));

  $task->setModifiedAt(new DateTime("now"));
  
  $task->setModifiedByUser($user);

  // echo "editing in progress ...";
  // exit();
  // --------------------------------------------------------------------------

  $entityManager->flush();


  // $db->connection->query("UPDATE project_task SET title='$title', status_id='$status_id', employee_id='$employee_id', start='$start', end='$end'  WHERE id = '$task_id' ") or die(mysqli_error($db->connection));

  die('<script>location.href = "?view&project_id='.$project_id.'" </script>');
}