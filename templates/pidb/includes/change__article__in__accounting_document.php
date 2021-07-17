<?php
// Change Article inside Accounting Document.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editArticleDataInAccountingDocument"]) ) {

  $accounting_document_id = htmlspecialchars($_GET["pidb_id"]);
  $pidb_article_id = htmlspecialchars($_GET["pidb_article_id"]);
  $pidb_article = $entityManager->find('\Roloffice\Entity\AccountingDocumentArticle', $pidb_article_id);

  $old_article = $entityManager->find('\Roloffice\Entity\Article', $pidb_article->getArticle()->getId());
  $old_article_id = $old_article->getId(); 
  
  $new_article_id = htmlspecialchars($_POST["article_id"]);
  $new_article = $entityManager->find('\Roloffice\Entity\Article', $new_article_id);
  
  // First check if article_id in Accounting Document Article changed.
  if ($old_article_id == $new_article_id){
    // Article not changed.
    echo "article not changed";
  } else {
    // Article changed.
    
    // Remove the Properties of the old Article. (from table v6__accounting_documents__articles__properties)
    if ($accounting_document__article__properties = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticleProperty')->getAccountingDocumentArticleProperties($pidb_article_id)) {
      foreach ($accounting_document__article__properties as $accounting_document__article__property) {
        $accountingDocumentArticleProperty = $entityManager->find("\Roloffice\Entity\AccountingDocumentArticleProperty", $accounting_document__article__property->getId());
        $entityManager->remove($accountingDocumentArticleProperty);
        $entityManager->flush();
      }
    } 
    
    // change Article from old to new
    $pidb_article->setArticle($new_article);
    $pidb_article->setPrice($new_article->getPrice());
    $pidb_article->setNote("");
    $pidb_article->setPieces(1);
    $entityManager->flush();
    
    //insert Article properties in table v6__accounting_documents__articles__properties
    $article_properties = $entityManager->getRepository('\Roloffice\Entity\ArticleProperty')->getArticleProperties($new_article->getId());
    foreach ($article_properties as $article_property) {
      // insert to table v6__accounting_documents__articles__properties
      $newAccountingDocumentArticleProperty = new \Roloffice\Entity\AccountingDocumentArticleProperty();
   
      $newAccountingDocumentArticleProperty->setAccountingDocumentArticle($pidb_article);
      $newAccountingDocumentArticleProperty->setProperty($article_property->getProperty());
      $newAccountingDocumentArticleProperty->setQuantity(0);

      $entityManager->persist($newAccountingDocumentArticleProperty);
      $entityManager->flush();
    }

  }

  die('<script>location.href = "?edit&pidb_id='.$accounting_document_id.'" </script>');
}
