<?php
// delete order
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delOrder"]) ) {

    $order_id = htmlspecialchars($_GET["order_id"]);

    $db = new DB();
    $connection = $db->connectDB();

    // prvo treba obrisati artikle iz tabele orderm_article
    $result_article = $connection->query("SELECT * FROM orderm_material WHERE order_id='$order_id'") or die(mysqli_error($connection));

    while($row_article = mysqli_fetch_array($result_article)){
      $connection->query("DELETE FROM orderm_material WHERE order_id='$order_id' ") or die(mysqli_error($connection));
    }

    $connection->query("DELETE FROM orderm WHERE id='$order_id' ") or die(mysqli_error($connection));

    die('<script>location.href = "?name=&search=" </script>');
}


// delete article from order
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delMaterialInOrder"]) ) {

    $order_id = htmlspecialchars($_GET["order_id"]);
    $orderm_material_id = htmlspecialchars($_GET["orderm_material_id"]);

    // sledeća metoda briše i artikal iz pidb_article i property-e iz pidb_article_property
    $order->delMaterialFromOrder($orderm_material_id);

    die('<script>location.href = "?edit&order_id='.$order_id.'" </script>');
}
