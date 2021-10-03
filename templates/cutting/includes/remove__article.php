<?php

// Remove Article from CuttingSheet.
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["removeArticle"])) {

  $id = htmlspecialchars($_GET['id']);
  $cutting_sheet__article_id = htmlspecialchars($_GET['cutting_sheet__article_id']);
  $cutting_sheet__article = $entityManager->find("\Roloffice\Entity\CuttingSheetArticle", $cutting_sheet__article_id);

  $entityManager->remove($cutting_sheet__article);
  $entityManager->flush();

  die('<script>location.href = "?edit&id='.$id.'" </script>');
}