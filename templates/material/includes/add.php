<?php
use Roloffice\Core\Database;

// dodaj dobavljača
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["newSupplier"]) ) {  
  
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');
    $material_id = htmlspecialchars($_POST['material_id']);
    $client_id = htmlspecialchars($_POST['client_id']);
    $code = htmlspecialchars($_POST['code']);
    if($_POST['price']) {
      $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
    } else {
      $price = 0;
    }
    if($client_id == "") die('<script>location.href = "?inc=alert&ob=4" </script>');
    
    $db = new Database();
  
    $db->connection->query("INSERT INTO material_suppliers (material_id, client_id, code, price, date ) "
                   . "VALUES ('$material_id', '$client_id', '$code', '$price', '$date' )") or die(mysqli_error($db->connection));

    die('<script>location.href = "?edit&material_id='.$material_id.'" </script>');
}

// add property to material
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["newProperty"]) ) {

    $user_id = $_SESSION['user_id'];

    $date = date('Y-m-d h:i:s');
    $material_id = htmlspecialchars($_POST['material_id']);
    $property_item_id = htmlspecialchars($_POST['property_item_id']);
    $min = htmlspecialchars($_POST['min']);
    $max = htmlspecialchars($_POST['max']);

    $db = new Database();

    $db->connection->query("INSERT INTO material_property (material_id, property_id, min, max) "
                     . "VALUES ('$material_id', '$property_item_id', '$min', '$max')") or die(mysqli_error($db->connection));

    die('<script>location.href = "?edit&material_id='.$material_id.'" </script>');
}
