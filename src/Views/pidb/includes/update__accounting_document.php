<?php
// Update Accounting Document.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET) && str_contains($_GET['url'], 'edit')) {

  // Curent loged user.
  $user = $entityManager->find("\App\Entity\User", $user_id);
  
  $accounting_document = $entityManager->find("\App\Entity\AccountingDocument", $pidb_id);
  
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

  die('<script>location.href = "/pidb/'.$pidb_id.'" </script>');
}
