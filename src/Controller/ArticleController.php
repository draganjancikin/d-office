<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\Article;
use App\Entity\ArticleGroup;
use App\Entity\ArticleProperty;
use App\Entity\Preferences;
use App\Entity\Property;
use App\Entity\Unit;
use App\Entity\User;

/**
 * ArticleController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ArticleController extends BaseController
{

    private $page;
    private $page_title;
    private $stylesheet;

    /**
     * ArticleController constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->page = 'article';
        $this->page_title = 'Proizvodi';
        $this->stylesheet = '/../libraries/';
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function index($search = NULL): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->findAll();

        $last_articles = $this->entityManager->getRepository(Article::class)->getLastArticles(15);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'search' => $search,
            'article_groups' => $article_groups,
            'last_articles' => $last_articles,
            'preferences' => $preferences,
            'tools_menu' => [
                'article' => FALSE,
                'group' => FALSE,
            ],
        ];

        $this->render('article/index.html.twig', $data);
    }

    /**
     * Form for adding a new article.
     *
     * @return void
     */
    public function addForm(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->getArticleGroups();
        $units = $this->entityManager->getRepository(Unit::class)->findBy(array(), array('name' => 'ASC'));

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'article_groups' => $article_groups,
            'units' => $units,
        ];

        $this->render('article/add.html.twig', $data);
    }

    /**
     * Add a new Article.
     *
     * @return void
     */
    public function add(): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

        $group_id = htmlspecialchars($_POST['group_id']);
        $group = $this->entityManager->find(ArticleGroup::class, $group_id);

        $name = htmlspecialchars($_POST['name']);
        if ($name == "") die('<script>location.href = "?inc=alert&ob=4" </script>');

        $unit_id = htmlspecialchars($_POST['unit_id']);
        $unit = $this->entityManager->find(Unit::class, $unit_id);

        $weight = $_POST['weight'] ? htmlspecialchars($_POST['weight']) : 0;
        $min_calc_measure = str_replace(",", ".", htmlspecialchars($_POST['min_calc_measure']));
        $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;
        $note = htmlspecialchars($_POST['note']);

        $newArticle = new Article();

        $newArticle->setGroup($group);
        $newArticle->setUnit($unit);
        $newArticle->setName($name);
        $newArticle->setWeight($weight);
        $newArticle->setMinCalcMeasure($min_calc_measure);
        $newArticle->setPrice($price);

        $newArticle->setNote($note);
        $newArticle->setCreatedAt(new \DateTime("now"));
        $newArticle->setCreatedByUser($user);
        $newArticle->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newArticle);
        $this->entityManager->flush();

        // Get last id and redirect.
        $new_article_id = $newArticle->getId();
        die('<script>location.href = "/article/' . $new_article_id . '" </script>');
    }

    /**
     * View article.
     *
     * @param int $article_id
     *
     * @return void
     */
    public function view(int $article_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $article_data = $this->entityManager->find(Article::class, $article_id);
        $article_properties = $this->entityManager->getRepository(ArticleProperty::class)->getArticleProperties($article_id);
        $property_list = $this->entityManager->getRepository(Property::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'article_id' => $article_id,
            'article_data' => $article_data,
            'article_properties' => $article_properties,
            'property_list' => $property_list,
            'tools_menu' => [
                'article' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
        ];

        $this->render('article/view.html.twig', $data);
    }

    /**
     * Edit Article form.
     *
     * @param int $article_id
     *
     * @return void
     */
    public function editForm(int $article_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $article_data = $this->entityManager->find(Article::class, $article_id);
        $article_properties = $this->entityManager->getRepository(ArticleProperty::class)->getArticleProperties($article_id);
        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->getArticleGroups();
        $units = $this->entityManager->getRepository(Unit::class)->findBy([], ['name' => 'ASC']);
        $property_list = $this->entityManager->getRepository(Property::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'article_id' => $article_id,
            'article_data' => $article_data,
            'article_properties' => $article_properties,
            'article_groups' => $article_groups,
            'units' => $units,
            'property_list' => $property_list,
            'tools_menu' => [
                'article' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
        ];

        $this->render('article/edit.html.twig', $data);
    }

    /**
     * Edit Article.
     *
     * @param int $article_id
     *
     * @return void
     */
    public function edit(int $article_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

        $group_id = htmlspecialchars($_POST['group_id']);
        $group = $this->entityManager->find(ArticleGroup::class, $group_id);

        $name = htmlspecialchars($_POST["name"]);

        $unit_id = htmlspecialchars($_POST["unit_id"]);
        $unit = $this->entityManager->find(Unit::class, $unit_id);

        $weight = 0;
        if ($_POST['weight']){
            $weight = htmlspecialchars($_POST['weight']);
        }

        $min_calc_measure = str_replace(",", ".", htmlspecialchars($_POST['min_calc_measure']));
        $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
        $note = htmlspecialchars($_POST["note"]);

        $article = $this->entityManager->find(Article::class, $article_id);

        if ($article === null) {
            echo "Article with ID $article_id does not exist.\n";
            exit(1);
        }

        $article->setGroup($group);
        $article->setName($name);
        $article->setunit($unit);
        $article->setWeight($weight);
        $article->setMinCalcMeasure($min_calc_measure);
        $article->setPrice($price);
        $article->setNote($note);
        $article->setModifiedByUser($user);
        $article->setModifiedAt(new \DateTime("now"));

        $this->entityManager->flush();

        die('<script>location.href = "/article/' . $article_id . '" </script>');
    }

    /**
     * Add property to article.
     *
     * @param int $article_id
     *
     * @return void
     */
    public function addProperty(int $article_id): void
    {
        $article = $this->entityManager->find(Article::class, $article_id);

        $property_id = htmlspecialchars($_POST['property_id']);
        $property = $this->entityManager->find(Property::class, $property_id);

        $min_size = 0;
        if (isset($_POST['min'])) {
            $min_size = trim(htmlspecialchars($_POST['min']));
        }

        $max_size = 0;
        if (isset($_POST['max'])) {
            $max_size = trim(htmlspecialchars($_POST['max']));
        }

        $newArticleProperty = new ArticleProperty();

        $newArticleProperty->setArticle($article);
        $newArticleProperty->setProperty($property);
        $newArticleProperty->setMinSize($min_size);
        $newArticleProperty->setMaxSize($max_size);

        $this->entityManager->persist($newArticleProperty);
        $this->entityManager->flush();

        die('<script>location.href = "/article/' . $article_id . '" </script>');
    }

    /**
     * Delete property from article.
     *
     * @param int $article_id
     * @param int $property_id
     *
     * @return void
     */
    public function deleteProperty(int $article_id, int $property_id): void
    {
        $article_property = $this->entityManager->find(ArticleProperty::class, $property_id);

        $this->entityManager->remove($article_property);
        $this->entityManager->flush();

        die('<script>location.href = "/article/' . $article_id . '" </script>');
    }

    /**
     * Groups list view.
     *
     * @return void
     */
    public function groups(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'article_groups' => $article_groups,
        ];

        $this->render('article/groups.html.twig', $data);
    }

    /**
     * Add article group form.
     *
     * @return void
     */
    public function addGroupForm(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
        ];

        $this->render('article/add_group.html.twig', $data);
    }

    /**
     * Add Article Group.
     *
     * @return void
     */
    public function addGroup(): void
    {
        $name = htmlspecialchars($_POST['name']);
        if ($name == "") die('<script>location.href = "?inc=alert&ob=4" </script>');

        $newArticleGroup = new \App\Entity\ArticleGroup();
        $newArticleGroup->setName($name);
        $this->entityManager->persist($newArticleGroup);
        $this->entityManager->flush();

        // Get last article group id and redirect.
        $new_article_group_id = $newArticleGroup->getId();

        die('<script>location.href = "/articles/group/' . $new_article_group_id . '" </script>');
    }

    /**
     * Group view.
     *
     * @param $group_id
     *
     * @return void
     */
    public function viewGroup($group_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $article_group_data = $this->entityManager->find(ArticleGroup::class, $group_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'group_id' => $group_id,
            'article_group_data' => $article_group_data,
            'tools_menu' => [
                'group' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
        ];

        $this->render('article/view_group.html.twig', $data);
    }

    /**
     * Edit Article Group form.
     *
     * @param int $group_id
     *
     * @return void
     */
    public function editGroupForm(int $group_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $article_group_data = $this->entityManager->find("\App\Entity\ArticleGroup", $group_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'group_id' => $group_id,
            'article_group_data' => $article_group_data,
            'tools_menu' => [
                'group' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
        ];

      $this->render('article/edit_group.html.twig', $data);
    }

    /**
     * Edit Article Group.
     *
     * @param int $group_id
     *
     * @return void
     */
    public function editGroup(int $group_id): void
    {
        $name = htmlspecialchars($_POST["name"]);
        $article_group = $this->entityManager->find(ArticleGroup::class, $group_id);

        if ($article_group === null) {
            echo "Article Group with ID $group_id does not exist.\n";
            exit(1);
        }

        $article_group->setName($name);
        $this->entityManager->flush();

        die('<script>location.href = "/articles/group/' . $group_id . '" </script>');
    }

    /**
     * Article price list.
     *
     * @param int $group_id
     *
     * @return void
     */
    public function priceList(int $group_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $group_id = htmlspecialchars($_GET['group_id']);
        $group = $this->entityManager->find(ArticleGroup::class, $group_id);
        $articles_by_group =  $this->entityManager->getRepository(Article::class)->getArticlesByGroup($group_id);
        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->findAll();
        $preferences = $this->entityManager->find(Preferences::class, 1);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'group' => $group,
            'articles_by_group' => $articles_by_group,
            'article_groups' => $article_groups,
            'preferences' => $preferences,
        ];

        $this->render('article/price_list.html.twig', $data);
    }

    /**
     * Search for articles.
     *
     * @param string $term
     *   Search term.
     *
     * @return void
     */
    public function search(string $term): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->findAll();

        $articles = $this->entityManager->getRepository(Article::class)->search($term);

        $preferences = $this->entityManager->find(Preferences::class, 1);

        $data = [
            'app_version' => APP_VERSION,
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'article_groups' => $article_groups,
            'articles' => $articles,
            'preferences' => $preferences,
        ];

        $this->render('article/search.html.twig', $data);
    }

}
