<?php
// Remove Article from AccountingDocument.
if ($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["removeArticlefromAccountingDocument"])) {
  $accounting_document__id = htmlspecialchars($_GET["pidb_id"]);
  
  $accounting_document__article__id = htmlspecialchars($_GET["pidb_article_id"]);
  $accounting_document__article = $entityManager->find("\App\Entity\AccountingDocumentArticle", $accounting_document__article__id);

  // First remove properties from table v6__accounting_documents__articles__properties.
  if ($accounting_document__article__properties = $entityManager->getRepository('\App\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $accounting_document__article__id), array())) {
    foreach ($accounting_document__article__properties as $accounting_document__article__property) {
      $accountingDocumentArticleProperty = $entityManager->find("\App\Entity\AccountingDocumentArticleProperty", $accounting_document__article__property->getId());
      $entityManager->remove($accountingDocumentArticleProperty);
      $entityManager->flush();
    }
  }
  
  // Second remove Article from table v6__accounting_documents__articles
  $entityManager->remove($accounting_document__article);
  $entityManager->flush();

  die('<script>location.href = "?edit&pidb_id='.$accounting_document__id.'" </script>');
}
