<?php
// Project update.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['updateProject']) ) {

  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);

  $project_id = htmlspecialchars($_GET["project_id"]);
  $project = $entityManager->find("\Roloffice\Entity\Project", $project_id);

  $project_priority_id = htmlspecialchars($_POST["project_priority_id"]);
  $project_priority = $entityManager->find("\Roloffice\Entity\ProjectPriority", $project_priority_id);

  $client_id = htmlspecialchars($_POST["client_id"]);
  $client = $entityManager->find("\Roloffice\Entity\Client", $client_id);

  $title = htmlspecialchars($_POST["title"]);

  $status_id = htmlspecialchars($_POST["status_id"]);
  $status = $entityManager->find("\Roloffice\Entity\ProjectStatus", $status_id);
  
  // $note = htmlspecialchars($_POST["note"]);

  $project->setPriority($project_priority);
  $project->setClient($client);
  $project->setTitle($title);
  $project->setStatus($status);
  $project->setModifiedAt(new DateTime("now"));

  $entityManager->flush();

  die('<script>location.href = "?view&project_id='.$project_id.'" </script>');
  }