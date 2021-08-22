<?php
/**
 * Create project note.
 */
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['addNote']) ) {
  
  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);

  $project_id = $_GET["project_id"];
  $project = $entityManager->find("\Roloffice\Entity\Project", $project_id);
  
  $note = htmlspecialchars($_POST['note']);
  
  // Save a new Task note.
  $newProjectNote = new \Roloffice\Entity\ProjectNote();
  
  $newProjectNote->setNote($note);
  
  $newProjectNote->setProject($project);
  $newProjectNote->setCreatedAt(new DateTime("now"));
  
  $newProjectNote->setCreatedByUser($user);
  $newProjectNote->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

  $entityManager->persist($newProjectNote);
  $entityManager->flush();

  // ovde link da vodi na pregled projekta
  die('<script>location.href = "?view&project_id=' .$project_id. '" </script>');
}