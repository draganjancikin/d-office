<?php
/**
 * Set start and end date for task.
 */
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['setTaskStart']) ) {

  $task_id = htmlspecialchars($_GET["task_id"]);
  $task = $entityManager->find("\Roloffice\Entity\ProjectTask", $task_id);

  $project_id = htmlspecialchars($_GET["project_id"]);
  
  $status_id = 2;
  $status = $entityManager->find("\Roloffice\Entity\ProjectTaskStatus", $status_id);

  $task->setStartDate(new DateTime("now"));
  $task->setStatus($status);
  
  $entityManager->flush();

  die('<script>location.href = "?editTask&project_id='.$project_id.'&task_id='.$task_id.'" </script>');
}

if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['setTaskEnd']) ) {

    $task_id = htmlspecialchars($_GET["task_id"]);
    $task = $entityManager->find("\Roloffice\Entity\ProjectTask", $task_id);
    
    $project_id = htmlspecialchars($_GET["project_id"]);

    $start = $task->getStartDate()->format('Y-m-d H:i:s');
    
    if($start == '1970-01-01 00:00:00' ){
        // $end = '0000-00-00 00:00:00';
        // $status_id = 1;
        die('<script>location.href = "?editTask&project_id='.$project_id.'&task_id='.$task_id.'&alertEnd" </script>');

    }
    $status_id = 3;
    $status = $entityManager->find("\Roloffice\Entity\ProjectTaskStatus", $status_id);
    
    $task->setEndDate(new DateTime("now"));
    $task->setStatus($status);
    
    $entityManager->flush();
    
    die('<script>location.href = "?editTask&project_id='.$project_id.'&task_id='.$task_id.'" </script>');
}
