<?php
// Delete AccountingDocument Payment (transaction).
if ($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["deleteTransaction"]) ) {
  
  $accounting_document_id = htmlspecialchars($_GET["pidb_id"]);
  $accounting_document = $entityManager->find("\App\Entity\AccountingDocument", $accounting_document_id);

  $transaction_id = htmlspecialchars($_GET["transaction_id"]);
  $transaction = $entityManager->find("\App\Entity\Payment", $transaction_id);
  
  
  $entityManager->remove($transaction);
  
  $accounting_document->getPayments()->remove($transaction);
  
  $entityManager->flush();
  
  die('<script>location.href = "?transactions&pidb_id=' . $accounting_document_id . '" </script>');
}