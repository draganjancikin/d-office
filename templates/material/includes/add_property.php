<?php
// Add Property to Material.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addProperty"]) ) {
  $material_id = htmlspecialchars($_POST['id']);
  $material = $entityManager->find("\App\Entity\Material", $material_id);
  
  $property_item_id = htmlspecialchars($_POST['property_item_id']);
  $property = $entityManager->find("\App\Entity\Property", $property_item_id);
  
  $min_size = htmlspecialchars($_POST['min_size']);
  $max_size = htmlspecialchars($_POST['max_size']);
  
  $newMaterialproperty = new \App\Entity\MaterialProperty();
  
  $newMaterialproperty->setMaterial($material);
  $newMaterialproperty->setProperty($property);
  $newMaterialproperty->setMinSize($min_size);
  $newMaterialproperty->setMaxSize($max_size);
  
  $entityManager->persist($newMaterialproperty);
  $entityManager->flush();
  
  die('<script>location.href = "?edit&id='.$material_id.'" </script>');
}