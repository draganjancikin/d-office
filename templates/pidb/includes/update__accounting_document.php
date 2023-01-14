<?php
// Update Accounting Document.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateAcountingDocument"])) {
    $current_user_id = $_SESSION['user_id'];
    $current_user = $entityManager->find("\Roloffice\Entity\User", $current_user_id);

    $ad_id = htmlspecialchars($_GET["pidb_id"]);
    $accounting_document = $entityManager->find("\Roloffice\Entity\AccountingDocument", $ad_id);

    $title = htmlspecialchars($_POST["title"]);

    $client_id = htmlspecialchars($_POST["client_id"]);
    $client = $entityManager->find("\Roloffice\Entity\Client", $client_id);

    $is_archived = htmlspecialchars($_POST["archived"]);
    $note = htmlspecialchars($_POST["note"]);

    $accounting_document->setTitle($title);
    $accounting_document->setClient($client);
    $accounting_document->setIsArchived($is_archived);
    $accounting_document->setNote($note);
    $accounting_document->setModifiedByUser($current_user);
    $accounting_document->setModifiedAt(new DateTime("now"));

    $entityManager->flush();

    die('<script>location.href = "?edit&pidb_id='.$ad_id.'" </script>');
}
