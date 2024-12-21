<?php
// Create Client.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createClient"])) {
  
    // Current logged User.
    $user_id = $_SESSION['user_id'];
    $user = $entityManager->find("\App\Entity\User", $user_id);

    $type_id = $_POST["type_id"];
    $type = $entityManager->find("\App\Entity\ClientType", $type_id);

    if (empty($_POST['name'])) {
        $nameError = 'Ime mora biti upisano';
        die('<script>location.href = "?new&name_error" </script>');
	} else {
        $name = basicValidation($_POST['name']);
    }

    $name_note = basicValidation($_POST["name_note"]);

    $lb = $_POST["lb"] ?? '';
    $is_supplier = $_POST["is_supplier"] ?? 0;

    $country_id = $_POST["country_id"];
    $country = $entityManager->find("\App\Entity\Country", $country_id);
    $city_id = $_POST["city_id"];
    $city = $entityManager->find("\App\Entity\City", $city_id);
    $street_id = $_POST["street_id"];
    $street = $entityManager->find("\App\Entity\Street", $street_id);
    $home_number = basicValidation($_POST["home_number"]);
    $address_note = basicValidation($_POST["address_note"]);
    $note = basicValidation($_POST["note"]);

    // check if name already exist in database
    $control_name = $entityManager->getRepository('\App\Entity\Client')->findBy( array('name' => $name) );
    if ($control_name) {
        echo "Username already exist in database. Please choose new username!";
        exit(1);
        // die('<script>location.href = "?alert&ob=2" </script>');
    }

    $newClient = new \App\Entity\Client();

    $newClient->setType($type);
    $newClient->setName($name);
    $newClient->setNameNote($name_note);
    $newClient->setLb($lb);
    $newClient->setIsSupplier($is_supplier);
    $newClient->setCountry($country);
    $newClient->setCity($city);
    $newClient->setStreet($street);
    $newClient->setHomeNumber($home_number);
    $newClient->setAddressNote($address_note);
    $newClient->setNote($note);
    $newClient->setCreatedAt(new DateTime("now"));
    $newClient->setCreatedByUser($user);
    $newClient->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

    $entityManager->persist($newClient);
    $entityManager->flush();

    // Get last id and redirect.
    $new_client_id = $newClient->getId();
    die('<script>location.href = "?view&client_id='.$new_client_id.'" </script>');
}
