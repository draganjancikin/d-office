<?php

use App\Entity\Country;

// Create country.
if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_GET) && str_contains($_GET['url'], 'addCountry')) {
  
  $user = $entityManager->find("\App\Entity\User", $user_id);

  if (empty($_POST['name'])) {
    $nameError = 'Ime mora biti upisano';
    die('<script>location.href = "?new&name_error" </script>');
	}
  else {
    $name = basicValidation($_POST['name']);
  }

  // Check if name already exist in database.
  $control_country = $entityManager->getRepository('\App\Entity\Country')->findBy( array('name' => $name) );
  if ($control_country) {
    echo 'Country with name: "<strong>'.$name.'</strong>" already exist in database. Please choose new name!';
    exit(1);
    // die('<script>location.href = "?alert&ob=2" </script>');
  }

  $abbr = "";
  if (!empty($_POST['abbr'])) {
    $abbr = basicValidation($_POST['abbr']);
  }

  $newCountry = new Country();

  $newCountry->setName($name);
  $newCountry->setAbbr($abbr);
  $newCountry->setCreatedAt(new DateTime("now"));
  $newCountry->setCreatedByUser($user);
  $newCountry->setModifiedAt(new DateTime("0000-01-01 00:00:00"));

  $entityManager->persist($newCountry);
  $entityManager->flush();

  die('<script>location.href = "/clients/" </script>');
}
