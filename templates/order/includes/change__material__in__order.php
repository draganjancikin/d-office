<?php
// Change Material inside Order.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editMaterialDataInOrder"]) ) {
  $order_id = htmlspecialchars($_GET["order_id"]);
  $material_on_order_id = htmlspecialchars($_GET["material_on_order_id"]);

  $order_material = $entityManager->find('\Roloffice\Entity\OrderMaterial', $material_on_order_id);

  $old_material = $entityManager->find('\Roloffice\Entity\Material', $order_material->getMaterial()->getId());
  $old_material_id = $old_material->getId();

  $new_material_id = htmlspecialchars($_POST["material_id"]);
  $new_material = $entityManager->find('\Roloffice\Entity\Material', $new_material_id);

  // Check if material_id in Order changed.
  if ($old_material_id != $new_material_id){
    // Remove the Properties of the old Material. (from table v6__order__materials__properties)
    if (
      $order__material__properties = $entityManager->getRepository('\Roloffice\Entity\OrderMaterialProperty')->findBy(['order_material' => $material_on_order_id], [])
    ) {
      foreach ($order__material__properties as $order__material__property) {
        $orderMaterialProperty = $entityManager->find("\Roloffice\Entity\OrderMaterialProperty", $order__material__property->getId());
        $entityManager->remove($orderMaterialProperty);
        $entityManager->flush();
      }
    }

    // Change Material from old to new.
    $order_material->setMaterial($new_material);
    $order_material->setPrice($new_material->getPrice());
    $order_material->setNote("");
    $order_material->setPieces(1);
    $entityManager->flush();

    // insert Material properties in table v6__order__materials__properties
    $material_properties = $entityManager->getRepository('\Roloffice\Entity\MaterialProperty')->getMaterialProperties($new_material->getId());
    foreach ($material_properties as $material_property) {
      // insert to v6__order__materials__properties
      $newOrderMaterialProperty = new \Roloffice\Entity\OrderMaterialProperty();

      $newOrderMaterialProperty->setOrderMaterial($order_material);
      $newOrderMaterialProperty->setProperty($material_property->getProperty());
      $newOrderMaterialProperty->setQuantity(0);

      $entityManager->persist($newOrderMaterialProperty);
      $entityManager->flush();
    }
  }

  die('<script>location.href = "?edit&id='.$order_id.'" </script>');
}
