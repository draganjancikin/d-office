<?php
// Add Article to Accounting Document
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["addArticleToAccountingDocument"]) ) {

    $ad_id = htmlspecialchars($_POST["pidb_id"]);
    $accounting_document = $entityManager->find("\App\Entity\AccountingDocument", $ad_id);

    $article_id = htmlspecialchars($_POST["article_id"]);
    $article = $entityManager->find("\App\Entity\Article", $article_id);

    $price = $article->getPrice();
    $discount = 0;
    $weight = $article->getWeight();
    $pieces = $_POST["pieces"];

    $preferences = $entityManager->find('App\Entity\Preferences', 1);
    $tax = $preferences->getTax();

    $note = htmlspecialchars($_POST["note"]);

    $newAccountingDocumentArticle = new \App\Entity\AccountingDocumentArticle();

    $newAccountingDocumentArticle->setAccountingDocument($accounting_document);
    $newAccountingDocumentArticle->setArticle($article);
    $newAccountingDocumentArticle->setPieces($pieces);
    $newAccountingDocumentArticle->setPrice($price);
    $newAccountingDocumentArticle->setDiscount($discount);
    $newAccountingDocumentArticle->setTax($tax);
    $newAccountingDocumentArticle->setWeight($weight);
    $newAccountingDocumentArticle->setNote($note);

    $entityManager->persist($newAccountingDocumentArticle);
    $entityManager->flush();

    // Last inserted Accounting Document Article.
    // $last__accounting_document__article_id = $newAccountingDocumentArticle->getId();

    //insert Article properties in table v6__accounting_documents__articles__properties
    $article_properties = $entityManager->getRepository('\App\Entity\ArticleProperty')->getArticleProperties($article->getId());
    foreach ($article_properties as $article_property) {
        // insert to table v6__accounting_documents__articles__properties
        $newAccountingDocumentArticleProperty = new \App\Entity\AccountingDocumentArticleProperty();

        $newAccountingDocumentArticleProperty->setAccountingDocumentArticle($newAccountingDocumentArticle);
        $newAccountingDocumentArticleProperty->setProperty($article_property->getProperty());
        $newAccountingDocumentArticleProperty->setQuantity(0);

        $entityManager->persist($newAccountingDocumentArticleProperty);
        $entityManager->flush();
    }

    die('<script>location.href = "?edit&pidb_id='.$ad_id.' " </script>');
}
