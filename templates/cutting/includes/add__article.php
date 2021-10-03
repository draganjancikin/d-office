<?php
// Add Article to CuttingSheet.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addArticle"]))  {
  
  $cutting_sheet_id = htmlspecialchars($_POST['cutting_sheet_id']);
  $cutting_sheet = $entityManager->find("\Roloffice\Entity\CuttingSheet", $cutting_sheet_id);
  
  $fence_model_id = htmlspecialchars($_POST['fence_model_id']);
  $fence_model = $entityManager->find("\Roloffice\Entity\FenceModel", $fence_model_id);

  $picket_width = htmlspecialchars($_POST['picket_width']);
  $width = htmlspecialchars($_POST['width']);
  $height = htmlspecialchars($_POST['height']);
  if($_POST['mid_height']){
      $mid_height = htmlspecialchars($_POST['mid_height']);    
  } else {
      $mid_height = 0;
  }
  
  $space = htmlspecialchars($_POST['space']);
  $number_of_fields = htmlspecialchars($_POST['number_of_fields']);

  $newCuttingShettArticle = new \Roloffice\Entity\CuttingSheetArticle();

  $newCuttingShettArticle->setCuttingSheet($cutting_sheet);
  $newCuttingShettArticle->setFenceModel($fence_model);
  $newCuttingShettArticle->setPicketWidth($picket_width);
  $newCuttingShettArticle->setWidth($width);
  $newCuttingShettArticle->setHeight($height);
  $newCuttingShettArticle->setMidHeight($mid_height);
  $newCuttingShettArticle->setSpace($space);
  $newCuttingShettArticle->setNumberOfFields($number_of_fields);

  $entityManager->persist($newCuttingShettArticle);
  $entityManager->flush();

  die('<script>location.href = "?edit&cutting_sheet_id='.$cutting_sheet_id.'" </script>');
}