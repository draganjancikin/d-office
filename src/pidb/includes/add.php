<?php
// add new document
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addPidb"]) ) {

    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');
    
    $client_id = htmlspecialchars($_POST["client_id"]);
    $pidb_tip_id = htmlspecialchars($_POST["pidb_tip_id"]);

    if($_POST["project_id"]) {
        $project_id = htmlspecialchars($_POST["project_id"]);
    } else {
        $project_id = 0;
    }
    
    $title = htmlspecialchars($_POST["title"]);
    $note = htmlspecialchars($_POST["note"]);
    
    $db = new DB();
    $connect_db = $db->connectDB();
    
    $connect_db->query("INSERT INTO pidb (tip_id, date, client_id, project_id, title, note) " 
    . " VALUES ('$pidb_tip_id', '$date', '$client_id', '$project_id', '$title', '$note')") or die(mysqli_error($connect_db));
    
    $pidb_id = $connect_db->insert_id;
    
    // if exist project_id write project_id and pidb_id to table project_pidb
    if(isset($_POST["project_id"])){
        $project_id = htmlspecialchars($_POST["project_id"]);
        $connect_db->query("INSERT INTO project_pidb (project_id, pidb_id) VALUES ('$project_id', '$pidb_id')") or die(mysqli_error($connect_db)); 
    }

    $y_id = $pidb->setYid($pidb_tip_id);

    die('<script>location.href = "?view&pidb_id='.$pidb_id.'&pidb_tip_id='.$pidb_tip_id.'" </script>');
}

// add article in document
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addArticleInPidb"]) ) {

    $pidb_id = htmlspecialchars($_POST["pidb_id"]);
    $pidb_tip_id = $pidb->getTipid($pidb_id);

    $article_id = htmlspecialchars($_POST["article_id"]);
    $note = htmlspecialchars($_POST["note"]);
    $pieces = htmlspecialchars($_POST["pieces"]);

    $price = $article->getPrice($article_id);
  
    $tax = $pidb->getTax();

    $db = new DB();
    $connection = $db->connectDB();

    $connection->query("INSERT INTO pidb_article (pidb_id, article_id, note, pieces, price, tax) " 
                    . " VALUES ('$pidb_id', '$article_id', '$note', '$pieces', '$price', '$tax' )") or die(mysqli_error($connection));
  
    // treba nam i pidb_article_id (id artikla u pidb dokumentu) to je u stvari zadnji unos
    $pidb_article_id = $connection->insert_id;;

    //insert property-a artikla u tabelu pidb_article_property
    $propertys = $connection->query( "SELECT * FROM article_property WHERE article_id ='$article_id'");
    while($row_property = mysqli_fetch_array($propertys)){
    
        $property_id = $row_property['property_id'];
        $quantity = 0;

        $connection->query("INSERT INTO pidb_article_property (pidb_article_id, property_id, quantity) " 
                        . " VALUES ('$pidb_article_id', '$property_id', '$quantity' )") or die(mysqli_error($connection));
    }

    die('<script>location.href = "?edit&pidb_id='.$pidb_id.'&pidb_tip_id='.$pidb_tip_id.'" </script>');
}

// duplicate article in document
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["duplicateArticleInPidb"])) {
    
    $pidb_id = htmlspecialchars($_GET["pidb_id"]);
    $pidb_tip_id = htmlspecialchars($_GET["pidb_tip_id"]);

    // id in table pidb_article
    $pidb_article_id = htmlspecialchars($_GET["pidb_article_id"]);
    
    // sledeća metoda duplicira artikal iz pidb_article i property-e iz pidb_article_property
    $pidb->duplicateArticleInPidb($pidb_article_id);
    
    die('<script>location.href = "?edit&pidb_id='.$pidb_id.'" </script>');
    
  }
  