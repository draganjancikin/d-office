<?php
// delete document
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delPidb"]) ) {

    $pidb_id = htmlspecialchars($_GET["pidb_id"]);
    $pidb_tip_id = htmlspecialchars($_GET["pidb_tip_id"]);

    $db = new DB();
    $connection = $db->connectDB();

    // brisanje artikala dokumenta iz tabele pidb_article i brisanje property-a iz pidb_article_property
    $result_pidb_articles = $connection->query("SELECT * FROM pidb_article WHERE pidb_id='$pidb_id'") or die(mysqli_error($connection));
    while($row_pidb_article = mysqli_fetch_array($result_pidb_articles)){
        $pidb_article_id = $row_pidb_article['id'];
        // sledeća metoda briše i artikal iz pidb_article i property-e iz pidb_article_property
        $pidb->delArticleFromPidb($pidb_article_id);
    }

    // brisanje dokumenta iz tabele pidb
    $connection->query("DELETE FROM pidb WHERE id='$pidb_id' ") or die(mysqli_error($connection));

    die('<script>location.href = "?name=&search" </script>');
    // die('<script>location.href = "index.php" </script>');
}

// brisanje artikala iz dokumenta
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delArticleInPidb"])) {
    
  $pidb_id = htmlspecialchars($_GET["pidb_id"]);
  $pidb_tip_id = htmlspecialchars($_GET["pidb_tip_id"]);
  $pidb_article_id = htmlspecialchars($_GET["pidb_article_id"]);
    
  // sledeća metoda briše i artikal iz pidb_article i property-e iz pidb_article_property
  $pidb->delArticleFromPidb($pidb_article_id);
  
  die('<script>location.href = "?edit&pidb_id='.$pidb_id.'" </script>');
}
