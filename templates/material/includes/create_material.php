<?php
// Create material.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createMaterial"])) {
  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);
  
  if (empty($_POST['name'])) {
    $nameError = 'Ime mora biti upisano';
    die('<script>location.href = "?new&name_error" </script>');
	} else {
    $name = htmlspecialchars($_POST['name']);
  }
  
  $unit_id = htmlspecialchars($_POST['unit_id']);
  $unit = $entityManager->find("\Roloffice\Entity\Unit", $unit_id);

  if($_POST['weight']) {
    $weight = htmlspecialchars($_POST['weight']);
  } else {
    $weight = 0;
  }

  if($_POST['price']) {
    $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
  } else {
    $price = 0;
  }

  $min_obrac_mera = 0;

  $note = htmlspecialchars($_POST["note"]);

  // check if name already exist in database
  $control_name = $entityManager->getRepository('\Roloffice\Entity\Material')->findBy( array('name' => $name) );
  
  if ($control_name) {
    echo "Username already exist in database. Please choose new username!";
    exit(1);
    // die('<script>location.href = "?alert&ob=2" </script>');
  }
 
  $newMaterial = new \Roloffice\Entity\Material();
 
  $newMaterial->setName($name);
  $newMaterial->setUnit($unit);
  $newMaterial->setWeight($weight);
  $newMaterial->setPrice($price);
  $newMaterial->setMinObracMera($min_obrac_mera);
  $newMaterial->setNote($note);
  $newMaterial->setCreatedAt(new DateTime("now"));
  $newMaterial->setCreatedByUser($user);
  $newMaterial->setModifiedAt(new DateTime("0000-01-01 00:00:00"));

  $entityManager->persist($newMaterial);
  $entityManager->flush();

  // gest last id and redirect
  $new_material_id = $newMaterial->getId();
  die('<script>location.href = "?viewMaterial&material_id='.$material_id.'" </script>');
}
