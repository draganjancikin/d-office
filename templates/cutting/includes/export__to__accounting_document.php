<?php
use Roloffice\Core\Database;

// export cutting to proforma-invoice
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["exportToAccountingDocument"]) ) {

    // Current loged user.
    $user_id = $_SESSION['user_id'];
    $user = $entityManager->find("\Roloffice\Entity\User", $user_id);

    $cutting_id = htmlspecialchars($_GET['cutting_id']);
    $cutting = $entityManager->find("\Roloffice\Entity\CuttingSheet", $cutting_id);

    // Get total length of pickets in cm.
    $total_picket_lenght = htmlspecialchars($_GET['total_picket_lenght']) / 10;

    // Get total number of caps.
    $total_kap = htmlspecialchars($_GET['total_kap']);

    // Get Picket width.
    $picket_width = htmlspecialchars($_GET['picket_width']);

    $ordinal_num_in_year = 0;
    $title = "PVC letvice";
    $note = "ROLOSTIL szr je PDV obveznik.";
    
    $accounting_document__type_id = 1;
    $accounting_document__type = $entityManager->find("\Roloffice\Entity\AccountingDocumentType", $accounting_document__type_id);

    // Create a new AccountingDocument (Proforma).
    $newProforma = new \Roloffice\Entity\AccountingDocument();

    $newProforma->setOrdinalNumInYear($ordinal_num_in_year);
    $newProforma->setDate(new DateTime("now"));
    $newProforma->setIsArchived(0);
    $newProforma->setType($accounting_document__type);
    $newProforma->setClient($cutting->getClient());
    $newProforma->setTitle($title);
    $newProforma->setNote($note);
    
    $newProforma->setCreatedAt(new DateTime("now"));
    $newProforma->setCreatedByUser($user);
    $newProforma->setModifiedAt(new DateTime("1970-01-01 00:00:00"));
    
    $entityManager->persist($newProforma);
    $entityManager->flush();
    
    // Get id of last AccountingDocument.
    $newProforma_id = $newProforma->getId();
    
    // Set Ordinal Number In Year.
    $entityManager->getRepository('Roloffice\Entity\AccountingDocument')->setOrdinalNumInYear($newProforma_id);

    // Add Article to Proforma.
    // First add picket.
    switch ($picket_width) {
        case '35':
            $article_id = 67;
            break;
        case '60':
            $article_id = 58;
            break;
        case '80':
            $article_id = 6;
            break;
        case '100':
            $article_id = 64;
            break;
        default:
            $article_id = 6;
            break;
    }
    $article_picket = $entityManager->find("\Roloffice\Entity\Article", $article_id);
    $note = "";
    $pieces = 1;
    $preferences = $entityManager->find('Roloffice\Entity\Preferences', 1);
    $tax = $preferences->getTax();

    // Add article to proforma.
    $newProformaArticle = new \Roloffice\Entity\AccountingDocumentArticle();
    
    $newProformaArticle->setAccountingDocument($newProforma);
    $newProformaArticle->setArticle($article_picket);
    $newProformaArticle->setPieces($pieces);
    $newProformaArticle->setPrice($article_picket->getPrice());
    $newProformaArticle->setDiscount(0);
    $newProformaArticle->setTax($tax);
    $newProformaArticle->setWeight($article_picket->getWeight());
    $newProformaArticle->setNote($note);
    $entityManager->persist($newProformaArticle);
    $entityManager->flush();

    // Last inserted Accounting Document Article.
    $last__accounting_document__article_id = $newProformaArticle->getId();

    // Add article properties to AccountingDocumentArticle.
    $article_properties = $entityManager->getRepository('\Roloffice\Entity\ArticleProperty')->getArticleProperties($article_picket->getId());
    foreach ($article_properties as $article_property) {
        $newProformaArticleProperty = new \Roloffice\Entity\AccountingDocumentArticleProperty();
        
        $newProformaArticleProperty->setAccountingDocumentArticle($newProformaArticle);
        $newProformaArticleProperty->setProperty($article_property->getProperty());
        $newProformaArticleProperty->setQuantity($total_picket_lenght);

        $entityManager->persist($newProformaArticleProperty);
        $entityManager->flush();
    }

    // Second add pvc caps.
    switch ($picket_width) {
        case '35':
            $cap_article_id = 70;
            break;
        case '60':
            $cap_article_id = 147;
            break;
        case '80':
            $cap_article_id = 7;
            break;
        case '100':
            $cap_article_id = 142;
            break;
        default:
            $cap_article_id = 7;
            break;
    }
    $note = "";
    $cap_pieces = $total_kap;
    $article_cap = $entityManager->find("\Roloffice\Entity\Article", $cap_article_id);


    // Add article to proforma.
    $newProformaArticle = new \Roloffice\Entity\AccountingDocumentArticle();
    
    $newProformaArticle->setAccountingDocument($newProforma);
    $newProformaArticle->setArticle($article_cap);
    $newProformaArticle->setPieces($cap_pieces);
    $newProformaArticle->setPrice($article_cap->getPrice());
    $newProformaArticle->setDiscount(0);
    $newProformaArticle->setTax($tax);
    $newProformaArticle->setWeight($article_cap->getWeight());
    $newProformaArticle->setNote($note);
    $entityManager->persist($newProformaArticle);
    $entityManager->flush();

    die('<script>location.href = "/pidb/index.php?edit&pidb_id='.$newProforma_id.'" </script>');
    
}
