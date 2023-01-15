<?php
// Create country.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createCountry"])) {
    $current_user_id = $_SESSION['user_id'];
    $current_user = $entityManager->find("\App\Entity\User", $current_user_id);

    if (empty($_POST['name'])) {
        $nameError = 'Ime mora biti upisano';
        die('<script>location.href = "?new&name_error" </script>');
	} else {
        $name = basicValidation($_POST['name']);
    }

    // Check if name already exist in database.
    $control_country = $entityManager->getRepository('\App\Entity\Country')->findBy( array('name' => $name) );
    if ($control_country) {
        echo 'Drzava sa nazivom: "<strong>'.$name.'</strong>" vec postoji u bazi podataka. Molimo upisite novo ime!';
        echo '<a href="/">Povratak na pocetnu stranicu</a>';
        exit(1);
        // die('<script>location.href = "?alert&ob=2" </script>');
    }

    if (empty($_POST['abbr'])) {
        $abbr = "";
    } else {
        $abbr = basicValidation($_POST['abbr']);
    }

    $newCountry = new \App\Entity\Country();

    $newCountry->setName($name);
    $newCountry->setAbbr($abbr);
    $newCountry->setCreatedAt(new DateTime("now"));
    $newCountry->setCreatedByUser($current_user);
    $newCountry->setModifiedAt(new DateTime("0000-01-01 00:00:00"));

    $entityManager->persist($newCountry);
    $entityManager->flush();

    die('<script>location.href = "/clients/" </script>');
}