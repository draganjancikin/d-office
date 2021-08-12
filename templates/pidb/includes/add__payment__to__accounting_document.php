<?php
// Add Payment to AccountingDocument.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addPayment"]) ) {
  if ($entityManager->getRepository('Roloffice\Entity\Payment')->ifExistFirstCashInput()) {
    // TODO Dragan: Created error message
    echo "Već ste uneli početno stanje!";
    die();
  }

  // Curent logged User.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);
  
  $payment_type_id = htmlspecialchars($_POST["type_id"]);
  $payment_type = $entityManager->find("\Roloffice\Entity\PaymentType", $payment_type_id);
  
  // Date from new payment form.
  if (!isset($_POST["date"])) {
    $date = date('Y-m-d H:i:s');
  } else {
    $date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
  }
  
  $amount = htmlspecialchars($_POST["amount"]);
  // Correct decimal separator.
  $amount = str_replace(",", ".", $amount);
  
  $note = htmlspecialchars($_POST["note"]);
  
  // Create a new Payment.
  $newPayment = new \Roloffice\Entity\Payment();
  
  $newPayment->setType($payment_type);
  /*
  if ($type_id == 6) {
    $amount = "-".$amount;
  }
  */  
  $newPayment->setAmount($amount);
  $newPayment->setDate(new DateTime($date));
  $newPayment->setNote($note);
  $newPayment->setCreatedAt(new DateTime("now"));
  $newPayment->setCreatedByUser($user);
  
  $entityManager->persist($newPayment);
  $entityManager->flush();
  
  if (isset($_POST["pidb_id"])) {
    $accounting_document_id = htmlspecialchars($_POST["pidb_id"]);
    $accounting_document = $entityManager->find("\Roloffice\Entity\AccountingDocument", $accounting_document_id);
    // Add Payment to AccountingDocument.
    $accounting_document->getPayments()->add($newPayment);
    $entityManager->flush();
    die('<script>location.href = "?view&pidb_id='.$accounting_document_id.' " </script>');
  }
  die('<script>location.href = "?cashRegister" </script>');
}
