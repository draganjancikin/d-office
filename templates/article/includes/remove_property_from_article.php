<?php
// Remove Property from Article
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["removePropertyFromArticle"]) ) {
  
  $article_id = htmlspecialchars($_GET["article_id"]);
  
  $article_property_id = htmlspecialchars($_GET["property_id"]);
  $article_property = $entityManager->find("\App\Entity\ArticleProperty", $article_property_id);
  
  $entityManager->remove($article_property);
  $entityManager->flush();

  die('<script>location.href = "?view&article_id='.$article_id.'" </script>');
}
