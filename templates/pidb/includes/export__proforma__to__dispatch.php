<?php
// Export proforma to dispatch.
if(isset($_GET["exportProformaToDispatch"]) ) {
  
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

  // TODO Dragan: Get articles from proforma and save to dispatch.





  echo "exporting ...";
  exit();

  /*
    // get articles from proforma and save to dispatch
    $result_pidb_articles = $db->connection->query("SELECT * FROM pidb_article WHERE pidb_id = '$pidb_id'") or die(mysqli_error($db->connection));
    while($row_pidb_article = $result_pidb_articles->fetch_assoc()){
        $pidb_article_id = $row_pidb_article['id'];
        $article_id = $row_pidb_article['article_id'];
        $article_note = $row_pidb_article['note'];
        $article_pieces = $row_pidb_article['pieces'];
        $article_price = $row_pidb_article['price'];
        $article_discounts = $row_pidb_article['discounts'];
        $article_tax = $row_pidb_article['tax'];
        $article_weight = $row_pidb_article['weight'];

        $db->connection->query("INSERT INTO pidb_article (pidb_id, article_id, note, pieces, price, discounts, tax, weight) " 
                        . " VALUES ('$pidb_id_last', '$article_id', '$article_note', '$article_pieces', '$article_price', '$article_discounts', '$article_tax', '$article_weight' )") or die(mysqli_error($db->connection));

        $pidb_article_id_last = $db->connection->insert_id;

        // za svaki artikal u predračunu treba proveriti da li postoji property i ako postoji upisati 
        // ga i za novootvorenu otpremnicu

        $result_pidb_articles_propertys = $db->connection->query("SELECT * FROM pidb_article_property WHERE pidb_article_id = '$pidb_article_id'") or die(mysqli_error($db->connection));
        while($row_pidb_articles_property = $result_pidb_articles_propertys->fetch_assoc()){
            $property_id = $row_pidb_articles_property['property_id'];
            $quantity = $row_pidb_articles_property['quantity'];

            $db->connection->query("INSERT INTO pidb_article_property (pidb_article_id, property_id, quantity) VALUES ('$pidb_article_id_last', '$property_id', '$quantity' )") or die(mysqli_error($db->connection));
        }

    }

    // proforma go to archive
    $db->connection->query("UPDATE pidb SET archived='1' WHERE id = '$pidb_id' ") or die(mysqli_error($db->connection));
    */
    
  die('<script>location.href = "?view&pidb_id='.$last_accounting_document_id.'" </script>');
}
