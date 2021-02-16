<?php
// Create city.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createCity"])) {
  
  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);

  if (empty($_POST['name'])) {
    $nameError = 'Ime mora biti upisano';
    die('<script>location.href = "?new&name_error" </script>');
	} else {
    $name = basicValidation($_POST['name']);
  }

  // Check if name already exist in database.
  $control_name = $entityManager->getRepository('\Roloffice\Entity\City')->findBy( array('name' => $name) );
  if ($control_name) {
    echo 'City wit name: "<strong>'.$name.'</strong>" already exist in database!';
    exit(1);
    // die('<script>location.href = "?alert&ob=2" </script>');
  }

  $newCity = new \Roloffice\Entity\City();

  $newCity->setName($name);
  $newCity->setCreatedAt(new DateTime("now"));
  $newCity->setCreatedByUser($user);
  $newCity->setModifiedAt(new DateTime("0001-01-01 00:00:00"));

  $entityManager->persist($newCity);
  $entityManager->flush();

  die('<script>location.href = "index.php" </script>');
}