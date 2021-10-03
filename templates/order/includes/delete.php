<?php

// Delete Order.
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["deleteOrder"]) ) {
  
  $order_id = htmlspecialchars($_GET["order_id"]);
  
  // Check if exist Order.
  if ($order = $entityManager->find("\Roloffice\Entity\Order", $order_id)) {
    
    // Check if exist Materials in Order.
    if ($order_materials = $entityManager->getRepository('\Roloffice\Entity\OrderMaterial')->getOrderMaterials($order_id)) {

      // Loop trough all materials
      foreach ($order_materials as $order_material) {
        
        // Check if exist Properties in Order Material
        if ($order_material_properties = $entityManager->getRepository('\Roloffice\Entity\OrderMaterialProperty')->getOrderMaterialProperties($order_material->getId())) {
          
          // Remove Properties.
          foreach ($order_material_properties as $order_material_property) {
            $orderMaterialProperty = $entityManager->find("\Roloffice\Entity\OrderMaterialProperty", $order_material_property->getId());
            $entityManager->remove($orderMaterialProperty);
            $entityManager->flush();
          }
        }

        // Remove Material
        $entityManager->remove($order_material);
        $entityManager->flush();

      }

    }

    // Remove Order
    $entityManager->remove($order);
    $entityManager->flush();

  }

  die('<script>location.href = "?name=&search=" </script>');
}
