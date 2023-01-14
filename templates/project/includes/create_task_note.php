<?php
/**
 * Create a new note for task.
 */
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['addTaskNote']) ) {

  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);

  $project_id = $_GET["project_id"];
  $task_id = $_GET["task_id"];
  $task = $entityManager->find("\App\Entity\ProjectTask", $task_id);
  
  $note = htmlspecialchars($_POST['note']);


  // Save a new Task note.
  $newTaskNote = new \App\Entity\ProjectTaskNote();

  $newTaskNote->setProjectTask($task);
  $newTaskNote->setNote($note);

  $newTaskNote->setCreatedAt(new DateTime("now"));
  $newTaskNote->setCreatedByUser($user);
  $newTaskNote->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

  $entityManager->persist($newTaskNote);
  $entityManager->flush();
  
  // ovde link da vodi na pregled zadatka
  die('<script>location.href = "?editTask&task_id=' .$task_id. '&project_id=' .$project_id. '" </script>');
}
