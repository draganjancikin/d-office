<?php
// Update Client.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateClient"])) {
  
  // curent loged user
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);

  $client_id = $_POST["client_id"];

  $type_id = $_POST["type_id"];
  $type = $entityManager->find("\App\Entity\ClientType", $type_id);

  if (empty($_POST['name'])) {
    $nameError = 'Ime mora biti upisano';
    die('<script>location.href = "?new&name_error" </script>');
	} else {
    $name = basicValidation($_POST['name']);
  }

  $name_note = basicValidation($_POST["name_note"]);

  if(isset($_POST["lb"])) {
    $lb = $_POST["lb"];
  } else {
    $lb = "";
  }

  if(isset($_POST["is_supplier"])) {
    $is_supplier = $_POST["is_supplier"];
  } else {
    $is_supplier = 0;
  }

  $country_id = $_POST["country_id"];
  $country = $entityManager->find("\App\Entity\Country", $country_id);
  $city_id = $_POST["city_id"];
  $city = $entityManager->find("\App\Entity\City", $city_id);
  $street_id = $_POST["street_id"];
  $street = $entityManager->find("\App\Entity\Street", $street_id);
  $home_number = basicValidation($_POST["home_number"]);
  $address_note = basicValidation($_POST["address_note"]);
  $note = basicValidation($_POST["note"]);

  $client = $entityManager->find('\App\Entity\Client', $client_id);

  if ($client === null) {
    echo "Client with ID $client_id does not exist.\n";
    exit(1);
  }

  $client->setType($type);
  $client->setName($name);
  $client->setNameNote($name_note);
  $client->setLb($lb);
  $client->setIsSupplier($is_supplier);
  $client->setCountry($country);
  $client->setCity($city);
  $client->setStreet($street);
  $client->setHomeNumber($home_number);
  $client->setAddressNote($address_note);
  $client->setNote($note);
  $client->setModifiedByUser($user);
  $client->setModifiedAt(new DateTime("now"));

  $entityManager->flush();

  die('<script>location.href = "?view&client_id='.$client_id.'" </script>');

}