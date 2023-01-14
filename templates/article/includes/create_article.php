<?php
// Create a new Article.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createArticle"])) {
    // Current logged user.
    $user_id = $_SESSION['user_id'];
    $user = $entityManager->find("\App\Entity\User", $user_id);

    $group_id = htmlspecialchars($_POST['group_id']);
    $group = $entityManager->find("\App\Entity\ArticleGroup", $group_id);

    $name = htmlspecialchars($_POST['name']);
    if ($name == "") die('<script>location.href = "?inc=alert&ob=4" </script>');

    $unit_id = htmlspecialchars($_POST['unit_id']);
    $unit = $entityManager->find("\App\Entity\Unit", $unit_id);

    $weight = $_POST['weight'] ? htmlspecialchars($_POST['weight']) : 0;
    $min_calc_measure = str_replace(",", ".", htmlspecialchars($_POST['min_calc_measure']));
    $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;
    $note = htmlspecialchars($_POST['note']);

    $newArticle = new \App\Entity\Article();

    $newArticle->setGroup($group);
    $newArticle->setUnit($unit);
    $newArticle->setName($name);
    $newArticle->setWeight($weight);
    $newArticle->setMinCalcMeasure($min_calc_measure);
    $newArticle->setPrice($price);

    $newArticle->setNote($note);
    $newArticle->setCreatedAt(new DateTime("now"));
    $newArticle->setCreatedByUser($user);
    $newArticle->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

    $entityManager->persist($newArticle);
    $entityManager->flush();

    // Get last id and redirect.
    $new_article_id = $newArticle->getId();
    die('<script>location.href = "?view&article_id='.$new_article_id.'" </script>');
}
