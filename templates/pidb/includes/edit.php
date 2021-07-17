<?php

// change article inside document
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editArticleDataInPidb"]) ) {

  $pidb_id = htmlspecialchars($_GET["pidb_id"]);
  $pidb_article_id = htmlspecialchars($_GET["pidb_article_id"]);

  $new_article_id = htmlspecialchars($_POST["article_id"]);

  // read old article_id
  $old_article = $pidb->getArticleInPidb($pidb_article_id);
  $old_article_id = $old_article['article_id'];

  $db = new Database();

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
