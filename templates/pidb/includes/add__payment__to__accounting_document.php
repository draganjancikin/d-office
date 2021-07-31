<?php
// Add Payment to AccountingDocument
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addPayment"]) ) {
  // @TODO finish method that check if exist first cash Input
  // if ($pidb->ifExistFirstCashInput()) {
  // }

  // Curent logged User.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);
  
  $accounting_document_id = htmlspecialchars($_POST["pidb_id"]);
  $accounting_document = $entityManager->find("\Roloffice\Entity\AccountingDocument", $accounting_document_id);

  $payment_type_id = htmlspecialchars($_POST["type_id"]);
  $payment_type = $entityManager->find("\Roloffice\Entity\PaymentType", $payment_type_id);
  
  // Date from new payment form.
  $date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
  
  $amount = htmlspecialchars($_POST["amount"]);
  // Correct decimal separator
  $amount = str_replace(",", ".", $amount);
  
  $note = htmlspecialchars($_POST["note"]);
  
  // Create a new Payment.
  $newPayment = new \Roloffice\Entity\Payment();
  
  $newPayment->setType($payment_type);
  $newPayment->setAmount($amount);
  $newPayment->setDate(new DateTime($date));
  $newPayment->setNote($note);
  $newPayment->setCreatedAt(new DateTime("now"));
  $newPayment->setCreatedByUser($user);
  
  $entityManager->persist($newPayment);
  $entityManager->flush();
  
  // TODO: Dragan - Add Payment to AccountingDocument
  
  $accounting_document->getPayments()->add($newPayment);
  
  $entityManager->flush();

  // echo "transaction in progress ...";
  // exit();
  /*
  $client_id = htmlspecialchars($_POST["client_id"]);q
    
  if ($type_id == 6) {
    $amount = "-".$amount;
  }
  */  
  die('<script>location.href = "?view&pidb_id='.$accounting_document_id.' " </script>');
}
