<?php
// Edit Article in AccountingDocument.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editArticleInAccountingDocument"]) ) {
  
  $accounting_document_id = htmlspecialchars($_GET["pidb_id"]);
  $accounting_document__article_id = htmlspecialchars($_GET["pidb_article_id"]);
  
  $article_id = htmlspecialchars($_POST["article_id"]);
  
  $note = htmlspecialchars($_POST["note"]);
  
  $pieces_1 = htmlspecialchars($_POST["pieces"]);
  $pieces = str_replace(",", ".", $pieces_1);

  $price_1 = htmlspecialchars($_POST["price"]);
  $price = str_replace(",", ".", $price_1);

  $discounts_1 = htmlspecialchars($_POST["discounts"]);
  $discounts = str_replace(",", ".", $discounts_1);
  
  $accountingDocumentArticle = $entityManager->find("\Roloffice\Entity\AccountingDocumentArticle", $accounting_document__article_id);

  $accountingDocumentArticle->setNote($note);
  $accountingDocumentArticle->setPieces($pieces);
  $accountingDocumentArticle->setPrice($price);
  $accountingDocumentArticle->setDiscount($discounts);
  $entityManager->flush();

  // Properies update in table v6__accounting_documents__articles__properties
  $accounting_document__article__properties = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $accounting_document__article_id), array());
  foreach ($accounting_document__article__properties as $accounting_document__article__property) {
    
    // Get property name from $accounting_document__article__property.
    $property_name = $accounting_document__article__property->getProperty()->getName();
    // Get property value from $_POST
    $property_value = str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));

    $accountingDocumentArticleProperty = $entityManager->find("\Roloffice\Entity\AccountingDocumentArticleProperty", $accounting_document__article__property->getId());

    $accountingDocumentArticleProperty->setQuantity($property_value);
    $entityManager->flush();
  }
  
  die('<script>location.href = "?edit&pidb_id='.$accounting_document_id.'" </script>');
}