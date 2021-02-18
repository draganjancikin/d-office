<?php
// Add Property to Material.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addProperty"]) ) {
  $id = htmlspecialchars($_POST['id']);
  $material = $entityManager->find("\Roloffice\Entity\Material", $id);
  
  $property_item_id = htmlspecialchars($_POST['property_item_id']);
  $property = $entityManager->find("\Roloffice\Entity\Property", $property_item_id);
  
  $min_size = htmlspecialchars($_POST['min_size']);
  $max_size = htmlspecialchars($_POST['max_size']);
  
  $newMaterialproperty = new \Roloffice\Entity\MaterialProperty();
  
  $newMaterialproperty->setMaterial($material);
  $newMaterialproperty->setProperty($property);
  $newMaterialproperty->setMinSize($min_size);
  $newMaterialproperty->setMaxSize($max_size);
  
  $entityManager->persist($newMaterialproperty);
  $entityManager->flush();
  
  die('<script>location.href = "?edit&id='.$id.'" </script>');
}