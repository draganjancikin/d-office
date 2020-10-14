<?php
// export cutting to proforma-invoice
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["exportCuttingToPidb"]) ) {

    $cutting_id = htmlspecialchars($_GET['cutting_id']);

    $db = new DB();
    $connect_db = $db->connectDB();

    $result_cutting_fence = $connect_db->query("SELECT * FROM cutting_fence WHERE id = $cutting_id ") or die(mysqli_error($connect_db));
    $row_cutting_fence = mysqli_fetch_array($result_cutting_fence);
        $client_id = $row_cutting_fence['client_id'];

    $total_picket_lenght = htmlspecialchars($_GET['total_picket_lenght']);
    $total_kap = htmlspecialchars($_GET['total_kap']);
    $title = "PVC letvice";
    $note = "ROLOSTIL szr je PDV obveznik.";

    $date = date('Y-m-d h:i:s');
    $pidb_tip_id = 1;

    // add new proforma-invoice
    $connect_db->query("INSERT INTO pidb (tip_id, date, client_id, title, note) VALUES ('$pidb_tip_id', '$date', '$client_id', '$title', '$note')") or die(mysqli_error($connect_db));

    $pidb_id = $connect_db->insert_id;
    $y_id = $pidb->setYid($pidb_tip_id);

    // add article to proforma-invoice

    // first: pvc "letvice"
    $article_id = 6;
    $note = "";
    $pieces = 1;

    $price = $article->getPrice($article_id);
    $tax = $pidb->getTax();

    $connect_db->query("INSERT INTO pidb_article (pidb_id, article_id, note, pieces, price, tax) VALUES ('$pidb_id', '$article_id', '$note', '$pieces', '$price', '$tax' )") or die(mysqli_error($connect_db));

    // treba nam i pidb_article_id (id artikla u pidb dokumentu) to je u stvari zadnji unos
    $pidb_article_id = $connect_db->insert_id;;

    //insert property-a artikla u tabelu pidb_article_property
    $propertys = $connect_db->query( "SELECT * FROM article_property WHERE article_id ='$article_id'");
    while($row_property = mysqli_fetch_array($propertys)){
    
        $property_id = $row_property['property_id'];
        $quantity = $total_picket_lenght/10;

        $connect_db->query("INSERT INTO pidb_article_property (pidb_article_id, property_id, quantity) VALUES ('$pidb_article_id', '$property_id', '$quantity' )") or die(mysqli_error($connect_db));
    }

    // second: pvc caps
    $article_id = 7;
    $note = "";
    $pieces = $total_kap;

    $price = $article->getPrice($article_id);

    $connect_db->query("INSERT INTO pidb_article (pidb_id, article_id, note, pieces, price, tax) VALUES ('$pidb_id', '$article_id', '$note', '$pieces', '$price', '$tax' )") or die(mysqli_error($connect_db));

    die('<script>location.href = "/pidb/index.php?edit&pidb_id='.$pidb_id.'" </script>');
}
