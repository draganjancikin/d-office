<?php
$user_id = $_SESSION['user_id'];
$date = date('Y-m-d h:i:s');

// izmena projekta
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['editProject']) ) {

    $project_id = htmlspecialchars($_GET["project_id"]);
    $client_id = htmlspecialchars($_POST["client_id"]);
    $title = htmlspecialchars($_POST["title"]);
    // $priority_id = htmlspecialchars($_POST["priority_id"]);
    $priority_id = 2;
    $status = htmlspecialchars($_POST["status"]);
    // $note = htmlspecialchars($_POST["note"]);

    $db = new DBconnection();

    // $db->connection->query("UPDATE project SET client_id='$client_id', title='$title', priority_id='$priority_id', note='$note' WHERE id = '$project_id' ") or die(mysql_error($db->connection));
    $db->connection->query("UPDATE project SET client_id='$client_id', title='$title', priority_id='$priority_id', status='$status' WHERE id = '$project_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&project_id='.$project_id.'" </script>');
}

// izmena zadatka
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['editTask']) ) {

    $task_id = htmlspecialchars($_GET["task_id"]);
    $project_id = htmlspecialchars($_GET["project_id"]);
    $title = htmlspecialchars($_POST["title"]);
    $employee_id = htmlspecialchars($_POST["employee_id"]);
    $start = htmlspecialchars($_POST["start"]);
    $end = htmlspecialchars($_POST["end"]);

    $db = new DBconnection();


    /*
    if($start=='0000-00-00 00:00:00'){
        // $start='0000-00-00 00:00:00';
        $status_id = '1';
    }else if($start==''){
        $start='0000-00-00 00:00:00';
        $status_id = '1';
    }else{
        $result_start = $connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysql_error());
        $row_start = mysqli_fetch_array($result_start);
        $start = $row_start['start'];
        if($end=='0000-00-00 00:00:00' OR $end==''){
            $status_id = '2';
        }else{
            $status_id = '3';
        }

    }
    */

    if($start == '0000-01-01 00:00:00' AND $end == '0000-01-01 00:00:00') {

        // zadatak je nov i još nije setovan ni start ni end
        $status_id = 1;
        // echo 'zadatak je nov i još nije setovan ni start ni end';
        // exit();

    }elseif ( $start <> '' AND $start <> '0000-01-01 00:00:00' AND $end == '0000-01-01 00:00:00') {

        // start postoji i ne menja se, a end nije setovan
        $result_start = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
          $row_start = mysqli_fetch_array($result_start);
          $start = $row_start['start'];
        $status_id = 2;
        // echo 'start postoji i ne menja se, a end nije setovan';
        // exit();

    }elseif ( ($start == '' OR $start <> '0000-01-01 00:00:00' ) AND $end == '0000-01-01 00:00:00') {

        //start postoji pa je brisan u formi a end nije setovan
        $start='0000-01-01 00:00:00';
        $status_id = 1;
        // echo 'start postoji pa je brisan u formi a end nije setovan';
        // exit();

    }elseif ($start<>'0000-01-01 00:00:00' AND $start<>'' AND $end <>'' AND $start<>'0000-01-01 00:00:00') {

        // start postoji i nemenja se i end postoji i nemenja se
        $result_start_end = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
        $row_start_end = mysqli_fetch_array($result_start_end);
          $start = $row_start_end['start'];
          $end = $row_start_end['end'];
        $status_id = 3;

        // echo 'start je setovan i nemenja se i end je setovan i nemenja se';
        // exit();

    }elseif ($start <> '0000-01-01 00:00:00' AND $start <> '' AND $end =='') {    

        // start postoji i nemenja se a end postoji pa je brisan u formi
        $result_start = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
          $row_start = mysqli_fetch_array($result_start);
          $start = $row_start['start'];
          $end = '0000-01-01 00:00:00';
        $status_id = 2;
        
        // echo 'start postoji i nemenja se a end postoji pa je brisan u formi';
        // exit();

    }elseif ($start=='' AND $end <>'' AND $end<>'0000-01-01 00:00:00') {

        // end postoji i ne menja se a start postoji pa je obrisan u formi
        $result_start_end = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
          $row_start_end = mysqli_fetch_array($result_start_end);
          $start = $row_start_end['start'];
          $end = $row_start_end['end'];
        $status_id = 3;
        // echo 'end postoji i ne menja se a start postoji pa je obrisan u formi';
        // exit();

    }

    $db->connection->query("UPDATE project_task SET title='$title', status_id='$status_id', employee_id='$employee_id', start='$start', end='$end'  WHERE id = '$task_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&project_id='.$project_id.'" </script>');
}


// postavi datum početka realizacije zadatka
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['setTaskStart']) ) {

    $task_id = htmlspecialchars($_GET["task_id"]);
    $project_id = htmlspecialchars($_GET["project_id"]);

    $start = date('Y-m-d h:i:s');
    $status_id = 2;

    $db = new DBconnection();

    $db->connection->query("UPDATE project_task SET start='$start', status_id='$status_id' WHERE id = '$task_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?editTask&project_id='.$project_id.'&task_id='.$task_id.'" </script>');
}


// postavi datum završetka realizacije zadatka
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['setTaskEnd']) ) {

    $task_id = htmlspecialchars($_GET["task_id"]);
    $project_id = htmlspecialchars($_GET["project_id"]);

    $db = new DBconnection();

    $result_start = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
        $row_start = mysqli_fetch_array($result_start);
        $start = $row_start['start'];

    if($start == '0000-01-01 00:00:00' ){
        // $end = '0000-00-00 00:00:00';
        // $status_id = 1;
        die('<script>location.href = "?editTask&project_id='.$project_id.'&task_id='.$task_id.'&alertEnd" </script>');

    }else{
        $end = date('Y-m-d h:i:s');
        $status_id = 3;
    }

    $db->connection->query("UPDATE project_task SET end='$end', status_id='$status_id' WHERE id = '$task_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?editTask&project_id='.$project_id.'&task_id='.$task_id.'" </script>');
}
