<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\AccountingDocument;
use App\Entity\AccountingDocumentArticle;
use App\Entity\AccountingDocumentArticleProperty;
use App\Entity\Payment;

/**
 * PidbController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class PidbController extends BaseController
{

    private $page = 'pidbs';
    private $page_title = 'Dokumenti';
    private $stylesheet = '/../libraries/';

    /**
     * PidbController constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Index action.
     *
     * @param string|null $search
     *
     * @return void
     */
    public function index(string $search = NULL) {
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'search' => $search,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('index', $data);
    }

    /**
     * Add form action.
     *
     * @param int|null $client_id
     * @param int|null $project_id
     *
     * @return void
     */
    public function addForm(int $client_id = NULL, int $project_id = NULL) {
        $data = [
          'page' => $this->page,
          'page_title' => $this->page_title,
          'stylesheet' => $this->stylesheet,
          'user_id' => $this->user_id,
          'username' => $this->username,
          'user_role_id' => $this->user_role_id,
          'entityManager' => $this->entityManager,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('add', $data);
    }

    /**
     * Add new Accounting Document.
     *
     * @return void
     */
    public function add(): void
    {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $ordinal_num_in_year = 0;

        $client_id = htmlspecialchars($_POST["client_id"]);
        $client = $this->entityManager->find("\App\Entity\Client", $client_id);

        $accd_type_id = htmlspecialchars($_POST["pidb_type_id"]);
        $accd_type = $this->entityManager->find("\App\Entity\AccountingDocumentType", $accd_type_id);

        $title = htmlspecialchars($_POST["title"]);
        $note = htmlspecialchars($_POST["note"]);

        // Create a new AccountingDocument.
        $newAccountingDocument = new AccountingDocument();

        $newAccountingDocument->setOrdinalNumInYear($ordinal_num_in_year);
        $newAccountingDocument->setDate(new \DateTime("now"));
        $newAccountingDocument->setIsArchived(0);

        $newAccountingDocument->setType($accd_type);
        $newAccountingDocument->setClient($client);
        $newAccountingDocument->setTitle($title);
        $newAccountingDocument->setNote($note);

        $newAccountingDocument->setCreatedAt(new \DateTime("now"));
        $newAccountingDocument->setCreatedByUser($user);
        $newAccountingDocument->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newAccountingDocument);
        $this->entityManager->flush();

        // Get id of last AccountingDocument.
        $new_accounting_document_id = $newAccountingDocument->getId();

        // Set Ordinal Number In Year.
        $this->entityManager->getRepository('App\Entity\AccountingDocument')->setOrdinalNumInYear($new_accounting_document_id);


        if (isset($_POST["project_id"])) {
            $project_id = htmlspecialchars($_POST["project_id"]);
            $project = $this->entityManager->find("\App\Entity\Project", $project_id);

            $project->getAccountingDocuments()->add($newAccountingDocument);

            $this->entityManager->flush();
        }
        else {
            $project_id = NULL;
        }

        die('<script>location.href = "/pidb/'.$new_accounting_document_id.'" </script>');
    }

    /**
     * @param int $pidb_id
     * @param $search
     *
     * @return void
     */
    public function view(int $pidb_id, $search = NULL): void
    {
        $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);

        // get client data from $pidb_data
        $client_id = $pidb_data->getClient()->getId();
        $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);

        $all_articles = $this->entityManager->getRepository('\App\Entity\Article')->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
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
     * @param int $pidb_id
     *
     * @return void
     */
    public function editForm(int $pidb_id): void
    {
        $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);

        // get client data from $pidb_data
        $client_id = $pidb_data->getClient()->getId();
        $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);

        $all_articles = $this->entityManager->getRepository('\App\Entity\Article')->findAll();

        switch ($pidb_data->getType()->getId()) {
            case 1:
                $vrsta = "Predračun";
                $oznaka = "P_";
                $style = 'info';
                break;

            case 2:
                $vrsta = "Otpremnica";
                $oznaka = "O_";
                $style = 'secondary';
                break;

            case 4:
                $vrsta = "Povratnica";
                $oznaka = "POV_";
                $style = 'warning';
                break;

            default:
                $vrsta = "_";
                $oznaka = "_";
                $style = 'default';
                break;
        }

        $data = [
          'page' => $this->page,
          'page_title' => $this->page_title,
          'stylesheet' => $this->stylesheet,
          'user_id' => $this->user_id,
          'username' => $this->username,
          'user_role_id' => $this->user_role_id,
          'entityManager' => $this->entityManager,
          'client' => $client,
          'pidb_id' => $pidb_id,
          'pidb_data' => $pidb_data,
          'all_articles' => $all_articles,
          'vrsta' => $vrsta,
          'oznaka' => $oznaka,
          'style' => $style,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('edit', $data);
    }

    /**
     * Edit Accounting Document.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function edit(int $pidb_id): void
    {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $accounting_document = $this->entityManager->find("\App\Entity\AccountingDocument", $pidb_id);

        $title = htmlspecialchars($_POST["title"]);

        $client_id = htmlspecialchars($_POST["client_id"]);
        $client = $this->entityManager->find("\App\Entity\Client", $client_id);

        $is_archived = htmlspecialchars($_POST["archived"]);
        $note = htmlspecialchars($_POST["note"]);

        $accounting_document->setTitle($title);
        $accounting_document->setClient($client);
        $accounting_document->setIsArchived($is_archived);
        $accounting_document->setNote($note);
        $accounting_document->setModifiedByUser($user);
        $accounting_document->setModifiedAt(new \DateTime("now"));

        $this->entityManager->flush();

        die('<script>location.href = "/pidb/'.$pidb_id.'" </script>');
    }

    /**
     * Delete Accounting Document.
     *
     * @param int $pidb_id
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete(int $pidb_id): void
    {
        $acc_doc_id = $pidb_id;

        // Check if exist AccountingDocument.
        if ($accounting_document = $this->entityManager->find("\App\Entity\AccountingDocument", $acc_doc_id)) {

            // Check if AccountingDocument have Payments, where PaymentType is Income.
            if ($this->entityManager->getRepository('\App\Entity\AccountingDocument')->getPaymentsByIncome($acc_doc_id)) {
                echo "Brisanje dokumenta nije moguće jer postoje uplate vezane za ovaj dokument!";
                exit();
            }
            else {
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
                    $this->entityManager->flush();
                }
                else {
                    if ( $this->entityManager->getRepository('\App\Entity\AccountingDocument')->getPaymentsByAvans($acc_doc_id) ){
                        echo "Brisanje dokumenta nije moguće jer postoje avansi vezani za ovaj dokument!";
                        exit();
                    }
                }

            }

            // Check if exist Articles in AccountingDocument.
            if ($accounting_document__articles = $this->entityManager->getRepository('\App\Entity\AccountingDocumentArticle')->findBy(array('accounting_document' => $acc_doc_id), array())) {

              // Loop trough all articles.
              foreach ($accounting_document__articles as $accounting_document__article) {

                // Check if exist Properties in AccontingDocument Article.
                if ($accounting_document__article__properties = $this->entityManager->getRepository('\App\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $accounting_document__article))) {

                  // Remove AccountingDocument Article Properties.
                  foreach ($accounting_document__article__properties as $accounting_document__article__property) {
                    $this->entityManager->remove($accounting_document__article__property);
                    $this->entityManager->flush();
                  }

                }

                // Delete Article from AccountingDocument.
                $this->entityManager->remove($accounting_document__article);
                $this->entityManager->flush();
              }

            }

            // Delete AccountingDocument.
            $this->entityManager->remove($accounting_document);
            $this->entityManager->flush();
        }

        die('<script>location.href = "/pidbs/?search=" </script>');
    }

    /**
     * Add article to Accounting Document.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function addArticle(int $pidb_id): void
    {
        $accounting_document = $this->entityManager->find("\App\Entity\AccountingDocument", $pidb_id);

        $article_id = htmlspecialchars($_POST["article_id"]);
        $article = $this->entityManager->find("\App\Entity\Article", $article_id);

        $price = $article->getPrice();
        $discount = 0;
        $weight = $article->getWeight();

        $pieces = 0;
        if (isset($_POST["pieces"]) && is_numeric($_POST["pieces"])) {
            $pieces = htmlspecialchars($_POST["pieces"]);
        }

        $preferences = $this->entityManager->find('App\Entity\Preferences', 1);
        $tax = $preferences->getTax();

        $note = htmlspecialchars($_POST["note"]);

        $newAccountingDocumentArticle = new AccountingDocumentArticle();

        $newAccountingDocumentArticle->setAccountingDocument($accounting_document);
        $newAccountingDocumentArticle->setArticle($article);
        $newAccountingDocumentArticle->setPieces($pieces);
        $newAccountingDocumentArticle->setPrice($price);
        $newAccountingDocumentArticle->setDiscount($discount);
        $newAccountingDocumentArticle->setTax($tax);
        $newAccountingDocumentArticle->setWeight($weight);
        $newAccountingDocumentArticle->setNote($note);

        $this->entityManager->persist($newAccountingDocumentArticle);
        $this->entityManager->flush();

        // Last inserted Accounting Document Article.
        // $last__accounting_document__article_id = $newAccountingDocumentArticle->getId();

        // Insert Article properties in table v6__accounting_documents__articles__properties.
        $article_properties =
            $this->entityManager
                ->getRepository('\App\Entity\ArticleProperty')->getArticleProperties($article->getId());
        foreach ($article_properties as $article_property) {
            // Insert to table v6__accounting_documents__articles__properties.
            $newAccountingDocumentArticleProperty = new AccountingDocumentArticleProperty();

            $newAccountingDocumentArticleProperty->setAccountingDocumentArticle($newAccountingDocumentArticle);
            $newAccountingDocumentArticleProperty->setProperty($article_property->getProperty());
            $newAccountingDocumentArticleProperty->setQuantity(0);

            $this->entityManager->persist($newAccountingDocumentArticleProperty);
            $this->entityManager->flush();
        }

        die('<script>location.href = "/pidb/' . $pidb_id . '/edit " </script>');
    }

    /**
     * Print Accounting Document.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function printAccountingDocument(int $pidb_id): void
    {
        $data = [
            'entityManager' => $this->entityManager,
            'accounting_document__id' => $pidb_id,
        ];
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printAccountingDocument', $data);
    }

    /**
     * Print Accounting Document W.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function printAccountingDocumentW(int $pidb_id): void
    {
        $data = [
            'entityManager' => $this->entityManager,
            'accounting_document__id' => $pidb_id,
        ];
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printAccountingDocumentW', $data);
    }

    /**
     * Print Accounting Document I.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function printAccountingDocumentI(int $pidb_id): void
    {
        $data = [
            'entityManager' => $this->entityManager,
            'accounting_document__id' => $pidb_id,
        ];
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printAccountingDocumentI', $data);
    }

    /**
     * Print Accounting Document IW.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function printAccountingDocumentIW(int $pidb_id): void
    {
        $data = [
            'entityManager' => $this->entityManager,
            'accounting_document__id' => $pidb_id,
        ];
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printAccountingDocumentIW', $data);
    }

    /**
     * Export Proforma to Dispatch.
     *
     * @param int $pidb_id
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function exportProformaToDispatch(int $pidb_id): void
    {
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
            $proforma_article_properties =
              $this->entityManager
                ->getRepository('\App\Entity\AccountingDocumentArticleProperty')
                ->findBy(array('accounting_document_article' => $proforma_article->getId()), []);

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

    /**
     * Edit article in Accounting Document.
     *
     * @param int $pidb_id
     * @param int $pidb_article_id
     *
     * @return void
     */
    public function editArticleInAccountingDocument(int $pidb_id, int $pidb_article_id): void
    {
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
        $accounting_document__article__properties =
            $this->entityManager
                ->getRepository('\App\Entity\AccountingDocumentArticleProperty')
                ->findBy(array('accounting_document_article' => $accounting_document__article_id), array());
        foreach ($accounting_document__article__properties as $accounting_document__article__property) {
            // Get property name from $accounting_document__article__property.
            $property_name = $accounting_document__article__property->getProperty()->getName();
            // Get property value from $_POST.
            $property_value = str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));

            $accountingDocumentArticleProperty =
                $this->entityManager
                    ->find("\App\Entity\AccountingDocumentArticleProperty", $accounting_document__article__property->getId());

            $accountingDocumentArticleProperty->setQuantity($property_value);
            $this->entityManager->flush();
        }

        die('<script>location.href = "/pidb/'.$pidb_id.'" </script>');
    }

    /**
     * Change Article in Accounting Document Form.
     *
     * @param int $pidb_id
     * @param int $pidb_article_id
     *
     * @return void
     */
    public function changeArticleInAccountingDocumentForm(int $pidb_id, int $pidb_article_id): void
    {
        $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);
        $article_data = $this->entityManager->find('\App\Entity\AccountingDocumentArticle', $pidb_article_id);
        $all_articles = $this->entityManager->getRepository('\App\Entity\Article')->findAll();

        switch ($pidb_data->getType()->getId()) {
            case '1':
                $style = 'info';
                break;

            case '2':
                $style = 'secondary';
                break;

            case '4':
                $style = 'warning';
                break;

            default:
                $style = 'default';
                break;
        }

        $data = [
          'page' => $this->page,
          'page_title' => $this->page_title,
          'stylesheet' => $this->stylesheet,
          'user_id' => $this->user_id,
          'username' => $this->username,
          'user_role_id' => $this->user_role_id,
          'entityManager' => $this->entityManager,
          'pidb_id' => $pidb_id,
          'pidb_data' => $pidb_data,
          'pidb_article_id' => $pidb_article_id,
          'article_data' => $article_data,
          'all_articles' => $all_articles,
          'style' => $style,
        ];
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('changeArticleInAccountingDocument', $data);
    }

    /**
     * Change Article in Accounting Document.
     *
     * @param int $pidb_id
     * @param int $pidb_article_id
     *
     * @return void
     */
    public function changeArticleInAccountingDocument(int $pidb_id, int $pidb_article_id): void
    {
        $accounting_document_id = $pidb_id;
        $pidb_article = $this->entityManager->find('\App\Entity\AccountingDocumentArticle', $pidb_article_id);

        $old_article = $this->entityManager->find('\App\Entity\Article', $pidb_article->getArticle()->getId());
        $old_article_id = $old_article->getId();

        $new_article_id = htmlspecialchars($_POST["article_id"]);
        $new_article = $this->entityManager->find('\App\Entity\Article', $new_article_id);

        // First check if article_id in Accounting Document Article changed.
        if ($old_article_id == $new_article_id){
            // Article not changed.
            echo "article not changed";
        }
        else {
            // Article changed.

            // Remove the Properties of the old Article. (from table v6__accounting_documents__articles__properties)
            if (
                $accounting_document__article__properties =
                    $this->entityManager
                        ->getRepository('\App\Entity\AccountingDocumentArticleProperty')
                        ->findBy(array('accounting_document_article' => $pidb_article_id), array())
            ) {
                foreach ($accounting_document__article__properties as $accounting_document__article__property) {
                    $accountingDocumentArticleProperty =
                        $this->entityManager
                          ->find("\App\Entity\AccountingDocumentArticleProperty", $accounting_document__article__property->getId());
                    $this->entityManager->remove($accountingDocumentArticleProperty);
                    $this->entityManager->flush();
                }
            }

            // change Article from old to new
            $pidb_article->setArticle($new_article);
            $pidb_article->setPrice($new_article->getPrice());
            $pidb_article->setNote("");
            $pidb_article->setPieces(1);
            $this->entityManager->flush();

            // Insert Article properties in table v6__accounting_documents__articles__properties.
            $article_properties = $this->entityManager->getRepository('\App\Entity\ArticleProperty')->getArticleProperties($new_article->getId());
            foreach ($article_properties as $article_property) {
                // Insert to table v6__accounting_documents__articles__properties.
                $newAccountingDocumentArticleProperty = new \App\Entity\AccountingDocumentArticleProperty();

                $newAccountingDocumentArticleProperty->setAccountingDocumentArticle($pidb_article);
                $newAccountingDocumentArticleProperty->setProperty($article_property->getProperty());
                $newAccountingDocumentArticleProperty->setQuantity(0);

                $this->entityManager->persist($newAccountingDocumentArticleProperty);
                $this->entityManager->flush();
            }

        }

        die('<script>location.href = "/pidb/' . $accounting_document_id . '/edit" </script>');
    }

    /**
     * Duplicate Article in Accounting Document.
     *
     * @param int $pidb_id
     * @param int $pidb_article_id
     *
     * @return void
     */
    public function duplicateArticleInAccountingDocument(int $pidb_id, int $pidb_article_id): void
    {
        // sledeća metoda duplicira artikal iz pidb_article i property-e iz pidb_article_property
        $accounting_document__article__properties =
            $this->entityManager
                ->getRepository('\App\Entity\AccountingDocumentArticle')
                ->duplicateArticleInAccountingDocument($pidb_article_id);

        die('<script>location.href = "/pidb/' . $pidb_id . '/edit" </script>');
    }

  /**
   * @param int $pidb_id
   * @param int $pidb_article_id
   *
   * @return void
   */
    public function deleteArticleInAccountingDocument(int $pidb_id, int $pidb_article_id): void
    {
        $accounting_document__article = $this->entityManager->find("\App\Entity\AccountingDocumentArticle", $pidb_article_id);

        // First remove properties from table v6__accounting_documents__articles__properties.
        if (
            $accounting_document__article__properties =
                $this->entityManager
                    ->getRepository('\App\Entity\AccountingDocumentArticleProperty')
                    ->findBy(array('accounting_document_article' => $pidb_article_id), [])
        ) {
            foreach ($accounting_document__article__properties as $accounting_document__article__property) {
                $accountingDocumentArticleProperty = $this->entityManager
                    ->find("\App\Entity\AccountingDocumentArticleProperty", $accounting_document__article__property->getId());
                $this->entityManager->remove($accountingDocumentArticleProperty);
                $this->entityManager->flush();
            }
        }

        // Second remove Article from table v6__accounting_documents__articles
        $this->entityManager->remove($accounting_document__article);
        $this->entityManager->flush();

        die('<script>location.href = "/pidb/' . $pidb_id . '" </script>');
    }

    /**
     * Document transactions.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function transactions(int $pidb_id): void
    {
        $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);
        $transactions = $this->entityManager->getRepository('\App\Entity\AccountingDocument')->getLastTransactions(10);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'transactions' => $transactions,
            'pidb_id' => $pidb_id,
            'accounting_document_id' => $pidb_id,
            'pidb_data' => $pidb_data,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('transactions', $data);
    }

    /**
     * Edit transaction form.
     *
     * @param int $pidb_id
     * @param int $transaction_id
     *
     * @return void
     */
    public function formEditTransaction(int $pidb_id, int $transaction_id): void {
        $pidb_data = $this->entityManager->find('\App\Entity\AccountingDocument', $pidb_id);
        $transaction = $this->entityManager->find('\App\Entity\Payment', $transaction_id);
        $client_id = $pidb_data->getClient()->getId();
        $client_data = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'pidb_id' => $pidb_id,
            'pidb_data' => $pidb_data,
            'transaction' => $transaction,
            'client_data' => $client_data,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('transaction', $data);
    }

    /**
     * Edit transaction.
     *
     * @param int $pidb_id
     * @param int $transaction_id
     *
     * @return void
     * @throws \DateMalformedStringException
     */
    public function editTransaction(int $pidb_id, int $transaction_id): void
    {
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

    /**
     * Delete transaction.
     *
     * @param int $pidb_id
     * @param int $transaction_id
     *
     * @return void
     */
    public function deleteTransaction(int $pidb_id, int $transaction_id): void
    {
        $transaction = $this->entityManager->find("\App\Entity\Payment", $transaction_id);
        $this->entityManager->remove($transaction);
        $this->entityManager->flush();

        die('<script>location.href = "/pidb/' . $pidb_id . '/transactions" </script>');
    }

    /**
     * Edit preferences form.
     *
     * @return void
     */
    public function editPreferencesForm(): void
    {
        $preferences = $this->entityManager->find('App\Entity\Preferences', 1);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'preferences' => $preferences,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('editPreferences', $data);
    }

    /**
     * Edit Preferences.
     *
     * @return void
     */
    public function editPreferences(): void
    {
        $kurs = str_replace(",", ".", htmlspecialchars($_POST["kurs"]));
        $tax = str_replace(",", ".", htmlspecialchars($_POST["tax"]));

        $preferences = $this->entityManager->find('\App\Entity\Preferences', 1);

        $preferences->setKurs($kurs);
        $preferences->setTax($tax);
        $this->entityManager->flush();

        die('<script>location.href = "/pidbs/preferences" </script>');
    }

    /**
     * Add payment to Accounting Document.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function addPayment(int $pidb_id): void
    {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $payment_type_id = htmlspecialchars($_POST["type_id"]);
        $payment_type = $this->entityManager->find("\App\Entity\PaymentType", $payment_type_id);

        if ($payment_type_id == 5 && $this->entityManager->getRepository('App\Entity\Payment')->ifExistFirstCashInput()) {
            // TODO Dragan: Create error message
            ?>
            <p>Već ste uneli početno stanje!</p>
            <a href="/pidb/?cashRegister">Povratak na Kasu</a>
            <?php
            exit();
        }

        // Date from new payment form.
        if (!isset($_POST["date"])) {
            $date = date('Y-m-d H:i:s');
        }
        else {
            $date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
        }

        $amount = htmlspecialchars($_POST["amount"]);
        // Correct decimal separator.
        $amount = str_replace(",", ".", $amount);

        $note = htmlspecialchars($_POST["note"]);

        // Create a new Payment.
        $newPayment = new Payment();

        $newPayment->setType($payment_type);

        if ($payment_type_id == 6 || $payment_type_id == 7) {
            $amount = "-".$amount;
        }

        $newPayment->setAmount($amount);
        $newPayment->setDate(new \DateTime($date));
        $newPayment->setNote($note);
        $newPayment->setCreatedAt(new \DateTime("now"));
        $newPayment->setCreatedByUser($user);

        $this->entityManager->persist($newPayment);
        $this->entityManager->flush();

        $accounting_document_id = htmlspecialchars($_POST["pidb_id"]);
        $accounting_document = $this->entityManager->find("\App\Entity\AccountingDocument", $accounting_document_id);
        // Add Payment to AccountingDocument.
        $accounting_document->getPayments()->add($newPayment);
        $this->entityManager->flush();
        die('<script>location.href = "/pidb/'.$accounting_document_id.' " </script>');
    }

    /**
     * A helper method to render views.
     *
     * @param $view
     * @param array $data
     *
     * @return void
     */
    private function render($view, array $data = []): void
    {
        // Extract data array to variables.
        extract($data);
        // Include the view file.
        require_once __DIR__ . "/../Views/pidb/$view.php";
    }

}
