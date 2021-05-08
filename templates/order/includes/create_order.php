<?php
// Create a new Order.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createOrder"]) ) { 
  
  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);

  $ordinal_num_in_year = 0;
  
  $supplier_id = htmlspecialchars($_POST["supplier_id"]);
  $supplier = $entityManager->find("\Roloffice\Entity\Client", $supplier_id);
  
  $title = htmlspecialchars($_POST["title"]);
  $note = htmlspecialchars($_POST["note"]);
  
  // Save a new order.
  $newOrder = new \Roloffice\Entity\Order();
  
  $newOrder->setOrdinalNumInYear($ordinal_num_in_year);
  $newOrder->setSupplier($supplier);
  $newOrder->setTitle($title);
  $newOrder->setNote($note);
  $newOrder->setStatus(0);
  $newOrder->setIsArchived(0);
  
  $newOrder->setDate(new DateTime("now"));
  $newOrder->setCreatedAt(new DateTime("now"));
  $newOrder->setCreatedByUser($user);
  $newOrder->setModifiedAt(new DateTime("0000-01-01 00:00:00"));
  
  $entityManager->persist($newOrder);
  $entityManager->flush();
  
  // Get id of last Order.
  $new_order_id = $newOrder->getId();
  
  // Set Ordinal Number In Year.
  $entityManager->getRepository('Roloffice\Entity\Order')->setOrdinalNumInYear($new_order_id);
  
  // If exist project in Order, then add $newOrder to table v6_projects_orders.
  if (NULL != $_POST["project_id"] ) {
    $project_id = htmlspecialchars($_POST["project_id"]);
    $project = $entityManager->find("\Roloffice\Entity\Project", $project_id);
    
    $project->getOrders()->add($newOrder);
    $entityManager->flush();
  }
  
  die('<script>location.href = "?view&order_id=' .$new_order_id. '" </script>');
}