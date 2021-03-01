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
  
  // First save a new order.
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

  // gest last id
  $new_order_id = $newOrder->getId();
  // die('<script>location.href = "?view&order_id=' .$new_order_id. '" </script>');

  // TODO Dragan: then if exist project save order to project
  // One Project can have multiple orders
  
  // Add $newOrder to table v6_projects_orders.
  if (NULL != $_POST["project_id"] ) {
    $project_id = htmlspecialchars($_POST["project_id"]);
    $project = $entityManager->find("\Roloffice\Entity\Project", $project_id);
    
    $project->getOrders()->add($newOrder);
    $entityManager->flush();
  }
  
  die('<script>location.href = "?view&order_id=' .$new_order_id. '" </script>');
  
  /*
  $sql = "INSERT INTO orderm (date, supplier_id, project_id, title, note ) VALUES ( '$date', '$supplier_id', '$project_id', '$title', '$note' )";
  
  if ($db->connection->query($sql) === TRUE) {
      // echo "New record created successfully";
  } else {
      echo "Error: " . $sql . "<br>" . $db->connection->error;
      exit();
  }
      
  $order_id = $db->connection->insert_id;
  $o_id = $order->setOid();

  */
}