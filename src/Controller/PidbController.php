<?php

namespace App\Controller;

use App\Core\BaseController;
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
use TCPDF;

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
            'app_version' => APP_VERSION,
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'search' => $search,
            'tools_menu' => [
                'pidb' => FALSE,
            ],
            'proformas' => $this->entityManager->getRepository(AccountingDocument::class)->getLast(1, 0, 10),
            'delivery_notes' => $this->entityManager->getRepository(AccountingDocument::class)->getLast(2, 0, 10),
            'return_receipts' => $this->entityManager->getRepository(AccountingDocument::class)->getLast(4, 0, 10),
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('pidb/index.html.twig', $data);
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

        if (isset($_GET['project_id'])) {
            $project_id = htmlspecialchars($_GET['project_id']);
        }

        if (isset($_GET['client_id'])) {
            $client_id = htmlspecialchars($_GET['client_id']);
            $client = $this->entityManager->find(Client::class, $client_id);
        }

        $clients_list = $this->entityManager
            ->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
            'client' => $client ?? NULL,
            'clients_list' => $clients_list,
          ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('pidb/add.html.twig', $data);
    }

    /**
     * Add new Accounting Document.
     *
     * @return void
     */
    public function add(): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

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
        $pidb_data = $this->entityManager->find(AccountingDocument::class, $pidb_id);

        $client_id = $pidb_data->getClient()->getId();
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $all_articles = $this->entityManager->getRepository(Article::class)->findAll();

        $pidb_type_id = $pidb_data->getType()->getId();

        [$pidb_tupe, $pidb_tag, $pidb_style] = match ($pidb_type_id) {
            1 => ["Predračun", "P_", 'info'],
            2 => ["Otpremnica", "O_", 'secondary'],
            4 => ["Povratnica", "POV_", 'warning'],
            default => ["_", "_", 'default'],
        };

        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($pidb_id);

        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($pidb_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($pidb_id);

        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;

        $previous = $this->entityManager
          ->getRepository(AccountingDocument::class)->getPrevious($pidb_id, $pidb_data->getType()->getId());

        $next = $this->entityManager
          ->getRepository(AccountingDocument::class)->getNext($pidb_id, $pidb_data->getType()->getId());

        $avans_eur = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($pidb_id);
        $avans_rsd = $avans_eur * $kurs;
        $income_eur = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($pidb_id);
        $income_rsd = $income_eur * $kurs;
        $remaining_rsd = $total_rsd - $avans_rsd - $income_rsd;
        $remaining_eur = ($total_rsd / $kurs) - $avans_eur - $income_eur;

        $data = [
            'app_version' => APP_VERSION,
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
            'pidb_type_id' => $pidb_type_id,
            'pidb_type' => $pidb_tupe,
            'pidb_tag' => $pidb_tag,
            'pidb_style' => $pidb_style,
            'client' => $client,
            'all_articles' => $all_articles,
            'kurs' => $kurs,
            'accounting_document_articles_data' => $accounting_document_articles_data,
            'tools_menu' => [
                'pidb' => TRUE,
                'view' => TRUE,
                'edit' => TRUE,
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
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('pidb/view.html.twig', $data);
    }

    /**
     * @param int $pidb_id
     *
     * @return void
     */
    public function editForm(int $pidb_id): void
    {
        $pidb_data = $this->entityManager->find(AccountingDocument::class, $pidb_id);

        // get client data from $pidb_data
        $client_id = $pidb_data->getClient()->getId();
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $all_articles = $this->entityManager->getRepository(Article::class)->findAll();

        $pidb_type_id = $pidb_data->getType()->getId();
        [$pidb_tupe, $pidb_tag, $pidb_style] = match ($pidb_type_id) {
            1 => ["Predračun", "P_", 'info'],
            2 => ["Otpremnica", "O_", 'secondary'],
            4 => ["Povratnica", "POV_", 'warning'],
            default => ["_", "_", 'default'],
        };

        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($pidb_id);

        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($pidb_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($pidb_id);

        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;

        $previous = $this->entityManager
            ->getRepository(AccountingDocument::class)->getPrevious($pidb_id, $pidb_data->getType()->getId());

        $next = $this->entityManager
            ->getRepository(AccountingDocument::class)->getNext($pidb_id, $pidb_data->getType()->getId());

        $avans_eur = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($pidb_id);
        $avans_rsd = $avans_eur * $kurs;
        $income_eur = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($pidb_id);
        $income_rsd = $income_eur * $kurs;
        $remaining_rsd = $total_rsd - $avans_rsd - $income_rsd;
        $remaining_eur = ($total_rsd / $kurs) - $avans_eur - $income_eur;

        $clients_list = $this->entityManager->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        $data = [
            'app_version' => APP_VERSION,
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
            'pidb_type_id' => $pidb_type_id,
            'pidb_type' => $pidb_tupe,
            'pidb_style' => $pidb_style,
            'all_articles' => $all_articles,
            'tools_menu' => [
                'pidb' => TRUE,
                'edit' => TRUE,
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
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('pidb/edit.html.twig', $data);
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
        $user = $this->entityManager->find(User::class, $this->user_id);

        $accounting_document = $this->entityManager->find(AccountingDocument::class, $pidb_id);

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
        if ($accounting_document = $this->entityManager->find(AccountingDocument::class, $acc_doc_id)) {

            // Check if AccountingDocument have Payments, where PaymentType is Income.
            if ($this->entityManager->getRepository(AccountingDocument::class)->getPaymentsByIncome($acc_doc_id)) {
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
        $accounting_document = $this->entityManager->find(AccountingDocument::class, $pidb_id);

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
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $pidb = $this->entityManager->find(AccountingDocument::class, $pidb_id);
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
            'pidb_id' => $pidb_id,
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

        // Render HTML content from a Twig template (or similar)
        ob_start();
        $this->render('pidb/print_accounting_document.html.twig', $data);
        $html = ob_get_clean();

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Dokument');
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

        // Close and output PDF document to browser.
        $pdf->Output($pidb_type . '_' . $pidb_ordinal_number_in_year . '-' . $pidb_date_month . '.pdf', 'I');
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
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $pidb = $this->entityManager->find(AccountingDocument::class, $pidb_id);
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

        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($pidb_id);

        $total_tax_base_rsd = $this->getAccountingDocumentTotalTaxBaseRSD($pidb_id);
        $total_tax_amount_rsd = $this->getAccountingDocumentTotalTaxAmountRSD($pidb_id);
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

        // Render HTML content from a Twig template (or similar)
        ob_start();
        $this->render('pidb/print_accounting_document_w.html.twig', $data);
        $html = ob_get_clean();

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Dokument');
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

        // Close and output PDF document to browser.
        $pdf->Output($pidb_type . '_' . $pidb_ordinal_number_in_year . '-' . $pidb_date_month . '.pdf', 'I');
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
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $pidb = $this->entityManager->find(AccountingDocument::class, $pidb_id);
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

        // Render HTML content from a Twig template (or similar)
        ob_start();
        $this->render('pidb/print_accounting_document_i.html.twig', $data);
        $html = ob_get_clean();

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Dokument');
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

        // Close and output PDF document to browser.
        $pdf->Output($pidb_type . '_' . $pidb_ordinal_number_in_year . '-' . $pidb_date_month . '.pdf', 'I');
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
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);

        $pidb = $this->entityManager->find(AccountingDocument::class, $pidb_id);
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

        // Render HTML content from a Twig template (or similar)
        ob_start();
        $this->render('pidb/print_accounting_document_iw.html.twig', $data);
        $html = ob_get_clean();

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Dokument');
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

        // Close and output PDF document to browser.
        $pdf->Output($pidb_type . '_' . $pidb_ordinal_number_in_year . '-' . $pidb_date_month . '.pdf', 'I');
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
        $user = $this->entityManager->find(User::class, $user_id);

        $proforma_id = $pidb_id;
        $proforma = $this->entityManager->find(AccountingDocument::class, $proforma_id);

        $ordinal_num_in_year = 0;

        // Save Proforma data to Dispatch.
        $newDispatch = new AccountingDocument();

        $newDispatch->setOrdinalNumInYear($ordinal_num_in_year);
        $newDispatch->setDate(new \DateTime("now"));
        $newDispatch->setIsArchived(0);

        $newDispatch->setType($this->entityManager->find(AccountingDocumentType::class, 2));
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
        $this->entityManager->getRepository(AccountingDocument::class)->setOrdinalNumInYear($last_accounting_document_id);

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
            $result = $queryBuilder ->executeStatement();
        }

        // Get articles from proforma.
        $proforma_articles = $this->entityManager->getRepository(AccountingDocument::class)->getArticles($proforma->getId());

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

            $this->entityManager->persist($newDispatchArticle);
            $this->entityManager->flush();

            // Get $proforma_article properies
            $proforma_article_properties =
              $this->entityManager
                ->getRepository(AccountingDocumentArticleProperty::class)
                ->findBy(array('accounting_document_article' => $proforma_article->getId()), []);

            // Save $proforma_article properies to $newDispatchArticle
            foreach ($proforma_article_properties as $article_property) {
                $newDispatchArticleProperty = new AccountingDocumentArticleProperty();

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
        $project = $this->entityManager->getRepository(AccountingDocument::class)->getProjectByAccountingDocument
        ($proforma->getId());

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

        $accountingDocumentArticle = $this->entityManager->find(AccountingDocumentArticle::class,
          $accounting_document__article_id);

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
            $property_value = str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));

            $accountingDocumentArticleProperty = $this->entityManager
                ->find(AccountingDocumentArticleProperty::class, $accounting_document__article__property->getId());

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
        $pidb_data = $this->entityManager->find(AccountingDocument::class, $pidb_id);
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

        $this->render('pidb/changeArticleInAccountingDocument.html.twig', $data);
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
                ->getRepository(AccountingDocumentArticle::class)
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
        $accounting_document__article = $this->entityManager->find(AccountingDocumentArticle::class, $pidb_article_id);

        // First remove properties from table v6__accounting_documents__articles__properties.
        if (
            $accounting_document__article__properties =
                $this->entityManager
                    ->getRepository(AccountingDocumentArticleProperty::class)
                    ->findBy(array('accounting_document_article' => $pidb_article_id), [])
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

        die('<script>location.href = "/pidb/' . $pidb_id . '" </script>');
    }

    public function transactions($limit = 10): void{
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
//            'app_version' => APP_VERSION,
//            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
//            'user_role_id' => $this->user_role_id,
            'transactions' => $transactions_with_accounting_document,
        ];

        $this->render('pidb/transactions.html.twig', $data);
    }

    /**
     * Document transactions.
     *
     * @param int $pidb_id
     *
     * @return void
     */
    public function transactionsByDocument(int $pidb_id): void
    {
        $pidb = $this->entityManager->find(AccountingDocument::class, $pidb_id);
        $client = $this->entityManager->find(Client::class,$pidb->getClient());
        $total = $this->entityManager
            ->getRepository(AccountingDocument::class)
            ->getTotalAmountsByAccountingDocument($pidb_id);

        // $transactions = $this->entityManager->getRepository('\App\Entity\AccountingDocument')->getLastTransactions(10);
        $transactions = $pidb->getPayments();

        $avans = $this->entityManager->getRepository(AccountingDocument::class)->getAvans($pidb_id);
        $income = $this->entityManager->getRepository(AccountingDocument::class)->getIncome($pidb_id);
        $total_income = $avans + $income;
        $saldo = $total - $total_income;

        $saldo_class = (round($total, 4) - round($total_income, 4)) <= 0
          ? "bg-success"
          : "bg-danger text-white";

        $data = [
            'app_version' => APP_VERSION,
            'page_title' => $this->page_title,
            'page' => $this->page,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $this->user_role_id,
            'pidb_id' => $pidb_id,
            'pidb' => $pidb,
            'client' => $client,
            'total' => $total,
            'transactions' => $transactions,
            'total_income' => $total_income,
            'saldo' => $saldo,
            'saldo_class' => $saldo_class,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('pidb/transactions_by_document.html.twig', $data);
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
        $pidb = $this->entityManager->find(AccountingDocument::class, $pidb_id);
        $transaction = $this->entityManager->find(Payment::class, $transaction_id);
        $client_id = $pidb->getClient()->getId();
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'pidb_id' => $pidb_id,
            'pidb' => $pidb,
            'transaction' => $transaction,
            'client' => $client,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('pidb/transaction.html.twig', $data);
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
        $transaction = $this->entityManager->find(Payment::class, $transaction_id);
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
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $preferences = $this->entityManager->find(Preferences::class, 1);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'preferences' => $preferences,
        ];

        $this->render('pidb/edit_preferences.html.twig', $data);
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

        $preferences = $this->entityManager->find(Preferences::class, 1);

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
        $user = $this->entityManager->find(User::class, $this->user_id);

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
        die('<script>location.href = "/pidb/'.$accounting_document_id.' " </script>');
    }

    /**
     * Search for clients.
     *
     * @param string $term
     *   Search term.
     *
     * @return void
     */
    public function search(string $term): void {
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
            'proformas' => $proformas,
            'proformas_archived' => $proformas_archived,
            'delivery_notes' => $delivery_notes,
            'delivery_notes_archived' => $delivery_notes_archived,
            'return_notes' => $return_notes,
            'return_notes_archived' => $return_notes_archived,
            'last_pidb' => $last_pidb,
            'page_title' => $this->page_title,
            'stylesheet' => '/../libraries/',
            'page' => $this->page,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('pidb/search.html.twig', $data);
    }

    private function getAccountingDocumentArticlesData($pidb_id)
    {
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $accounting_document_articles = $this->entityManager
            ->getRepository(AccountingDocument::class)->getArticles($pidb_id);
        $total_tax_base_rsd = 0;
        $total_tax_amount_rsd = 0;
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

    private function getAccountingDocumentTotalTaxBaseRSD(int $pidb_id): float
    {
        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($pidb_id);
        $total_tax_base_rsd = 0;
        foreach ($accounting_document_articles_data as $index => $accounting_document_article) {
            $total_tax_base_rsd += $accounting_document_article['article']['tax_base_rsd'];
        }
        return $total_tax_base_rsd;
    }

    private function getAccountingDocumentTotalTaxAmountRSD(int $pidb_id) {
        $accounting_document_articles_data = $this->getAccountingDocumentArticlesData($pidb_id);
        $total_tax_amount_rsd = 0;
        foreach ($accounting_document_articles_data as $index => $accounting_document_article) {
            $total_tax_amount_rsd += $accounting_document_article['article']['tax_amount_rsd'];
        }
        return $total_tax_amount_rsd;
    }

    public function cashRegister()
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
            'app_version' => APP_VERSION,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'daily_transactions' => $daily_transactions_with_accounting_document,
            'daily_cash_saldo' => $daily_cash_saldo,
            'tools_menu' => [
                'cash_register' => TRUE,
            ],
        ];

        $this->render('pidb/cash_register.html.twig', $data);
    }

    /**
     * Cache In and Out.
     *
     * @return void
     */
    public function cacheInOut(): void
    {
        $payment_type_id = $_POST["type_id"];

        if ($payment_type_id == 5 && $this->entityManager->getRepository(Payment::class)->ifExistFirstCashInput()) {
            // @todo Create error message object or something like that, and
            // display it in the view.
            ?>
            <p>Već ste uneli početno stanje!</p>
            <a href="/pidbs/cashRegister">Povratak na Kasu</a>
            <?php
            exit();
        }

        $user = $this->entityManager->find(User::class, $this->user_id);
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

      die('<script>location.href = "/pidbs/cashRegister" </script>');
    }

    public function printDailyCacheReport()
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
        ob_start();
        $this->render('pidb/print_daily_cache_report.html.twig', $data);
        $html = ob_get_clean();

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

        // Close and output PDF document to browser.
        $pdf->Output( 'dnevni izvestaj.pdf', 'I');
    }
}
