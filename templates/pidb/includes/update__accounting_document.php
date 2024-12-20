<?php
// Update Accounting Document.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateAcountingDocument"])) {

  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);
  
  $ad_id = htmlspecialchars($_GET["pidb_id"]);
  $accounting_document = $entityManager->find("\App\Entity\AccountingDocument", $ad_id);
  
  $title = htmlspecialchars($_POST["title"]);

  $client_id = htmlspecialchars($_POST["client_id"]);
  $client = $entityManager->find("\App\Entity\Client", $client_id);

  $is_archived = htmlspecialchars($_POST["archived"]);
  $note = htmlspecialchars($_POST["note"]);
  
  $accounting_document->setTitle($title);
  $accounting_document->setClient($client);
  $accounting_document->setIsArchived($is_archived);
  $accounting_document->setNote($note);
  $accounting_document->setModifiedByUser($user);
  $accounting_document->setModifiedAt(new DateTime("now"));

  $entityManager->flush();

  die('<script>location.href = "?edit&pidb_id='.$ad_id.'" </script>');
}
