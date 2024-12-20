<?php
// Create a new AccountingDocument.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createAccountingDocument"]) ) {
  // Current loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);
  
  $ordinal_num_in_year = 0;

  $client_id = htmlspecialchars($_POST["client_id"]);
  $client = $entityManager->find("\App\Entity\Client", $client_id);

  $accd_type_id = htmlspecialchars($_POST["pidb_type_id"]);
  $accd_type = $entityManager->find("\App\Entity\AccountingDocumentType", $accd_type_id);
  
  $title = htmlspecialchars($_POST["title"]);
  $note = htmlspecialchars($_POST["note"]);

  // Create a new AccountingDocument.
  $newAccountingDocument = new \App\Entity\AccountingDocument();

  $newAccountingDocument->setOrdinalNumInYear($ordinal_num_in_year);
  $newAccountingDocument->setDate(new DateTime("now"));
  $newAccountingDocument->setIsArchived(0);

  $newAccountingDocument->setType($accd_type);
  $newAccountingDocument->setClient($client);
  $newAccountingDocument->setTitle($title);
  $newAccountingDocument->setNote($note);
  
  $newAccountingDocument->setCreatedAt(new DateTime("now"));
  $newAccountingDocument->setCreatedByUser($user);
  $newAccountingDocument->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

  $entityManager->persist($newAccountingDocument);
  $entityManager->flush();

  // Get id of last AccountingDocument.
  $new_accounting_document_id = $newAccountingDocument->getId();

  // Set Ordinal Number In Year.
  $entityManager->getRepository('App\Entity\AccountingDocument')->setOrdinalNumInYear($new_accounting_document_id);


  if (isset($_POST["project_id"])) {
    $project_id = htmlspecialchars($_POST["project_id"]);
    $project = $entityManager->find("\App\Entity\Project", $project_id);

    $project->getAccountingDocuments()->add($newAccountingDocument);
  
    $entityManager->flush();
    
  } else {
    $project_id = NULL;
  }

  die('<script>location.href = "?view&pidb_id=' . $new_accounting_document_id . '" </script>');
}