<?php
// edit order
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateOrder"]) ) {
echo "Order updating ...";
  /*
  $order_id = htmlspecialchars($_GET["order_id"]);
  $project_id = htmlspecialchars($_POST["project_id"]);
  $title = htmlspecialchars($_POST["title"]);
  $status = htmlspecialchars($_POST["status"]);

  if ( isset($_POST["is_archived"]) && $_POST["is_archived"] == 1 ) {
      $is_archived = htmlspecialchars($_POST["is_archived"]);
  } else {
      $is_archived = 0;
  }

  $note = htmlspecialchars($_POST["note"]);
  
  $db = new Database();

  $db->connection->query(" UPDATE orderm " 
                   . " SET project_id ='$project_id', title='$title', status='$status', is_archived='$is_archived', note='$note' " 
                   . " WHERE id = '$order_id' ") or die(mysqli_error($db->connection));

  die('<script>location.href = "?view&order_id='.$order_id.'" </script>');
  */
}