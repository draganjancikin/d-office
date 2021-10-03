<?php

// Create CuttingSheet.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["create"]) ) {

  // Curent logged User.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);
  
  $ordinal_num_in_year = 0;

  $client_id = htmlspecialchars($_POST['client_id']);
  $client = $entityManager->find("\Roloffice\Entity\Client", $client_id);
  
  $newCuttingSheet = new \Roloffice\Entity\CuttingSheet();

  $newCuttingSheet->setOrdinalNumInYear($ordinal_num_in_year);
  $newCuttingSheet->setClient($client);
  $newCuttingSheet->setCreatedAt(new DateTime("now"));
  $newCuttingSheet->setCreatedByUser($user);
  $newCuttingSheet->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

  $entityManager->persist($newCuttingSheet);
  $entityManager->flush();

  // Get id of last CuttingSheet.
  $new__cutting_sheet__id = $newCuttingSheet->getId();
  
  // Set Ordinal Number In Year.
  $entityManager->getRepository('Roloffice\Entity\CuttingSheet')->setOrdinalNumInYear($new__cutting_sheet__id);

  die('<script>location.href = "?view&id='.$new__cutting_sheet__id.'" </script>');
}
