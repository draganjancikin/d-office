<?php
/**
 * Delete note from project.
 */
// brisanje beleške iz projekta
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['delNote']) ) {
  
  $note_id = htmlspecialchars($_GET["note_id"]);
  $note = $entityManager->find("\Roloffice\Entity\ProjectNote", $note_id);
  
  $project_id = htmlspecialchars($_GET["project_id"]);
  
  $entityManager->remove($note);
  $entityManager->flush();

  die('<script>location.href = "?view&project_id='.$project_id.'" </script>');
}
