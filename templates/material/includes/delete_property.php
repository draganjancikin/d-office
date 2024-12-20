<?php
// Delete Material Property.
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["deleteMaterialProperty"]) ) {
  
  $id = htmlspecialchars($_GET["id"]);
  
  $material_property_id = htmlspecialchars($_GET["material_property_id"]);
  $material_property = $entityManager->find("\App\Entity\MaterialProperty", $material_property_id);
  
  $entityManager->remove($material_property);
  $entityManager->flush();;
  
  die('<script>location.href = "?edit&id='.$id.'" </script>');
}
