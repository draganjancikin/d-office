<?php

use App\Entity\Street;

// Create city.
if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_GET) && str_contains($_GET['url'], 'addStreet')) {
  
  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);

  if (empty($_POST['name'])) {
    $nameError = 'Ime mora biti upisano';
    die('<script>location.href = "?new&name_error" </script>');
	}
  else {
    $name = basicValidation($_POST['name']);
  }

  // Check if name already exist in database.
  $control_name = $entityManager->getRepository('\App\Entity\Street')->findBy( array('name' => $name) );
  if ($control_name) {
    echo 'Street wit name: "<strong>'.$name.'</strong>" already exist in database. Please choose new name!';
    exit(1);
    // die('<script>location.href = "?alert&ob=2" </script>');
  }

  $newStreet = new Street();

  $newStreet->setName($name);
  $newStreet->setCreatedAt(new DateTime("now"));
  $newStreet->setCreatedByUser($user);
  $newStreet->setModifiedAt(new DateTime("0000-01-01 00:00:00"));

  $entityManager->persist($newStreet);
  $entityManager->flush();

  die('<script>location.href = "/clients/" </script>');
}