<?php
// Create a new AccountingDocument.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createAccountingDocument"]) ) {
  // Current loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);
  
  $ordinal_num_in_year = 0;

  $client_id = htmlspecialchars($_POST["client_id"]);
  $client = $entityManager->find("\Roloffice\Entity\Client", $client_id);

  $accd_type_id = htmlspecialchars($_POST["pidb_type_id"]);
  $accd_type = $entityManager->find("\Roloffice\Entity\AccountingDocumentType", $accd_type_id);
  
  

  $title = htmlspecialchars($_POST["title"]);
  $note = htmlspecialchars($_POST["note"]);

  // Save a new AccountingDocument.
  $newAccountingDocument = new \Roloffice\Entity\AccountingDocument();

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
  $entityManager->getRepository('Roloffice\Entity\AccountingDocument')->setOrdinalNumInYear($new_accounting_document_id);


  if(isset($_POST["project_id"])) {
    // $project_id = htmlspecialchars($_POST["project_id"]);
    /*
    $accounting_document_id = $GET['accounting_document_id'];
    $accounting_document = $entityManager->find("\Roloffice\Entity\AccountingDocument", $accounting_document_id);

    $newProject->getAccountingDocuments()->add($accounting_document);
  
    $entityManager->flush();
    */
  } else {
    $project_id = NULL;
  }

  die('<script>location.href = "?view&pidb_id='.$pidb_id.'&pidb_tip_id='.$accd_type_id.'" </script>');
}