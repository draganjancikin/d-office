<?php
// Add Supplier to Material.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addSupplier"]) ) {  
    $current_user_id = $_SESSION['user_id'];
    $current_user = $entityManager->find("\App\Entity\User", $current_user_id);

    $id = htmlspecialchars($_POST['id']);
    $material = $entityManager->find("\App\Entity\Material", $id);

    $supplier_id = htmlspecialchars($_POST['supplier_id']);
    if ($supplier_id == "") die('<script>location.href = "?inc=alert&ob=4" </script>');
    $supplier = $entityManager->find("\App\Entity\Client", $supplier_id);

    $note = htmlspecialchars($_POST['note']);

    if ($_POST['price']) {
        $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
    } else {
        $price = 0;
    }

    $newMaterialSupplier = new \App\Entity\MaterialSupplier();

    $newMaterialSupplier->setMaterial($material);
    $newMaterialSupplier->setSupplier($supplier);
    $newMaterialSupplier->setNote($note);
    $newMaterialSupplier->setPrice($price);
    $newMaterialSupplier->setCreatedAt(new DateTime("now"));
    $newMaterialSupplier->setCreatedByUser($current_user);
    $newMaterialSupplier->setModifiedAt(new DateTime("1070-01-01 00:00:00"));

    $entityManager->persist($newMaterialSupplier);
    $entityManager->flush();

    die('<script>location.href = "?edit&id='.$id.'" </script>');
}
