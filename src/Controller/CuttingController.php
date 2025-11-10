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
use App\Entity\CuttingSheet;
use App\Entity\CuttingSheetArticle;
use App\Entity\FenceModel;
use App\Entity\Preferences;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TCPDF;

/**
 * CuttingController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class CuttingController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private string $page;
    private string $page_title;
    protected string $stylesheet;
    protected string $app_version;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->page_title = 'Krojne liste';
        $this->page = 'cuttings';
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
        $this->app_version = $this->loadAppVersion();
    }

    /**
     * Displays the Cutting Sheets home page.
     *
     * Starts a session and checks if the user is logged in (redirects to login if not).
     * Fetches the last 10 cutting sheets from the database.
     * Passes page, title, tools menu, cutting sheets, stylesheet, user role, username, and app version to the view.
     * Renders the 'cutting/index.html.twig' template with the data.
     *
     * @return Response
     *   The HTTP response with the rendered template or a redirect.
     */
    #[Route('/cuttings/', name: 'cuttings_index')]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $cutting_sheets = $this->entityManager->getRepository(CuttingSheet::class)->getLastCuttingSheets(10);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'tools_menu' => [
                'cutting' => FALSE,
            ],
            'cutting_sheets' => $cutting_sheets,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('cutting/index.html.twig', $data);
    }

    /**
     * Displays the form for adding a new CuttingSheet.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves a list of clients from the database, ordered by name.
     * - Optionally loads a specific client if 'client_id' is present in the GET parameters.
     * - Prepares data including page info, client list, selected client, stylesheet, user role, username, tools menu,
     * and app version.
     * - Renders the 'cutting/cutting_new.html.twig' template with the data.
     *
     * @param int|null $project_id
     *   Optional project ID for context.
     * @param int|null $client_id
     *   Optional client ID for pre-selection.
     *
     * @return Response
     *   The HTTP response with the rendered template or a redirect.
     */
    #[Route('/cuttings/new/', name: 'cutting_new_form')]
    public function new(int $project_id = NULL, int $client_id = NULL): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $clients_list = $this->entityManager->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        if (isset($_GET['client_id'])) {
            $client_id = htmlspecialchars($_GET['client_id']);
            $client = $this->entityManager->find(Client::class, $client_id);
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'clients_list' => $clients_list,
            'client_id' => $client_id,
            'client' => $client ?? NULL,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'cutting' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('cutting/cutting_new.html.twig', $data);
    }

    /**
     * Add CuttingSheet.
     *
     * @param int|null $project_id
     * @param int|null $client_id
     *
     * @return Response
     */
    #[Route('/cuttings/create', name: 'cutting_create', methods: ['POST'])]
    public function create(int $project_id = NULL, int $client_id = NULL): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $ordinal_num_in_year = 0;

        if (!$client_id) {
            $client_id = htmlspecialchars($_POST['client_id']);
        }
        $client = $this->entityManager->find(Client::class, $client_id);

        $newCuttingSheet = new CuttingSheet();

        $newCuttingSheet->setOrdinalNumInYear($ordinal_num_in_year);
        $newCuttingSheet->setClient($client);
        $newCuttingSheet->setCreatedAt(new \DateTime("now"));
        $newCuttingSheet->setCreatedByUser($user);
        $newCuttingSheet->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newCuttingSheet);
        $this->entityManager->flush();

        // Get id of last CuttingSheet.
        $new__cutting_sheet__id = $newCuttingSheet->getId();

        // Set Ordinal Number In Year.
        $this->entityManager->getRepository(CuttingSheet::class)->setOrdinalNumInYear($new__cutting_sheet__id);

        return $this->redirectToRoute('cuttings_show', ['cutting_id' => $new__cutting_sheet__id]);
    }

    /**
     * Displays the details of a specific Cutting Sheet.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Loads the CuttingSheet entity by its ID.
     * - Retrieves client data for the cutting sheet.
     * - Fetches all articles associated with the cutting sheet.
     * - Calculates total picket length and cap count for the sheet.
     * - Loads available fence models.
     * - Prepares and passes all relevant data to the 'cutting/cutting_view.html.twig' template.
     * - Renders the template with the data for display.
     *
     * @param int $cutting_id
     *   The ID of the Cutting Sheet to display.
     *
     * @return Response
     *   The HTTP response with the rendered template or a redirect.
     */
    #[Route('/cuttings/{cutting_id}', name: 'cuttings_show', requirements: ['cutting_id' => '\d+'], methods: ['GET'])]
    public function show(int $cutting_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $cutting = $this->entityManager->find(CuttingSheet::class, $cutting_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($cutting->getClient()->getId());

        $cutting_sheet_articles = $this->entityManager->getRepository(CuttingSheet::class)
          ->getArticlesOnCuttingSheet($cutting_id);

        $total_picket_lenght = 0;
        $total_cap = 0;
        foreach ($cutting_sheet_articles as $cutting_sheet_article) {
            $cutting_sheet__article__picket_number = $this->entityManager
                ->getRepository(CuttingSheetArticle::class)
                ->getPicketsNumber($cutting_sheet_article->getId()) * $cutting_sheet_article->getNumberOfFields();

            $cutting_sheet__article__picket_lenght = $this->entityManager
                ->getRepository(CuttingSheetArticle::class)
                ->getPicketsLength($cutting_sheet_article->getId()) * $cutting_sheet_article->getNumberOfFields();

            $total_picket_lenght = $total_picket_lenght + $cutting_sheet__article__picket_lenght;
            $total_cap = $total_cap + $cutting_sheet__article__picket_number;
        }

        $fence_models = $this->entityManager->getRepository(FenceModel::class)->findBy([], ['name' => 'ASC']);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'cutting_id' => $cutting_id,
            'cutting' => $cutting,
            'client' => $client,
            'fence_models' => $fence_models,
            'tools_menu' => [
                'cutting' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
            'cutting_sheet_articles' => $cutting_sheet_articles,
            'total_picket_lenght' => $total_picket_lenght / 1000,
            'total_cap' => $total_cap,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('cutting/cutting_view.html.twig', $data);
    }

    /**
     * Displays the edit form for a specific Cutting Sheet.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Loads the CuttingSheet entity by its ID.
     * - Retrieves client data for the cutting sheet.
     * - Fetches all articles associated with the cutting sheet.
     * - Calculates total picket length and cap count for the sheet.
     * - Loads available fence models.
     * - Prepares and passes all relevant data to the 'cutting/cutting_edit.html.twig' template.
     * - Renders the template with the data for editing.
     *
     * @param int $cutting_id
     *   The ID of the Cutting Sheet to edit.
     *
     * @return Response
     *   The HTTP response with the rendered template or a redirect.
     */
    #[Route('/cuttings/{cutting_id}/edit', name: 'cutting_edit_form')]
    public function edit(int $cutting_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $cutting = $this->entityManager->find(CuttingSheet::class, $cutting_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($cutting->getClient()->getId());

        $cutting_sheet_articles = $this->entityManager->getRepository(CuttingSheet::class)
            ->getArticlesOnCuttingSheet($cutting_id);

        $total_picket_lenght = 0;
        $total_cap = 0;
        foreach ($cutting_sheet_articles as $cutting_sheet_article) {
            $cutting_sheet__article__picket_number = $this->entityManager
                ->getRepository(CuttingSheetArticle::class)
                ->getPicketsNumber($cutting_sheet_article->getId()) * $cutting_sheet_article->getNumberOfFields();

            $cutting_sheet__article__picket_lenght = $this->entityManager
                ->getRepository(CuttingSheetArticle::class)
                ->getPicketsLength($cutting_sheet_article->getId()) * $cutting_sheet_article->getNumberOfFields();

            $total_picket_lenght = $total_picket_lenght + $cutting_sheet__article__picket_lenght;
            $total_cap = $total_cap + $cutting_sheet__article__picket_number;
        }

        $fence_models = $this->entityManager->getRepository(FenceModel::class)->findBy([], ['name' => 'ASC']);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'client' => $client,
            'cutting_id' => $cutting_id,
            'cutting' => $cutting,
            'fence_models' => $fence_models,
            'tools_menu' => [
                'cutting' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
            'cutting_sheet_articles' => $cutting_sheet_articles,
            'total_picket_lenght' => $total_picket_lenght / 1000,
            'total_cap' => $total_cap,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('cutting/cutting_edit.html.twig', $data);
    }

    /**
     * Generates and displays a PDF for a specific Cutting Sheet.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Loads company info and the CuttingSheet entity by its ID.
     * - Retrieves all articles associated with the cutting sheet and computes their data (including picket calculations
     * and geometry).
     * - Prepares data for the PDF, renders HTML using a Twig template, and generates a PDF using TCPDF.
     * - Sets appropriate HTTP headers and returns the PDF as an inline response to the browser.
     *
     * @param int $cutting_id
     *   The ID of the Cutting Sheet to print.
     *
     * @return Response
     *   The HTTP response containing the generated PDF or a redirect.
     */
    #[Route('/cuttings/{cutting_id}/print', name: 'cutting_print')]
    public function print(int $cutting_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);

        $cutting_sheet = $this->entityManager->find(CuttingSheet::class, $cutting_id);

        $articles = $this->entityManager->getRepository(CuttingSheet::class)->getArticlesOnCuttingSheet($cutting_id);
        $article_repo = $this->entityManager->getRepository(CuttingSheetArticle::class);

        $articles_data = [];
        foreach ($articles as $key => $article) {
            $fence_model_id = $article->getFenceModel()->getId();
            $fence_model = $article->getFenceModel()->getName();
            $article_width = $article->getWidth();
            $article_height = $article->getHeight();
            $article_middle_height = $article->getMidHeight();

            $picket_width = $article->getPicketWidth();
            $article_space = $article->getSpace();
            $article_field_number = $article->getNumberOfFields();

            // Izracunavanje broja letvica u zavisnosti od sirine polja.
            $pickets_number = $article_repo->getPicketsNumber($article->getId());

            // Real space between pickets.
            $space_between_pickets = $article_repo->getSpaceBetweenPickets($article_width, $pickets_number, $picket_width);

            // Legs of triangle for angle calculation.
            $height_leg = $article_repo->getDiffMinMax($article_height, $article_middle_height);
            $width_leg = $article_repo->getWidthForAngleCalc(
                $article_repo->isEven($pickets_number),
                $article_width,
                $space_between_pickets,
                $picket_width
            );

            $articles_data[$key]['fence_model'] = $fence_model;
            $articles_data[$key]['picket_width'] = $picket_width;
            $articles_data[$key]['article_field_number'] = $article_field_number;
            $articles_data[$key]['article_width'] = $article_width;
            $articles_data[$key]['article_height'] = $article_height;
            $articles_data[$key]['article_middle_height'] = $article_middle_height;
            $articles_data[$key]['space_between_pickets'] = $space_between_pickets;
            $articles_data[$key]['pickets_number'] = $pickets_number;
            $articles_data[$key]['pickets'] = $this->getPickets(
                $fence_model_id,
                $article_height,
                $pickets_number,
                $height_leg,
                $width_leg,
                $picket_width,
                $space_between_pickets,
                $article_width,
            );
        }

        $data = [
            'company_info' => $company_info,
            'cutting_sheet' => $cutting_sheet,
            'articles_data' => $articles_data,
        ];

        // Render HTML content from a Twig template (or similar)
        $html = $this->renderView('cutting/print_cutting.html.twig', $data);

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(
          PDF_PAGE_ORIENTATION,
          PDF_UNIT,
          PDF_PAGE_FORMAT,
          true,
          'UTF-8',
          false
        );

        // Set document information.
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Krojna lista');
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

        // Write HTML content.
        $pdf->writeHTML($html, true, false, true, false, '');

        // Reset pointer to the last page.
        $pdf->lastPage();

        // Output PDF document to browser as a Symfony Response
        $filename = 'krojna_lista_' . $data['cutting_sheet']->getOrdinalNumInYear() . '.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        // Remove leading __ from filename for the response
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

    /**
     * Adds a new Article to the specified Cutting Sheet.
     *
     * - Retrieves the CuttingSheet entity by its ID.
     * - Gets the FenceModel and article parameters from the POST request.
     * - Creates a new CuttingSheetArticle entity and sets its properties.
     * - Persists the new article to the database.
     * - Redirects to the Cutting Sheet details page after adding the article.
     *
     * @param int $cutting_id
     *   The ID of the Cutting Sheet to which the article will be added.
     *
     * @return Response
     *   The HTTP response with a redirect to the Cutting Sheet details page.
     */
    #[Route('/cuttings/{cutting_id}/add-article', name: 'cutting_add_article', methods: ['POST'])]
    public function addArticle(int $cutting_id): Response
    {
        $cutting_sheet = $this->entityManager->find(CuttingSheet::class, $cutting_id);

        $fence_model_id = htmlspecialchars($_POST['fence_model_id']);
        $fence_model = $this->entityManager->find(FenceModel::class, $fence_model_id);

        $picket_width = htmlspecialchars($_POST['picket_width']);
        $width = htmlspecialchars($_POST['width']);
        $height = htmlspecialchars($_POST['height']);

        $mid_height = 0;
        if ($_POST['mid_height']){
            $mid_height = htmlspecialchars($_POST['mid_height']);
        }

        $space = htmlspecialchars($_POST['space']);
        $number_of_fields = htmlspecialchars($_POST['number_of_fields']);

        $newCuttingShettArticle = new CuttingSheetArticle();

        $newCuttingShettArticle->setCuttingSheet($cutting_sheet);
        $newCuttingShettArticle->setFenceModel($fence_model);
        $newCuttingShettArticle->setPicketWidth($picket_width);
        $newCuttingShettArticle->setWidth($width);
        $newCuttingShettArticle->setHeight($height);
        $newCuttingShettArticle->setMidHeight($mid_height);
        $newCuttingShettArticle->setSpace($space);
        $newCuttingShettArticle->setNumberOfFields($number_of_fields);

        $this->entityManager->persist($newCuttingShettArticle);
        $this->entityManager->flush();

        return $this->redirectToRoute('cuttings_show', ['cutting_id' => $cutting_id]);
    }

    /**
     * Updates an existing Article in the specified Cutting Sheet.
     *
     * - Retrieves the CuttingSheetArticle entity by its ID.
     * - Gets the FenceModel and article parameters from the POST request.
     * - Updates the article's properties with the new values.
     * - Persists the changes to the database.
     * - Redirects to the Cutting Sheet details page after editing the article.
     *
     * @param int $cutting_id
     *   The ID of the Cutting Sheet containing the article.
     * @param int $article_id
     *   The ID of the Article to edit.
     *
     * @return Response
     *   The HTTP response with a redirect to the Cutting Sheet details page.
     */
    #[Route('/cuttings/{cutting_id}/articles/{article_id}/edit', name: 'cutting_edit_article', methods: ['POST'])]
    public function editArticle(int $cutting_id, int $article_id): Response
    {
        $cutting_sheet__article = $this->entityManager->find(CuttingSheetArticle::class, $article_id);

        $fence_model_id = htmlspecialchars($_POST['fence_model_id']);
        $fence_model = $this->entityManager->find(FenceModel::class, $fence_model_id);

        $picket_width = htmlspecialchars($_POST['picket_width']);
        $width = htmlspecialchars($_POST['width']);
        $height = htmlspecialchars($_POST['height']);

        $mid_height = 0;
        if ($_POST['mid_height']){
            $mid_height = htmlspecialchars($_POST['mid_height']);
        }

        $space = htmlspecialchars($_POST['space']);
        $number_of_fields = htmlspecialchars($_POST['number_of_fields']);

        $cutting_sheet__article->setFenceModel($fence_model);
        $cutting_sheet__article->setPicketWidth($picket_width);
        $cutting_sheet__article->setWidth($width);
        $cutting_sheet__article->setHeight($height);
        $cutting_sheet__article->setMidHeight($mid_height);
        $cutting_sheet__article->setSpace($space);
        $cutting_sheet__article->setNumberOfFields($number_of_fields);

        $this->entityManager->flush();

        return $this->redirectToRoute('cuttings_show', ['cutting_id' => $cutting_id]);
    }

    /**
     * Deletes an Article from the specified Cutting Sheet.
     *
     * - Retrieves the CuttingSheetArticle entity by its ID.
     * - Removes the article from the database.
     * - Persists the changes.
     * - Redirects to the Cutting Sheet details page after deletion.
     *
     * @param int $cutting_id
     *   The ID of the Cutting Sheet containing the article.
     * @param int $article_id
     *   The ID of the Article to delete.
     *
     * @return Response
     *   The HTTP response with a redirect to the Cutting Sheet details page.
     */
    #[Route('/cuttings/{cutting_id}/articles/{article_id}/delete', name: 'cutting_delete_article')]
    public function deleteArticle(int $cutting_id, int $article_id): Response
    {
        $cutting_sheet__article = $this->entityManager->find(CuttingSheetArticle::class, $article_id);

        $this->entityManager->remove($cutting_sheet__article);
        $this->entityManager->flush();

        return $this->redirectToRoute('cuttings_show', ['cutting_id' => $cutting_id]);
    }

    /**
     * Exports the specified Cutting Sheet to an Accounting Document (Proforma).
     *
     * - Retrieves total picket length, total cap count, and picket width from the request.
     * - Loads the CuttingSheet and the current user.
     * - Creates a new AccountingDocument and sets its properties.
     * - Adds picket and cap articles to the document based on picket width.
     * - Adds article properties to the AccountingDocumentArticle.
     * - Persists all changes to the database.
     * - Redirects to the Accounting Document details page after export.
     *
     * @param int $cutting_id
     *   The ID of the Cutting Sheet to export.
     * @param Request $request
     *   The HTTP request containing export parameters.
     *
     * @return Response
     *   The HTTP response with a redirect to the Accounting Document details page.
     */
    #[Route('/cuttings/{cutting_id}/export-to-accounting-document', name: 'cutting_export_to_accounting_document')]
    public function exportToAccountingDocument(int $cutting_id, Request $request): Response
    {
        $total_picket_lenght = htmlspecialchars($request->query->get('total_picket_lenght'));
        $total_kap = htmlspecialchars($request->query->get('total_kap'));
        $picket_width = htmlspecialchars($request->query->get('picket_width'));

        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $cutting = $this->entityManager->find(CuttingSheet::class, $cutting_id);

        // Total length of pickets in cm.
        $total_picket_lenght = $total_picket_lenght * 100;

        $ordinal_num_in_year = 0;
        $title = "PVC letvice";
        $note = "ROLOSTIL szr je PDV obveznik.";

        $accounting_document__type_id = 1;
        $accounting_document__type = $this->entityManager
            ->find(AccountingDocumentType::class, $accounting_document__type_id);

        // Create a new AccountingDocument (Proforma).
        $newProforma = new AccountingDocument();

        $newProforma->setOrdinalNumInYear($ordinal_num_in_year);
        $newProforma->setDate(new \DateTime("now"));
        $newProforma->setIsArchived(0);
        $newProforma->setType($accounting_document__type);
        $newProforma->setClient($cutting->getClient());
        $newProforma->setTitle($title);
        $newProforma->setNote($note);
        $newProforma->setCreatedAt(new \DateTime("now"));
        $newProforma->setCreatedByUser($user);
        $newProforma->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newProforma);
        $this->entityManager->flush();

        // Get id of last AccountingDocument.
        $newProforma_id = $newProforma->getId();

        // Set Ordinal Number In Year.
        $this->entityManager->getRepository(AccountingDocument::class)->setOrdinalNumInYear($newProforma_id);

        // Add Article to the Proforma.
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

        $article_picket = $this->entityManager->find(Article::class, $article_id);
        $note = "";
        $pieces = 1;
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $tax = $preferences->getTax();

        // Add article to proforma.
        $newProformaArticle = new AccountingDocumentArticle();

        $newProformaArticle->setAccountingDocument($newProforma);
        $newProformaArticle->setArticle($article_picket);
        $newProformaArticle->setPieces($pieces);
        $newProformaArticle->setPrice($article_picket->getPrice());
        $newProformaArticle->setDiscount(0);
        $newProformaArticle->setTax($tax);
        $newProformaArticle->setWeight($article_picket->getWeight());
        $newProformaArticle->setNote($note);
        $this->entityManager->persist($newProformaArticle);
        $this->entityManager->flush();

        // Last inserted Accounting Document Article.
        $last__accounting_document__article_id = $newProformaArticle->getId();

        // Add article properties to AccountingDocumentArticle.
        $article_properties = $this->entityManager->getRepository(ArticleProperty::class)->getArticleProperties
        ($article_picket->getId());
        foreach ($article_properties as $article_property) {
            $newProformaArticleProperty = new AccountingDocumentArticleProperty();

            $newProformaArticleProperty->setAccountingDocumentArticle($newProformaArticle);
            $newProformaArticleProperty->setProperty($article_property->getProperty());
            $newProformaArticleProperty->setQuantity($total_picket_lenght);

            $this->entityManager->persist($newProformaArticleProperty);
            $this->entityManager->flush();
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
        $article_cap = $this->entityManager->find(Article::class, $cap_article_id);

        // Add article to proforma invoice.
        $newProformaArticle = new AccountingDocumentArticle();

        $newProformaArticle->setAccountingDocument($newProforma);
        $newProformaArticle->setArticle($article_cap);
        $newProformaArticle->setPieces($cap_pieces);
        $newProformaArticle->setPrice($article_cap->getPrice());
        $newProformaArticle->setDiscount(0);
        $newProformaArticle->setTax($tax);
        $newProformaArticle->setWeight($article_cap->getWeight());
        $newProformaArticle->setNote($note);
        $this->entityManager->persist($newProformaArticle);
        $this->entityManager->flush();

        return $this->redirectToRoute('document_show', ['document_id' => $newProforma_id]);
    }

    /**
     * Delete CuttingSheet.
     *
     * @param int $cutting_id
     *
     * @return Response
     */
    #[Route('/cuttings/{cutting_id}/delete', name: 'cutting_delete')]
    public function delete(int $cutting_id): Response
    {
        // Check if exist CuttingSheet.
        if ($cs = $this->entityManager->find(CuttingSheet::class, $cutting_id)) {

            // Check if exist Article in CuttingSheet.
            if ($cs_articles = $this->entityManager->getRepository(CuttingSheetArticle::class)->getCuttingSheetArticles
            ($cutting_id)) {

                // Loop through all Articles of CuttingSheet.
                foreach ($cs_articles as $cs_article) {
                    // Remove Article.
                    $this->entityManager->remove($cs_article);
                    $this->entityManager->flush();
                }

            }

            // Remove CuttingSheet.
            $this->entityManager->remove($cs);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('cuttings_search', ['term' => '']);
    }

  /**
   * @param $fence_model_id
   * @param $article_height
   * @param $pickets_number
   * @param $heigth_leg
   * @param $width_leg
   * @param $picket_width
   * @param $space_between_pickets
   * @param $article_width
   *
   * @return array
   */
    protected  function getPickets(
        $fence_model_id,
        $article_height,
        $pickets_number,
        $heigth_leg,
        $width_leg,
        $picket_width,
        $space_between_pickets,
        $article_width
    ): array
    {
        $article_repo = $this->entityManager->getRepository(CuttingSheetArticle::class);
        $pickets = [];

        // Classic fence model.
        if ($fence_model_id == 1) {
            $pickets[] = [
                'height' => number_format($article_height, 0, ',', '.'),
                'number' => $pickets_number,
            ];
        }

        // Alpine fence model.
        if ($fence_model_id == 2) {
            $alpha_angle = rad2deg(atan($heigth_leg / $width_leg));
            for ( $i=1; $i <= ceil($pickets_number/2); $i++ ) {
                $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1);
                $picket_height_over_post = tan(deg2rad($alpha_angle)) * $picket_x_position;
                $picket_height = $article_height + $picket_height_over_post;

                if ( $i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0 ) {
                    $pieces = 1;
                }
                else {
                    $pieces = 2;
                }

                $pickets[] = [
                    'height' => number_format($picket_height, 0, ',', '.'),
                    'number' => $pieces,
                ];
            }
        }

        // Arizona fence model.
        if ($fence_model_id == 3) {
            // Tendon of circle.
            $tendon = $article_repo->getTendon($article_width, $space_between_pickets, $heigth_leg);

            $alpha_angle = rad2deg(atan( ($heigth_leg * 2) / ($article_width - $space_between_pickets * 2)));
            $beta_angle = 90 - $alpha_angle;
            $radius = $tendon / (2*cos(deg2rad($beta_angle)));

            for ( $i = 1; $i <= ceil($pickets_number/2); $i++ ) {
                $corective_factor = 0;
                if ($i > 1 ) {
                    $corective_factor = ($space_between_pickets / 2) / ceil($pickets_number/2);
                }
                if ($i > 1 && $article_repo->isEven($pickets_number)) {
                    $corective_factor = ($picket_width + $space_between_pickets / 2) / ceil($pickets_number/2);
                }
                $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1) + $corective_factor * $i;
                $y = sqrt( $radius ** 2 - ((($article_width - $space_between_pickets * 2) / 2 - $picket_x_position) ** 2 ) );
                $picket_height_over_post = $y - ($radius - $heigth_leg);
                $picket_height = $article_height + $picket_height_over_post;
                if ( $i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0 ) {
                    $pieces = 1;
                }
                else {
                    $pieces = 2;
                }

                $pickets[] = [
                    'height' => number_format($picket_height, 0, ',', '.'),
                    'number' => $pieces,
                ];
            }
        }

        // Pacific fence model.
        if ($fence_model_id == 4) {
            $tendon = $article_repo->getTendon($article_width, $space_between_pickets, $heigth_leg);

            $alpha_angle = rad2deg(atan(($heigth_leg * 2)/($article_width-$space_between_pickets * 2)));
            $beta_angle = 90 - $alpha_angle;
            $radius = $tendon / (2*cos(deg2rad($beta_angle)));

            for ($i=1; $i<=ceil($pickets_number/2); $i++) {
                $corective_factor = 0;
                if ($i > 1 ) {
                    $corective_factor = ($space_between_pickets / 2) / ceil($pickets_number/2);
                }
                if ($i > 1 && $article_repo->isEven($pickets_number)) {
                    $corective_factor = ($picket_width + $space_between_pickets / 2) / ceil($pickets_number/2);
                }
                $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1) + $corective_factor * $i;;
                $y = sqrt( $radius ** 2 - ((($article_width - $space_between_pickets * 2) / 2 - $picket_x_position) ** 2 ) );
                $picket_height_over_post = $y - ($radius - $heigth_leg);
                $picket_height = $article_height - $picket_height_over_post;

                if ( $i==ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2))>0 ) {
                    $pieces = 1;
                }
                else {
                    $pieces = 2;
                }

                $pickets[] = [
                    'height' => number_format($picket_height, 0, ',', '.'),
                    'number' => $pieces,
                ];
            }
        }

        // Panonka fence model.
        if ($fence_model_id == 5) {
            // Ugaona brzina.
            $omega = 360 / $article_width;
            // Fazno pomeranje za 90 stepeni.
            $teta = 90;
            for ($i = 1; $i <= ceil($pickets_number/2); $i++) {
                $picket_x_position = $space_between_pickets + $picket_width*($i-1) + $space_between_pickets*($i-1);
                $y = sin(deg2rad($omega*$picket_x_position - $teta));
                $picket_height = $article_height + ($heigth_leg / 2) + ($y * $heigth_leg )/2;

                if ($i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0) {
                    $pieces = 1;
                }
                else {
                    $pieces = 2;
                }

                $pickets[] = [
                    'height' => number_format($picket_height, 0, ',', '.'),
                    'number' => $pieces,
                ];
            }
        }

        return $pickets;
    }

    /**
     * Searches for Cutting Sheets based on a search term.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the search term from the request query parameters.
     * - Fetches cutting sheets matching the search term from the database.
     * - Loads the last cutting sheet for display.
     * - Prepares and passes all relevant data to the 'cutting/search.html.twig' template.
     * - Renders the template with the search results.
     *
     * @param Request $request
     *   The HTTP request containing the search term.
     *
     * @return Response
     *   The HTTP response with the rendered search results template or a redirect.
     */
    #[Route('/cuttings/search', name: 'cuttings_search')]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = $request->query->get('term', '');

        $cuttings = $this->entityManager->getRepository(CuttingSheet::class)->search($term);

        $last_cutting_sheet = $this->entityManager->getRepository(CuttingSheet::class)->getLastCuttingSheet();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'cuttings' => $cuttings,
            'last_cutting_sheet' => $last_cutting_sheet,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'cutting' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('cutting/search.html.twig', $data);
    }

    /**
     * Loads the application version from composer.json.
     *
     * @return string
     *   The app version, or 'unknown' if not found.
     */
    private function loadAppVersion(): string
    {
        $composerJsonPath = __DIR__ . '/../../composer.json';
        if (file_exists($composerJsonPath)) {
            $composerData = json_decode(file_get_contents($composerJsonPath), true);
            return $composerData['version'] ?? 'unknown';
        }
        return 'unknown';
    }

}
