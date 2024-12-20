<?php
// Update Material.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateMaterial"])) {
  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);
  
  $id = htmlspecialchars($_GET["id"]);

  if (empty($_POST['name'])) {
    $nameError = 'Ime mora biti upisano';
    die('<script>location.href = "?new&name_error" </script>');
	} else {
    $name = htmlspecialchars($_POST['name']);
  }

  $unit_id = $_POST["unit_id"];
  $unit = $entityManager->find("\App\Entity\Unit", $unit_id);
  
  $weight = htmlspecialchars($_POST['weight']);
  $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
  $note = htmlspecialchars($_POST['note']);

  $material = $entityManager->find('\App\Entity\Material', $id);

  if ($material === null) {
    echo "Client with ID $client_id does not exist.\n";
    exit(1);
  }

  $material->setName($name);
  $material->setUnit($unit);
  $material->setWeight($weight);
  $material->setPrice($price);
  $material->setNote($note);

  $material->setModifiedByUser($user);

  $material->setModifiedAt(new DateTime("now", new DateTimeZone('GMT+2')));

  $entityManager->flush();

  die('<script>location.href = "?view&id='.$id.'" </script>');
}
