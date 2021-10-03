<?php
// Update order.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateOrder"]) ) {
  
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
  
  $order->setTitle($title);
  $order->setStatus($status);
  $order->setIsArchived($is_archived);
  $order->setNote($note);
  $entityManager->flush();
  
  // Update order in project if project exist
  if (NULL != $_POST["project_id"] ) {
    
    $old_project_id = $_POST["old_project_id"];
    $new_project_id = htmlspecialchars($_POST["project_id"]);
    
    $new_project = $entityManager->find("\Roloffice\Entity\Project", $new_project_id);
    $old_project = $entityManager->find("\Roloffice\Entity\Project", $old_project_id);
    
    if ( $old_project_id != $new_project_id) {

      if ( $old_project_id  != 0) {
        // delete order form old project
        $old_project->getOrders()->removeElement($order);
      }
    
      // add order to new project
      $new_project->getOrders()->add($order);
      $entityManager->flush();
    }
  
  }
  die('<script>location.href = "?view&id='.$order_id.'" </script>');
}
