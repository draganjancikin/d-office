<?php

// Add material to order.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addMaterialToOrder"]) ) {
  
  $order_id = htmlspecialchars($_GET["order_id"]);
  $order = $entityManager->find("\Roloffice\Entity\Order", $order_id);
  
  $material_id = htmlspecialchars($_POST["materijal_id"]);
  $material = $entityManager->find("\Roloffice\Entity\Material", $material_id);
  
  $price = $material->getPrice();
  $weight = $material->getWeight();
  
  $pieces = htmlspecialchars($_POST["pieces"]);
  
  $preferences = $entityManager->find('Roloffice\Entity\Preferences', 1);
  $tax = $preferences->getTax();

  $note = htmlspecialchars($_POST["note"]);
  
  $newOrderMaterial = new \Roloffice\Entity\OrderMaterial();

  $newOrderMaterial->setOrder($order);
  $newOrderMaterial->setMaterial($material);
  $newOrderMaterial->setPieces($pieces);
  $newOrderMaterial->setPrice($price);
  $newOrderMaterial->setDiscount(0);
  $newOrderMaterial->setTax($tax);
  $newOrderMaterial->setWeight($weight);
  $newOrderMaterial->setNote($note);

  $entityManager->persist($newOrderMaterial);
  $entityManager->flush();
  
  // Last inserted order material.
  $last_order_material_id = $newOrderMaterial->getId();
  
  //insert material properties in table v6_orders_materials_properties
  $material_properties = $entityManager->getRepository('\Roloffice\Entity\MaterialProperty')->getMaterialProperties($material->getId());
  foreach ($material_properties as $material_property) {
    
    // insert to table v6_orders_materials_properties
    $newOrderMaterialProperty = new \Roloffice\Entity\OrderMaterialProperty();
    $newOrderMaterialProperty->setOrderMaterial($newOrderMaterial);
    $newOrderMaterialProperty->setProperty($material_property->getProperty());
    $newOrderMaterialProperty->setQuantity(0);

    $entityManager->persist($newOrderMaterialProperty);
    $entityManager->flush();

  }

  die('<script>location.href = "?edit&id=' .$order_id. '" </script>');
}
