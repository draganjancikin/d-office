<?php
// Add Article to CuttingSheet.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addArticleToCuttingSheet"]))  {
  // TODO: Dragan
  echo "Adding in progress ...";
  exit();
  /*
  $cutting_id = htmlspecialchars($_POST['cutting_id']);
  
  $cutting_fence_model_id = htmlspecialchars($_POST['cutting_fence_model_id']);
  
  $width = htmlspecialchars($_POST['width']);
  $height = htmlspecialchars($_POST['height']);
  if($_POST['mid_height']){
      $mid_height = htmlspecialchars($_POST['mid_height']);    
  } else {
      $mid_height = 0;
  }
  
  $space = htmlspecialchars($_POST['space']);
  $field_number = htmlspecialchars($_POST['field_number']);
  
  $db = new Database();
  
  $db->connection->query(" INSERT INTO cutting_fence_article (cutting_fence_id, cutting_fence_model_id, width, height, mid_height, space, field_number) " 
                   . " VALUES ('$cutting_id', '$cutting_fence_model_id', '$width', '$height', '$mid_height', '$space', '$field_number')") or die(mysqli_error($db->connection));
  
  die('<script>location.href = "?edit&cutting_id='.$cutting_id.'" </script>');
  */
}