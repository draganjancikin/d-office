<?php
// Delete Acconting Document.
if ($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["deleteAccountingDocument"]) ) {
  
  $acc_doc_id = htmlspecialchars($_GET["acc_doc_id"]);
  
  // Check if exist AccountingDocument.
  if ($accounting_document = $entityManager->find("\Roloffice\Entity\AccountingDocument", $acc_doc_id)) {
  
    // Check if AccountingDocument have Payments, where PaymentType is Income.
    if ( $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getPaymentsByIncome($acc_doc_id) ) {
          
      echo "Brisanje dokumenta nije moguće jer postoje uplate vezane za ovaj dokument!";
      exit();

    } else {
      
      // Parent Accounting Document update.
      // Check if parent exist.
      if ($parent = $accounting_document->getParent()) {

        // Update Payments.
        // Get all AccountingDocument Payments.
        $payments = $accounting_document->getPayments();

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
            ->set('accountingdocument_id', ':parent')
            ->where('payment_id = :payment')
            ->setParameter('parent', $parent->getId())
            ->setParameter('payment', $payment->getId());
          $result = $queryBuilder ->execute();
        }

        // Set Parent to active
        $parent->setIsArchived(0);
        $entityManager->flush();
        
      } else {

        if ( $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getPaymentsByAvans($acc_doc_id) ){
          echo "Brisanje dokumenta nije moguće jer postoje avansi vezani za ovaj dokument!";
          exit();
        }

      }

    }

    // Check if exist Articles in AccountingDocument.
    if ($accounting_document__articles = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')->findBy(array('accounting_document' => $acc_doc_id), array())) {

      // Loop trough all articles.
      foreach ($accounting_document__articles as $accounting_document__article) {

        // Check if exist Properties in AccontingDocument Article.
        if ($accounting_document__article__properties = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $accounting_document__article))) {

          // Remove AccountingDocument Article Properties.
          foreach ($accounting_document__article__properties as $accounting_document__article__property) {
            $entityManager->remove($accounting_document__article__property);
            $entityManager->flush();
          }

        }

        // Delete Article from AccountingDocument.
        $entityManager->remove($accounting_document__article);
        $entityManager->flush();
      }

    }

    // Delete AccountingDocument.
    $entityManager->remove($accounting_document);
    $entityManager->flush();
  }

  die('<script>location.href = "?name=&search" </script>');
}
