<?php
// Create city.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createCity"])) {

    $current_user_id = $_SESSION['user_id'];
    $current_user = $entityManager->find("\App\Entity\User", $current_user_id);

    if (empty($_POST['name'])) {
        $nameError = 'Ime mora biti upisano';
        die('<script>location.href = "?new&name_error" </script>');
	} else {
        $name = basicValidation($_POST['name']);
    }

    // Check if name already exist in database.
    $control_name = $entityManager->getRepository('\App\Entity\City')->findBy( array('name' => $name) );
    if ($control_name) {
        echo 'Naselje sa nazivom: "<strong>'.$name.'</strong>" vec postoji u bazi podataka. Molimo upisite novo ime!';
        echo '<a href="/">Povratak na pocetnu stranicu</a>';
        // die('<script>location.href = "?alert&ob=2" </script>');
    }

    $newCity = new \App\Entity\City();

    $newCity->setName($name);
    $newCity->setCreatedAt(new DateTime("now"));
    $newCity->setCreatedByUser($current_user);
    $newCity->setModifiedAt(new DateTime("0001-01-01 00:00:00"));

    $entityManager->persist($newCity);
    $entityManager->flush();

    die('<script>location.href = "/clients/" </script>');
}
