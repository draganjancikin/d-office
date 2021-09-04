<?php
// Delete Acconting Document.
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delPidb"]) ) {
    echo "Deleting has been soon ...";
    exit();
    
    $pidb_id = htmlspecialchars($_GET["pidb_id"]);
    $pidb_tip_id = htmlspecialchars($_GET["pidb_type_id"]);

    $db = new Database();

    // brisanje artikala dokumenta iz tabele pidb_article i brisanje property-a iz pidb_article_property
    $result_pidb_articles = $db->connection->query("SELECT * FROM pidb_article WHERE pidb_id='$pidb_id'") or die(mysqli_error($db->connection));
    while($row_pidb_article = mysqli_fetch_array($result_pidb_articles)){
        $pidb_article_id = $row_pidb_article['id'];
        // sledeća metoda briše i artikal iz pidb_article i property-e iz pidb_article_property
        $pidb->delArticleFromPidb($pidb_article_id);
    }

    // get parent_id from pidb_id
    $result_parent_id = $db->connection->query("SELECT parent_id FROM pidb WHERE id = '$pidb_id' ") or die(mysqli_error($db->connection));
    $row_parent_id =  mysqli_fetch_array($result_parent_id);
    $parent_id = $row_parent_id['parent_id'];

    // update payment where pidb_id = $pidb_id
    $db->connection->query("UPDATE payment "
                        . "SET pidb_id='$parent_id' "
                        . "WHERE pidb_id = '$pidb_id' ") or die(mysqli_error($db->connection));

    // brisanje dokumenta iz tabele pidb
    $db->connection->query("DELETE FROM pidb WHERE id='$pidb_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?name=&search" </script>');
}
