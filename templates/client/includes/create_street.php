<?php
// Create street.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createStreet"])) {

    $current_user_id = $_SESSION['user_id'];
    $current_user = $entityManager->find("\App\Entity\User", $current_user_id);

    if (empty($_POST['name'])) {
        $nameError = 'Ime mora biti upisano';
        die('<script>location.href = "?new&name_error" </script>');
	} else {
        $name = basicValidation($_POST['name']);
    }

    // Check if name already exist in database.
    $control_name = $entityManager->getRepository('\App\Entity\Street')->findBy(['name' => $name]);
    if ($control_name) {
        echo 'Ulica sa nazivom: "<strong>'.$name.'</strong>" vec postoji u bazi podataka. Molimo upisite novo ime!';
        echo '<a href="/">Povratak na pocetnu stranicu</a>';
        exit(1);
        // die('<script>location.href = "?alert&ob=2" </script>');
    }

    $newStreet = new \App\Entity\Street();

    $newStreet->setName($name);
    $newStreet->setCreatedAt(new DateTime("now"));
    $newStreet->setCreatedByUser($current_user);
    $newStreet->setModifiedAt(new DateTime("0000-01-01 00:00:00"));

    $entityManager->persist($newStreet);
    $entityManager->flush();

    die('<script>location.href = "/clients/" </script>');
}