<?php
// new cutting
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["newCutting"]) ) {
    
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');
    $client_id = htmlspecialchars($_POST['client_id']);
    
    $db = new DB();
    $connect_db = $db->connectDB();
    
    $connect_db->query(" INSERT INTO cutting_fence ( date, client_id) " 
                     . " VALUES ( '$date', '$client_id')") or die(mysqli_error($connect_db));

    $cutting_id = $connect_db->insert_id;
    $c_id = $cutting->setCid();
    
    die('<script>location.href = "?view&cutting_id='.$cutting_id.'" </script>');
}


// add new article to cutting
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addArticleToCutting"]))  {
    
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
    
    $db = new DB();
    $connect_db = $db->connectDB();
    
    $connect_db->query(" INSERT INTO cutting_fence_article (cutting_fence_id, cutting_fence_model_id, width, height, mid_height, space, field_number) " 
                     . " VALUES ('$cutting_id', '$cutting_fence_model_id', '$width', '$height', '$mid_height', '$space', '$field_number')") or die(mysqli_error($connect_db));
    
    die('<script>location.href = "?edit&cutting_id='.$cutting_id.'" </script>');
}
