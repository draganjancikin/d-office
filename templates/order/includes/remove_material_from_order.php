<?php
// Remove material from Order.
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["removeMaterialFromOrder"]) ) {
  
  $order_id = htmlspecialchars($_GET["order_id"]);

  $order_material_id = htmlspecialchars($_GET["order_material_id"]);
  $order_material = $entityManager->find("\Roloffice\Entity\OrderMaterial", $order_material_id);

  // First remove properties from table v6_orders_materials_properties.
  if ($order_material_properties = $entityManager->getRepository('\Roloffice\Entity\OrderMaterialProperty')->getOrderMaterialProperties($order_material_id)) {
    foreach ($order_material_properties as $order_material_property) {
      $orderMaterialProperty = $entityManager->find("\Roloffice\Entity\OrderMaterialProperty", $order_material_property->getId());
      $entityManager->remove($orderMaterialProperty);
      $entityManager->flush();
    }
  }
  
  // Second remove materials from table v6_orders_materials
  $entityManager->remove($order_material);
  $entityManager->flush();

  die('<script>location.href = "?edit&order_id='.$order_id.'" </script>');
}
