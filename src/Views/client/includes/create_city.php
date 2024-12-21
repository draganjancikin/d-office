<?php

use App\Entity\City;

// Create city.
if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_GET) && str_contains($_GET['url'], 'addCity')) {
  
  // Curent loged user.
  $user = $entityManager->find("\App\Entity\User", $user_id);

  if (empty($_POST['name'])) {
    $nameError = 'Ime mora biti upisano';
    die('<script>location.href = "?new&name_error" </script>');
	}
  else {
    $name = basicValidation($_POST['name']);
  }

  // Check if name already exist in database.
  $control_name = $entityManager->getRepository('\App\Entity\City')->findBy( array('name' => $name) );
  if ($control_name) {
    echo 'City wit name: "<strong>'.$name.'</strong>" already exist in database!';
    exit(1);
    // die('<script>location.href = "?alert&ob=2" </script>');
  }

  $newCity = new City();

  $newCity->setName($name);
  $newCity->setCreatedAt(new DateTime("now"));
  $newCity->setCreatedByUser($user);
  $newCity->setModifiedAt(new DateTime("0001-01-01 00:00:00"));

  $entityManager->persist($newCity);
  $entityManager->flush();

  die('<script>location.href = "/clients/" </script>');
}