<?php
/**
 * Create new task.
 */
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['createTask']) ) {
  
  // Current loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);

  $project_id = $_GET["project_id"];
  $project = $entityManager->find("\App\Entity\Project", $project_id);

  $type_id = $_POST["type_id"];
  $type = $entityManager->find("\App\Entity\ProjectTaskType", $type_id);
  
  $status_id = $_POST["status_id"];
  $status = $entityManager->find("\App\Entity\ProjectTaskStatus", $status_id);
  
  $title = htmlspecialchars($_POST['title']);

  // Save a new Task.
  $newTask = new \App\Entity\ProjectTask();

  $newTask->setProject($project);
  $newTask->setType($type);
  $newTask->setStatus($status);
  $newTask->setTitle($title);
  $newTask->setStartDate(new DateTime("1970-01-01 00:00:00"));
  $newTask->setEndDate(new DateTime("1970-01-01 00:00:00"));

  $newTask->setCreatedAt(new DateTime("now"));
  $newTask->setCreatedByUser($user);
  $newTask->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

  $entityManager->persist($newTask);
  $entityManager->flush();
  
  // Redirect to view Project page.
  die('<script>location.href = "?view&project_id=' .$project_id. '" </script>');
}