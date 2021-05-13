<?php

// Update CuttingSheetArticle.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateCuttingSheetArticle"]) ) {
  
  $cutting_sheet_id = htmlspecialchars($_POST['cutting_sheet_id']);
  
  $cutting_sheet__article_id = htmlspecialchars($_POST['cutting_sheet__article_id']);
  $cutting_sheet__article = $entityManager->find("\Roloffice\Entity\CuttingSheetArticle", $cutting_sheet__article_id);
  
  $fence_model_id = htmlspecialchars($_POST['fence_model_id']);
  $fence_model = $entityManager->find("\Roloffice\Entity\FenceModel", $fence_model_id);

  $width = htmlspecialchars($_POST['width']);
  $height = htmlspecialchars($_POST['height']);
  
  if($_POST['mid_height']){
      $mid_height = htmlspecialchars($_POST['mid_height']);    
  } else {
      $mid_height = 0;
  }
  
  $space = htmlspecialchars($_POST['space']);
  $number_of_fields = htmlspecialchars($_POST['number_of_fields']);



  $cutting_sheet__article->setFenceModel($fence_model);
  $cutting_sheet__article->setWidth($width);
  $cutting_sheet__article->setHeight($height);
  $cutting_sheet__article->setMidHeight($mid_height);
  $cutting_sheet__article->setSpace($space);
  $cutting_sheet__article->setNumberOfFields($number_of_fields);
  
  $entityManager->flush();
  
  die('<script>location.href = "?view&cutting_sheet_id='.$cutting_sheet_id.'" </script>');
}