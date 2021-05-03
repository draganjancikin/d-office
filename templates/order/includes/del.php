<?php
use Roloffice\Core\Database;

// delete order
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delOrder"]) ) {

    $order_id = htmlspecialchars($_GET["order_id"]);

    $db = new Database();

    // prvo treba obrisati artikle iz tabele orderm_article
    $result_article = $db->connection->query("SELECT * FROM orderm_material WHERE order_id='$order_id'") or die(mysqli_error($db->connection));

    while($row_article = mysqli_fetch_array($result_article)){
      $db->connection->query("DELETE FROM orderm_material WHERE order_id='$order_id' ") or die(mysqli_error($db->connection));
    }

    $db->connection->query("DELETE FROM orderm WHERE id='$order_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?name=&search=" </script>');
}
