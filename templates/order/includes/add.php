<?php
use Roloffice\Core\Database;

// add new order
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addOrder"]) ) { 
    
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');

    $supplier_id = htmlspecialchars($_POST["supplier_id"]);
    $project_id = htmlspecialchars($_POST["project_id"]);
    $title = htmlspecialchars($_POST["title"]);
    $note = htmlspecialchars($_POST["note"]);
  
    $db = new Database();

    $sql = "INSERT INTO orderm (date, supplier_id, project_id, title, note ) VALUES ( '$date', '$supplier_id', '$project_id', '$title', '$note' )";
    
    if ($db->connection->query($sql) === TRUE) {
        // echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $db->connection->error;
        exit();
    }
        
    $order_id = $db->connection->insert_id;
    $o_id = $order->setOid();

    die('<script>location.href = "?view&order_id=' .$order_id. '" </script>');
}

// duplicate material in order
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["duplicateMaterialInOrder"])) {
    
    $order_id = htmlspecialchars($_GET["order_id"]);
    
    // id in table order_material
    $orderm_material_id = htmlspecialchars($_GET["orderm_material_id"]);

    // sledeća metoda duplicira material iz orderm_material i property-e iz orderm_material_property
    $order->duplicateMaterialInOrder($orderm_material_id);

    die('<script>location.href = "?edit&order_id='.$order_id.'" </script>');
    
  }
