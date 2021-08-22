<?php
/**
 * Delete note from task.
 */
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['delTaskNote']) ) {
  
  $task_note_id = htmlspecialchars($_GET["task_note_id"]);
  $task_note = $entityManager->find("\Roloffice\Entity\ProjectTaskNote", $task_note_id);
  
  $project_id = htmlspecialchars($_GET["project_id"]);
  $task_id = htmlspecialchars($_GET["task_id"]);

  $entityManager->remove($task_note);
  $entityManager->flush();
  
  die('<script>location.href = "?editTask&project_id='.$project_id.'&task_id='.$task_id.'" </script>');
}