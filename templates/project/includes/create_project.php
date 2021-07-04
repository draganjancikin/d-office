<?php
// Create a new Project.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['createProject']) ) {
  
  // Current loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);

  $ordinal_num_in_year = 0;

  $client_id = htmlspecialchars($_POST["client_id"]);
  $client = $entityManager->find("\Roloffice\Entity\Client", $client_id);

  $title = htmlspecialchars($_POST['title']);
  
  $project_priority_id = htmlspecialchars($_POST['project_priority_id']);
  $project_priority = $entityManager->find("\Roloffice\Entity\ProjectPriority", $project_priority_id);
  
  // $note = htmlspecialchars($_POST['note']);
  $note = "";

  $project_status = $entityManager->find("\Roloffice\Entity\ProjectStatus", 1);

  // Save a new Project.
  $newProject = new \Roloffice\Entity\Project();

  $newProject->setOrdinalNumInYear($ordinal_num_in_year);
  $newProject->setClient($client);
  $newProject->setTitle($title);
  $newProject->setPriority($project_priority);
  $newProject->setNote($note);
  // New Projest has status '1' => 'Is active'.
  $newProject->setStatus($project_status);

  $newProject->setCreatedAt(new DateTime("now"));
  $newProject->setCreatedByUser($user);
  $newProject->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

  $entityManager->persist($newProject);
  $entityManager->flush();

  // Get id of last Project.
  $new_project_id = $newProject->getId();


  // Set Ordinal Number In Year.
  $entityManager->getRepository('Roloffice\Entity\Project')->setOrdinalNumInYear($new_project_id);
  
  // TODO: ako se otvara novi projekat iz accounting documenta preuzeti accounting document ID i upisati u Project (getAccountingDocuments()->add($newAccountingDocument) )

  if (isset($_GET['accounting_document_id'])) {
    $accounting_document_id = $GET['accounting_document_id'];
    $accounting_document = $entityManager->find("\Roloffice\Entity\AccountingDocument", $accounting_document_id);

    $newProject->getAccountingDocuments()->add($accounting_document);
  
    $entityManager->flush();
  }

  die('<script>location.href = "?view&project_id=' .$new_project_id. '" </script>');
}