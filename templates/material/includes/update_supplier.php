<?php
// Update Material Supplier.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateMaterialSupplier"]) ) {
    // Current logged user.
    $user_id = $_SESSION['user_id'];
    $user = $entityManager->find("\App\Entity\User", $user_id);

    $id = htmlspecialchars($_GET["id"]);
    $material = $entityManager->find('\App\Entity\Material', $id);

    $supplier_id = htmlspecialchars($_POST["supplier_id"]);
    $supplier = $entityManager->find('\App\Entity\Client', $supplier_id);

    $note = htmlspecialchars($_POST["note"]);
    $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;

    $material_supplier_id = htmlspecialchars($_POST["material_supplier_id"]);
    $material_supplier = $entityManager->find('\App\Entity\MaterialSupplier', $material_supplier_id);

    $material_supplier->setMaterial($material);
    $material_supplier->setSupplier($supplier);
    $material_supplier->setNote($note);
    $material_supplier->setPrice($price);

    $material_supplier->setModifiedByUser($user);
    $material_supplier->setModifiedAt(new DateTime("now"));

    $entityManager->flush();

    die('<script>location.href = "?view&id='.$id.'" </script>');
}
