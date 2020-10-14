<?php
// export predračuna u otpremnicu
if(isset($_GET["exportProformaToDispatch"]) ) {

    $pidb_id = htmlspecialchars($_GET["pidb_id"]);
    $pidb_tip_id = 2;
    $date = date('Y-m-d h:i:s');
    $date_control = date ('Y');

    $db = new DB();
    $connection = $db->connectDB();

    $result_pidbs = $connection->query("SELECT * FROM pidb WHERE id = '$pidb_id' ") or die(mysqli_error($connection));
        $row_pidb = mysqli_fetch_array($result_pidbs);
        $client_id = $row_pidb['client_id'];
        $project_id = $row_pidb['project_id'];
        $title = $row_pidb['title'];
        $note = $row_pidb['note'];
    
    $connection->query("INSERT INTO pidb (tip_id, date, client_id, parent_id, project_id, title, note) VALUES ('$pidb_tip_id', '$date', '$client_id', '$pidb_id', '$project_id', '$title', '$note')") or die(mysqli_error($connection));
    
    $pidb_id_last = $connection->insert_id;
    $y_id = $pidb->setYid($pidb_tip_id);    
    
    $result_pidb_articles = $connection->query("SELECT * FROM pidb_article WHERE pidb_id = '$pidb_id'") or die(mysqli_error($connection));
    while($row_pidb_article = mysqli_fetch_array($result_pidb_articles)){
        $pidb_article_id = $row_pidb_article['id'];
        $article_id = $row_pidb_article['article_id'];
        $article_note = $row_pidb_article['note'];
        $article_pieces = $row_pidb_article['pieces'];
        $article_price = $row_pidb_article['price'];
        $article_discounts = $row_pidb_article['discounts'];
        $article_tax = $row_pidb_article['tax'];
        $article_weight = $row_pidb_article['weight'];

        $connection->query("INSERT INTO pidb_article (pidb_id, article_id, note, pieces, price, discounts, tax, weight) " 
                        . " VALUES ('$pidb_id_last', '$article_id', '$article_note', '$article_pieces', '$article_price', '$article_discounts', '$article_tax', '$article_weight' )") or die(mysqli_error($connection));

        $pidb_article_id_last = $connection->insert_id;
    
        // za svaki artikal u predračunu treba proveriti da li postoji property i ako postoji upisati 
        // ga i za novootvorenu otpremnicu

        $result_pidb_articles_propertys = $connection->query("SELECT * FROM pidb_article_property WHERE pidb_article_id = '$pidb_article_id'") or die(mysqli_error($connection));
        while($row_pidb_articles_property = mysqli_fetch_array($result_pidb_articles_propertys)){
            $property_id = $row_pidb_articles_property['property_id'];
            $quantity = $row_pidb_articles_property['quantity'];

            $connection->query("INSERT INTO pidb_article_property (pidb_article_id, property_id, quantity) VALUES ('$pidb_article_id_last', '$property_id', '$quantity' )") or die(mysqli_error($connection));
  
        }
    
    }

    die('<script>location.href = "?view&pidb_id='.$pidb_id_last.'&pidb_tip_id='.$pidb_tip_id.'" </script>');
}
