<?php

// Duplicate Material in Order
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["duplicateMaterialInOrder"])) {
    
    $order_id = htmlspecialchars($_GET["order_id"]);
    
    $order_material_id = htmlspecialchars($_GET["order_material_id"]);
    $orderMaterial = $entityManager->find("\Roloffice\Entity\OrderMaterial", $order_material_id);

    $newOrderMaterial = new \Roloffice\Entity\OrderMaterial();

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

    die('<script>location.href = "?edit&order_id='.$order_id.'" </script>');
}
