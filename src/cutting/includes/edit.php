<?php
$user_id = $_SESSION['user_id'];
$date = date('Y-m-d h:i:s');

// edit cutting article
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editCuttingArticle"]) ) {
    
    $cutting_fence_article_id = htmlspecialchars($_POST['cutting_fence_article_id']);
    $cutting_fence_id = htmlspecialchars($_POST['cutting_fence_id']);
    
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
    
    $db = new DBconnection();
    
    $db->connection->query("UPDATE cutting_fence_article "
                     . "SET cutting_fence_model_id = '$cutting_fence_model_id', width = '$width', height = '$height', mid_height = '$mid_height', space = '$space', field_number = '$field_number' WHERE id = '$cutting_fence_article_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&cutting_id='.$cutting_fence_id.'" </script>');
}
