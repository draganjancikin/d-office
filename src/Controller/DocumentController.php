<?php

namespace App\Controller;

use App\Entity\AccountingDocument;
use App\Entity\AccountingDocumentArticle;
use App\Entity\AccountingDocumentArticleProperty;
use App\Entity\AccountingDocumentType as AccountingDocumentTypeEntity;
use App\Entity\Article;
use App\Entity\ArticleProperty;
use App\Entity\Client;
use App\Entity\CompanyInfo;
use App\Entity\Payment;
use App\Entity\PaymentType;
use App\Entity\Preferences;
use App\Entity\Project;
use App\Entity\User;
use App\Form\AccountingDocumentType;
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

    private EntityManagerInterface $em;
    private string $page = 'documents';
    private string $pageTitle = 'Dokumenti';
    protected string $stylesheet;

    /**
     * DocumentController constructor.
     *
     * @param EntityManagerInterface $em The entity manager for database interactions.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
    }

    /**
     * Displays the documents index page.
     *
     * Checks if the user is logged in, retrieves recent proformas, delivery notes, and return receipts, and renders
     * the documents index view.
     *
     * @return Response The HTTP response object.
     */
    #[Route('/documents', name: 'documents_index', methods: ['GET'])]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = $this->getDefaultData();
        $data += [
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
            'proformas' => $this->em->getRepository(AccountingDocument::class)->getLast(1, 0, 10),
            'delivery_notes' => $this->em->getRepository(AccountingDocument::class)->getLast(2, 0, 10),
            'return_receipts' => $this->em->getRepository(AccountingDocument::class)->getLast(4, 0, 10),
        ];

        return $this->render('document/index.html.twig', $data);
    }

    /**
     * Displays the form for creating a new accounting document.
     *
     * Checks if the user is logged in, retrieves optional client and project data, fetches the list of clients, and
     * renders the new document form view.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Response The HTTP response object.
     */
    #[Route('/documents/new', name: 'documents_new_form', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = new AccountingDocument();
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $document->setCreatedByUser($user);

        $form = $this->createForm(AccountingDocumentType::class);

        if ($request->query->get('client_id')) {
            $client = $this->em->find(Client::class, $request->query->get('client_id'));
            $form->get('client')->setData($client);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $document->setDate(new \DateTime("now"));
            $document->setOrdinalNumInYear(0);
            $document->setTitle($form->get('title')->getData());
            $document->setIsArchived(0);

            $document->setType($form->get('type')->getData());
            $document->setClient($form->get('client')->getData());

            $noteValue = $form->get('note')->getData();
            $document->setNote($noteValue !== null ? $noteValue : '');

            $document->setCreatedAt(new \DateTime("now"));
            $document->setModifiedAt(new \DateTime("now"));

            $this->em->persist($document);
            $this->em->flush();

            $this->em->getRepository(AccountingDocument::class)->setOrdinalNumInYear($document->getId());

            if ($request->query->get('project_id')) {
                $project = $this->em->find(Project::class, $request->query->get('project_id'));
                $project->getAccountingDocuments()->add($document);
                $this->em->flush();
            }

            return $this->redirectToRoute('document_edit_form', ['id' => $document->getId()]);
        }

        $data = $this->getDefaultData();
        $data += [
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
            'form' => $form->createView(),
        ];

        return $this->render('document/new.html.twig', $data);
    }

    /**
     * Displays the details of a specific accounting document.
     *
     * Checks if the user is logged in, retrieves the accounting document and its related data (client, articles,
     * totals, payments, etc.), prepares navigation and financial summary, and renders the document details view.
     *
     * @param int $id The ID of the accounting document to display.
     *
     * @return Response The HTTP response object.
     */
    #[Route('/documents/{id}', name: 'document_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = $this->em->find(AccountingDocument::class, $id);

        $clientId = $document->getClient()->getId();
        $client = $this->em->getRepository(Client::class)->getClientData($clientId);

        $allArticles = $this->em->getRepository(Article::class)->findAll();

        [$documentType, $documentTag, $documentStyle] = $this->getDocumentTypeData($document->getType()->getId());

        $preferences = $this->em->find(Preferences::class, 1);
        $exchangeRate = $preferences->getKurs();

        $accountingDocumentArticlesData = $this->getAccountingDocumentArticlesData($id);

        $totalTaxBaseRsd = $this->getAccountingDocumentTotalTaxBaseRSD($id);
        $totalTaxAmountRsd = $this->getAccountingDocumentTotalTaxAmountRSD($id);

        $totalRsd = $totalTaxBaseRsd + $totalTaxAmountRsd;

        $previous = $this->em->getRepository(AccountingDocument::class)->getPrevious($id, $document->getType()->getId());

        $next = $this->em->getRepository(AccountingDocument::class)->getNext($id, $document->getType()->getId());

        $advancePaymentEur = $this->em->getRepository(AccountingDocument::class)->getAvans($id);
        $advancePaymentRsd = $advancePaymentEur * $exchangeRate;
        $incomeEur = $this->em->getRepository(AccountingDocument::class)->getIncome($id);
        $incomeRsd = $incomeEur * $exchangeRate;
        $remainingRsd = $totalRsd - $advancePaymentRsd - $incomeRsd;
        $remainingEur = ($totalRsd / $exchangeRate) - $advancePaymentEur - $incomeEur;

        $data = $this->getDefaultData();

        $data += [
            'tools_menu' => [
                'document' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
                'cash_register' => TRUE,
            ],
            'document' => $document,
            'document_type' => $documentType,
            'document_tag' => $documentTag,
            'document_style' => $documentStyle,
            'client' => $client,
            'all_articles' => $allArticles,
            'accounting_document_articles_data' => $accountingDocumentArticlesData,
            'previous' => $previous,
            'next' => $next,
            'advance_payment_eur' => $advancePaymentEur,
            'advance_payment_rsd' => $advancePaymentRsd,
            'income_eur' => $incomeEur,
            'income_rsd' => $incomeRsd,
            'total_tax_base_rsd' => $totalTaxBaseRsd,
            'total_tax_amount_rsd' => $totalTaxAmountRsd,
            'total_rsd' => $totalRsd,
            'total_eur' => $totalRsd / $exchangeRate,
            'remaining_rsd' => $remainingRsd,
            'remaining_eur' => $remainingEur,
        ];

        return $this->render('document/view.html.twig', $data);
    }

    /**
     * Displays the edit form for a specific accounting document.
     *
     * Checks if the user is logged in, retrieves the accounting document and its related data (client, articles,
     * totals, payments, etc.), prepares navigation, financial summary, and a list of clients, and renders the document edit view.
     *
     * @param int $id The ID of the accounting document to edit.
     *
     * @return Response The HTTP response object.
     */
    #[Route('/documents/{id}/edit', name: 'document_edit_form', methods: ['GET'])]
    public function edit(int $id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = $this->em->find(AccountingDocument::class, $id);

        $clientId = $document->getClient()->getId();
        $client = $this->em->getRepository(Client::class)->getClientData($clientId);

        $allArticles = $this->em->getRepository(Article::class)->findAll();

        [$documentType, $documentTag, $documentStyle] = $this->getDocumentTypeData($document->getType()->getId());

        $preferences = $this->em->find(Preferences::class, 1);
        $exchangeRate = $preferences->getKurs();

        $accountingDocumentArticlesData = $this->getAccountingDocumentArticlesData($id);

        $totalTaxBaseRsd = $this->getAccountingDocumentTotalTaxBaseRSD($id);
        $totalTaxAmountRsd = $this->getAccountingDocumentTotalTaxAmountRSD($id);

        $totalRsd = $totalTaxBaseRsd + $totalTaxAmountRsd;

        $previous = $this->em->getRepository(AccountingDocument::class)->getPrevious($id, $document->getType()->getId());

        $next = $this->em->getRepository(AccountingDocument::class)->getNext($id, $document->getType()->getId());

        $advancePaymentEur = $this->em->getRepository(AccountingDocument::class)->getAvans($id);
        $advancePaymentRsd = $advancePaymentEur * $exchangeRate;
        $incomeEur = $this->em->getRepository(AccountingDocument::class)->getIncome($id);
        $incomeRsd = $incomeEur * $exchangeRate;
        $remainingRsd = $totalRsd - $advancePaymentRsd - $incomeRsd;
        $remainingEur = ($totalRsd / $exchangeRate) - $advancePaymentEur - $incomeEur;

        $clients_list = $this->em->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        $data = $this->getDefaultData();
        $data += [
            'client' => $client,
            'document' => $document,
            'document_type' => $documentType,
            'document_tag' => $documentTag,
            'document_style' => $documentStyle,
            'all_articles' => $allArticles,
            'tools_menu' => [
                'document' => TRUE,
                'edit' => TRUE,
                'view' => FALSE,
                'cash_register' => TRUE,
            ],
            'accounting_document_articles_data' => $accountingDocumentArticlesData,
            'previous' => $previous,
            'next' => $next,
            'advance_payment_eur' => $advancePaymentEur,
            'advance_payment_rsd' => $advancePaymentEur * $exchangeRate,
            'income_eur' => $incomeEur,
            'income_rsd' => $incomeRsd,
            'total_tax_base_rsd' => $totalTaxBaseRsd,
            'total_tax_amount_rsd' => $totalTaxAmountRsd,
            'total_rsd' => $totalRsd,
            'total_eur' => $totalRsd / $exchangeRate,
            'remaining_rsd' => $remainingRsd,
            'remaining_eur' => $remainingEur,
            'clients_list' => $clients_list,
        ];

        return $this->render('document/document_edit.html.twig', $data);
    }

    /**
     * Updates an existing accounting document with new data from the edit form.
     *
     * Checks user session, retrieves the document and user, processes POST data (title, client, archive status, note),
     * updates the document fields, saves changes, and redirects to the document details page.
     *
     * @param int $id The ID of the accounting document to update.
     *
     * @return Response Redirects to the document details page after updating.
     */
    #[Route('/documents/{id}/update', name: 'document_update', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function update(int $id): Response
    {
        session_start();
        $user = $this->em->find(User::class, $_SESSION['user_id']);

        $document = $this->em->find(AccountingDocument::class, $id);

        $title = htmlspecialchars($_POST["title"]);

        $clientId = htmlspecialchars($_POST["client_id"]);
        $client = $this->em->find(Client::class, $clientId);

        $isArchived = htmlspecialchars($_POST["archived"]);
        $note = htmlspecialchars($_POST["note"]);

        $document->setTitle($title);
        $document->setClient($client);
        $document->setIsArchived($isArchived);
        $document->setNote($note);
        $document->setModifiedByUser($user);
        $document->setModifiedAt(new \DateTime("now"));

        $this->em->flush();

        return $this->redirectToRoute('document_edit_form', ['id' => $id]);
    }

    /**
     * Deletes an accounting document if there are no related payments or advances.
     *
     * This method checks for related payments (income) and advances. If any exist, it prevents deletion and displays a
     * message. If the document has a parent, it reassigns payments to the parent and unarchives it. It then removes all
     * related articles and their properties before deleting the document itself. Finally, it redirects to the search
     * page.
     *
     * @param int $id The ID of the accounting document to delete.
     *
     * @return Response Redirects to the search page after deletion or if deletion is not possible.
     *
     * @throws \Doctrine\DBAL\Exception If a database error occurs during the deletion process.
     */
    #[Route('/documents/{id}/delete', name: 'document_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        // Check if exist AccountingDocument.
        if ($document = $this->em->find(AccountingDocument::class, $id)) {

            // Check if AccountingDocument have Payments, where PaymentType is Income.
            if ($this->em->getRepository(AccountingDocument::class)->getPaymentsByIncome($id)) {
                echo "Brisanje dokumenta nije moguće jer postoje uplate vezane za ovaj dokument!";
                echo "<br><a href='/documents/$id/transactions'>Idi na transakcije dokumenta >></a>";
                exit();
            }
            else {
                // Parent Accounting Document update.
                // Check if parent exist.
                if ($parent = $document->getParent()) {

                    // Update Payments.
                    // Get all AccountingDocument Payments.
                    $payments = $document->getPayments();

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
                            ->set('accountingdocument_id', ':parent')
                            ->where('payment_id = :payment')
                            ->setParameter('parent', $parent->getId())
                            ->setParameter('payment', $payment->getId());
                        $queryBuilder ->executeStatement();
                    }

                    // Set Parent to active
                    $parent->setIsArchived(0);
                    $this->em->flush();
                }
                else {
                    if ($this->em->getRepository(AccountingDocument::class)->getPaymentsByAvans($id) ){
                        echo "Brisanje dokumenta nije moguće jer postoje avansi vezani za ovaj dokument!<br>";
                        echo "<a href='/documents/$id/transactions'>Idi na transakcije dokumenta >></a>";
                        exit();
                    }
                }
            }

            // Check if exist Articles in AccountingDocument.
            if (
                $documentArticles = $this->em->getRepository(AccountingDocumentArticle::class)->findBy(['accounting_document' => $id], [])
            ) {

                // Loop through all articles.
                foreach ($documentArticles as $documentArticle) {

                    // Check if exist Properties in AccontingDocument Article.
                    if (
                        $documentArticleProperties =
                            $this->em->getRepository(AccountingDocumentArticleProperty::class)
                                ->findBy(['accounting_document_article' => $documentArticle])
                    ) {
                        // Remove AccountingDocument Article Properties.
                        foreach ($documentArticleProperties as $documentArticleProperty) {
                            $this->em->remove($documentArticleProperty);
                            $this->em->flush();
                        }
                    }

                    // Delete Article from AccountingDocument.
                    $this->em->remove($documentArticle);
                    $this->em->flush();
                }
            }

            // Delete AccountingDocument.
            $this->em->remove($document);
            $this->em->flush();
        }
        return $this->redirectToRoute('documents_search', ['term' => '']);
    }

    /**
     * Adds a new article to the specified accounting document.
     *
     * Retrieves the document and article, processes form data (pieces, note, etc.), creates and persists a new
     * AccountingDocumentArticle entity, copies article properties, and redirects to the document edit form.
     *
     * @param int $id The ID of the accounting document to which the article will be added.
     *
     * @return Response Redirects to the document edit form.
     */
    #[Route('/documents/{id}/articles/add', name: 'document_add_article', methods: ['POST'])]
    public function addArticle(int $id): Response
    {
        $document = $this->em->find(AccountingDocument::class, $id);

        $articleId = htmlspecialchars($_POST["article_id"]);
        $article = $this->em->find(Article::class, $articleId);

        $price = $article->getPrice();
        $discount = 0;
        $weight = $article->getWeight();

        $pieces = 0;
        if (isset($_POST["pieces"]) && is_numeric($_POST["pieces"])) {
            $pieces = htmlspecialchars($_POST["pieces"]);
        }

        $preferences = $this->em->find(Preferences::class, 1);
        $tax = $preferences->getTax();

        $note = htmlspecialchars($_POST["note"]);

        $newDocumentArticle = new AccountingDocumentArticle();

        $newDocumentArticle->setAccountingDocument($document);
        $newDocumentArticle->setArticle($article);
        $newDocumentArticle->setPieces($pieces);
        $newDocumentArticle->setPrice($price);
        $newDocumentArticle->setDiscount($discount);
        $newDocumentArticle->setTax($tax);
        $newDocumentArticle->setWeight($weight);
        $newDocumentArticle->setNote($note);

        $this->em->persist($newDocumentArticle);
        $this->em->flush();

        // Last inserted Accounting Document Article.
        // $last__accounting_document__article_id = $newAccountingDocumentArticle->getId();

        // Insert Article properties in table v6__accounting_documents__articles__properties.
        $articleProperties = $this->em->getRepository(ArticleProperty::class)->getArticleProperties($article->getId());
        foreach ($articleProperties as $articleProperty) {
            // Insert to table v6__accounting_documents__articles__properties.
            $newDocumentArticleProperty = new AccountingDocumentArticleProperty();

            $newDocumentArticleProperty->setAccountingDocumentArticle($newDocumentArticle);
            $newDocumentArticleProperty->setProperty($articleProperty->getProperty());
            $newDocumentArticleProperty->setQuantity(0);

            $this->em->persist($newDocumentArticleProperty);
            $this->em->flush();
        }
        return $this->redirectToRoute('document_edit_form', ['id' => $id]);
    }

    /**
     * Generates and outputs a PDF for the specified accounting document.
     *
     * This method is mapped to the 'document_print' route. It fetches all necessary data for the document, renders the
     * content using a Twig template, generates a PDF using TCPDF, and returns it as an inline browser response. The
     * generated PDF filename initially includes leading double underscores, which are removed before sending the file
     * to the client.
     *
     * @param int $id The ID of the accounting document to print.
     *
     * @return Response The PDF file as a Symfony inline response.
     */
    #[Route('/documents/{id}/print', name: 'document_print', methods: ['GET'])]
    public function printAccountingDocument(int $id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = $this->em->find(AccountingDocument::class, $id);
        $documentType = $document->getType()->getName();

        $documentOrdinalNumberInYear = str_pad($document->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT);
        $documentDateMonth = $document->getDate()->format('m');

        $client = $this->em->getRepository(Client::class)->getClientData($document->getClient()->getId());

        $companyInfo = $this->em->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $preferences = $this->em->find(Preferences::class, 1);
        $exchangeRate = $preferences->getKurs();

        $documentArticlesSata = $this->getAccountingDocumentArticlesData($id);

        $totalTaxBaseRsd = $this->getAccountingDocumentTotalTaxBaseRSD($id);
        $totalTaxAmountRsd = $this->getAccountingDocumentTotalTaxAmountRSD($id);
        $totalRsd = $totalTaxBaseRsd + $totalTaxAmountRsd;

        $advancePaymentEur = $this->em->getRepository(AccountingDocument::class)->getAvans($id);
        $advancePaymentRsd = $advancePaymentEur * $exchangeRate;
        $incomeEur = $this->em->getRepository(AccountingDocument::class)->getIncome($id);
        $incomeRsd = $incomeEur * $exchangeRate;
        $remainingRsd = $totalRsd - $advancePaymentRsd - $incomeRsd;
        $remainingEur = ($totalRsd / $exchangeRate) - $advancePaymentEur - $incomeEur;

        $data = [
            'document' => $document,
            'company_info' => $companyInfo,
            'client' => $client,
            'accounting_document_articles_data' => $documentArticlesSata,
            'total_tax_base_rsd' => $totalTaxBaseRsd,
            'total_tax_amount_rsd' => $totalTaxAmountRsd,
            'total_rsd' => $totalRsd,
            'advance_payment_rsd' => $advancePaymentRsd,
            'remaining_rsd' => $remainingRsd,
            'remaining_eur' => $remainingEur,
        ];

      // Render HTML content from a Twig template
      $html = $this->renderView('document/print_accounting_document.html.twig', $data);

      require_once '../config/packages/tcpdf_include.php';

      // Create a new TCPDF object / PDF document
      $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

      // Set document information
      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetAuthor($companyInfo['name']);
      $pdf->SetTitle($companyInfo['name'] . ' - '. $documentType);
      $pdf->SetSubject($companyInfo['name']);
      $pdf->SetKeywords($companyInfo['name'] . ', PDF, Proforma, Invoice');

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
      $filename = '__' . $documentType . '_' . $documentOrdinalNumberInYear . '-' . $documentDateMonth . '.pdf';
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
     * @param int $id The ID of the accounting document to print.
     *
     * @return Response The PDF file as a Symfony inline response.
     */
    #[Route('/documents/{id}/print-w', name: 'document_print_w', methods: ['GET'])]
    public function printAccountingDocumentW(int $id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = $this->em->find(AccountingDocument::class, $id);
        $documentType = $document->getType()->getName();

        $documentOrdinalNumberInYear = str_pad($document->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT);
        $documentDateMonth = $document->getDate()->format('m');
        $companyInfo = $this->em->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $preferences = $this->em->find(Preferences::class, 1);
        $exchangeRate = $preferences->getKurs();
        $documentArticlesData = $this->getAccountingDocumentArticlesData($id);
        $totalTaxBaseRsd = $this->getAccountingDocumentTotalTaxBaseRSD($id);
        $totalTaxAmountRsd = $this->getAccountingDocumentTotalTaxAmountRSD($id);
        $totalRsd = $totalTaxBaseRsd + $totalTaxAmountRsd;
        $data = [
            'company_info' => $companyInfo,
            'document' => $document,
            'accounting_document_articles_data' => $documentArticlesData,
            'total_tax_base_rsd' => $totalTaxBaseRsd,
            'total_tax_amount_rsd' => $totalTaxAmountRsd,
            'total_rsd' => $totalRsd,
            'total_eur' => $totalRsd / $exchangeRate,
        ];
        $html = $this->renderView('document/print_accounting_document_w.html.twig', $data);
        require_once '../config/packages/tcpdf_include.php';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($companyInfo['name']);
        $pdf->SetTitle($companyInfo['name'] . ' - Dokument');
        $pdf->SetSubject($companyInfo['name']);
        $pdf->SetKeywords($companyInfo['name'] . ', PDF, Proforma, Invoice');
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
        $filename = '__' . $documentType . '_' . $documentOrdinalNumberInYear . '-' . $documentDateMonth . '.pdf';
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
     * @param int $id
     *
     * @return Response
     */
    #[Route('/documents/{id}/print-i', name: 'document_print_i', methods: ['GET'])]
    public function printAccountingDocumentI(int $id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = $this->em->find(AccountingDocument::class, $id);
        $documentType = $document->getType()->getName();

        $documentOrdinalNumberInYear = str_pad($document->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT);
        $documentDateMonth = $document->getDate()->format('m');
        $client = $this->em->getRepository(Client::class)->getClientData($document->getClient()->getId());
        $companyInfo = $this->em->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $preferences = $this->em->find(Preferences::class, 1);
        $exchangeRate = $preferences->getKurs();
        $documentArticlesData = $this->getAccountingDocumentArticlesData($id);
        $totalTaxBaseRsd = $this->getAccountingDocumentTotalTaxBaseRSD($id);
        $totalTaxAmountRsd = $this->getAccountingDocumentTotalTaxAmountRSD($id);
        $totalRsd = $totalTaxBaseRsd + $totalTaxAmountRsd;
        $advancePaymentEur = $this->em->getRepository(AccountingDocument::class)->getAvans($id);
        $advancePaymentRsd = $advancePaymentEur * $exchangeRate;
        $incomeEur = $this->em->getRepository(AccountingDocument::class)->getIncome($id);
        $incomeRsd = $incomeEur * $exchangeRate;
        $remainingRsd = $totalRsd - $advancePaymentRsd - $incomeRsd;
        $remainingEur = ($totalRsd / $exchangeRate) - $advancePaymentEur - $incomeEur;
        $data = [
            'company_info' => $companyInfo,
            'document' => $document,
            'client' => $client,
            'accounting_document_articles_data' => $documentArticlesData,
            'total_tax_base_rsd' => $totalTaxBaseRsd,
            'total_tax_amount_rsd' => $totalTaxAmountRsd,
            'total_rsd' => $totalRsd,
            'total_eur' => $totalRsd / $exchangeRate,
            'advance_payment_rsd' => $advancePaymentRsd,
            'income_rsd' => $incomeRsd,
            'remaining_rsd' => $remainingRsd,
            'remaining_eur' => $remainingEur,
        ];
        $html = $this->renderView('document/print_accounting_document_i.html.twig', $data);
        require_once '../config/packages/tcpdf_include.php';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($companyInfo['name']);
        $pdf->SetTitle($companyInfo['name'] . ' - Dokument');
        $pdf->SetSubject($companyInfo['name']);
        $pdf->SetKeywords($companyInfo['name'] . ', PDF, Proforma, Invoice');
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
        $filename = '__' . $documentType . '_' . $documentOrdinalNumberInYear . '-' . $documentDateMonth . '.pdf';
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
     * @param int $id
     * @return Response
     */
    #[Route('/documents/{id}/print-iw', name: 'document_print_iw', methods: ['GET'])]
    public function printAccountingDocumentIW(int $id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $companyInfo = $this->em->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $document = $this->em->find(AccountingDocument::class, $id);
        $documentType = $document->getType()->getName();

        $documentOrdinalNumberInYear = str_pad($document->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT);
        $documentDateMonth = $document->getDate()->format('m');
        $preferences = $this->em->find(Preferences::class, 1);
        $exchangeRate = $preferences->getKurs();
        $documentArticlesData = $this->getAccountingDocumentArticlesData($id);
        $totalTaxBaseRsd = $this->getAccountingDocumentTotalTaxBaseRSD($id);
        $totalTaxAmountRsd = $this->getAccountingDocumentTotalTaxAmountRSD($id);
        $totalRsd = $totalTaxBaseRsd + $totalTaxAmountRsd;
        $advancePaymentEur = $this->em->getRepository(AccountingDocument::class)->getAvans($id);
        $advancePaymentRsd = $advancePaymentEur * $exchangeRate;
        $incomeEur = $this->em->getRepository(AccountingDocument::class)->getIncome($id);
        $incomeRsd = $incomeEur * $exchangeRate;
        $remainingRsd = $totalRsd - $advancePaymentRsd - $incomeRsd;
        $remainingEur = ($totalRsd / $exchangeRate) - $advancePaymentEur - $incomeEur;
        $data = [
            'company_info' => $companyInfo,
            'document' => $document,
            'accounting_document_articles_data' => $documentArticlesData,
            'total_tax_base_rsd' => $totalTaxBaseRsd,
            'total_tax_amount_rsd' => $totalTaxAmountRsd,
            'total_rsd' => $totalRsd,
            'total_eur' => $totalRsd / $exchangeRate,
            'advance_payment_rsd' => $advancePaymentRsd,
            'income_rsd' => $incomeRsd,
            'remaining_rsd' => $remainingRsd,
            'remaining_eur' => $remainingEur,
        ];
        $html = $this->renderView('document/print_accounting_document_iw.html.twig', $data);
        require_once '../config/packages/tcpdf_include.php';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($companyInfo['name']);
        $pdf->SetTitle($companyInfo['name'] . ' - Dokument');
        $pdf->SetSubject($companyInfo['name']);
        $pdf->SetKeywords($companyInfo['name'] . ', PDF, Proforma, Invoice');
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
        $filename = '__' . $documentType . '_' . $documentOrdinalNumberInYear . '-' . $documentDateMonth . '.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

    /**
     * Export Pro-form to Dispatch.
     *
     * @param int $id The ID of the pro-form document to be exported.
     *
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route(
        '/documents/{id}/export-proforma-to-dispatch',
        name: 'document_export_proforma_to_dispatch',
        methods: ['GET']
    )]
    public function exportProformaToDispatch(int $id): Response
    {
        session_start();
        $user = $this->em->find(User::class, $_SESSION['user_id']);

        $proforma = $this->em->find(AccountingDocument::class, $id);

        $ordinalNumberInYear = 0;

        // Save Proforma data to Dispatch.
        $newDispatch = new AccountingDocument();

        $newDispatch->setOrdinalNumInYear($ordinalNumberInYear);
        $newDispatch->setDate(new \DateTime("now"));
        $newDispatch->setIsArchived(0);

        $newDispatch->setType($this->em->find(AccountingDocumentTypeEntity::class, 2));
        $newDispatch->setTitle($proforma->getTitle());
        $newDispatch->setClient($proforma->getClient());
        $newDispatch->setParent($proforma);
        $newDispatch->setNote($proforma->getNote());

        $newDispatch->setCreatedAt(new \DateTime("now"));
        $newDispatch->setCreatedByUser($user);
        $newDispatch->setModifiedAt(new \DateTime("now"));

        $this->em->persist($newDispatch);
        $this->em->flush();

        // Get id of last AccountingDocument.
        $lastDocumentId = $newDispatch->getId();

        // Set Ordinal Number In Year.
        $this->em->getRepository(AccountingDocument::class)->setOrdinalNumInYear($lastDocumentId);

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
                ->setParameter('dispatch', $lastDocumentId)
                ->setParameter('payment', $payment->getId());
            $queryBuilder ->executeStatement();
        }

        // Get articles from pro-form.
        $proformaArticles = $this->em->getRepository(AccountingDocument::class)->getArticles($proforma->getId());

        // Save articles to dispatch.
        foreach ($proformaArticles as $proformaArticle) {
            $newDispatchArticle = new AccountingDocumentArticle();

            $newDispatchArticle->setAccountingDocument($newDispatch);
            $newDispatchArticle->setArticle($proformaArticle->getArticle());
            $newDispatchArticle->setPieces($proformaArticle->getPieces());
            $newDispatchArticle->setPrice($proformaArticle->getPrice());
            $newDispatchArticle->setDiscount($proformaArticle->getDiscount());
            $newDispatchArticle->setTax($proformaArticle->getTax());
            $newDispatchArticle->setWeight($proformaArticle->getWeight());
            $newDispatchArticle->setNote($proformaArticle->getNote());

            $this->em->persist($newDispatchArticle);
            $this->em->flush();

            // Get $proformaArticle properties.
            $proformaArticleProperties =
                $this->em->getRepository(AccountingDocumentArticleProperty::class)
                    ->findBy(array('accounting_document_article' => $proformaArticle->getId()), []);

            // Save $proforma_article properties to $newDispatchArticle.
            foreach ($proformaArticleProperties as $articleProperty) {
                $newDispatchArticleProperty = new AccountingDocumentArticleProperty();

                $newDispatchArticleProperty->setAccountingDocumentArticle($newDispatchArticle);
                $newDispatchArticleProperty->setProperty($articleProperty->getProperty());
                $newDispatchArticleProperty->setQuantity($articleProperty->getQuantity());
                $this->em->persist($newDispatchArticleProperty);
                $this->em->flush();
            }
        }

        // Set Pro-form to archive.
        $proforma->setIsArchived(1);
        $this->em->flush();

        // Check if pro-form belong to any Project
        $project = $this->em->getRepository(AccountingDocument::class)->getProjectByAccountingDocument
        ($proforma->getId());

        if ($project) {
            // Set same project to dispatch.
            $project->getAccountingDocuments()->add($newDispatch);
            $this->em->flush();
        }

        return $this->redirectToRoute('document_show', ['id' => $lastDocumentId]);
    }

    /**
     * Updates an article and its properties in a specific accounting document.
     *
     * Processes POST data for the article (note, pieces, price, discounts), updates the corresponding
     * AccountingDocumentArticle entity, then updates all related article properties with new values, and finally
     * redirects to the document details page.
     *
     * @param int $id The ID of the accounting document containing the article.
     * @param int $documentArticleId The ID of the article within the accounting document to update.
     *
     * @return Response Redirects to the document details page.
     */
    #[Route('/documents/{id}/articles/{documentArticleId}/edit', name: 'edit_article_in_accounting_document',
        methods: ['POST'])]
    public function editArticleInAccountingDocument(int $id, int $documentArticleId): Response
    {
        $note = htmlspecialchars($_POST["note"]);

        $pieces_1 = htmlspecialchars($_POST["pieces"]);
        $pieces = str_replace(",", ".", $pieces_1);

        $price_1 = htmlspecialchars($_POST["price"]);
        $price = str_replace(",", ".", $price_1);

        $discounts_1 = htmlspecialchars($_POST["discounts"]);
        $discounts = str_replace(",", ".", $discounts_1);

        $documentArticle = $this->em->find(AccountingDocumentArticle::class, $documentArticleId);

        $documentArticle->setNote($note);
        $documentArticle->setPieces($pieces);
        $documentArticle->setPrice($price);
        $documentArticle->setDiscount($discounts);
        $this->em->flush();

        // Properties update in table v6__accounting_documents__articles__properties.
        $documentArticleProperties = $this->em->getRepository(AccountingDocumentArticleProperty::class)
            ->findBy(['accounting_document_article' => $documentArticleId], []);
        foreach ($documentArticleProperties as $documentArticleProperty) {
            // Get property name from $accounting_document__article__property.
            $propertyName = $documentArticleProperty->getProperty()->getName();
            // Get property value from $_POST.
            $propertyValue = str_replace(".", "", htmlspecialchars($_POST["$propertyName"]));
            $propertyValue = str_replace(",", ".", htmlspecialchars($propertyValue));

            $documentArticleProperty = $this->em->find(AccountingDocumentArticleProperty::class, $documentArticleProperty->getId());

            $documentArticleProperty->setQuantity($propertyValue);
            $this->em->flush();
        }
        return $this->redirectToRoute('document_edit_form', ['id' => $id]);
    }

    /**
     * Displays the form to change an article in a specific accounting document.
     *
     * Checks if the user is logged in, retrieves the accounting document and the article to be changed, fetches all
     * available articles, determines the style based on document type, and renders the change article form view.
     *
     * @param int $id The ID of the accounting document containing the article.
     * @param int $article_id The ID of the article within the accounting document to change.
     *
     * @return Response Renders the change article form view.
     */
    #[Route('/documents/{id}/articles/{article_id}/change', name: 'change_article_in_document_form')]
    public function changeArticleInDocument(int $id, int $article_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = $this->em->find(AccountingDocument::class, $id);
        $articleData = $this->em->find(AccountingDocumentArticle::class, $article_id)->getArticle();
        $allArticles = $this->em->getRepository(Article::class)->findAll();

        switch ($document->getType()->getId()) {
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

        $data = $this->getDefaultData();
        $data += [
            'document' => $document,
            'document_article_id' => $article_id,
            'article_data' => $articleData,
            'all_articles' => $allArticles,
            'style' => $style,
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
     * @param int $id The ID of the accounting document containing the article.
     * @param int $article_id The ID of the article within the accounting document to update.
     *
     * @return Response Redirects to the document edit form.
     */
    #[Route('/documents/{id}/articles/{article_id}/update', name: 'update_article_in_accounting_document')]
    public function updateArticleInAccountingDocument(int $id, int $article_id): Response
    {
        $documentArticle = $this->em->find(AccountingDocumentArticle::class, $article_id);

        $oldArticle = $this->em->find(Article::class, $documentArticle->getArticle()->getId());
        $oldArticleId = $oldArticle->getId();

        $newArticleId = htmlspecialchars($_POST["article_id"]);
        $newArticle = $this->em->find(Article::class, $newArticleId);

        // First check if article_id in Accounting Document Article changed.
        if ($oldArticleId == $newArticleId){
            // Article not changed.
            echo "article not changed";
        }
        else {
            // Article changed.

            // Remove the Properties of the old Article. (from table v6__accounting_documents__articles__properties)
            if (
                $document__article__properties =
                    $this->em
                        ->getRepository(AccountingDocumentArticleProperty::class)
                        ->findBy(array('accounting_document_article' => $article_id), array())
            ) {
                foreach ($document__article__properties as $document__article__property) {
                    $accountingDocumentArticleProperty =
                        $this->em->find(AccountingDocumentArticleProperty::class, $document__article__property->getId());
                    $this->em->remove($accountingDocumentArticleProperty);
                    $this->em->flush();
                }
            }

            // change Article from old to new
            $documentArticle->setArticle($newArticle);
            $documentArticle->setPrice($newArticle->getPrice());
            $documentArticle->setNote("");
            $documentArticle->setPieces(1);
            $this->em->flush();

            // Insert Article properties in table v6__accounting_documents__articles__properties.
            $articleProperties = $this->em->getRepository(ArticleProperty::class)->getArticleProperties($newArticle->getId());
            foreach ($articleProperties as $articleProperty) {
                // Insert to table v6__accounting_documents__articles__properties.
                $newAccountingDocumentArticleProperty = new AccountingDocumentArticleProperty();

                $newAccountingDocumentArticleProperty->setAccountingDocumentArticle($documentArticle);
                $newAccountingDocumentArticleProperty->setProperty($articleProperty->getProperty());
                $newAccountingDocumentArticleProperty->setQuantity(0);

                $this->em->persist($newAccountingDocumentArticleProperty);
                $this->em->flush();
            }

        }
        return $this->redirectToRoute('document_edit_form', ['id' => $id]);
    }

    /**
     * Duplicates an article and its properties in a specific accounting document.
     *
     * Calls the repository method to duplicate the specified article (and its properties) within the accounting
     * document, then redirects to the document edit form.
     *
     * @param int $id The ID of the accounting document containing the article to duplicate.
     * @param int $article_id The ID of the article within the accounting document to duplicate.
     *
     * @return Response Redirects to the document edit form.
     */
    #[Route('/documents/{id}/articles/{article_id}/duplicate', name: 'duplicate_article_in_accounting_document')]
    public function duplicateArticleInAccountingDocument(int $id, int $article_id): Response
    {
        $this->em->getRepository(AccountingDocumentArticle::class)->duplicateArticleInAccountingDocument($article_id);

        return $this->redirectToRoute('document_edit_form', ['id' => $id]);
    }

    /**
     * Deletes an article and its properties from a specific accounting document.
     *
     * Removes all properties associated with the specified article from the join table, then removes the article itself
     * from the accounting document, and finally redirects to the document details page.
     *
     * @param int $id The ID of the accounting document containing the article to delete.
     * @param int $article_id The ID of the article within the accounting document to delete.
     *
     * @return Response Redirects to the document details page.
     */
    #[Route('/documents/{id}/articles/{article_id}/delete', name: 'delete_article_in_accounting_document')]
    public function deleteArticleInAccountingDocument(int $id, int $article_id): Response
    {
        $documentArticle = $this->em->find(AccountingDocumentArticle::class, $article_id);

        // First remove properties from table v6__accounting_documents__articles__properties.
        if (
            $documentArticleProperties =
                $this->em->getRepository(AccountingDocumentArticleProperty::class)
                    ->findBy(['accounting_document_article' => $article_id], [])
        ) {
            foreach ($documentArticleProperties as $document__article__property) {
                $accountingDocumentArticleProperty = $this->em
                    ->find(AccountingDocumentArticleProperty::class, $document__article__property->getId());
                $this->em->remove($accountingDocumentArticleProperty);
                $this->em->flush();
            }
        }

        // Second remove Article from table v6__accounting_documents__articles
        $this->em->remove($documentArticle);
        $this->em->flush();

        return $this->redirectToRoute('document_edit_form', ['id' => $id]);
    }

    /**
     * Displays a list of recent transactions (payments) with their associated accounting documents.
     *
     * This method checks user authentication, retrieves the latest transactions up to the specified limit, fetches the
     * related accounting documents for each transaction, and prepares all relevant data for the transactions view.
     *
     * @param int $limit The maximum number of transactions to display (default is 10).
     *
     * @return Response Renders the transactions view with transaction and document data.
     */
    #[Route('/documents/transactions', name: 'transactions_view', methods: ['GET'])]
    public function transactions(int $limit = 10): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $transactions = $this->em->getRepository(AccountingDocument::class)->getLastTransactions($limit);

        $transactionsWithAccountingDocument = [];
        foreach ($transactions as $index => $transaction) {
            $transactionsWithAccountingDocument[$index] = [
                'transaction' => $transaction,
                'accounting_document' => $this->em
                  ->getRepository(AccountingDocument::class)->getAccountingDocumentByTransaction($transaction->getId()),
            ];
        }

        $data = $this->getDefaultData();
        $data += [
            'transactions' => $transactionsWithAccountingDocument,
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
     * @param int $id The ID of the accounting document whose transactions are to be displayed.
     *
     * @return Response Renders the transactions view for the specified accounting document.
     */
    #[Route('/documents/{id}/transactions', name: 'document_transactions', methods: ['GET'])]
    public function transactionsByDocument(int $id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = $this->em->find(AccountingDocument::class, $id);
        $client = $this->em->find(Client::class,$document->getClient());
        $total = $this->em->getRepository(AccountingDocument::class)->getTotalAmountsByAccountingDocument($id);

        $transactions = $document->getPayments();

        $avans = $this->em->getRepository(AccountingDocument::class)->getAvans($id);
        $income = $this->em->getRepository(AccountingDocument::class)->getIncome($id);
        $totalIncome = $avans + $income;
        $saldo = $total - $totalIncome;

        $saldo_class = (round($total, 4) - round($totalIncome, 4)) <= 0
          ? "bg-success"
          : "bg-danger text-white";

        $data = $this->getDefaultData();
        $data += [
            'document' => $document,
            'client' => $client,
            'total' => $total,
            'transactions' => $transactions,
            'total_income' => $totalIncome,
            'saldo' => $saldo,
            'saldo_class' => $saldo_class,
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
     * @param int $id The ID of the accounting document containing the transaction.
     * @param int $transaction_id The ID of the transaction to edit.
     *
     * @return Response Renders the transaction edit form view.
     */
    #[Route('/documents/{id}/transactions/{transaction_id}/edit', name: 'transaction_edit_form', methods: ['GET'])]
    public function transactionEdit(int $id, int $transaction_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $document = $this->em->find(AccountingDocument::class, $id);
        $transaction = $this->em->find(Payment::class, $transaction_id);
        $clientId = $document->getClient()->getId();
        $client = $this->em->getRepository(Client::class)->getClientData($clientId);

        $data = $this->getDefaultData();
        $data += [
            'document' => $document,
            'transaction' => $transaction,
            'client' => $client,
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
     * @param int $id The ID of the accounting document containing the transaction.
     * @param int $transaction_id The ID of the transaction to update.
     *
     * @return Response Redirects to the document's transactions view after update.
     */
    #[Route('/documents/{id}/transactions/{transaction_id}/update', name: 'transaction_update', methods: ['POST'])]
    public function transactionUpdate(int $id, int $transaction_id): Response
    {
        $transaction = $this->em->find(Payment::class, $transaction_id);

        $typeId = htmlspecialchars($_POST["type_id"]);
        $type = $this->em->find(PaymentType::class, $typeId);

        $date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
        $amount_1 = htmlspecialchars($_POST["amount"]);
        $amount = str_replace(",", ".", $amount_1);
        $note = htmlspecialchars($_POST["note"]);

        $transaction->setType($type);
        $transaction->setDate(new \DateTime($date));
        $transaction->setAmount($amount);
        $transaction->setNote($note);

        $this->em->flush();

        return $this->redirectToRoute('document_transactions', ['id' => $id]);
    }

    /**
     * Delete transaction.
     *
     * @param int $id
     * @param int $transaction_id
     *
     * @return Response
     */
    #[Route('/documents/{id}/transactions/{transaction_id}/delete', name: 'transaction_delete', methods: ['GET'])]
    public function transactionDelete(int $id, int $transaction_id): Response
    {
        $transaction = $this->em->find(Payment::class, $transaction_id);
        $this->em->remove($transaction);
        $this->em->flush();

        return $this->redirectToRoute('document_transactions', ['id' => $id]);
    }

    /**
     * Displays the edit form for application preferences (such as exchange rate and tax).
     *
     * This method checks user authentication, retrieves the current preferences, prepares all relevant data, and
     * renders the preferences edit form view.
     *
     * @return Response Renders the preferences edit form view.
     */
    #[Route('/documents/preferences', name: 'preferences_edit_form')]
    public function preferencesEdit(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $preferences = $this->em->find(Preferences::class, 1);

        $data = $this->getDefaultData();
        $data += [
            'preferences' => $preferences,
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
     * @return Response Redirects to the preferences edit form view after update.
     */
    #[Route('/documents/preferences/update', name: 'preferences_update', methods: ['POST'])]
    public function preferencesUpdate(): Response
    {
        $exchangeRate = str_replace(",", ".", htmlspecialchars($_POST["exchange_rate"]));
        $tax = str_replace(",", ".", htmlspecialchars($_POST["tax"]));

        $preferences = $this->em->find(Preferences::class, 1);

        $preferences->setKurs($exchangeRate);
        $preferences->setTax($tax);
        $this->em->flush();

        return $this->redirectToRoute('preferences_edit_form');
    }

    /**
     * Add payment to Accounting Document.
     *
     * @param int $id
     *
     * @return Response
     */
    #[Route('/documents/{id}/add-payment', name: 'add_payment_to_accounting_document', methods: ['POST'])]
    public function addPayment(int $id): Response
    {
        session_start();
        $user = $this->em->find(User::class, $_SESSION['user_id']);

        $paymentTypeId = htmlspecialchars($_POST["type_id"]);
        $paymentType = $this->em->find(PaymentType::class, $paymentTypeId);

//        if ($payment_type_id == 5 && $this->em->getRepository(Payment::class)->ifExistFirstCashInput()) {
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
        } else {
            $date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
        }

        $amount = htmlspecialchars($_POST["amount"]);
        // Correct decimal separator.
        $amount = str_replace(",", ".", $amount);

        $note = htmlspecialchars($_POST["note"]);

        // Create a new Payment.
        $newPayment = new Payment();

        $newPayment->setType($paymentType);

//        if ($paymentTypeId == 6 || $paymentTypeId == 7) {
//            $amount = "-".$amount;
//        }

        $newPayment->setAmount($amount);
        $newPayment->setDate(new \DateTime($date));
        $newPayment->setNote($note);
        $newPayment->setCreatedAt(new \DateTime("now"));
        $newPayment->setCreatedByUser($user);

        $this->em->persist($newPayment);
        $this->em->flush();

        $accountingDocument = $this->em->find(AccountingDocument::class, $id);
        // Add Payment to AccountingDocument.
        $accountingDocument->getPayments()->add($newPayment);
        $this->em->flush();

        return $this->redirectToRoute('document_show', ['id' => $id]);
    }

    /**
     * Searches for accounting documents (proformas, delivery notes, return notes) by a search term.
     *
     * This method checks user authentication, retrieves the search term from the request, queries the database for
     * matching documents (including archived and non-archived for each type), and prepares all relevant data for the
     * search results view.
     *
     * @param Request $request The HTTP request containing the search term as a query parameter.
     *
     * @return Response Renders the search results view with matching documents.
     */
    #[Route('/documents/search', name: 'documents_search')]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = $request->query->get('term', '');

        $data = $this->getDefaultData();
        $data += [
            'proformas' => $this->em->getRepository(AccountingDocument::class )->search([1, $term, 0]),
            'proformas_archived' => $this->em->getRepository(AccountingDocument::class)->search([1, $term, 1]),
            'delivery_notes' => $this->em->getRepository(AccountingDocument::class)->search([2, $term, 0]),
            'delivery_notes_archived' => $this->em->getRepository(AccountingDocument::class)->search([2, $term, 1]),
            'return_notes' => $this->em->getRepository(AccountingDocument::class)->search([4, $term, 0]),
            'return_notes_archived' => $this->em->getRepository(AccountingDocument::class)->search([4, $term, 1]),
            'last' => $this->em->getRepository(AccountingDocument::class)->getLastAccountingDocument(),
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => FALSE,
            ],
        ];

        return $this->render('document/search.html.twig', $data);
    }

    /**
     * Get accounting document articles data.
     *
     * @param int $id The ID of the accounting document.
     *
     * @return array Returns an array containing detailed data for each article in the accounting document.
     */
    private function getAccountingDocumentArticlesData(int $id): array
    {
        $preferences = $this->em->find(Preferences::class, 1);
        $exchangeRate = $preferences->getKurs();

        $documentArticles = $this->em->getRepository(AccountingDocument::class)->getArticles($id);

        $documentArticlesData = [];

        foreach ($documentArticles as $index => $documentArticle) {

            $accounting_document_article_properties = $this->em
                ->getRepository(AccountingDocumentArticleProperty::class)
                ->findBy(['accounting_document_article' => $documentArticle->getId()], []);

            $documentArticlesData[$index]['article']['id'] = $documentArticle->getId();
            $documentArticlesData[$index]['article']['pieces'] = $documentArticle->getPieces();
            $documentArticlesData[$index]['article']['name'] = $documentArticle->getArticle()->getName();
            foreach ($accounting_document_article_properties as $propertyKey => $accounting_document_article_property) {
                $documentArticlesData[$index]['article']['properties'][$propertyKey]['name']
                    = $accounting_document_article_property->getProperty()->getName();
                $documentArticlesData[$index]['article']['properties'][$propertyKey]['quantity']
                    = $accounting_document_article_property->getQuantity();
            }
            $documentArticlesData[$index]['article']['unit'] = $documentArticle->getArticle()
                ->getUnit()->getName();
            $documentArticlesData[$index]['article']['quantity'] = $this->em
                ->getRepository(AccountingDocumentArticle::class)
                ->getQuantity(
                    $documentArticle->getId(),
                    $documentArticle->getArticle()->getMinCalcMeasure(),
                    $documentArticle->getPieces()
                );
            $documentArticlesData[$index]['article']['note'] = $documentArticle->getNote();
            $documentArticlesData[$index]['article']['price'] = $documentArticle->getPrice();
            $documentArticlesData[$index]['article']['price_rsd'] = $documentArticle->getPrice() * $exchangeRate;
            $documentArticlesData[$index]['article']['discount'] = $documentArticle->getDiscount();
            $documentArticlesData[$index]['article']['tax_base_rsd'] =  $this->em
                ->getRepository(AccountingDocumentArticle::class)
                ->getTaxBase(
                    $documentArticle->getPrice(),
                    $documentArticle->getDiscount(),
                    $documentArticlesData[$index]['article']['quantity']
                ) * $exchangeRate;
            $documentArticlesData[$index]['article']['tax'] = $documentArticle->getTax();
            $documentArticlesData[$index]['article']['tax_amount_rsd'] = $this->em
                ->getRepository(AccountingDocumentArticle::class)
                ->getTaxAmount(
                    $documentArticlesData[$index]['article']['tax_base_rsd'],
                    $documentArticlesData[$index]['article']['tax']
                );
            $documentArticlesData[$index]['article']['sub_total_rsd'] = $this->em
                ->getRepository(AccountingDocumentArticle::class)
                ->getSubTotal(
                    $documentArticlesData[$index]['article']['tax_base_rsd'],
                    $documentArticlesData[$index]['article']['tax_amount_rsd']
                );
        }
        return $documentArticlesData;
    }

    /**
     * Get accounting document total tax base in RSD.
     *
     * @param int $id
     *
     * @return float
     */
    private function getAccountingDocumentTotalTaxBaseRSD(int $id): float
    {
        $documentArticlesData = $this->getAccountingDocumentArticlesData($id);
        $totalTaxBaseRsd = 0;
        foreach ($documentArticlesData as $documentArticle) {
          $totalTaxBaseRsd += $documentArticle['article']['tax_base_rsd'];
        }
        return $totalTaxBaseRsd;
    }

    /**
     * Get accounting document total tax amount in RSD.
     *
     * @param int $id
     *
     * @return int|mixed
     */
    private function getAccountingDocumentTotalTaxAmountRSD(int $id)
    {
      $documentArticlesData = $this->getAccountingDocumentArticlesData($id);
        $totalTaxAmountRsd = 0;
        foreach ($documentArticlesData as $index => $documentArticle) {
          $totalTaxAmountRsd += $documentArticle['article']['tax_amount_rsd'];
        }
        return $totalTaxAmountRsd;
    }

    /**
     * Displays the daily cash register view with all cash transactions and the current cash saldo.
     *
     * This method checks user authentication, retrieves all daily cash transactions, fetches the related accounting
     * documents for each transaction, calculates the daily cash saldo, and prepares all relevant data for the cash
     * register view.
     *
     * @return Response Renders the cash register view with transaction and saldo data.
     */
    #[Route('/documents/cash-register', name: 'cash_register')]
    public function cashRegister(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $dailyTransactions = $this->em->getRepository(Payment::class)->getDailyCashTransactions();
        $dailyTransactionsWithAccountingDocument = [];
        foreach ($dailyTransactions as $index => $dailyTransaction) {
          $dailyTransactionsWithAccountingDocument[$index] = [
                'transaction' => $dailyTransaction,
                'accounting_document' => $this->em
                    ->getRepository(AccountingDocument::class)
                    ->getAccountingDocumentByTransaction($dailyTransaction->getId()),
            ];
        }

        $daily_cash_saldo = $this->em->getRepository(Payment::class)->getDailyCashSaldo();

        $data = $this->getDefaultData();
        $data += [
            'daily_transactions' => $dailyTransactionsWithAccountingDocument,
            'daily_cash_saldo' => $daily_cash_saldo,
            'tools_menu' => [
                'document' => FALSE,
                'cash_register' => TRUE,
            ],
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
        $paymentTypeId = $_POST["type_id"];

        if ($paymentTypeId == 5 && $this->em->getRepository(Payment::class)->ifExistFirstCashInput()) {
            // @todo Create error message object or something like that, and
            // display it in the view.
            ?>
            <p>Već ste uneli početno stanje!</p>
            <a href="/documents/cash-register">Povratak na Kasu</a>
            <?php
            exit();
        }

        session_start();
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $paymentType = $this->em->find(PaymentType::class, $paymentTypeId);

        $amount = htmlspecialchars($_POST["amount"]);
        // Correct decimal separator.
        $amount = str_replace(",", ".", $amount);

        if ($paymentTypeId == 6 || $paymentTypeId == 7) {
            $amount = "-".$amount;
        }

        $note = htmlspecialchars($_POST["note"]);

        // Create a new Payment.
        $newPayment = new Payment();

        $newPayment->setType($paymentType);

        $newPayment->setAmount($amount);
        $newPayment->setDate(new \DateTime("now"));
        $newPayment->setNote($note);
        $newPayment->setCreatedAt(new \DateTime("now"));
        $newPayment->setCreatedByUser($user);

        $this->em->persist($newPayment);
        $this->em->flush();

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

        $companyInfo = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);

        $date = date('Y-m-d');
        $dailyTransactions = $this->em->getRepository(Payment::class)->getDailyCashTransactions($date);

        $dailyTransactionsWithAccountingDocument = [];
        foreach ($dailyTransactions as $index => $dailyTransaction) {
          $dailyTransactionsWithAccountingDocument[$index] = [
                'transaction' => $dailyTransaction,
                'accounting_document' => $this->em
                    ->getRepository(AccountingDocument::class)
                    ->getAccountingDocumentByTransaction($dailyTransaction->getId()),
            ];
        }

        $daily_cash_saldo = $this->em->getRepository('\App\Entity\Payment')->getDailyCashSaldo($date);

        $data = [
            'company_info' => $companyInfo,
            'daily_transactions' => $dailyTransactionsWithAccountingDocument,
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
        $pdf->SetAuthor($companyInfo['name']);
        $pdf->SetTitle($companyInfo['name'] . ' - Dokument');
        $pdf->SetSubject($companyInfo['name']);
        $pdf->SetKeywords($companyInfo['name'] . ', PDF, dnevni izveštaj');

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

    /**
     * Get Document Type Data.
     *
     * @param int $documentTypeId
     *   The document type ID.
     *
     * @return string[]
     *   An array containing the document type name, prefix, and badge class.
     */
    private function getDocumentTypeData(int $documentTypeId): array
    {
      return match ($documentTypeId) {
        1 => ["Predračun", "P_", 'info'],
        2 => ["Otpremnica", "O_", 'secondary'],
        4 => ["Povratnica", "POV_", 'warning'],
        default => ["_", "_", 'default'],
      };
    }

    /**
     * Returns default data array for views.
     *
     * @return array An associative array containing default data for views.
     */
    private function getDefaultData(): array
    {
        return [
            'page' => $this->page,
            'page_title' => $this->pageTitle,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];
    }

}
