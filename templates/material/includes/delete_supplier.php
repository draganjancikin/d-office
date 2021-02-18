<?php
// Delete Material Supplier
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["deleteMaterialSupplier"]) ) {

  $id = htmlspecialchars($_GET["id"]);

  $material_supplier_id = htmlspecialchars($_GET["material_supplier_id"]);
  $material_supplier = $entityManager->find("\Roloffice\Entity\MaterialSupplier", $material_supplier_id);
  
  $entityManager->remove($material_supplier);
  $entityManager->flush();
  
  die('<script>location.href = "?edit&id='.$id.'" </script>');
}
