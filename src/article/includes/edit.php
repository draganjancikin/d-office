<?php
$user_id = $_SESSION['user_id'];

// izmena artikla
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editArticle"]) ) {
    
    $article_id = htmlspecialchars($_GET["article_id"]);
    $date = date('Y-m-d h:i:s');
    $group_id = htmlspecialchars($_POST['group_id']);
    $name = htmlspecialchars($_POST["name"]);
    $unit_id = htmlspecialchars($_POST["unit_id"]);
    if($_POST['weight']){
        $weight = htmlspecialchars($_POST['weight']);
    } else {
        $weight = 0;
    }

    $min_obrac_mera = str_replace(",", ".", htmlspecialchars($_POST['min_obrac_mera']));
    $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
    $note = htmlspecialchars($_POST["note"]);
    
    $db = new DB();
    $connection = $db->connectDB();
    
    $connection->query("UPDATE article 
                        SET group_id='$group_id', name='$name', unit_id='$unit_id', date='$date', weight='$weight', min_obrac_mera='$min_obrac_mera', price='$price', note='$note' 
                        WHERE id = '$article_id' ") or die(mysqli_error($connection));
    
    die('<script>location.href = "?view&article_id='.$article_id.'" </script>');
}
