<?php
// TODO: Remove material from order.
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["removeMaterialFromOrder"]) ) {
  
  $order_id = htmlspecialchars($_GET["order_id"]);

  $order_material_id = htmlspecialchars($_GET["order_material_id"]);
  $order_material = $entityManager->find("\Roloffice\Entity\OrderMaterial", $order_material_id);

  echo "Removing material from order ...";

  // First remove properties from table v6_orders_materials_properties.

  // Second materials from table v6_orders_materials where id = $order_material_id.
  $entityManager->remove($order_material);
  $entityManager->flush();
 

  /*
  // sledeća metoda briše i artikal iz pidb_article i property-e iz pidb_article_property
  $order->delMaterialFromOrder($orderm_material_id);

  public function delMaterialFromOrder($orderm_material_id){

        $this->delete("DELETE FROM orderm_material WHERE id='$orderm_material_id' ");
        
        $propertys = $this->get("SELECT * FROM orderm_material_property WHERE orderm_material_id = '$orderm_material_id' ");
        foreach($propertys as $property) {
            $property_id = $property['id'];
            $this->delete("DELETE FROM orderm_material_property WHERE id='$property_id' ");
        }
    }

  die('<script>location.href = "?edit&order_id='.$order_id.'" </script>');
  */
}
