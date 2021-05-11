<?php
// dodaj materijal u sastavnicu
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["newMaterial"]) ) { 

    echo 'dodaj materijal u sastavnoicu';
    $user_id = $_SESSION['user_id'];

    $date = date('Y-m-d h:i:s');
    $article_id = htmlspecialchars($_POST['article_id']);
    $material_item_id = htmlspecialchars($_POST['material_item_id']);
    $function = htmlspecialchars($_POST['function']);

    $db = new Database();

    $db->connection->query("INSERT INTO article_material (article_id, material_id, function) "
                                           . "VALUES ('$article_id', '$material_item_id', '$function' )") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&article_id='.$article_id.'" </script>');

}
