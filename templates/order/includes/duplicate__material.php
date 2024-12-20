<?php

// Duplicate Material in Order.
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["duplicateMaterialInOrder"])) {

  $order_id = htmlspecialchars($_GET["order_id"]);

  $order_material_id = htmlspecialchars($_GET["order_material_id"]);
  $orderMaterial = $entityManager->find("\App\Entity\OrderMaterial", $order_material_id);

  $newOrderMaterial = new \App\Entity\OrderMaterial();

  $newOrderMaterial->setOrder($orderMaterial->getOrder());
  $newOrderMaterial->setMaterial($orderMaterial->getMaterial());
  $newOrderMaterial->setPieces($orderMaterial->getPieces());
  $newOrderMaterial->setPrice($orderMaterial->getPrice());
  $newOrderMaterial->setDiscount(0);
  $newOrderMaterial->setTax($orderMaterial->getTax());
  $newOrderMaterial->setWeight($orderMaterial->getWeight());
  $newOrderMaterial->setNote($orderMaterial->getNote());

  $entityManager->persist($newOrderMaterial);
  $entityManager->flush();

  // Get Properties from old OrderMaterial and add to newOrderMaterial
  $material_on_order_properties = $entityManager->getRepository('\App\Entity\OrderMaterial')->getProperties($order_material_id);

  foreach ($material_on_order_properties as $material_on_order_property) {

    // insert to table v6__orders__materials__properties
    $newOrderMaterialProperty = new \App\Entity\OrderMaterialProperty();

    $newOrderMaterialProperty->setOrderMaterial($newOrderMaterial);
    $newOrderMaterialProperty->setProperty($material_on_order_property->getProperty());
    $newOrderMaterialProperty->setQuantity(0);

    $entityManager->persist($newOrderMaterialProperty);
    $entityManager->flush();

  }

  die('<script>location.href = "?edit&id='.$order_id.'" </script>');
}
