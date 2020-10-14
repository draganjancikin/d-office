<?php
// add new order
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addOrder"]) ) { 
    
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');

    $supplier_id = htmlspecialchars($_POST["supplier_id"]);
    $project_id = htmlspecialchars($_POST["project_id"]);
    $title = htmlspecialchars($_POST["title"]);
    $note = htmlspecialchars($_POST["note"]);
  
    $db = new DB();
    $conn = $db->connectDB();
    $sql = "INSERT INTO orderm (date, supplier_id, project_id, title, note ) VALUES ( '$date', '$supplier_id', '$project_id', '$title', '$note' )";
    
    if ($conn->query($sql) === TRUE) {
        // echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        exit();
    }
        
    $order_id = $conn->insert_id;
    $o_id = $order->setOid();

    die('<script>location.href = "?view&order_id=' .$order_id. '" </script>');
}


// add article to order
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addMaterialInOrder"]) ) {

    $order_id = htmlspecialchars($_GET["order_id"]);

    $material_id = htmlspecialchars($_POST["materijal_id"]);
    $note = htmlspecialchars($_POST["note"]);
    $pieces = htmlspecialchars($_POST["pieces"]);

    $price = $material->getPrice($material_id);
    $tax = $material->getTax();

    $db = new DB();
    $connection = $db->connectDB();

    $connection->query("INSERT INTO orderm_material (order_id, material_id, note, pieces, price, tax) " 
                    . " VALUES ('$order_id', '$material_id', '$note', '$pieces', '$price', '$tax' )") or die(mysqli_error($connection));

    // treba nam i pidb_article_id (id artikla u pidb dokumentu) to je u stvari zadnji unos
    $orderm_material_id = $connection->insert_id;;

    //insert property-a mateijala u tabelu orderm_article_property
    $propertys = $connection->query( "SELECT * FROM material_property WHERE material_id ='$material_id'");
    while($row_property = mysqli_fetch_array($propertys)){
        $property_id = $row_property['property_id'];
        $quantity = 0;
        $connection->query("INSERT INTO orderm_material_property (orderm_material_id, property_id, quantity) " 
                        . " VALUES ('$orderm_material_id', '$property_id', '$quantity' )") or die(mysqli_error($connection));
    }

    die('<script>location.href = "?edit&order_id=' .$order_id. '" </script>');
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
