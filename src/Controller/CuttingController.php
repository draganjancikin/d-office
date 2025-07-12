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
use App\Entity\CuttingSheet;
use App\Entity\CuttingSheetArticle;
use App\Entity\FenceModel;
use App\Entity\Preferences;
use App\Entity\User;
use TCPDF;

/**
 * CuttingController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class CuttingController extends BaseController
{

    private $page = 'cuttings';
    private $page_title = 'Krojne liste';

    /**
     * Cutting home page.
     *
     * @param string|null $search
     *
     * @return void
     */
    public function index(string $search = NULL): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $cutting_sheets = $this->entityManager->getRepository(CuttingSheet::class)->getLastCuttingSheets(10);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'tools_menu' => [
                'cutting' => FALSE,
            ],
            'cutting_sheets' => $cutting_sheets,
            // 'username' => $this->username,
            // 'user_role_id' => $this->user_role_id,
            // 'entityManager' => $this->entityManager,
            // 'search' => $search,
        ];

        $this->render('cutting/index.html.twig', $data);
    }

    /**
     * Form for adding CuttingSheet.
     *
     * @param int|null $project_id
     * @param int|null $client_id
     *
     * @return void
     */
    public function formAdd(int $project_id = NULL, int $client_id = NULL): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $clients_list = $this->entityManager->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        if (isset($_GET['client_id'])) {
            $client_id = htmlspecialchars($_GET['client_id']);
            $client = $this->entityManager->find(Client::class, $client_id);
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'clients_list' => $clients_list,
            'client_id' => $client_id,
            'client' => $client ?? NULL,
        ];

        $this->render('cutting/add.html.twig', $data);
    }

    /**
     * Add CuttingSheet.
     *
     * @param int|null $project_id
     * @param int|null $client_id
     *
     * @return void
     */
    public function add(int $project_id = NULL, int $client_id = NULL): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

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

        die('<script>location.href = "/cutting/'.$new__cutting_sheet__id.'" </script>');
    }

    /**
     * View Cutting Sheet form.
     *
     * @param int $cutting_id
     *
     * @return void
     */
    public function view(int $cutting_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
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
        ];

      $this->render('cutting/view.html.twig', $data);
    }

    /**
     * Edit Cutting Sheet form.
     *
     * @param int $cutting_id
     *
     * @return void
     */
    public function edit(int $cutting_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
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
        ];

        $this->render('cutting/edit.html.twig', $data);
    }

    /**
     * Print Cutting Sheet.
     *
     * @param int $cutting_id
     *
     * @return void
     */
    public function print(int $cutting_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
            $article_mid_height = $article->getMidHeight();

            $picket_width = $article->getPicketWidth();
            $article_space = $article->getSpace();
            $article_field_number = $article->getNumberOfFields();

            // Izracunavanje broja letvica u zavisnosti od sirine polja.
            $pickets_number = $article_repo->getPicketsNumber($article->getId());

            // Real space between pickets.
            $space_between_pickets = $article_repo->getSpaceBetweenPickets($article_width, $pickets_number, $picket_width);

            // Legs of triangle for angle calculation.
            $heigth_leg = $article_repo->getDiffMinMax($article_height, $article_mid_height);
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
            $articles_data[$key]['space_between_pickets'] = $space_between_pickets;
            $articles_data[$key]['pickets_number'] = $pickets_number;
            $articles_data[$key]['pickets'] = $this->getPickets(
                $fence_model_id,
                $article_height,
                $pickets_number,
                $heigth_leg,
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
//            'entityManager' => $this->entityManager,
//            'cutting_id' => $cutting_id,
        ];

        // Render HTML content from a Twig template (or similar)
        ob_start();
        $this->render('cutting/print_cutting.html.twig', $data);
        $html = ob_get_clean();

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
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

        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Reset pointer to the last page.
        $pdf->lastPage();

        // Close and output PDF document to browser.
        $pdf->Output('rolostil_krojna_lista.pdf', 'I');
    }

    /**
     * Add Article to CuttingSheet.
     *
     * @param int $cutting_id
     *
     * @return void
     */
    public function addArticle(int $cutting_id): void
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

        die('<script>location.href = "/cutting/'.$cutting_id.'" </script>');
    }

    /**
     * Edit Article in CuttingSheet.
     *
     * @param int $cutting_id
     * @param int $article_id
     *
     * @return void
     */
    public function editArticle(int $cutting_id, int $article_id): void
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

        die('<script>location.href = "/cutting/' . $cutting_id . '" </script>');
    }

    /**
     * Delete Article from CuttingSheet.
     *
     * @param int $cutting_id
     * @param int $article_id
     *
     * @return void
     */
    public function deleteArticle(int $cutting_id, int $article_id): void
    {
        $cutting_sheet__article = $this->entityManager->find(CuttingSheetArticle::class, $article_id);

        $this->entityManager->remove($cutting_sheet__article);
        $this->entityManager->flush();

        die('<script>location.href = "/cutting/' . $cutting_id . '" </script>');
    }

    /**
     * Export to Accounting Document.
     *
     * @param int $cutting_id
     * @param $total_picket_lenght
     * @param $total_kap
     * @param $picket_width
     *
     * @return void
     */
    public function exportToAccountingDocument(
      int $cutting_id,
      $total_picket_lenght,
      $total_kap,
      $picket_width): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

        $cutting = $this->entityManager->find(CuttingSheet::class, $cutting_id);

        // Total length of pickets in cm.
        $total_picket_lenght = $total_picket_lenght * 100;

        $ordinal_num_in_year = 0;
        $title = "PVC letvice";
        $note = "ROLOSTIL szr je PDV obveznik.";

        $accounting_document__type_id = 1;
        $accounting_document__type = $this->entityManager->find(AccountingDocumentType::class,
          $accounting_document__type_id);

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

        die('<script>location.href = "/pidb/' . $newProforma_id.'" </script>');
    }

    /**
     * Delete CuttingSheet.
     *
     * @param int $cutting_id
     *
     * @return void
     */
    public function delete(int $cutting_id): void
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

        die('<script>location.href = "/cuttings/?search=" </script>');
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
   * @param string $term
   * @return void
   */
    public function search(string $term): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $cuttings = $this->entityManager->getRepository(CuttingSheet::class)->search($term);

        $last_cutting_sheet = $this->entityManager->getRepository(CuttingSheet::class)->getLastCuttingSheet();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'cuttings' => $cuttings,
            'last_cutting_sheet' => $last_cutting_sheet,
        ];

        $this->render('cutting/search.html.twig', $data);
    }

}
