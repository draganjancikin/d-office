<?php

// Edit material in order.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editMaterialInOrder"]) ) {
  
  $order_id = htmlspecialchars($_GET["order_id"]);
  // $order = $entityManager->find("\App\Entity\Order", $order_id);
  
  $order_material_id = htmlspecialchars($_GET["orderm_material_id"]);
  
  $material_id = htmlspecialchars($_POST["material_id"]);
  // $material = $entityManager->find("\App\Entity\Material", $material_id);
  
  $note = htmlspecialchars($_POST["note"]);

  $pieces_1 = htmlspecialchars($_POST["pieces"]);
  $pieces = str_replace(",", ".", $pieces_1);

  $price_1 = htmlspecialchars($_POST["price"]);
  $price = str_replace(",", ".", $price_1);

  $discount_1 = htmlspecialchars($_POST["discount"]);
  $discount = str_replace(",", ".", $discount_1);
  
  $orderMaterial = $entityManager->find("\App\Entity\OrderMaterial", $order_material_id);
  // $orderMaterial->setOrder($order);
  // $orderMaterial->setMaterial($material);
  $orderMaterial->setNote($note);
  $orderMaterial->setPieces($pieces);
  $orderMaterial->setPrice($price);
  $orderMaterial->setDiscount($discount);
  $entityManager->flush();

  // Properies update in table v6_orders_materials_properties
  $order_material_properties = $entityManager->getRepository('\App\Entity\OrderMaterialProperty')->getOrderMaterialProperties($order_material_id);
  foreach ($order_material_properties as $order_material_property) {
    
    // Get property name from $order_material_property.
    $property_name = $order_material_property->getProperty()->getName();
    // Get property value from $_POST
    $property_value = str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));

    $orderMaterialProperty = $entityManager->find("\App\Entity\OrderMaterialProperty", $order_material_property->getId());

    $orderMaterialProperty->setQuantity($property_value);
    $entityManager->flush();
  }
  die('<script>location.href = "?edit&id='.$order_id.'" </script>');
}
