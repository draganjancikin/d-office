<?php
$user_id = $_SESSION['user_id'];

// edit material
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editMaterial"]) ) {

    $material_id = htmlspecialchars($_GET["material_id"]);
    $date = date('Y-m-d h:i:s');
    $name = htmlspecialchars($_POST["name"]);
    $unit_id = htmlspecialchars($_POST["unit_id"]);
    $weight = htmlspecialchars($_POST['weight']);
    $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
    $note = htmlspecialchars($_POST['note']);

    $db = new DBconnection();

    $db->connection->query("UPDATE material "
                     . "SET name='$name', unit_id='$unit_id', date='$date', weight='$weight', price='$price', note='$note' "
                     . "WHERE id = '$material_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&material_id='.$material_id.'" </script>');
}

// edit material supplier
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editMaterialSupplier"]) ) { 

    $date = date('Y-m-d h:i:s');
    $material_id = htmlspecialchars($_POST["material_id"]);
    $client_id = htmlspecialchars($_POST["client_id"]);
    $client_id_temp = htmlspecialchars($_POST["client_id_temp"]);

    $code = htmlspecialchars($_POST["code"]);
    $price = str_replace(",", ".", htmlspecialchars($_POST['price']));

    $db = new DBconnection();

    $db->connection->query("UPDATE material_suppliers "
                     . "SET material_id='$material_id', client_id='$client_id', code='$code', price='$price' "
                     . "WHERE (material_id = '$material_id' AND client_id = '$client_id_temp') ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&material_id='.$material_id.'" </script>');
}
