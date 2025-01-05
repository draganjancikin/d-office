<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\Article;
use App\Entity\ArticleProperty;

/**
 * ArticleController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ArticleController extends BaseController {

  private $page = 'article';
  private $page_title = 'Proizvodi';
  private $stylesheet = '/../libraries/';

  /**
   * ArticleController constructor.
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
    $article_groups = $this->entityManager->getRepository('\App\Entity\ArticleGroup')->findAll();
    $last_articles = $this->entityManager->getRepository('\App\Entity\Article')->getLastArticles(15);
    $preferences = $this->entityManager->find('\App\Entity\Preferences', 1);
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
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('index', $data);
  }

  /**
   * Form for adding a new article.
   *
   * @return void
   */
  public function addForm(): void {
    $article_groups = $this->entityManager->getRepository('\App\Entity\ArticleGroup')->getArticleGroups();
    $units = $this->entityManager->getRepository('\App\Entity\Unit')->findBy(array(), array('name' => 'ASC'));

    $data = [
      'page' => $this->page,
      'page_title' => $this->page_title,
      'stylesheet' => $this->stylesheet,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'article_groups' => $article_groups,
      'units' => $units,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('add', $data);
  }

  /**
   * Add a new Article.
   *
   * @return void
   */
  public function add(): void {

    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

    $group_id = htmlspecialchars($_POST['group_id']);
    $group = $this->entityManager->find("\App\Entity\ArticleGroup", $group_id);

    $name = htmlspecialchars($_POST['name']);
    if ($name == "") die('<script>location.href = "?inc=alert&ob=4" </script>');

    $unit_id = htmlspecialchars($_POST['unit_id']);
    $unit = $this->entityManager->find("\App\Entity\Unit", $unit_id);

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
   * @param $article_id
   *
   * @return void
   */
  public function view($article_id): void {
    $article_data = $this->entityManager->find("\App\Entity\Article", $article_id);
    $article_properties = $this->entityManager->getRepository('\App\Entity\ArticleProperty')->getArticleProperties($article_id);

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
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('view', $data);
  }

  /**
   * Edit Article form.
   *
   * @param $article_id
   *
   * @return void
   */
  public function editForm($article_id): void {
    $article_data = $this->entityManager->find("\App\Entity\Article", $article_id);
    $article_properties = $this->entityManager->getRepository('\App\Entity\ArticleProperty')->getArticleProperties($article_id);
    $article_groups = $this->entityManager->getRepository('\App\Entity\ArticleGroup')->getArticleGroups();
    $units = $this->entityManager->getRepository('\App\Entity\Unit')->findBy([], ['name' => 'ASC']);

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
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('edit', $data);
  }

  /**
   * Edit Article.
   *
   * @param $article_id
   *
   * @return void
   */
  public function edit($article_id): void {
    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

    $group_id = htmlspecialchars($_POST['group_id']);
    $group = $this->entityManager->find("\App\Entity\ArticleGroup", $group_id);

    $name = htmlspecialchars($_POST["name"]);

    $unit_id = htmlspecialchars($_POST["unit_id"]);
    $unit = $this->entityManager->find("\App\Entity\Unit", $unit_id);

    $weight = 0;
    if ($_POST['weight']){
      $weight = htmlspecialchars($_POST['weight']);
    }

    $min_calc_measure = str_replace(",", ".", htmlspecialchars($_POST['min_calc_measure']));
    $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
    $note = htmlspecialchars($_POST["note"]);

    $article = $this->entityManager->find('\App\Entity\Article', $article_id);

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
   * @param $article_id
   *
   * @return void
   */
  public function addProperty($article_id): void {
    $article = $this->entityManager->find("\App\Entity\Article", $article_id);

    $property_id = htmlspecialchars($_POST['property_id']);
    $property = $this->entityManager->find("\App\Entity\Property", $property_id);

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
   * @param $article_id
   * @param $property_id
   *
   * @return void
   */
  public function deleteProperty($article_id, $property_id): void {
    $article_property = $this->entityManager->find("\App\Entity\ArticleProperty", $property_id);

    $this->entityManager->remove($article_property);
    $this->entityManager->flush();

    die('<script>location.href = "/article/' . $article_id . '" </script>');
  }

  /**
   * Groups list view.
   *
   * @return void
   */
  public function groups(): void {
    $article_groups = $this->entityManager->getRepository('\App\Entity\ArticleGroup')->findAll();

    $data = [
      'page' => $this->page,
      'page_title' => $this->page_title,
      'stylesheet' => $this->stylesheet,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'entityManager' => $this->entityManager,
      'article_groups' => $article_groups,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('groups', $data);
  }

  /**
   * Add article group form.
   *
   * @return void
   */
  public function addGroupForm(): void {
    $data = [
      'page' => $this->page,
      'page_title' => $this->page_title,
      'stylesheet' => $this->stylesheet,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('add_group', $data);
  }

  /**
   * Add Article Group.
   *
   * @return void
   */
  public function addGroup(): void {
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
  public function viewGroup($group_id): void {
    $article_group_data = $this->entityManager->find("\App\Entity\ArticleGroup", $group_id);

    $data = [
      'page' => $this->page,
      'page_title' => $this->page_title,
      'stylesheet' => $this->stylesheet,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'group_id' => $group_id,
      'article_group_data' => $article_group_data,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('view_group', $data);
  }

  /**
   * Edit Article Group form.
   *
   * @param $group_id
   *
   * @return void
   */
  public function editGroupForm($group_id): void {
    $article_group_data = $this->entityManager->find("\App\Entity\ArticleGroup", $group_id);

    $data = [
      'page' => $this->page,
      'page_title' => $this->page_title,
      'stylesheet' => $this->stylesheet,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'group_id' => $group_id,
      'article_group_data' => $article_group_data,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('edit_group', $data);
  }

  /**
   * Edit Article Group.
   *
   * @param $group_id
   *
   * @return void
   */
  public function editGroup($group_id): void {
    $name = htmlspecialchars($_POST["name"]);
    $article_group = $this->entityManager->find('\App\Entity\ArticleGroup', $group_id);

    if ($article_group === null) {
      echo "Article Group with ID $group_id does not exist.\n";
      exit(1);
    }

    $article_group->setName($name);
    $this->entityManager->flush();

    die('<script>location.href = "/articles/group/' . $group_id . '" </script>');
  }

  /**
   * A helper method to render views.
   *
   * @param $view
   * @param $data
   *
   * @return void
   */
  private function render($view, $data = []): void {
    // Extract data array to variables.
    extract($data);
    // Include the view file.
    require_once __DIR__ . "/../Views/$page/$view.php";
  }

}
