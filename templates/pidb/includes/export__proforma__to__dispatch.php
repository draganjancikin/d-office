<?php
// Export proforma to dispatch.
if (isset($_GET["exportProformaToDispatch"]) ) {
  
  // Current loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);
    
  $proforma_id = htmlspecialchars($_GET["pidb_id"]);
  
  // Get proforma data.
  $proforma = $entityManager->find("\Roloffice\Entity\AccountingDocument", $proforma_id);

  $ordinal_num_in_year = 0;

  // Save Proforma data to Dispatch.
  $newDispatch = new \Roloffice\Entity\AccountingDocument();

  $newDispatch->setOrdinalNumInYear($ordinal_num_in_year);
  $newDispatch->setDate(new DateTime("now"));
  $newDispatch->setIsArchived(0);

  $newDispatch->setType($entityManager->find("\Roloffice\Entity\AccountingDocumentType", 2));
  $newDispatch->setTitle($proforma->getTitle());
  $newDispatch->setClient($proforma->getClient());
  $newDispatch->setParent($proforma);
  $newDispatch->setNote($proforma->getNote());
  
  $newDispatch->setCreatedAt(new DateTime("now"));
  $newDispatch->setCreatedByUser($user);
  $newDispatch->setModifiedAt(new DateTime("1970-01-01 00:00:00"));

  $entityManager->persist($newDispatch);
  $entityManager->flush();

  // Get id of last AccountingDocument.
  $last_accounting_document_id = $newDispatch->getId();
  
  // Set Ordinal Number In Year.
  $entityManager->getRepository('Roloffice\Entity\AccountingDocument')->setOrdinalNumInYear($last_accounting_document_id);
  
  // Get proforma payments.
  $payments = $proforma->getPayments();
  // Update all payment.
  foreach ($payments as $payment) {
    // TODO Dragan: Rešiti bolje konekciju na bazu.
    $conn = \Doctrine\DBAL\DriverManager::getConnection([
      'dbname' => DB_NAME,
      'user' => DB_USERNAME,
      'password' => DB_PASSWORD,
      'host' => DB_SERVER,
      'driver' => 'mysqli',
    ]);
    $queryBuilder = $conn->createQueryBuilder();
    $queryBuilder
      ->update('v6__accounting_documents__payments')
      ->set('accountingdocument_id', ':dispatch')
      ->where('payment_id = :payment')
      ->setParameter('dispatch', $last_accounting_document_id)
      ->setParameter('payment', $payment->getId());
    $result = $queryBuilder ->execute();
  }

  // Get articles from proforma.
  $proforma_articles = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getArticles($proforma->getId());

  // Save articles to dispatch.

  foreach ($proforma_articles as $proforma_article) {
    $newDispatchArticle = new \Roloffice\Entity\AccountingDocumentArticle();

    $newDispatchArticle->setAccountingDocument($newDispatch);
    $newDispatchArticle->setArticle($proforma_article->getArticle());
    $newDispatchArticle->setPieces($proforma_article->getPieces());
    $newDispatchArticle->setPrice($proforma_article->getPrice());
    $newDispatchArticle->setDiscount($proforma_article->getDiscount());
    $newDispatchArticle->setTax($proforma_article->getTax());
    $newDispatchArticle->setWeight($proforma_article->getWeight());
    $newDispatchArticle->setNote($proforma_article->getNote());

    $entityManager->persist($newDispatchArticle);
    $entityManager->flush();

    // Get $proforma_article properies
    $proforma_article_properties = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $proforma_article->getId()), array());
    
    // Save $proforma_article properies to $newDispatchArticle
    foreach ($proforma_article_properties as $article_property) {
      $newDispatchArticleProperty = new \Roloffice\Entity\AccountingDocumentArticleProperty();
      
      $newDispatchArticleProperty->setAccountingDocumentArticle($newDispatchArticle);
      $newDispatchArticleProperty->setProperty($article_property->getProperty());
      $newDispatchArticleProperty->setQuantity($article_property->getQuantity());
      $entityManager->persist($newDispatchArticleProperty);
      $entityManager->flush();
    }

  }

  // Set Proforma to archive.
  $proforma->setIsArchived(1);
  $entityManager->flush();

  // Check if proforma belong to any Project
  $project = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getProjectByAccountingDocument($proforma->getId());

  if ($project) {
    // Set same project to dispatch.
    $project->getAccountingDocuments()->add($newDispatch);
    $entityManager->flush();
  }
    
  die('<script>location.href = "?view&pidb_id='.$last_accounting_document_id.'" </script>');
}
