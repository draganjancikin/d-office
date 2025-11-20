<?php

namespace App\Controller;

use App\Entity\AccountingDocument;
use App\Entity\AccountingDocumentArticle;
use App\Entity\AccountingDocumentArticleProperty;
use App\Entity\AccountingDocumentType;
use App\Entity\Article;
use App\Entity\ArticleProperty;
use App\Entity\Client;
use App\Entity\CompanyInfo;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\Preferences;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use TCPDF;

/**
 * DocumentController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class DocumentController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private string $page;
    private string $page_title;
    protected string $stylesheet;

    /**
     * DocumentController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->page_title = 'Dokumenti';
        $this->page = 'documents';
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
    }

    /**
     * Displays the documents index page.
     *
     * Checks if the user is logged in, retrieves recent proformas, delivery notes, and return receipts, and renders
     * the documents index view.
     *
     * @return Response
     */
    #[Route('/documents', name: 'documents_index', methods: ['GET'])]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
            'proformas' => $this->entityManager->getRepository(AccountingDocument::class)->getLast(1, 0, 10),
            'delivery_notes' => $this->entityManager->getRepository(AccountingDocument::class)->getLast(2, 0, 10),
            'return_receipts' => $this->entityManager->getRepository(AccountingDocument::class)->getLast(4, 0, 10),
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('document/index.html.twig', $data);
    }

    /**
     * Displays the form for creating a new accounting document.
     *
     * Checks if the user is logged in, retrieves optional client and project data, fetches the list of clients, and
     * renders the new document form view.
     *
     * @param int|null $client_id
     *   Optional client ID to preselect in the form.
     * @param int|null $project_id
     *   Optional project ID to preselect in the form.
     *
     * @return Response
     */
    #[Route('/documents/new', name: 'documents_new_form', methods: ['GET'])]
    public function new(?int $client_id = NULL, ?int $project_id = NULL): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        if (isset($_GET['project_id'])) {
            $project_id = htmlspecialchars($_GET['project_id']);
        }

        if (isset($_GET['client_id'])) {
            $client_id = htmlspecialchars($_GET['client_id']);
            $client = $this->entityManager->find(Client::class, $client_id);
        }

        $clients_list = $this->entityManager->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'project_id' => $project_id,
            'client' => $client ?? NULL,
            'clients_list' => $clients_list,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
        ];

        return $this->render('document/document_new.html.twig', $data);
    }

    /**
     * Handles the creation of a new accounting document.
     *
     * Validates user session, processes form data for a new document, persists the document and its associations
     * (client, type, project), and redirects to the document details page.
     *
     * @return Response
     */
    #[Route('/documents/create', name: 'documents_create', methods: ['POST'])]
    public function create(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $ordinal_num_in_year = 0;

        $client_id = htmlspecialchars($_POST["client_id"]);
        $client = $this->entityManager->find(Client::class, $client_id);

        $accd_type_id = htmlspecialchars($_POST["pidb_type_id"]);
        $accd_type = $this->entityManager->find(AccountingDocumentType::class, $accd_type_id);

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
        $this->entityManager->getRepository(AccountingDocument::class)->setOrdinalNumInYear($new_accounting_document_id);

        if (isset($_POST["project_id"])) {
            $project_id = htmlspecialchars($_POST["project_id"]);
            $project = $this->entityManager->find(Project::class, $project_id);

            $project->getAccountingDocuments()->add($newAccountingDocument);

            $this->entityManager->flush();
        }
        else {
            $project_id = NULL;
        }

        return $this->redirectToRoute('document_show', ['document_id' => $new_accounting_document_id]);
    }

    /**
     * Displays the details of a specific accounting document.
     *
     * Checks if the user is logged in, retrieves the accounting document and its related data (client, articles,
     * totals, payments, etc.), prepares navigation and financial summary, and renders the document details view.
     *
     * @param int $document_id
     *   The ID of the accounting document to display.
     *
     * @return Response
     */
    #[Route('/documents/{document_id}', name: 'document_show', requirements: ['document_id' => '\d+'], methods: ['GET'])]
    public function show(int $document_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document_data = $this->entityManager->find(AccountingDocument::class, $document_id);

        $client_id = $document_data->getClient()->getId();
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $all_articles = $this->entityManager->getRepository(Article::class)->findAll();

        $pidb_type_id = $document_data->getType()->getId();

        [$pidb_tupe, $pidb_tag, $pidb_style] = match ($pidb_type_id) {
            1 => ["Predračun", "P_", 'info'],
            2 => ["Otpremnica", "O_", 'secondary'],
            4 => ["Povratnica", "POV_", 'warning'],
            default => ["_", "_", 'default'],
        };

        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($document_id);

        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($document_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($document_id);

        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;

        $previous = $this->entityManager
          ->getRepository(AccountingDocument::class)->getPrevious($document_id, $document_data->getType()->getId());

        $next = $this->entityManager
          ->getRepository(AccountingDocument::class)->getNext($document_id, $document_data->getType()->getId());

        $avans_eur = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($document_id);
        $avans_rsd = $avans_eur * $kurs;
        $income_eur = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($document_id);
        $income_rsd = $income_eur * $kurs;
        $remaining_rsd = $total_rsd - $avans_rsd - $income_rsd;
        $remaining_eur = ($total_rsd / $kurs) - $avans_eur - $income_eur;

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'pidb_id' => $document_id,
            'pidb_data' => $document_data,
            'pidb_type_id' => $pidb_type_id,
            'pidb_type' => $pidb_tupe,
            'pidb_tag' => $pidb_tag,
            'pidb_style' => $pidb_style,
            'client' => $client,
            'all_articles' => $all_articles,
            'kurs' => $kurs,
            'accounting_document_articles_data' => $accounting_document_articles_data,
            'tools_menu' => [
                'document' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
                'cash_register' => TRUE,
            ],
            'previous' => $previous,
            'next' => $next,
            'avans_eur' => $avans_eur,
            'avans_rsd' => $avans_rsd,
            'income_eur' => $income_eur,
            'income_rsd' => $income_rsd,
            'total_tax_base_rsd' => $total_tax_base_rsd,
            'total_tax_amount_rsd' => $total_tax_amount_rsd,
            'total_rsd' => $total_rsd,
            'total_eur' => $total_rsd / $kurs,
            'remaining_rsd' => $remaining_rsd,
            'remaining_eur' => $remaining_eur,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('document/document_view.html.twig', $data);
    }

    /**
     * Displays the edit form for a specific accounting document.
     *
     * Checks if the user is logged in, retrieves the accounting document and its related data (client, articles,
     * totals, payments, etc.), prepares navigation, financial summary, and a list of clients, and renders the document edit view.
     *
     * @param int $document_id
     *   The ID of the accounting document to edit.
     * @return Response
     */
    #[Route('/documents/{document_id}/edit', name: 'document_edit_form', methods: ['GET'])]
    public function edit(int $document_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document_data = $this->entityManager->find(AccountingDocument::class, $document_id);

        $client_id = $document_data->getClient()->getId();
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $all_articles = $this->entityManager->getRepository(Article::class)->findAll();

        $pidb_type_id = $document_data->getType()->getId();
        [$pidb_tupe, $pidb_tag, $pidb_style] = match ($pidb_type_id) {
            1 => ["Predračun", "P_", 'info'],
            2 => ["Otpremnica", "O_", 'secondary'],
            4 => ["Povratnica", "POV_", 'warning'],
            default => ["_", "_", 'default'],
        };

        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($document_id);

        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($document_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($document_id);

        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;

        $previous = $this->entityManager
            ->getRepository(AccountingDocument::class)->getPrevious($document_id, $document_data->getType()->getId());

        $next = $this->entityManager
            ->getRepository(AccountingDocument::class)->getNext($document_id, $document_data->getType()->getId());

        $avans_eur = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($document_id);
        $avans_rsd = $avans_eur * $kurs;
        $income_eur = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($document_id);
        $income_rsd = $income_eur * $kurs;
        $remaining_rsd = $total_rsd - $avans_rsd - $income_rsd;
        $remaining_eur = ($total_rsd / $kurs) - $avans_eur - $income_eur;

        $clients_list = $this->entityManager->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'client' => $client,
            'pidb_id' => $document_id,
            'pidb_data' => $document_data,
            'pidb_type_id' => $pidb_type_id,
            'pidb_type' => $pidb_tupe,
            'pidb_tag' => $pidb_tag,
            'pidb_style' => $pidb_style,
            'all_articles' => $all_articles,
            'tools_menu' => [
                'document' => TRUE,
                'edit' => TRUE,
                'view' => FALSE,
                'cash_register' => TRUE,
            ],
            'accounting_document_articles_data' => $accounting_document_articles_data,
            'previous' => $previous,
            'next' => $next,
            'avans_eur' => $avans_eur,
            'avans_rsd' => $avans_eur * $kurs,
            'income_eur' => $income_eur,
            'income_rsd' => $income_rsd,
            'total_tax_base_rsd' => $total_tax_base_rsd,
            'total_tax_amount_rsd' => $total_tax_amount_rsd,
            'total_rsd' => $total_rsd,
            'total_eur' => $total_rsd / $kurs,
            'remaining_rsd' => $remaining_rsd,
            'remaining_eur' => $remaining_eur,
            'clients_list' => $clients_list,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('document/document_edit.html.twig', $data);
    }

    /**
     * Updates an existing accounting document with new data from the edit form.
     *
     * Checks user session, retrieves the document and user, processes POST data (title, client, archive status, note),
     * updates the document fields, saves changes, and redirects to the document details page.
     *
     * @param int $document_id
     *   The ID of the accounting document to update.
     *
     * @return Response
     */
//    #[Route('/documents/{document_id}/update', name: 'document_update', methods: ['POST'])]
    #[Route('/documents/{document_id}/update', name: 'document_update', requirements: ['document_id' => '\d+'], methods: ['POST'])]
    public function update(int $document_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $accounting_document = $this->entityManager->find(AccountingDocument::class, $document_id);

        $title = htmlspecialchars($_POST["title"]);

        $client_id = htmlspecialchars($_POST["client_id"]);
        $client = $this->entityManager->find(Client::class, $client_id);

        $is_archived = htmlspecialchars($_POST["archived"]);
        $note = htmlspecialchars($_POST["note"]);

        $accounting_document->setTitle($title);
        $accounting_document->setClient($client);
        $accounting_document->setIsArchived($is_archived);
        $accounting_document->setNote($note);
        $accounting_document->setModifiedByUser($user);
        $accounting_document->setModifiedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return $this->redirectToRoute('document_show', ['document_id' => $document_id]);
    }

    /**
     * Deletes an accounting document if there are no related payments or advances.
     *
     * This method checks for related payments (income) and advances. If any exist, it prevents deletion and displays a
     * message. If the document has a parent, it reassigns payments to the parent and unarchives it. It then removes all
     * related articles and their properties before deleting the document itself. Finally, it redirects to the search
     * page.
     *
     * @param int $document_id
     *   The ID of the accounting document to delete.
     *
     * @return Response
     *    Redirects to the search page after deletion or if deletion is not possible.
     *
     * @throws \Doctrine\DBAL\Exception If a database error occurs during the deletion process.
     */
    #[Route('/documents/{document_id}/delete', name: 'document_delete', methods: ['GET'])]
    public function delete(int $document_id): Response
    {
        $acc_doc_id = $document_id;

        // Check if exist AccountingDocument.
        if ($accounting_document = $this->entityManager->find(AccountingDocument::class, $acc_doc_id)) {

            // Check if AccountingDocument have Payments, where PaymentType is Income.
            if ($this->entityManager->getRepository(AccountingDocument::class)->getPaymentsByIncome($acc_doc_id)) {
                echo "Brisanje dokumenta nije moguće jer postoje uplate vezane za ovaj dokument!";
                echo "<br><a href='/pidb/{$acc_doc_id}/transactions'>Idi na transakcije dokumenta >></a>";
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
                        $result = $queryBuilder ->executeStatement();
                    }

                    // Set Parent to active
                    $parent->setIsArchived(0);
                    $this->entityManager->flush();
                }
                else {
                    if ( $this->entityManager->getRepository(AccountingDocument::class)->getPaymentsByAvans($acc_doc_id) ){
                        echo "Brisanje dokumenta nije moguće jer postoje avansi vezani za ovaj dokument!<br>";
                        echo "<a href='/pidb/{$acc_doc_id}/transactions'>Idi na transakcije dokumenta >></a>";
                        exit();
                    }
                }
            }

            // Check if exist Articles in AccountingDocument.
            if (
                $accounting_document__articles = $this->entityManager
                    ->getRepository(AccountingDocumentArticle::class)->findBy(['accounting_document' => $acc_doc_id], [])
            ) {

                // Loop through all articles.
                foreach ($accounting_document__articles as $accounting_document__article) {

                    // Check if exist Properties in AccontingDocument Article.
                    if (
                        $accounting_document__article__properties = $this->entityManager
                            ->getRepository(AccountingDocumentArticleProperty::class)
                            ->findBy(['accounting_document_article' => $accounting_document__article])
                    ) {
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
        return $this->redirectToRoute('documents_search', ['term' => '']);
    }

    /**
     * Adds a new article to the specified accounting document.
     *
     * Retrieves the document and article, processes form data (pieces, note, etc.), creates and persists a new
     * AccountingDocumentArticle entity, copies article properties, and redirects to the document edit form.
     *
     * @param int $document_id
     *   The ID of the accounting document to which the article will be added.
     *
     * @return Response
     *   Redirects to the document edit form.
     */
    #[Route('/documents/{document_id}/articles/add', name: 'document_add_article', methods: ['POST'])]
    public function addArticle(int $document_id): Response
    {
        $accounting_document = $this->entityManager->find(AccountingDocument::class, $document_id);

        $article_id = htmlspecialchars($_POST["article_id"]);
        $article = $this->entityManager->find(Article::class, $article_id);

        $price = $article->getPrice();
        $discount = 0;
        $weight = $article->getWeight();

        $pieces = 0;
        if (isset($_POST["pieces"]) && is_numeric($_POST["pieces"])) {
            $pieces = htmlspecialchars($_POST["pieces"]);
        }

        $preferences = $this->entityManager->find(Preferences::class, 1);
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
                ->getRepository(ArticleProperty::class)->getArticleProperties($article->getId());
        foreach ($article_properties as $article_property) {
            // Insert to table v6__accounting_documents__articles__properties.
            $newAccountingDocumentArticleProperty = new AccountingDocumentArticleProperty();

            $newAccountingDocumentArticleProperty->setAccountingDocumentArticle($newAccountingDocumentArticle);
            $newAccountingDocumentArticleProperty->setProperty($article_property->getProperty());
            $newAccountingDocumentArticleProperty->setQuantity(0);

            $this->entityManager->persist($newAccountingDocumentArticleProperty);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('document_edit_form', ['document_id' => $document_id]);
    }

    /**
     * Generates and outputs a PDF for the specified accounting document.
     *
     * This method is mapped to the 'document_print' route. It fetches all necessary data for the document, renders the
     * content using a Twig template, generates a PDF using TCPDF, and returns it as an inline browser response. The
     * generated PDF filename initially includes leading double underscores, which are removed before sending the file
     * to the client.
     *
     * @param int $document_id
     *   The ID of the accounting document to print.
     *
     * @return Response
     *   The PDF file as a Symfony inline response.
     */
    #[Route('/documents/{document_id}/print', name: 'document_print', methods: ['GET'])]
    public function printAccountingDocument(int $document_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $pidb = $this->entityManager->find(AccountingDocument::class, $document_id);
        $pidb_type = $pidb->getType()->getName();
        $pidb_type_id = $pidb->getType()->getId();
        [$pidb_tupe, $pidb_tag, $pidb_style] = match ($pidb_type_id) {
            1 => ["Predračun", "P_", 'info'],
            2 => ["Otpremnica", "O_", 'secondary'],
            4 => ["Povratnica", "POV_", 'warning'],
            default => ["_", "_", 'default'],
        };
        $pidb_ordinal_number_in_year = str_pad($pidb->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT);
        $pidb_date_month = $pidb->getDate()->format('m');

        $client = $this->entityManager->getRepository(Client::class)->getClientData($pidb->getClient()->getId());

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($document_id);

        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($document_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($document_id);
        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;

        $avans_eur = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($document_id);
        $avans_rsd = $avans_eur * $kurs;
        $income_eur = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($document_id);
        $income_rsd = $income_eur * $kurs;
        $remaining_rsd = $total_rsd - $avans_rsd - $income_rsd;
        $remaining_eur = ($total_rsd / $kurs) - $avans_eur - $income_eur;

        $data = [
            'pidb_id' => $document_id,
            'pidb' => $pidb,
            'pidb_type' => $pidb_tupe,
            'company_info' => $company_info,
            'client' => $client,
            'accounting_document_articles_data' => $accounting_document_articles_data,
            'total_tax_base_rsd' => $total_tax_base_rsd,
            'total_tax_amount_rsd' => $total_tax_amount_rsd,
            'total_rsd' => $total_rsd,
            'avans_eur' => $avans_eur,
            'avans_rsd' => $avans_rsd,
            'remaining_rsd' => $remaining_rsd,
            'remaining_eur' => $remaining_eur,
        ];

      // Render HTML content from a Twig template
      $html = $this->renderView('document/print_accounting_document.html.twig', $data);

      require_once '../config/packages/tcpdf_include.php';

      // Create a new TCPDF object / PDF document
      $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

      // Set document information
      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetAuthor($company_info['name']);
      $pdf->SetTitle($company_info['name'] . ' - '. $pidb_tupe);
      $pdf->SetSubject($company_info['name']);
      $pdf->SetKeywords($company_info['name'] . ', PDF, Proforma, Invoice');

      // Remove default header/footer.
      $pdf->setPrintHeader(false);
      $pdf->setPrintFooter(false);

      // Set default monospaced font.
      $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

      // Set margins.
      $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

      // Set auto page breaks.
      $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

      // Set image scale factor.
      $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

      // Set font.
      $pdf->SetFont('dejavusans', '', 10);

      // Add a page.
      $pdf->AddPage();

      // Write HTML content
      $pdf->writeHTML($html, true, false, true, false, '');

      // Reset pointer to the last page.
      $pdf->lastPage();

      // Output PDF document to browser as a Symfony Response
      $filename = '__' . $pidb_type . '_' . $pidb_ordinal_number_in_year . '-' . $pidb_date_month . '.pdf';
      $pdfContent = $pdf->Output($filename, 'S');
      // Remove leading __ from filename for the response
      $cleanFilename = ltrim($filename, '_');
      $response = new Response($pdfContent);
      $response->headers->set('Content-Type', 'application/pdf');
      $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
      return $response;
    }

    /**
     * Generates and outputs a PDF for the specified accounting document (IW variant).
     *
     * This method prepares all necessary data for the document, renders the content using a Twig template, generates a
     * PDF using TCPDF, and returns it as an inline browser response. The generated PDF filename initially includes
     * leading double underscores, which are removed before sending the file to the client.
     *
     * @param int $document_id
     *   The ID of the accounting document to print.
     *
     * @return Response
     *   The PDF file as a Symfony inline response.
     */
    #[Route('/documents/{document_id}/print-w', name: 'document_print_w', methods: ['GET'])]
    public function printAccountingDocumentW(int $document_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $pidb = $this->entityManager->find(AccountingDocument::class, $document_id);
        $pidb_type = $pidb->getType()->getName();
        $pidb_type_id = $pidb->getType()->getId();
        [$pidb_tupe, $pidb_tag, $pidb_style] = match ($pidb_type_id) {
            1 => ["Predračun", "P_", 'info'],
            2 => ["Otpremnica", "O_", 'secondary'],
            4 => ["Povratnica", "POV_", 'warning'],
            default => ["_", "_", 'default'],
        };
        $pidb_ordinal_number_in_year = str_pad($pidb->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT);
        $pidb_date_month = $pidb->getDate()->format('m');
        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();
        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($document_id);
        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($document_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($document_id);
        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;
        $data = [
            'company_info' => $company_info,
            'pidb' => $pidb,
            'pidb_type' => $pidb_tupe,
            'accounting_document_articles_data' => $accounting_document_articles_data,
            'total_tax_base_rsd' => $total_tax_base_rsd,
            'total_tax_amount_rsd' => $total_tax_amount_rsd,
            'total_rsd' => $total_rsd,
            'total_eur' => $total_rsd / $kurs,
        ];
        $html = $this->renderView('document/print_accounting_document_w.html.twig', $data);
        require_once '../config/packages/tcpdf_include.php';
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Dokument');
        $pdf->SetSubject($company_info['name']);
        $pdf->SetKeywords($company_info['name'] . ', PDF, Proforma, Invoice');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();
        $filename = '__' . $pidb_type . '_' . $pidb_ordinal_number_in_year . '-' . $pidb_date_month . '.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

    /**
     * Print Accounting Document I.
     *
     * @param int $pidb_id
     * @return Response
     */
    #[Route('/documents/{pidb_id}/print-i', name: 'document_print_i', methods: ['GET'])]
    public function printAccountingDocumentI(int $pidb_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $pidb = $this->entityManager->find(AccountingDocument::class, $pidb_id);
        $pidb_type = $pidb->getType()->getName();
        $pidb_type_id = $pidb->getType()->getId();
        [$pidb_tupe, $pidb_tag, $pidb_style] = match ($pidb_type_id) {
            1 => ["Predračun", "P_", 'info'],
            2 => ["Otpremnica - račun", "O_", 'secondary'],
            4 => ["Povratnica", "POV_", 'warning'],
            default => ["_", "_", 'default'],
        };
        $pidb_ordinal_number_in_year = str_pad($pidb->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT);
        $pidb_date_month = $pidb->getDate()->format('m');
        $client = $this->entityManager->getRepository(Client::class)->getClientData($pidb->getClient()->getId());
        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();
        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($pidb_id);
        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($pidb_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($pidb_id);
        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;
        $avans_eur = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($pidb_id);
        $avans_rsd = $avans_eur * $kurs;
        $income_eur = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($pidb_id);
        $income_rsd = $income_eur * $kurs;
        $remaining_rsd = $total_rsd - $avans_rsd - $income_rsd;
        $remaining_eur = ($total_rsd / $kurs) - $avans_eur - $income_eur;
        $data = [
            'company_info' => $company_info,
            'pidb' => $pidb,
            'pidb_type' => $pidb_tupe,
            'client' => $client,
            'accounting_document_articles_data' => $accounting_document_articles_data,
            'total_tax_base_rsd' => $total_tax_base_rsd,
            'total_tax_amount_rsd' => $total_tax_amount_rsd,
            'total_rsd' => $total_rsd,
            'total_eur' => $total_rsd / $kurs,
            'avans_eur' => $avans_eur,
            'avans_rsd' => $avans_rsd,
            'income_rsd' => $income_rsd,
            'remaining_rsd' => $remaining_rsd,
            'remaining_eur' => $remaining_eur,
        ];
        $html = $this->renderView('document/print_accounting_document_i.html.twig', $data);
        require_once '../config/packages/tcpdf_include.php';
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Dokument');
        $pdf->SetSubject($company_info['name']);
        $pdf->SetKeywords($company_info['name'] . ', PDF, Proforma, Invoice');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();
        $filename = '__' . $pidb_type . '_' . $pidb_ordinal_number_in_year . '-' . $pidb_date_month . '.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

    /**
     * Print Accounting Document IW.
     *
     * @param int $document_id
     * @return Response
     */
    #[Route('/documents/{document_id}/print-iw', name: 'document_print_iw', methods: ['GET'])]
    public function printAccountingDocumentIW(int $document_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $pidb = $this->entityManager->find(AccountingDocument::class, $document_id);
        $pidb_type = $pidb->getType()->getName();
        $pidb_type_id = $pidb->getType()->getId();
        [$pidb_tupe, $pidb_tag, $pidb_style] = match ($pidb_type_id) {
            1 => ["Predračun", "P_", 'info'],
            2 => ["Otpremnica - račun", "O_", 'secondary'],
            4 => ["Povratnica", "POV_", 'warning'],
            default => ["_", "_", 'default'],
        };
        $pidb_ordinal_number_in_year = str_pad($pidb->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT);
        $pidb_date_month = $pidb->getDate()->format('m');
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();
        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($document_id);
        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($document_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($document_id);
        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;
        $avans_eur = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($document_id);
        $avans_rsd = $avans_eur * $kurs;
        $income_eur = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($document_id);
        $income_rsd = $income_eur * $kurs;
        $remaining_rsd = $total_rsd - $avans_rsd - $income_rsd;
        $remaining_eur = ($total_rsd / $kurs) - $avans_eur - $income_eur;
        $data = [
            'company_info' => $company_info,
            'pidb' => $pidb,
            'pidb_type' => $pidb_tupe,
            'accounting_document_articles_data' => $accounting_document_articles_data,
            'total_tax_base_rsd' => $total_tax_base_rsd,
            'total_tax_amount_rsd' => $total_tax_amount_rsd,
            'total_rsd' => $total_rsd,
            'total_eur' => $total_rsd / $kurs,
            'avans_eur' => $avans_eur,
            'avans_rsd' => $avans_rsd,
            'income_rsd' => $income_rsd,
            'remaining_rsd' => $remaining_rsd,
            'remaining_eur' => $remaining_eur,
        ];
        $html = $this->renderView('document/print_accounting_document_iw.html.twig', $data);
        require_once '../config/packages/tcpdf_include.php';
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Dokument');
        $pdf->SetSubject($company_info['name']);
        $pdf->SetKeywords($company_info['name'] . ', PDF, Proforma, Invoice');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();
        $filename = '__' . $pidb_type . '_' . $pidb_ordinal_number_in_year . '-' . $pidb_date_month . '.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

    /**
     * Export Proforma to Dispatch.
     *
     * @param int $document_id
     *   The ID of the proforma document to be exported.
     * @param EntityManagerInterface $em
     *   The EntityManagerInterface instance.
     *
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route(
        '/documents/{document_id}/export-proforma-to-dispatch',
        name: 'document_export_proforma_to_dispatch',
        methods: ['GET']
    )]
    public function exportProformaToDispatch(int $document_id, EntityManagerInterface $em): Response
    {
        session_start();
        $user = $em->find(User::class, $_SESSION['user_id']);

        $proforma_id = $document_id;
        $proforma = $em->find(AccountingDocument::class, $proforma_id);

        $ordinal_num_in_year = 0;

        // Save Proforma data to Dispatch.
        $newDispatch = new AccountingDocument();

        $newDispatch->setOrdinalNumInYear($ordinal_num_in_year);
        $newDispatch->setDate(new \DateTime("now"));
        $newDispatch->setIsArchived(0);

        $newDispatch->setType($em->find(AccountingDocumentType::class, 2));
        $newDispatch->setTitle($proforma->getTitle());
        $newDispatch->setClient($proforma->getClient());
        $newDispatch->setParent($proforma);
        $newDispatch->setNote($proforma->getNote());

        $newDispatch->setCreatedAt(new \DateTime("now"));
        $newDispatch->setCreatedByUser($user);
        $newDispatch->setModifiedAt(new \DateTime("now"));

        $em->persist($newDispatch);
        $em->flush();

        // Get id of last AccountingDocument.
        $last_accounting_document_id = $newDispatch->getId();

        // Set Ordinal Number In Year.
        $em->getRepository(AccountingDocument::class)->setOrdinalNumInYear($last_accounting_document_id);

        // Get proforma payments.
        $payments = $proforma->getPayments();

        // Update all payment.
        foreach ($payments as $payment) {
            // TODO Dragan: Rešiti bolje konekciju na bazu.
            $conn = \Doctrine\DBAL\DriverManager::getConnection([
                'dbname' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASSWORD'],
                'host' => $_ENV['DB_SERVER'],
                'driver' => 'mysqli',
            ]);
            $queryBuilder = $conn->createQueryBuilder();
            $queryBuilder
                ->update('v6__accounting_documents__payments')
                ->set('accountingdocument_id', ':dispatch')
                ->where('payment_id = :payment')
                ->setParameter('dispatch', $last_accounting_document_id)
                ->setParameter('payment', $payment->getId());
            $result = $queryBuilder ->executeStatement();
        }

        // Get articles from proforma.
        $proforma_articles = $em->getRepository(AccountingDocument::class)->getArticles($proforma->getId());

        // Save articles to dispatch.
        foreach ($proforma_articles as $proforma_article) {
            $newDispatchArticle = new AccountingDocumentArticle();

            $newDispatchArticle->setAccountingDocument($newDispatch);
            $newDispatchArticle->setArticle($proforma_article->getArticle());
            $newDispatchArticle->setPieces($proforma_article->getPieces());
            $newDispatchArticle->setPrice($proforma_article->getPrice());
            $newDispatchArticle->setDiscount($proforma_article->getDiscount());
            $newDispatchArticle->setTax($proforma_article->getTax());
            $newDispatchArticle->setWeight($proforma_article->getWeight());
            $newDispatchArticle->setNote($proforma_article->getNote());

            $em->persist($newDispatchArticle);
            $em->flush();

            // Get $proforma_article properties.
            $proforma_article_properties =
                $em->getRepository(AccountingDocumentArticleProperty::class)
                    ->findBy(array('accounting_document_article' => $proforma_article->getId()), []);

            // Save $proforma_article properties to $newDispatchArticle.
            foreach ($proforma_article_properties as $article_property) {
                $newDispatchArticleProperty = new AccountingDocumentArticleProperty();

                $newDispatchArticleProperty->setAccountingDocumentArticle($newDispatchArticle);
                $newDispatchArticleProperty->setProperty($article_property->getProperty());
                $newDispatchArticleProperty->setQuantity($article_property->getQuantity());
                $em->persist($newDispatchArticleProperty);
                $em->flush();
            }
        }

        // Set Proforma to archive.
        $proforma->setIsArchived(1);
        $em->flush();

        // Check if proforma belong to any Project
        $project = $em->getRepository(AccountingDocument::class)->getProjectByAccountingDocument
        ($proforma->getId());

        if ($project) {
            // Set same project to dispatch.
            $project->getAccountingDocuments()->add($newDispatch);
            $em->flush();
        }

        return $this->redirectToRoute('document_show', ['document_id' => $last_accounting_document_id]);
    }

    /**
     * Updates an article and its properties in a specific accounting document.
     *
     * Processes POST data for the article (note, pieces, price, discounts), updates the corresponding
     * AccountingDocumentArticle entity, then updates all related article properties with new values, and finally
     * redirects to the document details page.
     *
     * @param int $document_id
     *   The ID of the accounting document containing the article.
     * @param int $pidb_article_id
     *   The ID of the article within the accounting document to update.
     *
     * @return Response
     *   Redirects to the document details page.
     */
    #[Route('/documents/{document_id}/articles/{pidb_article_id}/edit', name: 'edit_article_in_accounting_document',
        methods: ['POST'])]
    public function editArticleInAccountingDocument(int $document_id, int $pidb_article_id): Response
    {
        $accounting_document__article_id = $pidb_article_id;

        $note = htmlspecialchars($_POST["note"]);

        $pieces_1 = htmlspecialchars($_POST["pieces"]);
        $pieces = str_replace(",", ".", $pieces_1);

        $price_1 = htmlspecialchars($_POST["price"]);
        $price = str_replace(",", ".", $price_1);

        $discounts_1 = htmlspecialchars($_POST["discounts"]);
        $discounts = str_replace(",", ".", $discounts_1);

        $accountingDocumentArticle = $this->entityManager
            ->find(AccountingDocumentArticle::class, $accounting_document__article_id);

        $accountingDocumentArticle->setNote($note);
        $accountingDocumentArticle->setPieces($pieces);
        $accountingDocumentArticle->setPrice($price);
        $accountingDocumentArticle->setDiscount($discounts);
        $this->entityManager->flush();

        // Properties update in table v6__accounting_documents__articles__properties.
        $accounting_document__article__properties = $this->entityManager
            ->getRepository(AccountingDocumentArticleProperty::class)
            ->findBy(['accounting_document_article' => $accounting_document__article_id], []);
        foreach ($accounting_document__article__properties as $accounting_document__article__property) {
            // Get property name from $accounting_document__article__property.
            $property_name = $accounting_document__article__property->getProperty()->getName();
            // Get property value from $_POST.
            $property_value = str_replace(".", "", htmlspecialchars($_POST["$property_name"]));
            $property_value = str_replace(",", ".", htmlspecialchars($property_value));

            $accountingDocumentArticleProperty = $this->entityManager
                ->find(AccountingDocumentArticleProperty::class, $accounting_document__article__property->getId());

            $accountingDocumentArticleProperty->setQuantity($property_value);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('document_show', ['document_id' => $document_id]);
    }

    /**
     * Displays the form to change an article in a specific accounting document.
     *
     * Checks if the user is logged in, retrieves the accounting document and the article to be changed, fetches all
     * available articles, determines the style based on document type, and renders the change article form view.
     *
     * @param int $document_id
     *   The ID of the accounting document containing the article.
     * @param int $pidb_article_id
     *   The ID of the article within the accounting document to change.
     *
     * @return Response
     *   Renders the change article form view.
     */
    #[Route('/documents/{document_id}/articles/{pidb_article_id}/change', name: 'change_article_in_document_form')]
    public function changeArticleInDocument(int $document_id, int $pidb_article_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $pidb_data = $this->entityManager->find(AccountingDocument::class, $document_id);
        $article_data = $this->entityManager->find(AccountingDocumentArticle::class, $pidb_article_id)->getArticle();
        $all_articles = $this->entityManager->getRepository(Article::class)->findAll();

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
            'pidb_id' => $document_id,
            'pidb_data' => $pidb_data,
            'pidb_article_id' => $pidb_article_id,
            'article_data' => $article_data,
            'all_articles' => $all_articles,
            'style' => $style,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
        ];

        return $this->render('document/article_in_document_change.html.twig', $data);
    }

    /**
     * Updates the article reference and its properties in a specific accounting document.
     *
     * Checks if the article has changed; if so, removes old article properties, updates the article reference, resets
     * related fields, inserts new properties for the new article, and saves changes. Redirects to the document edit
     * form.
     *
     * @param int $document_id
     *   The ID of the accounting document containing the article.
     * @param int $pidb_article_id
     *   The ID of the article within the accounting document to update.
     *
     * @return Response
     *   Redirects to the document edit form.
     */
    #[Route('/documents/{document_id}/articles/{pidb_article_id}/update', name: 'update_article_in_accounting_document')]
    public function updateArticleInAccountingDocument(int $document_id, int $pidb_article_id): Response
    {
        $accounting_document_id = $document_id;
        $pidb_article = $this->entityManager->find(AccountingDocumentArticle::class, $pidb_article_id);

        $old_article = $this->entityManager->find(Article::class, $pidb_article->getArticle()->getId());
        $old_article_id = $old_article->getId();

        $new_article_id = htmlspecialchars($_POST["article_id"]);
        $new_article = $this->entityManager->find(Article::class, $new_article_id);

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
                        ->getRepository(AccountingDocumentArticleProperty::class)
                        ->findBy(array('accounting_document_article' => $pidb_article_id), array())
            ) {
                foreach ($accounting_document__article__properties as $accounting_document__article__property) {
                    $accountingDocumentArticleProperty =
                        $this->entityManager
                          ->find(AccountingDocumentArticleProperty::class, $accounting_document__article__property->getId());
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
            $article_properties = $this->entityManager->getRepository(ArticleProperty::class)->getArticleProperties
            ($new_article->getId());
            foreach ($article_properties as $article_property) {
                // Insert to table v6__accounting_documents__articles__properties.
                $newAccountingDocumentArticleProperty = new AccountingDocumentArticleProperty();

                $newAccountingDocumentArticleProperty->setAccountingDocumentArticle($pidb_article);
                $newAccountingDocumentArticleProperty->setProperty($article_property->getProperty());
                $newAccountingDocumentArticleProperty->setQuantity(0);

                $this->entityManager->persist($newAccountingDocumentArticleProperty);
                $this->entityManager->flush();
            }

        }
        return $this->redirectToRoute('document_edit_form', ['document_id' => $accounting_document_id]);
    }

    /**
     * Duplicates an article and its properties in a specific accounting document.
     *
     * Calls the repository method to duplicate the specified article (and its properties) within the accounting
     * document, then redirects to the document edit form.
     *
     * @param int $document_id
     *   The ID of the accounting document containing the article to duplicate.
     * @param int $pidb_article_id
     *   The ID of the article within the accounting document to duplicate.
     *
     * @return Response
     *  Redirects to the document edit form.
     */
    #[Route('/documents/{document_id}/articles/{pidb_article_id}/duplicate', name: 'duplicate_article_in_accounting_document')]
    public function duplicateArticleInAccountingDocument(int $document_id, int $pidb_article_id): Response
    {
        $accounting_document__article__properties =
            $this->entityManager
                ->getRepository(AccountingDocumentArticle::class)
                ->duplicateArticleInAccountingDocument($pidb_article_id);

        return $this->redirectToRoute('document_edit_form', ['document_id' => $document_id]);
    }

    /**
     * Deletes an article and its properties from a specific accounting document.
     *
     * Removes all properties associated with the specified article from the join table, then removes the article itself
     * from the accounting document, and finally redirects to the document details page.
     *
     * @param int $document_id
     *   The ID of the accounting document containing the article to delete.
     * @param int $pidb_article_id
     *   The ID of the article within the accounting document to delete.
     *
     * @return Response
     *   Redirects to the document details page.
     */
    #[Route('/documents/{document_id}/articles/{pidb_article_id}/delete', name: 'delete_article_in_accounting_document')]
    public function deleteArticleInAccountingDocument(int $document_id, int $pidb_article_id): Response
    {
        $accounting_document__article = $this->entityManager->find(AccountingDocumentArticle::class, $pidb_article_id);

        // First remove properties from table v6__accounting_documents__articles__properties.
        if (
            $accounting_document__article__properties =
                $this->entityManager
                    ->getRepository(AccountingDocumentArticleProperty::class)
                    ->findBy(['accounting_document_article' => $pidb_article_id], [])
        ) {
            foreach ($accounting_document__article__properties as $accounting_document__article__property) {
                $accountingDocumentArticleProperty = $this->entityManager
                    ->find(AccountingDocumentArticleProperty::class, $accounting_document__article__property->getId());
                $this->entityManager->remove($accountingDocumentArticleProperty);
                $this->entityManager->flush();
            }
        }

        // Second remove Article from table v6__accounting_documents__articles
        $this->entityManager->remove($accounting_document__article);
        $this->entityManager->flush();

        return $this->redirectToRoute('document_show', ['document_id' => $document_id]);
    }

    /**
     * Displays a list of recent transactions (payments) with their associated accounting documents.
     *
     * This method checks user authentication, retrieves the latest transactions up to the specified limit, fetches the
     * related accounting documents for each transaction, and prepares all relevant data for the transactions view.
     *
     * @param int $limit
     *   The maximum number of transactions to display (default is 10).
     *
     * @return Response
     *   Renders the transactions view with transaction and document data.
     */
    #[Route('/documents/transactions', name: 'transactions_view', methods: ['GET'])]
    public function transactions(int $limit = 10): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $transactions = $this->entityManager->getRepository(AccountingDocument::class)->getLastTransactions($limit);

        $transactions_with_accounting_document = [];
        foreach ($transactions as $index => $transaction) {
            $transactions_with_accounting_document[$index] = [
                'transaction' => $transaction,
                'accounting_document' => $this->entityManager
                  ->getRepository(AccountingDocument::class)->getAccountingDocumentByTransaction($transaction->getId()),
            ];
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'transactions' => $transactions_with_accounting_document,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
        ];

        return $this->render('document/transactions_view.html.twig', $data);
    }

    /**
     * Displays all transactions (payments and advances) for a specific accounting document.
     *
     * This method checks user authentication, retrieves the accounting document, client, total amounts, and all related transactions (payments and advances). It calculates the total income, saldo, and determines the saldo class for display. The method then prepares all relevant data and renders the transactions view for the document.
     *
     * @param int $document_id
     *   The ID of the accounting document whose transactions are to be displayed.
     *
     * @return Response
     *   Renders the transactions view for the specified accounting document.
     */
    #[Route('/documents/{document_id}/transactions', name: 'document_transactions', methods: ['GET'])]
    public function transactionsByDocument(int $document_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $pidb = $this->entityManager->find(AccountingDocument::class, $document_id);
        $client = $this->entityManager->find(Client::class,$pidb->getClient());
        $total = $this->entityManager
            ->getRepository(AccountingDocument::class)
            ->getTotalAmountsByAccountingDocument($document_id);

        $transactions = $pidb->getPayments();

        $avans = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($document_id);
        $income = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($document_id);
        $total_income = $avans + $income;
        $saldo = $total - $total_income;

        $saldo_class = (round($total, 4) - round($total_income, 4)) <= 0
          ? "bg-success"
          : "bg-danger text-white";

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'pidb_id' => $document_id,
            'pidb' => $pidb,
            'client' => $client,
            'total' => $total,
            'transactions' => $transactions,
            'total_income' => $total_income,
            'saldo' => $saldo,
            'saldo_class' => $saldo_class,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
        ];

        return $this->render('document/transactions_by_document.html.twig', $data);
    }

    /**
     * Displays the edit form for a specific transaction (payment) in an accounting document.
     *
     * This method checks user authentication, retrieves the accounting document, transaction, and client data,
     * prepares all relevant information, and renders the transaction edit form view.
     *
     * @param int $document_id
     *   The ID of the accounting document containing the transaction.
     * @param int $transaction_id
     *   The ID of the transaction to edit.
     *
     * @return Response
     *   Renders the transaction edit form view.
     */
    #[Route('/documents/{document_id}/transactions/{transaction_id}/edit', name: 'transaction_edit_form', methods: ['GET'])]
    public function transactionEdit(int $document_id, int $transaction_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $pidb = $this->entityManager->find(AccountingDocument::class, $document_id);
        $transaction = $this->entityManager->find(Payment::class, $transaction_id);
        $client_id = $pidb->getClient()->getId();
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'pidb_id' => $document_id,
            'pidb' => $pidb,
            'transaction' => $transaction,
            'client' => $client,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
        ];

        return $this->render('document/transaction_edit.html.twig', $data);
    }

    /**
     * Updates an existing transaction (payment) for a specific accounting document.
     *
     * This method retrieves the transaction and payment type, processes the submitted form data (date, amount, note),
     * updates the transaction fields, saves the changes, and redirects to the document's transactions view.
     *
     * @param int $document_id
     *   The ID of the accounting document containing the transaction.
     * @param int $transaction_id
     *   The ID of the transaction to update.
     *
     * @return Response
     *   Redirects to the document's transactions view after update.
     *
     * @throws \DateMalformedStringException
     *   If the provided date string is invalid.
     */
    #[Route('/documents/{document_id}/transactions/{transaction_id}/update', name: 'transaction_update', methods: ['POST'])]
    public function transactionUpdate(int $document_id, int $transaction_id): Response
    {
        $transaction = $this->entityManager->find(Payment::class, $transaction_id);

        $type_id = htmlspecialchars($_POST["type_id"]);
        $type = $this->entityManager->find(PaymentType::class, $type_id);

        $date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
        $amount_1 = htmlspecialchars($_POST["amount"]);
        $amount = str_replace(",", ".", $amount_1);
        $note = htmlspecialchars($_POST["note"]);

        $transaction->setType($type);
        $transaction->setDate(new \DateTime($date));
        $transaction->setAmount($amount);
        $transaction->setNote($note);

        $this->entityManager->flush();

        return $this->redirectToRoute('document_transactions', ['document_id' => $document_id]);
    }

    /**
     * Delete transaction.
     *
     * @param int $document_id
     * @param int $transaction_id
     *
     * @return Response
     */
    #[Route('/documents/{document_id}/transactions/{transaction_id}/delete', name: 'transaction_delete', methods: ['GET'])]
    public function transactionDelete(int $document_id, int $transaction_id): Response
    {
        $transaction = $this->entityManager->find(Payment::class, $transaction_id);
        $this->entityManager->remove($transaction);
        $this->entityManager->flush();

        return $this->redirectToRoute('document_transactions', ['document_id' => $document_id]);
    }

    /**
     * Displays the edit form for application preferences (such as exchange rate and tax).
     *
     * This method checks user authentication, retrieves the current preferences, prepares all relevant data, and
     * renders the preferences edit form view.
     *
     * @return Response
     *   Renders the preferences edit form view.
     */
    #[Route('/documents/preferences', name: 'preferences_edit_form')]
    public function preferencesEdit(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $preferences = $this->entityManager->find(Preferences::class, 1);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'preferences' => $preferences,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
        ];

        return $this->render('document/preferences_edit.html.twig', $data);
    }

    /**
     * Updates application preferences (such as exchange rate and tax) based on submitted form data.
     *
     * This method processes the POSTed form data, updates the preferences entity with new values, saves the changes,
     * and redirects to the preferences edit form view.
     *
     * @return Response
     *   Redirects to the preferences edit form view after update.
     */
    #[Route('/documents/preferences/update', name: 'preferences_update', methods: ['POST'])]
    public function preferencesUpdate(): Response
    {
        $kurs = str_replace(",", ".", htmlspecialchars($_POST["kurs"]));
        $tax = str_replace(",", ".", htmlspecialchars($_POST["tax"]));

        $preferences = $this->entityManager->find(Preferences::class, 1);

        $preferences->setKurs($kurs);
        $preferences->setTax($tax);
        $this->entityManager->flush();

        return $this->redirectToRoute('preferences_edit_form');
    }

    /**
     * Add payment to Accounting Document.
     *
     * @param int $document_id
     *
     * @return Response
     */
    #[Route('/documents/{document_id}/add-payment', name: 'add_payment_to_accounting_document', methods: ['POST'])]
    public function addPayment(int $document_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $payment_type_id = htmlspecialchars($_POST["type_id"]);
        $payment_type = $this->entityManager->find(PaymentType::class, $payment_type_id);

//        if ($payment_type_id == 5 && $this->entityManager->getRepository(Payment::class)->ifExistFirstCashInput()) {
//            // TODO Dragan: Create error message
//            ?>
<!--            <p>Već ste uneli početno stanje!</p>-->
<!--            <a href="/pidb/?cashRegister">Povratak na Kasu</a>-->
<!--            --><?php
//            exit();
//        }

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

//        if ($payment_type_id == 6 || $payment_type_id == 7) {
//            $amount = "-".$amount;
//        }

        $newPayment->setAmount($amount);
        $newPayment->setDate(new \DateTime($date));
        $newPayment->setNote($note);
        $newPayment->setCreatedAt(new \DateTime("now"));
        $newPayment->setCreatedByUser($user);

        $this->entityManager->persist($newPayment);
        $this->entityManager->flush();

        $accounting_document_id = htmlspecialchars($_POST["pidb_id"]);
        $accounting_document = $this->entityManager->find(AccountingDocument::class, $accounting_document_id);
        // Add Payment to AccountingDocument.
        $accounting_document->getPayments()->add($newPayment);
        $this->entityManager->flush();

        return $this->redirectToRoute('document_show', ['document_id' => $accounting_document_id]);
    }

    /**
     * Searches for accounting documents (proformas, delivery notes, return notes) by a search term.
     *
     * This method checks user authentication, retrieves the search term from the request, queries the database for
     * matching documents (including archived and non-archived for each type), and prepares all relevant data for the
     * search results view.
     *
     * @param Request $request
     *   The HTTP request containing the search term as a query parameter.
     *
     * @return Response
     *   Renders the search results view with matching documents.
     */
    #[Route('/documents/search', name: 'documents_search')]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = $request->query->get('term', '');

        $proformas = $this->entityManager
            ->getRepository(AccountingDocument::class )->search([1, $term, 0]);
        $proformas_archived = $this->entityManager
            ->getRepository(AccountingDocument::class)->search([1, $term, 1]);
        $delivery_notes = $this->entityManager
            ->getRepository(AccountingDocument::class)->search([2, $term, 0]);
        $delivery_notes_archived = $this->entityManager
            ->getRepository(AccountingDocument::class)->search([2, $term, 1]);
        $return_notes = $this->entityManager
            ->getRepository(AccountingDocument::class)->search([4, $term, 0]);
        $return_notes_archived = $this->entityManager
            ->getRepository(AccountingDocument::class)->search([4, $term, 1]);
        $last_pidb = $this->entityManager
            ->getRepository(AccountingDocument::class)->getLastAccountingDocument();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'proformas' => $proformas,
            'proformas_archived' => $proformas_archived,
            'delivery_notes' => $delivery_notes,
            'delivery_notes_archived' => $delivery_notes_archived,
            'return_notes' => $return_notes,
            'return_notes_archived' => $return_notes_archived,
            'last_pidb' => $last_pidb,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
        ];

        return $this->render('document/search.html.twig', $data);
    }

    /**
     * @param int $pidb_id
     *
     * @return array
     */
    private function getAccountingDocumentArticlesData(int $pidb_id): array
    {
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $accounting_document_articles = $this->entityManager
            ->getRepository(AccountingDocument::class)->getArticles($pidb_id);

        $accounting_document_articles_data = [];

        foreach ($accounting_document_articles as $index => $accounting_document_article) {

            $accounting_document_article_properties = $this->entityManager
                ->getRepository(AccountingDocumentArticleProperty::class)
                ->findBy(['accounting_document_article' => $accounting_document_article->getId()], []);

            $accounting_document_articles_data[$index]['article']['id'] = $accounting_document_article->getId();
            $accounting_document_articles_data[$index]['article']['pieces'] = $accounting_document_article->getPieces();
            $accounting_document_articles_data[$index]['article']['name'] = $accounting_document_article->getArticle()->getName();
            foreach ($accounting_document_article_properties as $property_key => $accounting_document_article_property) {
                $accounting_document_articles_data[$index]['article']['properties'][$property_key]['name']
                    = $accounting_document_article_property->getProperty()->getName();
                $accounting_document_articles_data[$index]['article']['properties'][$property_key]['quantity']
                    = $accounting_document_article_property->getQuantity();
            }
            $accounting_document_articles_data[$index]['article']['unit'] = $accounting_document_article->getArticle()
                ->getUnit()->getName();
            $accounting_document_articles_data[$index]['article']['quantity'] = $this->entityManager
                ->getRepository(AccountingDocumentArticle::class)
                ->getQuantity(
                    $accounting_document_article->getId(),
                    $accounting_document_article->getArticle()->getMinCalcMeasure(),
                    $accounting_document_article->getPieces()
                );
            $accounting_document_articles_data[$index]['article']['note'] = $accounting_document_article->getNote();
            $accounting_document_articles_data[$index]['article']['price'] = $accounting_document_article->getPrice();
            $accounting_document_articles_data[$index]['article']['price_rsd'] = $accounting_document_article->getPrice() * $kurs;
            $accounting_document_articles_data[$index]['article']['discount'] = $accounting_document_article->getDiscount();
            $accounting_document_articles_data[$index]['article']['tax_base_rsd'] =  $this->entityManager
                ->getRepository(AccountingDocumentArticle::class)
                ->getTaxBase(
                    $accounting_document_article->getPrice(),
                    $accounting_document_article->getDiscount(),
                    $accounting_document_articles_data[$index]['article']['quantity']
                ) * $kurs;
            $accounting_document_articles_data[$index]['article']['tax'] = $accounting_document_article->getTax();
            $accounting_document_articles_data[$index]['article']['tax_amount_rsd'] = $this->entityManager
                ->getRepository(AccountingDocumentArticle::class)
                ->getTaxAmount(
                    $accounting_document_articles_data[$index]['article']['tax_base_rsd'],
                    $accounting_document_articles_data[$index]['article']['tax']
                );
            $accounting_document_articles_data[$index]['article']['sub_total_rsd'] = $this->entityManager
                ->getRepository(AccountingDocumentArticle::class)
                ->getSubTotal(
                    $accounting_document_articles_data[$index]['article']['tax_base_rsd'],
                    $accounting_document_articles_data[$index]['article']['tax_amount_rsd']
                );
        }
        return $accounting_document_articles_data;
    }

    /**
     * @param int $pidb_id
     *
     * @return float
     */
    private function getAccountingDocumentTotalTaxBaseRSD(int $pidb_id): float
    {
        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($pidb_id);
        $total_tax_base_rsd = 0;
        foreach ($accounting_document_articles_data as $index => $accounting_document_article) {
            $total_tax_base_rsd += $accounting_document_article['article']['tax_base_rsd'];
        }
        return $total_tax_base_rsd;
    }

    /**
     * @param int $pidb_id
     *
     * @return int|mixed
     */
    private function getAccountingDocumentTotalTaxAmountRSD(int $pidb_id)
    {
        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($pidb_id);
        $total_tax_amount_rsd = 0;
        foreach ($accounting_document_articles_data as $index => $accounting_document_article) {
            $total_tax_amount_rsd += $accounting_document_article['article']['tax_amount_rsd'];
        }
        return $total_tax_amount_rsd;
    }

    /**
     * Displays the daily cash register view with all cash transactions and the current cash saldo.
     *
     * This method checks user authentication, retrieves all daily cash transactions, fetches the related accounting
     * documents for each transaction, calculates the daily cash saldo, and prepares all relevant data for the cash
     * register view.
     *
     * @return Response
     *   Renders the cash register view with transaction and saldo data.
     */
    #[Route('/documents/cash-register', name: 'cash_register')]
    public function cashRegister(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $daily_transactions = $this->entityManager
            ->getRepository(Payment::class)->getDailyCashTransactions();
        $daily_transactions_with_accounting_document = [];
        foreach ($daily_transactions as $index => $daily_transaction) {
            $daily_transactions_with_accounting_document[$index] = [
                'transaction' => $daily_transaction,
                'accounting_document' => $this->entityManager
                    ->getRepository(AccountingDocument::class)
                    ->getAccountingDocumentByTransaction($daily_transaction->getId()),
            ];
        }

        $daily_cash_saldo = $this->entityManager->getRepository(Payment::class)->getDailyCashSaldo();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'daily_transactions' => $daily_transactions_with_accounting_document,
            'daily_cash_saldo' => $daily_cash_saldo,
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => TRUE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('document/cash_register.html.twig', $data);
    }

    /**
     * Cache In and Out.
     *
     * @return Response
     */
    #[Route('/documents/cache-in-out', name: 'cache_in_out', methods: ['POST'])]
    public function cacheInOut(): Response
    {
        $payment_type_id = $_POST["type_id"];

        if ($payment_type_id == 5 && $this->entityManager->getRepository(Payment::class)->ifExistFirstCashInput()) {
            // @todo Create error message object or something like that, and
            // display it in the view.
            ?>
            <p>Već ste uneli početno stanje!</p>
            <a href="/documents/cash-register">Povratak na Kasu</a>
            <?php
            exit();
        }

        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);
        $payment_type = $this->entityManager->find(PaymentType::class, $payment_type_id);

        $date = date('Y-m-d H:i:s');

        $amount = htmlspecialchars($_POST["amount"]);
        // Correct decimal separator.
        $amount = str_replace(",", ".", $amount);

        if ($payment_type_id == 6 || $payment_type_id == 7) {
            $amount = "-".$amount;
        }

        $note = htmlspecialchars($_POST["note"]);

        // Create a new Payment.
        $newPayment = new Payment();

        $newPayment->setType($payment_type);

        $newPayment->setAmount($amount);
        $newPayment->setDate(new \DateTime($date));
        $newPayment->setNote($note);
        $newPayment->setCreatedAt(new \DateTime("now"));
        $newPayment->setCreatedByUser($user);

        $this->entityManager->persist($newPayment);
        $this->entityManager->flush();

        return $this->redirectToRoute('cash_register');
    }

    /**
     * Print Daily Cache Report.
     *
     * @return Response
     */
    #[Route('/documents/print-daily-cache-report', name: 'print_daily_cache_report', methods: ['GET'])]
    public function printDailyCacheReport(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);

        $date = date('Y-m-d');
        $daily_transactions = $this->entityManager->getRepository(Payment::class)->getDailyCashTransactions($date);

        $daily_transactions_with_accounting_document = [];
        foreach ($daily_transactions as $index => $daily_transaction) {
            $daily_transactions_with_accounting_document[$index] = [
                'transaction' => $daily_transaction,
                'accounting_document' => $this->entityManager
                    ->getRepository(AccountingDocument::class)
                    ->getAccountingDocumentByTransaction($daily_transaction->getId()),
            ];
        }

        $daily_cash_saldo = $this->entityManager->getRepository('\App\Entity\Payment')->getDailyCashSaldo($date);

        $data = [
            'company_info' => $company_info,
            'daily_transactions' => $daily_transactions_with_accounting_document,
            'daily_cash_saldo' => $daily_cash_saldo,
            'date' => $date,
        ];

        // Render HTML content from a Twig template (or similar)
        $html = $this->renderView('document/print_daily_cache_report.html.twig', $data);

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Dokument');
        $pdf->SetSubject($company_info['name']);
        $pdf->SetKeywords($company_info['name'] . ', PDF, dnevni izveštaj');

        // Remove default header/footer.
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set default monospaced font.
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins.
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // Set auto page breaks.
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor.
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set font.
        $pdf->SetFont('dejavusans', '', 10);

        // Add a page.
        $pdf->AddPage();

        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Reset pointer to the last page.
        $pdf->lastPage();

        $filename = '__dnevni_izvestaj_' . $date . '.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

}
