<?php 
// Update Preferences.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updatePreferences"]) ) {
  $kurs = str_replace(",", ".", htmlspecialchars($_POST["kurs"]));
  $tax = str_replace(",", ".", htmlspecialchars($_POST["tax"]));
  
  $preferences = $entityManager->find('\Roloffice\Entity\Preferences', 1);
  
  $preferences->setKurs($kurs);
  $preferences->setTax($tax);
  $entityManager->flush();

  die('<script>location.href = "?set" </script>');
}
