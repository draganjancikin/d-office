<?php
use Roloffice\Core\Database;

$user_id = $_SESSION['user_id'];
$date = date('Y-m-d h:i:s');

// edit order
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editOrder"]) ) {
    
    $order_id = htmlspecialchars($_GET["order_id"]);
    $project_id = htmlspecialchars($_POST["project_id"]);
    $title = htmlspecialchars($_POST["title"]);
    $status = htmlspecialchars($_POST["status"]);

    if ( isset($_POST["is_archived"]) && $_POST["is_archived"] == 1 ) {
        $is_archived = htmlspecialchars($_POST["is_archived"]);
    } else {
        $is_archived = 0;
    }

    $note = htmlspecialchars($_POST["note"]);
    
    $db = new Database();

    $db->connection->query(" UPDATE orderm " 
                     . " SET project_id ='$project_id', title='$title', status='$status', is_archived='$is_archived', note='$note' " 
                     . " WHERE id = '$order_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&order_id='.$order_id.'" </script>');
}


// edit material in order
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editMaterialInOrder"]) ) {
    
    $order_id = htmlspecialchars($_GET["order_id"]);
    $orderm_material_id = htmlspecialchars($_GET["orderm_material_id"]);
    $material_id = htmlspecialchars($_POST["material_id"]);
    
    $note = htmlspecialchars($_POST["note"]);
    
    $pieces_1 = htmlspecialchars($_POST["pieces"]);
    $pieces = str_replace(",", ".", $pieces_1);
    
    $price_1 = htmlspecialchars($_POST["price"]);
    $price = str_replace(",", ".", $price_1);
    
    $discounts_1 = htmlspecialchars($_POST["discounts"]);
    $discounts = str_replace(",", ".", $discounts_1);
    
    $db = new Database();
    
    $db->connection->query("UPDATE orderm_material " 
                    . " SET material_id='$material_id', note='$note', pieces='$pieces', price='$price', discounts='$discounts' " 
                    . " WHERE id = '$orderm_material_id' ") or die(mysqli_error($db->connection));
    
    // sa da treba uraditi i update property-a u tabeli pidb_article_property
    // 
    // da bi znali koliko property-a stiže na POST treba da izlistamo sve koji su upisani
    // u tabelu pidb_article_property i da uradimo POST za svaki, naravno u svakom prolazu
    // petlje treba da promenljiva ima naziv koji odgovara property-u
    
    $result_propertys = $db->connection->query("SELECT orderm_material_property.id, property.name "
                                         . "FROM orderm_material_property "
                                         . "JOIN (orderm_material, property)"
                                         . "ON (orderm_material_property.orderm_material_id = orderm_material.id AND orderm_material_property.property_id = property.id) "
                                         . "WHERE orderm_material.id = $orderm_material_id ") or die(mysqli_error($db->connection));
    
    while($row_property = mysqli_fetch_array($result_propertys)){
        
        $id = $row_property['id'];
        $property_name = $row_property['name'];
        
        // ${$property_name} =  htmlspecialchars($_POST["$property_name"]);
        ${$property_name} =  str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));
        
        $db->connection->query("UPDATE orderm_material_property " 
                        . " SET quantity='${$property_name}'  WHERE id = '$id' ") or die(mysqli_error($db->connection));
        
    }
    
    die('<script>location.href = "?edit&order_id='.$order_id.'" </script>');
}
