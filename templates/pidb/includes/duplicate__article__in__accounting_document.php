<?php
// Duplicate Article in Accounting Document.
if ($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["duplicateArticleInAccountingDocument"])) {
  $accounting_document_id = htmlspecialchars($_GET["pidb_id"]);
  $pidb_tip_id = htmlspecialchars($_GET["pidb_tip_id"]);
  
  // Accounting Document Article ID.
  $accounting_document__article__id = htmlspecialchars($_GET["pidb_article_id"]);
  
  // sledeća metoda duplicira artikal iz pidb_article i property-e iz pidb_article_property
  $accounting_document__article__properties = $entityManager->getRepository('\App\Entity\AccountingDocumentArticle')->duplicateArticleInAccountingDocument($accounting_document__article__id);
  
  die('<script>location.href = "?edit&pidb_id='.$accounting_document_id.'" </script>');
}
