<?php

namespace App\Controller;

use App\Core\BaseController;

/**
 * PidbController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class PidbController extends BaseController {

  /**
   * PidbController constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Index action.
   *
   * @return void
   */
  public function index($search = NULL) {
    $data = [
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'pidbs',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
      'search' => $search,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('index', $data);
  }
  /**
   * @return void
   */
  public function add() {
    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'pidbs',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('add', $data);
  }

  /**
   * @param $pidb_id
   * @param $search
   *
   * @return void
   */
  public function view($pidb_id, $search = NULL) {
    $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);

    // get client data from $pidb_data
    $client_id = $pidb_data->getClient()->getId();
    $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);

    $all_articles = $this->entityManager->getRepository('\App\Entity\Article')->findAll();

    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'pidb',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
      'search' => $search,
      'pidb_id' => $pidb_id,
      'pidb_data' => $pidb_data,
      'client' => $client,
      'all_articles' => $all_articles,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('view', $data);
  }

  /**
   * @param $pidb_id
   *
   * @return void
   */
  public function edit($pidb_id) {
    $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);

    // get client data from $pidb_data
    $client_id = $pidb_data->getClient()->getId();
    $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);

    $all_articles = $this->entityManager->getRepository('\App\Entity\Article')->findAll();

    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'pidb',
      'entityManager' => $this->entityManager,
      'stylesheet' => '/../libraries/',
      'client' => $client,
      'pidb_id' => $pidb_id,
      'pidb_data' => $pidb_data,
      'all_articles' => $all_articles,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('edit', $data);
  }

  public function printAccountingDocument($pidb_id) {
    $data = [
      'entityManager' => $this->entityManager,
      'accounting_document__id' => $pidb_id,
    ];
    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('printAccountingDocument', $data);
  }

  public function printAccountingDocumentW($pidb_id) {
    $data = [
      'entityManager' => $this->entityManager,
      'accounting_document__id' => $pidb_id,
    ];
    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('printAccountingDocumentW', $data);
  }

  public function printAccountingDocumentI($pidb_id) {
    $data = [
      'entityManager' => $this->entityManager,
      'accounting_document__id' => $pidb_id,
    ];
    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('printAccountingDocumentI', $data);
  }

  public function printAccountingDocumentIW($pidb_id) {
    $data = [
      'entityManager' => $this->entityManager,
      'accounting_document__id' => $pidb_id,
    ];
    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('printAccountingDocumentIW', $data);
  }

  public function exportProformaToDispatch($pidb_id) {
    // Current logged user.
    $user_id = $this->user_id;
    $user = $this->entityManager->find("\App\Entity\User", $user_id);

    $proforma_id = $pidb_id;
    $proforma = $this->entityManager->find("\App\Entity\AccountingDocument", $proforma_id);

    $ordinal_num_in_year = 0;

    // Save Proforma data to Dispatch.
    $newDispatch = new \App\Entity\AccountingDocument();

    $newDispatch->setOrdinalNumInYear($ordinal_num_in_year);
    $newDispatch->setDate(new \DateTime("now"));
    $newDispatch->setIsArchived(0);

    $newDispatch->setType($this->entityManager->find("\App\Entity\AccountingDocumentType", 2));
    $newDispatch->setTitle($proforma->getTitle());
    $newDispatch->setClient($proforma->getClient());
    $newDispatch->setParent($proforma);
    $newDispatch->setNote($proforma->getNote());

    $newDispatch->setCreatedAt(new \DateTime("now"));
    $newDispatch->setCreatedByUser($user);
    $newDispatch->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

    $this->entityManager->persist($newDispatch);
    $this->entityManager->flush();

    // Get id of last AccountingDocument.
    $last_accounting_document_id = $newDispatch->getId();

    // Set Ordinal Number In Year.
    $this->entityManager->getRepository('App\Entity\AccountingDocument')->setOrdinalNumInYear($last_accounting_document_id);

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
    $proforma_articles = $this->entityManager->getRepository('\App\Entity\AccountingDocument')->getArticles($proforma->getId());

    // Save articles to dispatch.
    foreach ($proforma_articles as $proforma_article) {
      $newDispatchArticle = new \App\Entity\AccountingDocumentArticle();

      $newDispatchArticle->setAccountingDocument($newDispatch);
      $newDispatchArticle->setArticle($proforma_article->getArticle());
      $newDispatchArticle->setPieces($proforma_article->getPieces());
      $newDispatchArticle->setPrice($proforma_article->getPrice());
      $newDispatchArticle->setDiscount($proforma_article->getDiscount());
      $newDispatchArticle->setTax($proforma_article->getTax());
      $newDispatchArticle->setWeight($proforma_article->getWeight());
      $newDispatchArticle->setNote($proforma_article->getNote());

      $this->entityManager->persist($newDispatchArticle);
      $this->entityManager->flush();

      // Get $proforma_article properies
      $proforma_article_properties = $this->entityManager->getRepository('\App\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $proforma_article->getId()), array());

      // Save $proforma_article properies to $newDispatchArticle
      foreach ($proforma_article_properties as $article_property) {
        $newDispatchArticleProperty = new \App\Entity\AccountingDocumentArticleProperty();

        $newDispatchArticleProperty->setAccountingDocumentArticle($newDispatchArticle);
        $newDispatchArticleProperty->setProperty($article_property->getProperty());
        $newDispatchArticleProperty->setQuantity($article_property->getQuantity());
        $this->entityManager->persist($newDispatchArticleProperty);
        $this->entityManager->flush();
      }

    }

    // Set Proforma to archive.
    $proforma->setIsArchived(1);
    $this->entityManager->flush();

    // Check if proforma belong to any Project
    $project = $this->entityManager->getRepository('\App\Entity\AccountingDocument')->getProjectByAccountingDocument($proforma->getId());

    if ($project) {
      // Set same project to dispatch.
      $project->getAccountingDocuments()->add($newDispatch);
      $this->entityManager->flush();
    }

    die('<script>location.href = "/pidb/'.$last_accounting_document_id.'" </script>');
  }

  public function editArticleInAccountingDocument($pidb_id, $pidb_article_id) {
    $accounting_document__article_id = $pidb_article_id;

    $note = htmlspecialchars($_POST["note"]);

    $pieces_1 = htmlspecialchars($_POST["pieces"]);
    $pieces = str_replace(",", ".", $pieces_1);

    $price_1 = htmlspecialchars($_POST["price"]);
    $price = str_replace(",", ".", $price_1);

    $discounts_1 = htmlspecialchars($_POST["discounts"]);
    $discounts = str_replace(",", ".", $discounts_1);

    $accountingDocumentArticle = $this->entityManager->find("\App\Entity\AccountingDocumentArticle", $accounting_document__article_id);

    $accountingDocumentArticle->setNote($note);
    $accountingDocumentArticle->setPieces($pieces);
    $accountingDocumentArticle->setPrice($price);
    $accountingDocumentArticle->setDiscount($discounts);
    $this->entityManager->flush();

    // Properties update in table v6__accounting_documents__articles__properties.
    $accounting_document__article__properties = $this->entityManager->getRepository('\App\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $accounting_document__article_id), array());
    foreach ($accounting_document__article__properties as $accounting_document__article__property) {

      // Get property name from $accounting_document__article__property.
      $property_name = $accounting_document__article__property->getProperty()->getName();
      // Get property value from $_POST.
      $property_value = str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));

      $accountingDocumentArticleProperty = $this->entityManager->find("\App\Entity\AccountingDocumentArticleProperty", $accounting_document__article__property->getId());

      $accountingDocumentArticleProperty->setQuantity($property_value);
      $this->entityManager->flush();
    }

    die('<script>location.href = "/pidb/'.$pidb_id.'" </script>');
  }

  public function transactions($pidb_id) {
    $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);
    $transactions = $this->entityManager->getRepository('\App\Entity\AccountingDocument')->getLastTransactions(10);

    $data = [
      'transactions' => $transactions,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'pidb',
      'entityManager' => $this->entityManager,
      'stylesheet' => '/../libraries/',
      'pidb_id' => $pidb_id,
      'accounting_document_id' => $pidb_id,
      'pidb_data' => $pidb_data,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('transactions', $data);
  }

  public function formEditTransaction($pidb_id, $transaction_id) {
    $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);
    $transaction = $this->entityManager->find('\App\Entity\Payment', $transaction_id);
    $client_id = $pidb_data->getClient()->getId();
    $client_data = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);
    $data = [
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'pidb',
      'entityManager' => $this->entityManager,
      'stylesheet' => '/../libraries/',
      'pidb_id' => $pidb_id,
      'pidb_data' => $pidb_data,
      'transaction' => $transaction,
      'client_data' => $client_data,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('transaction', $data);
  }

  public function editTransaction($pidb_id, $transaction_id) {
    $transaction = $this->entityManager->find("\App\Entity\Payment", $transaction_id);

    $type_id = htmlspecialchars($_POST["type_id"]);
    $type = $this->entityManager->find("\App\Entity\PaymentType", $type_id);

    $date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
    $amount_1 = htmlspecialchars($_POST["amount"]);
    $amount = str_replace(",", ".", $amount_1);
    $note = htmlspecialchars($_POST["note"]);

    $transaction->setType($type);
    $transaction->setDate(new \DateTime($date));
    $transaction->setAmount($amount);
    $transaction->setNote($note);

    $this->entityManager->flush();

    die('<script>location.href = "/pidb/' . $pidb_id . '/transactions" </script>');
  }

  public function deleteTransaction($pidb_id, $transaction_id) {
    $transaction = $this->entityManager->find("\App\Entity\Payment", $transaction_id);
    $this->entityManager->remove($transaction);
    $this->entityManager->flush();

    die('<script>location.href = "/pidb/' . $pidb_id . '/transactions" </script>');
  }

  /**
   * A helper method to render views.
   *
   * @param $view
   * @param $data
   *
   * @return void
   */
  private function render($view, $data = []) {
    // Extract data array to variables.
    extract($data);
    // Include the view file.
    require_once __DIR__ . "/../Views/pidb/$view.php";
  }

}
