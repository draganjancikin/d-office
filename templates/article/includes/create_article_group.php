<?php
// Create a new Article Group.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createArticleGroup"])) {
  $name = htmlspecialchars($_POST['name']);
	if ($name == "") die('<script>location.href = "?inc=alert&ob=4" </script>');

	$newArticleGroup = new \Roloffice\Entity\ArticleGroup();
	$newArticleGroup->setName($name);
	$entityManager->persist($newArticleGroup);
	$entityManager->flush();

	// Get last article group id and redirect.
	$new_article_group_id = $newArticleGroup->getId();

	die('<script>location.href = "?viewArticleGroup&article_group_id='.$new_article_group_id.'" </script>');
}