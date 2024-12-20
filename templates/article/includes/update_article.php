<?php
// Update Article.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateArticle"]) ) {
  
  // Curent logged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);

  $article_id = htmlspecialchars($_GET["article_id"]);
  
  $group_id = htmlspecialchars($_POST['group_id']);
  $group = $entityManager->find("\App\Entity\ArticleGroup", $group_id);
  
  $name = htmlspecialchars($_POST["name"]);
  
  $unit_id = htmlspecialchars($_POST["unit_id"]);
  $unit = $entityManager->find("\App\Entity\Unit", $unit_id);
  
  if($_POST['weight']){
    $weight = htmlspecialchars($_POST['weight']);
  } else {
    $weight = 0;
  }
  
  $min_calc_measure = str_replace(",", ".", htmlspecialchars($_POST['min_calc_measure']));
  $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
  $note = htmlspecialchars($_POST["note"]);
  
  $article = $entityManager->find('\App\Entity\Article', $article_id);

  if ($article === null) {
    echo "Article with ID $article_id does not exist.\n";
    exit(1);
  }

  $article->setGroup($group);
  $article->setName($name);
  $article->setunit($unit);
  $article->setWeight($weight);
  $article->setMinCalcMeasure($min_calc_measure);
  $article->setPrice($price);
  $article->setNote($note);
  $article->setModifiedByUser($user);
  $article->setModifiedAt(new DateTime("now"));

  $entityManager->flush();

  die('<script>location.href = "?view&article_id='.$article_id.'" </script>');
}