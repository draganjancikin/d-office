<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\AccountingDocument;
use App\Entity\AccountingDocumentArticle;
use App\Entity\AccountingDocumentArticleProperty;
use App\Entity\CuttingSheet;
use App\Entity\CuttingSheetArticle;

/**
 * CuttingController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class CuttingController extends BaseController
{

    /**
     * Cutting home page.
     *
     * @param string|null $search
     *
     * @return void
     */
    public function index(string $search = NULL): void
    {
        $data = [
            'page_title' => 'Krojne liste',
            'stylesheet' => '../libraries/',
            // 'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'cuttings',
            'entityManager' => $this->entityManager,
            'search' => $search,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('index', $data);
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
        $clients_list = $this->entityManager->getRepository('\App\Entity\Client')->findBy(array(), array('name' => "ASC"));
        if ($client_id) {
            $client_data = $this->entityManager->find('\App\Entity\Client', $client_id);
        }

        $data = [
            'page_title' => 'Krojne liste',
            'stylesheet' => '/../libraries/',
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'cutting',
            'clients_list' => $clients_list,
            'client_id' => $client_id,
            'client_data' => $client_data ?? NULL,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('add', $data);
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
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $ordinal_num_in_year = 0;

        if (!$client_id) {
            $client_id = htmlspecialchars($_POST['client_id']);
        }
        $client = $this->entityManager->find("\App\Entity\Client", $client_id);

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
        $this->entityManager->getRepository('App\Entity\CuttingSheet')->setOrdinalNumInYear($new__cutting_sheet__id);

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
        $cutting_data = $this->entityManager->find('\App\Entity\CuttingSheet', $cutting_id);
        $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($cutting_data->getClient()->getId());

        $fence_models = $this->entityManager->getRepository('\App\Entity\FenceModel')->findBy(array(), array('name' => 'ASC'));

        $data = [
            'page_title' => 'Krojne liste',
            'stylesheet' => '/../libraries/',
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'cutting',
            'entityManager' => $this->entityManager,
            'cutting_id' => $cutting_id,
            'cutting_data' => $cutting_data,
            'client' => $client,
            'fence_models' => $fence_models,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('view', $data);
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
        $cutting_data = $this->entityManager->find('\App\Entity\CuttingSheet', $cutting_id);
        $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($cutting_data->getClient()->getId());

        $fence_models = $this->entityManager->getRepository('\App\Entity\FenceModel')->findBy(array(), array('name' => 'ASC'));

        $data = [
            'page_title' => 'Krojne liste',
            'stylesheet' => '/../libraries/',
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'cutting',
            'entityManager' => $this->entityManager,
            'client' => $client,
            'cutting_id' => $cutting_id,
            'cutting_data' => $cutting_data,
            'fence_models' => $fence_models,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('edit', $data);
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
        $data = [
            'entityManager' => $this->entityManager,
            'cutting_id' => $cutting_id,
        ];
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printCutting', $data);
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
        $cutting_sheet = $this->entityManager->find("\App\Entity\CuttingSheet", $cutting_id);

        $fence_model_id = htmlspecialchars($_POST['fence_model_id']);
        $fence_model = $this->entityManager->find("\App\Entity\FenceModel", $fence_model_id);

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
        $cutting_sheet__article = $this->entityManager->find("\App\Entity\CuttingSheetArticle", $article_id);

        $fence_model_id = htmlspecialchars($_POST['fence_model_id']);
        $fence_model = $this->entityManager->find("\App\Entity\FenceModel", $fence_model_id);

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
        $cutting_sheet__article = $this->entityManager->find("\App\Entity\CuttingSheetArticle", $article_id);

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
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $cutting = $this->entityManager->find("\App\Entity\CuttingSheet", $cutting_id);

        // Total length of pickets in cm.
        $total_picket_lenght = $total_picket_lenght / 10;

        $ordinal_num_in_year = 0;
        $title = "PVC letvice";
        $note = "ROLOSTIL szr je PDV obveznik.";

        $accounting_document__type_id = 1;
        $accounting_document__type = $this->entityManager->find("\App\Entity\AccountingDocumentType", $accounting_document__type_id);

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
        $this->entityManager->getRepository('App\Entity\AccountingDocument')->setOrdinalNumInYear($newProforma_id);

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

        $article_picket = $this->entityManager->find("\App\Entity\Article", $article_id);
        $note = "";
        $pieces = 1;
        $preferences = $this->entityManager->find('App\Entity\Preferences', 1);
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
        $article_properties = $this->entityManager->getRepository('\App\Entity\ArticleProperty')->getArticleProperties($article_picket->getId());
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
        $article_cap = $this->entityManager->find("\App\Entity\Article", $cap_article_id);

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
        if ($cs = $this->entityManager->find("\App\Entity\CuttingSheet", $cutting_id)) {

          // Check if exist Article in CuttingSheet.
          if ($cs_articles = $this->entityManager->getRepository('\App\Entity\CuttingSheetArticle')->getCuttingSheetArticles($cutting_id)) {

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
        require_once __DIR__ . "/../Views/cutting/$view.php";
    }

}
