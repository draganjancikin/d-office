<?php
// Edit Article in AccountingDocument.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editTransaction"]) ) {
  
  $pidb_id = htmlspecialchars($_POST["pidb_id"]);
  $transaction_id = htmlspecialchars($_POST["transaction_id"]);
  $transaction = $entityManager->find("\App\Entity\Payment", $transaction_id);
    
  $type_id = htmlspecialchars($_POST["type_id"]);
  $type = $entityManager->find("\App\Entity\PaymentType", $type_id);
  
  $date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
  $amount_1 = htmlspecialchars($_POST["amount"]);
  $amount = str_replace(",", ".", $amount_1);
  $note = htmlspecialchars($_POST["note"]);
    
  $transaction->setType($type);
  $transaction->setDate(new DateTime($date));
  $transaction->setAmount($amount);
  $transaction->setNote($note);
  
  $entityManager->flush();
 
  die('<script>location.href = "?transactions&pidb_id=' . $pidb_id . '" </script>');
}