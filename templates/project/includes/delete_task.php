<?php
/**
 * Delete project task.
 */
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['delTask']) ) {
  $project_id = htmlspecialchars($_GET["project_id"]);
  
  $task_id = htmlspecialchars($_GET["task_id"]);
  $task = $entityManager->find("\Roloffice\Entity\ProjectTask", $task_id);
  
  // First deleting task notes.
  $task_notes = $entityManager->getRepository('\Roloffice\Entity\ProjectTaskNote')->findBy(array('project_task' => $task_id), array('id' => "ASC"));
  foreach ($task_notes as $task_note) {
    $task_note = $entityManager->find("\Roloffice\Entity\ProjectTaskNote", $task_note->getId());
    $entityManager->remove($task_note);
    $entityManager->flush();
  }

  // Second deleting task.
  $entityManager->remove($task);
  $entityManager->flush();

  die('<script>location.href = "?view&project_id='.$project_id.'" </script>');
}