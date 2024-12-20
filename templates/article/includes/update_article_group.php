<?php
// Update Article Group.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateArticleGroup"]) ) {
	$name = htmlspecialchars($_POST["name"]);
	$article_group_id = htmlspecialchars($_GET["article_group_id"]);
  $article_group = $entityManager->find('\App\Entity\ArticleGroup', $article_group_id);

  if ($article_group === null) {
    echo "Article Group with ID $article_group_id does not exist.\n";
    exit(1);
  }

  $article_group->setName($name);
  $entityManager->flush();

  die('<script>location.href = "?viewArticleGroup&article_group_id='.$article_group_id.'" </script>');
}