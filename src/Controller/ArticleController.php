<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleGroup;
use App\Entity\ArticleProperty;
use App\Entity\Preferences;
use App\Entity\Property;
use App\Entity\Unit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Compiler\ResolveAutowireInlineAttributesPass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ArticleController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ArticleController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private string $page;
    private string $page_title;
    protected string $stylesheet;

    /**
     * ArticleController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->page = 'article';
        $this->page_title = 'Proizvodi';
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
    }

    /**
     * Displays the articles index page with the latest articles and article groups.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves all article groups and the 15 most recent articles from the database.
     * - Loads user preferences and prepares data for the template, including user role, username, and app version.
     * - Renders the 'article/index.html.twig' template with the articles, groups, and related data.
     *
     * @return Response
     *   The HTTP response with the rendered articles list or a redirect to the login page.
     */
    #[Route('/articles/', name: 'articles_index')]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->findAll();

        $last_articles = $this->entityManager->getRepository(Article::class)->getLastArticles(15);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'article_groups' => $article_groups,
            'last_articles' => $last_articles,
            'preferences' => $preferences,
            'tools_menu' => [
                'article' => FALSE,
                'group' => FALSE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('article/index.html.twig', $data);
    }

    /**
     * Displays the form for adding a new article.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves all article groups and units from the database, ordered by name.
     * - Prepares data for the template, including user role, username, and app version.
     * - Renders the 'article/article_new.html.twig' template with the form and related data.
     *
     * @return Response
     *   The HTTP response with the rendered new article form or a redirect to the login page.
     */
    #[Route('/articles/new', name: 'article_new', methods: ['GET'])]
    public function new(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->getArticleGroups();
        $units = $this->entityManager->getRepository(Unit::class)->findBy([], ['name' => 'ASC']);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'article_groups' => $article_groups,
            'units' => $units,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'article' => FALSE,
            'group' => FALSE,
            ],
        ];

        return $this->render('article/article_new.html.twig', $data);
    }

    /**
     * Handles the creation of a new article from POST data.
     *
     * - Starts a session and retrieves the current user from the session.
     * - Validates and sanitizes POST input for group, name, unit, weight, minimum calculation measure, price, and note.
     * - Creates a new Article entity, sets its properties, and persists it to the database.
     * - Sets creation and modification timestamps and user.
     * - Redirects to the article details page for the newly created article.
     *
     * @return Response
     *   Redirects to the article details page after successful creation, or halts on invalid input.
     */
    #[Route('/articles/create', name: 'article_create', methods: ['POST'])]
    public function create(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

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
        return $this->redirectToRoute('article_show', ['article_id' => $new_article_id]);
    }

    /**
     * Displays the details of a specific article.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the article entity by its ID.
     * - Fetches article properties and the full property list from the database.
     * - Prepares data for the template, including user role, username, and app version.
     * - Renders the 'article/article_view.html.twig' template with the article details and related data.
     *
     * @param int $article_id The ID of the article to display.
     *
     * @return Response The HTTP response with the rendered article details or a redirect to the login page.
     */
    #[Route('/articles/{article_id}', name: 'article_show', requirements: ['article_id' => '\d+'], methods: ['GET'])]
    public function show(int $article_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $article_data = $this->entityManager->find(Article::class, $article_id);
        $article_properties = $this->entityManager->getRepository(ArticleProperty::class)->getArticleProperties($article_id);
        $property_list = $this->entityManager->getRepository(Property::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'article_id' => $article_id,
            'article_data' => $article_data,
            'article_properties' => $article_properties,
            'property_list' => $property_list,
            'tools_menu' => [
                'article' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
                'group' => FALSE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('article/article_view.html.twig', $data);
    }

    /**
     * Displays the form for editing an existing article.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the article entity by its ID.
     * - Fetches article properties, all article groups, units (ordered by name), and the full property list from the database.
     * - Prepares data for the template, including user role, username, and app version.
     * - Renders the 'article/article_edit.html.twig' template with the article data and related information for editing.
     *
     * @param int $article_id
     *   The ID of the article to edit.
     *
     * @return Response
     *   The HTTP response with the rendered article edit form or a redirect to the login page.
     */
    #[Route('/articles/{article_id}/edit', name: 'article_edit_form', methods: ['GET'])]
    public function edit(int $article_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $article_data = $this->entityManager->find(Article::class, $article_id);
        $article_properties = $this->entityManager->getRepository(ArticleProperty::class)->getArticleProperties($article_id);
        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->getArticleGroups();
        $units = $this->entityManager->getRepository(Unit::class)->findBy([], ['name' => 'ASC']);
        $property_list = $this->entityManager->getRepository(Property::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
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
                'group' => FALSE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('article/article_edit.html.twig', $data);
    }

    /**
     * Handles updating an existing article with data from a POST request.
     *
     * - Starts a session and retrieves the current user from the session.
     * - Validates and sanitizes POST input for group, name, unit, weight, minimum calculation measure, price, and note.
     * - Finds the Article entity by its ID and updates its properties.
     * - Sets the modification timestamp and user.
     * - Persists the changes to the database.
     * - Redirects to the article details page after successful update, or halts if the article does not exist.
     *
     * @param int $article_id
     *   The ID of the article to update.
     *
     * @return Response
     *   Redirects to the article details page after update, or halts if the article does not exist.
     */
    #[Route('/articles/{article_id}/update', name: 'article_update', methods: ['POST'])]
    public function update(int $article_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

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

        return $this->redirectToRoute('article_show', ['article_id' => $article_id]);
    }

    /**
     * Adds a property to an article using POST data.
     *
     * - Finds the article entity by its ID.
     * - Retrieves and sanitizes the property ID, minimum size, and maximum size from POST data.
     * - Finds the property entity by its ID.
     * - Creates a new ArticleProperty entity, sets its associations and size limits.
     * - Persists the new ArticleProperty to the database.
     * - Redirects to the article details page after successful addition.
     *
     * @param int $article_id
     *   The ID of the article to which the property will be added.
     *
     * @return Response
     *   Redirects to the article details page after adding the property.
     */
    #[Route('/articles/{article_id}/add-property', name: 'article_add_property', methods: ['POST'])]
    public function addProperty(int $article_id): Response
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

        return $this->redirectToRoute('article_show', ['article_id' => $article_id]);
    }

    /**
     * Deletes a property from an article.
     *
     * - Finds the ArticleProperty entity by its property ID.
     * - Removes the ArticleProperty from the database.
     * - Persists the change.
     * - Redirects to the article details page after successful deletion.
     *
     * @param int $article_id
     *   The ID of the article from which the property will be deleted.
     * @param int $property_id
     *   The ID of the ArticleProperty to delete.
     *
     * @return Response
     *   Redirects to the article details page after deleting the property.
     */
    #[Route('/articles/{article_id}/properties/{property_id}/delete', name: 'article_delete_property', methods: ['GET'])]
    public function deleteProperty(int $article_id, int $property_id): Response
    {
        $article_property = $this->entityManager->find(ArticleProperty::class, $property_id);

        $this->entityManager->remove($article_property);
        $this->entityManager->flush();

        return $this->redirectToRoute('article_show', ['article_id' => $article_id]);
    }

    /**
     * Displays the list of all article groups.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves all article groups from the database.
     * - Prepares data for the template, including user role, username, and app version.
     * - Renders the 'article/groups.html.twig' template with the list of article groups and related data.
     *
     * @return Response
     *   The HTTP response with the rendered article groups list or a redirect to the login page.
     */
    #[Route('/groups', name: 'article_groups_index', methods: ['GET'])]
    public function groups(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'article_groups' => $article_groups,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'article' => FALSE,
                'group' => FALSE,
            ],
        ];

        return $this->render('article/groups.html.twig', $data);
    }

    /**
     * Displays the form for adding a new article group.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Prepares data for the template, including user role, username, and app version.
     * - Renders the 'article/group_new.html.twig' template with the form for creating a new article group.
     *
     * @return Response
     *   The HTTP response with the rendered new article group form or a redirect to the login page.
     */
    #[Route('/groups/new', name: 'article_group_new', methods: ['GET'])]
    public function newGroup(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
              'article' => FALSE,
              'group' => FALSE,
            ],
        ];

        return $this->render('article/group_new.html.twig', $data);
    }

    /**
     * Handles the creation of a new article group from POST data.
     *
     * - Validates and sanitizes the group name from POST input.
     * - Creates a new ArticleGroup entity and sets its name.
     * - Persists the new group to the database.
     * - Redirects to the article group details page after successful creation, or halts on invalid input.
     *
     * @return Response
     *   Redirects to the article group details page after creation, or halts on invalid input.
     */
    #[Route('/groups/create', name: 'article_group_create', methods: ['POST'])]
    public function createGroup(): Response
    {
        $name = htmlspecialchars($_POST['name']);
        if ($name == "") die('<script>location.href = "?inc=alert&ob=4" </script>');

        $newArticleGroup = new \App\Entity\ArticleGroup();
        $newArticleGroup->setName($name);
        $this->entityManager->persist($newArticleGroup);
        $this->entityManager->flush();

        // Get last article group id and redirect.
        $new_article_group_id = $newArticleGroup->getId();

        return $this->redirectToRoute('article_group_show', ['article_group_id' => $new_article_group_id]);
    }

    /**
     * Displays the details of a specific article group.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the article group entity by its ID.
     * - Prepares data for the template, including user role, username, and app version.
     * - Renders the 'article/group_view.html.twig' template with the group details and related data.
     *
     * @param int $group_id
     *   The ID of the article group to display.
     *
     * @return Response
     *   The HTTP response with the rendered article group details or a redirect to the login page.
     */
    #[Route('/groups/{group_id}', name: 'article_group_show', methods: ['GET'])]
    public function showGroup(int $group_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $article_group_data = $this->entityManager->find(ArticleGroup::class, $group_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'group_id' => $group_id,
            'article_group_data' => $article_group_data,
            'tools_menu' => [
                'article' => FALSE,
                'group' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('article/group_view.html.twig', $data);
    }

    /**
     * Displays the form for editing an existing article group.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the article group entity by its ID.
     * - Prepares data for the template, including user role, username, and app version.
     * - Renders the 'article/group_edit.html.twig' template with the group data for editing.
     *
     * @param int $group_id
     *   The ID of the article group to edit.
     *
     * @return Response
     *   The HTTP response with the rendered article group edit form or a redirect to the login page.
     */
    #[Route('/groups/{group_id}/edit', name: 'article_group_edit_form', methods: ['GET'])]
    public function editGroup(int $group_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $article_group_data = $this->entityManager->find(ArticleGroup::class, $group_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'group_id' => $group_id,
            'article_group_data' => $article_group_data,
            'tools_menu' => [
                'article' => FALSE,
                'group' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('article/group_edit.html.twig', $data);
    }

    /**
     * Handles updating an existing article group with data from a POST request.
     *
     * - Validates and sanitizes the group name from POST input.
     * - Finds the ArticleGroup entity by its ID and updates its name.
     * - Persists the changes to the database.
     * - Redirects to the article group details page after successful update, or halts if the group does not exist.
     *
     * @param int $group_id
     *   The ID of the article group to update.
     *
     * @return Response
     *   Redirects to the article group details page after update, or halts if the group does not exist.
     */
    #[Route('/groups/{group_id}/update', name: 'article_group_update', methods: ['POST'])]
    public function updateGroup(int $group_id): Response
    {
        $name = htmlspecialchars($_POST["name"]);
        $article_group = $this->entityManager->find(ArticleGroup::class, $group_id);

        if ($article_group === null) {
            echo "Article Group with ID $group_id does not exist.\n";
            exit(1);
        }

        $article_group->setName($name);
        $this->entityManager->flush();

        return $this->redirectToRoute('article_group_show', ['group_id' => $article_group->getId()]);
    }

    /**
     * Article price list.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/articles/price-list', name: 'article_price_list', methods: ['GET'])]
    public function priceList(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $group_id = $request->query->get('group_id', 0);
        $group = $this->entityManager->find(ArticleGroup::class, $group_id);
        $articles_by_group =  $this->entityManager->getRepository(Article::class)->getArticlesByGroup($group_id);
        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->findAll();
        $preferences = $this->entityManager->find(Preferences::class, 1);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'group' => $group,
            'articles_by_group' => $articles_by_group,
            'article_groups' => $article_groups,
            'preferences' => $preferences,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'article' => FALSE,
                'group' => FALSE,
            ],
        ];

        return $this->render('article/price_list.html.twig', $data);
    }

    /**
     * Search for articles.
     *
     * @param Request $request
     *   Search term.
     *
     * @return Response
     */
    #[Route('/articles/search', name: 'article_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $article_groups = $this->entityManager->getRepository(ArticleGroup::class)->findAll();

        $term = $request->query->get('term', '');
        $articles = $this->entityManager->getRepository(Article::class)->search($term);

        $preferences = $this->entityManager->find(Preferences::class, 1);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'article_groups' => $article_groups,
            'articles' => $articles,
            'preferences' => $preferences,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'article' => FALSE,
                'group' => FALSE,
            ],
        ];

        return $this->render('article/search.html.twig', $data);
    }

}
