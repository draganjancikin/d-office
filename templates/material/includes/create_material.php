<?php
// Create material.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createMaterial"])) {
    // Current logged user.
    $user_id = $_SESSION['user_id'];
    $user = $entityManager->find("\App\Entity\User", $user_id);

    if (empty($_POST['name'])) {
        $nameError = 'Ime mora biti upisano';
        die('<script>location.href = "?new&name_error" </script>');
	} else {
        $name = htmlspecialchars($_POST['name']);
    }

    $unit_id = htmlspecialchars($_POST['unit_id']);
    $unit = $entityManager->find("\App\Entity\Unit", $unit_id);
    $weight = $_POST['weight'] ? htmlspecialchars($_POST['weight']) : 0;
    $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;
    $min_obrac_mera = 0;
    $note = htmlspecialchars($_POST["note"]);

    // Check if name already exist in database.
    $control_name = $entityManager->getRepository('\App\Entity\Material')->findBy( array('name' => $name) );

    if ($control_name) {
        echo "Username already exist in database. Please choose new username!";
        exit(1);
        // die('<script>location.href = "?alert&ob=2" </script>');
    }

    $newMaterial = new \App\Entity\Material();

    $newMaterial->setName($name);
    $newMaterial->setUnit($unit);
    $newMaterial->setWeight($weight);
    $newMaterial->setPrice($price);
    $newMaterial->setMinCalcMeasure($min_obrac_mera);
    $newMaterial->setNote($note);
    $newMaterial->setCreatedAt(new DateTime("now"));
    $newMaterial->setCreatedByUser($user);
    $newMaterial->setModifiedAt(new DateTime("0000-01-01 00:00:00"));

    $entityManager->persist($newMaterial);
    $entityManager->flush();

    // Get last id and redirect.
    $new_id = $newMaterial->getId();
    die('<script>location.href = "?view&id='.$new_id.'" </script>');
}
