<?php
$user_id = $_SESSION['user_id'];
$date = date('Y-m-d h:i:s');

// edit documents
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editPidb"]) ) {

    $pidb_id = htmlspecialchars($_GET["pidb_id"]);

    $title = htmlspecialchars($_POST["title"]);
    $client_id = htmlspecialchars($_POST["client_id"]);
    $archived = htmlspecialchars($_POST["archived"]);
    $note = htmlspecialchars($_POST["note"]);

    $db = new DatabaseController();

    $db->connection->query("UPDATE pidb SET title='$title', client_id='$client_id', archived='$archived', note='$note'  WHERE id = '$pidb_id' ") or die(mysqli_error($db->connection));
  
    die('<script>location.href = "?edit&pidb_id='.$pidb_id.'" </script>');
}

// edit article in document
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editArticleInPidb"]) ) {

    $pidb_id = htmlspecialchars($_GET["pidb_id"]);
    $pidb_article_id = htmlspecialchars($_GET["pidb_article_id"]);

    $article_id = htmlspecialchars($_POST["article_id"]);
    
    $db = new DatabaseController();

    $note = htmlspecialchars($_POST["note"]);
    $pieces_1 = htmlspecialchars($_POST["pieces"]);
    $pieces = str_replace(",", ".", $pieces_1);

    $price_1 = htmlspecialchars($_POST["price"]);
    $price = str_replace(",", ".", $price_1);

    $discounts_1 = htmlspecialchars($_POST["discounts"]);
    $discounts = str_replace(",", ".", $discounts_1);

    $db->connection->query("UPDATE pidb_article SET article_id='$article_id', note='$note', pieces='$pieces', price='$price', discounts='$discounts'  WHERE id = '$pidb_article_id' ") or die(mysqli_error($db->connection));
    
    // sada treba uraditi i update property-a u tabeli pidb_article_property
    // 
    // da bi znali koliko property-a stiže na POST treba da izlistamo sve koji su upisani
    // u tabelu pidb_article_property i da uradimo POST za svaki, naravno u svakom prolazu
    // petlje treba da promenljiva ima naziv koji odgovara property-u
    
    $result_propertys = $db->connection->query("SELECT pidb_article_property.id, property.name "
                                       . "FROM pidb_article_property "
                                       . "JOIN (pidb_article, property)"
                                       . "ON (pidb_article_property.pidb_article_id = pidb_article.id AND pidb_article_property.property_id = property.id) "
                                       . "WHERE pidb_article.id = $pidb_article_id ") or die(mysqli_error($db->connection));

    while($row_property = mysqli_fetch_array($result_propertys)){
        $id = $row_property['id'];
        $property_name = $row_property['name'];

        ${$property_name} =  str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));

        $db->connection->query("UPDATE pidb_article_property SET quantity='${$property_name}'  WHERE id = '$id' ") or die(mysqli_error($db->connection));
    }

    die('<script>location.href = "?edit&pidb_id='.$pidb_id.'" </script>');
}

// change article inside document
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editArticleDataInPidb"]) ) {
    
    $pidb_id = htmlspecialchars($_GET["pidb_id"]);
    $pidb_article_id = htmlspecialchars($_GET["pidb_article_id"]);

    $new_article_id = htmlspecialchars($_POST["article_id"]);
        
    // read old article_id
    $old_article = $pidb->getArticleInPidb($pidb_article_id);
    $old_article_id = $old_article['article_id'];
    
    $db = new DatabaseController();

    // first check if article_id in pidb_article_id changed
    if ($old_article_id == $new_article_id){
        // article not changed
    } else {
        // article changed

        // removed Old Article Properties
        $result_propertys = $db->connection->query("SELECT * FROM pidb_article_property WHERE pidb_article_id = $pidb_article_id ") or die(mysqli_error($db->connection));
        while($row = mysqli_fetch_array($result_propertys)):
            $id = $row['id'];
            $db->connection->query("DELETE FROM pidb_article_property WHERE id='$id' ") or die(mysqli_error($db->connection));
        endwhile;

        // change article from old article to new
        $db->connection->query("UPDATE pidb_article SET article_id='$new_article_id'  WHERE id = '$pidb_article_id' ") or die(mysqli_error($db->connection));
        
        // update price to new article prices
        $new_article = $article->getArticleById($new_article_id);
        $price = $new_article['price'];
        $note = "";
        $pieces = 1;
        $db->connection->query("UPDATE pidb_article SET price='$price', note='$note', pieces='$pieces' WHERE id = '$pidb_article_id' ") or die(mysqli_error($db->connection));

        // add propertys to table pidb_article_propertys
        $propertys = $db->connection->query( "SELECT * FROM article_property WHERE article_id ='$new_article_id'");
        while($row_property = mysqli_fetch_array($propertys)){
            $property_id = $row_property['property_id'];
            $quantity = 0;
            $db->connection->query("INSERT INTO pidb_article_property (pidb_article_id, property_id, quantity) " 
                            . " VALUES ('$pidb_article_id', '$property_id', '$quantity' )") or die(mysqli_error($db->connection));
        }

    }

    die('<script>location.href = "?edit&pidb_id='.$pidb_id.'" </script>');
}

// edit settings
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editSettings"]) ) {

    $kurs = str_replace(",", ".", htmlspecialchars($_POST["kurs"]));
    $tax = str_replace(",", ".", htmlspecialchars($_POST["tax"]));

    $db = new DatabaseController();

    $db->connection->query("UPDATE preferences SET kurs='$kurs', tax='$tax' WHERE id = '1' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?set" </script>');
}
