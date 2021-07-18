<?php
// add payment
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addPayment"]) ) {

  // TODO finish method that check if exist first cash Input
  if ($pidb->ifExistFirstCashInput()) {
    
  }
  
  if(!$_POST["date"]) {
    $date = date('Y-m-d H:i:s');
  }else{
    $date = date('Y-m-d H:i:s');
  }
  
  $pidb_id = htmlspecialchars($_POST["pidb_id"]);
  $client_id = htmlspecialchars($_POST["client_id"]);
  $type_id = htmlspecialchars($_POST["type_id"]);
  $amount = htmlspecialchars($_POST["amount"]);
  $amount = $pidb->correctDecimalSeparator($amount);
  if ($type_id == 6) {
    $amount = "-".$amount;
  }
  $note = htmlspecialchars($_POST["note"]);
  $created_at_date = date('Y-m-d H:i:s');
  $created_at_user_id = $username = $_SESSION['user_id'];
  $pidb->insertTransaction($date, $pidb_id, $client_id, $type_id, $amount, $note, $created_at_date, $created_at_user_id);
    
  die('<script>location.href = "?view&pidb_id='.$pidb_id.' " </script>');
}
