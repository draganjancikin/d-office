<?php
use Roloffice\Core\Database;

$user_id = $_SESSION['user_id'];

// edit material supplier
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editMaterialSupplier"]) ) { 

    $date = date('Y-m-d h:i:s');
    $material_id = htmlspecialchars($_POST["material_id"]);
    $client_id = htmlspecialchars($_POST["client_id"]);
    $client_id_temp = htmlspecialchars($_POST["client_id_temp"]);

    $code = htmlspecialchars($_POST["code"]);
    $price = str_replace(",", ".", htmlspecialchars($_POST['price']));

    $db = new Database();

    $db->connection->query("UPDATE material_suppliers "
                     . "SET material_id='$material_id', client_id='$client_id', code='$code', price='$price' "
                     . "WHERE (material_id = '$material_id' AND client_id = '$client_id_temp') ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&material_id='.$material_id.'" </script>');
}
