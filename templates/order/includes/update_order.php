<?php
// Update order.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateOrder"]) ) {
echo "Order updating ...";
  
  // curent loged user
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);

  $order_id = htmlspecialchars($_GET["order_id"]);
  $order = $entityManager->find("\Roloffice\Entity\Order", $order_id);

  $title = htmlspecialchars($_POST["title"]);
  $status = htmlspecialchars($_POST["status"]);

  if ( isset($_POST["is_archived"]) && $_POST["is_archived"] == 1 ) {
      $is_archived = htmlspecialchars($_POST["is_archived"]);
  } else {
      $is_archived = 0;
  }

  $note = htmlspecialchars($_POST["note"]);

  // If exist project in Order, then add $order to table v6_projects_orders.
  if (NULL != $_POST["project_id"] ) {
    $project_id = htmlspecialchars($_POST["project_id"]);
    $project = $entityManager->find("\Roloffice\Entity\Project", $project_id);
    
    $project->getOrders()->add($order);
  }

  $order->setTitle($title);
  $order->setStatus($status);
  $order->setIsArchived($is_archived);
  $order->setNote($note);
  
  $entityManager->flush();
  
  die('<script>location.href = "?view&order_id='.$order_id.'" </script>');
}