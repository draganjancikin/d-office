<?php

// Add Property to Article.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addPropertyToArticle"]) ) {

  $article_id = htmlspecialchars($_POST['article_id']);
  $article = $entityManager->find("\App\Entity\Article", $article_id);
  
  $property_id = htmlspecialchars($_POST['property_id']);
  $property = $entityManager->find("\App\Entity\Property", $property_id);
  
  if(isset($_POST['min'])) {
      $min_size = trim(htmlspecialchars($_POST['min']));
  } else {
      $min_size = 0;
  }

  if(isset($_POST['max'])) {
      $max_size = trim(htmlspecialchars($_POST['max']));
  } else {
      $max_size = 0;
  }
  
  $newArticleProperty = new \App\Entity\ArticleProperty();

  $newArticleProperty->setArticle($article);
  $newArticleProperty->setProperty($property);
  $newArticleProperty->setMinSize($min_size);
  $newArticleProperty->setMaxSize($max_size);

  $entityManager->persist($newArticleProperty);
  $entityManager->flush();

  die('<script>location.href = "?view&article_id='.$article_id.'" </script>');
}