<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Material;
use App\Entity\MaterialSupplier;
use App\Entity\MaterialProperty;
use App\Entity\Preferences;
use App\Entity\Property;
use App\Entity\Unit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * MaterialController class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class MaterialController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private string $page;
    private string $page_title;
    protected string $stylesheet;

    /**
     * MaterialController constructor.
     *
     * Initializes controller properties and loads the application version.
     *
     * @param EntityManagerInterface $entityManager
     *   The Doctrine entity manager for database operations.
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->page = 'materials';
        $this->page_title = 'Materijali';
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
    }

    /**
     * Displays the list of the latest materials.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the latest 10 materials from the database.
     * - Loads user preferences and prepares data for the template.
     * - Renders the 'material/index.html.twig' template with the materials and related data.
     *
     * @return Response
     *   The HTTP response with the rendered materials list or a redirect to the login page.
     */
    #[Route('/materials/', name: 'materials_index', methods: ['GET'])]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $materials = $this->entityManager->getRepository(Material::class)->getLastMaterials(10);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'materials' => $materials,
            'preferences' => $preferences,
            'tools_menu' => [
              'material' => FALSE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('material/index.html.twig', $data);
    }

    /**
     * Displays the form for adding a new material.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves all available units from the database.
     * - Prepares and passes data to the 'material/material_new.html.twig' template.
     * - Renders the template for creating a new material.
     *
     * @return Response
     *   The HTTP response with the rendered new material form or a redirect to the login page.
     */
    #[Route('/materials/new', name: 'material_new_form', methods: ['GET'])]
    public function new(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $units = $this->entityManager->getRepository(Unit::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'units' => $units,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'material' => FALSE,
            ],
        ];

        return $this->render('material/material_new.html.twig', $data);
    }

    /**
     * Handles the creation of a new material.
     *
     * - Starts a session and retrieves the current user.
     * - Validates the submitted form data for the new material.
     * - Checks for duplicate material names in the database.
     * - Creates and persists a new Material entity with the provided data.
     * - Redirects to the material details page upon successful creation.
     *
     * @return Response
     *   The HTTP response that redirects to the new material's details page or displays an error.
     */
    #[Route('/materials/create', name: 'material_create', methods: ['POST'])]
    public function create(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = htmlspecialchars($_POST['name']);
        }

        $unit_id = htmlspecialchars($_POST['unit_id']);
        $unit = $this->entityManager->find(Unit::class, $unit_id);
        $weight = $_POST['weight'] ? htmlspecialchars($_POST['weight']) : 0;
        $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;
        $min_obrac_mera = 0;
        $note = htmlspecialchars($_POST["note"]);

        // Check if name already exist in database.
        $control_name = $this->entityManager->getRepository(Material::class)->findBy( array('name' => $name) );

        if ($control_name) {
            echo "Username already exist in database. Please choose new username!";
            exit(1);
            // die('<script>location.href = "?alert&ob=2" </script>');
        }

        $newMaterial = new Material();

        $newMaterial->setName($name);
        $newMaterial->setUnit($unit);
        $newMaterial->setWeight($weight);
        $newMaterial->setPrice($price);
        $newMaterial->setMinCalcMeasure($min_obrac_mera);
        $newMaterial->setNote($note);
        $newMaterial->setCreatedAt(new \DateTime("now"));
        $newMaterial->setCreatedByUser($user);
        $newMaterial->setModifiedAt(new \DateTime("0000-01-01 00:00:00"));

        $this->entityManager->persist($newMaterial);
        $this->entityManager->flush();

        // Get last id and redirect.
        $new_id = $newMaterial->getId();

        return $this->redirectToRoute('materials_show', ['material_id' => $new_id]);
    }

    /**
     * Displays the details of a specific material.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the material, its suppliers, and properties from the database.
     * - Loads all suppliers and property definitions for selection.
     * - Prepares and passes all relevant data to the 'material/material_view.html.twig' template.
     * - Renders the template with the material details.
     *
     * @param int $material_id
     *   The ID of the material to display.
     *
     * @return Response
     *   The HTTP response with the rendered material details or a redirect to the login page.
     */
    #[Route('/materials/{material_id}', name: 'materials_show', requirements: ['material_id' => '\d+'], methods: ['GET'])]
    public function show(int $material_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $material = $this->entityManager->find(Material::class, $material_id);
        $material_suppliers = $this->entityManager
            ->getRepository(MaterialSupplier::class)->getMaterialSuppliers($material_id);
        $material_properties = $this->entityManager
            ->getRepository(MaterialProperty::class)->getMaterialProperties($material_id);
        $suppliers = $this->entityManager
            ->getRepository(Client::class)->findBy(['is_supplier' => 1], ['name' => 'ASC']);
        $property_list = $this->entityManager->getRepository(Property::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'material_id' => $material_id,
            'material' => $material,
            'material_suppliers' => $material_suppliers,
            'material_properties' => $material_properties,
            'suppliers' => $suppliers,
            'property_list' => $property_list,
            'tools_menu' => [
                'material' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('material/material_view.html.twig', $data);
    }

    /**
     * Displays the form for editing an existing material.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the material, its suppliers, properties, available units, and related data from the database.
     * - Prepares and passes all relevant data to the 'material/material_edit.html.twig' template.
     * - Renders the template for editing the material.
     *
     * @param int $material_id
     *   The ID of the material to edit.
     *
     * @return Response
     *   The HTTP response with the rendered edit material form or a redirect to the login page.
     */
    #[Route('/materials/{material_id}/edit', name: 'material_edit_form', methods: ['GET'])]
    public function edit($material_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $material = $this->entityManager->find(Material::class, $material_id);
        $material_suppliers = $this->entityManager
            ->getRepository(MaterialSupplier::class)->getMaterialSuppliers($material_id);
        $material_properties = $this->entityManager
            ->getRepository(MaterialProperty::class)->getMaterialProperties($material_id);
        $units = $this->entityManager->getRepository(Unit::class)->FindAll();
        $suppliers = $this->entityManager
            ->getRepository(Client::class)->findBy(['is_supplier' => 1], ['name' => 'ASC']);
        $property_list = $this->entityManager->getRepository(Property::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'material_id' => $material_id,
            'material' => $material,
            'material_suppliers' => $material_suppliers,
            'material_properties' => $material_properties,
            'units' => $units,
            'suppliers' => $suppliers,
            'property_list' => $property_list,
            'tools_menu' => [
                'material' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('material/material_edit.html.twig', $data);
    }

    /**
     * Handles updating an existing material.
     *
     * - Starts a session and retrieves the current user.
     * - Validates the submitted form data for the material.
     * - Updates the material's properties, unit, weight, price, and note.
     * - Sets the modified user and timestamp.
     * - Persists changes to the database.
     * - Redirects to the material details page upon successful update.
     *
     * @param int $material_id
     *   The ID of the material to update.
     *
     * @return Response
     *   The HTTP response that redirects to the updated material's details page or displays an error.
     */
    #[Route('/materials/{material_id}/update', name: 'material_update', methods: ['POST'])]
    public function update(int $material_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = htmlspecialchars($_POST['name']);
        }

        $unit_id = $_POST["unit_id"];
        $unit = $this->entityManager->find(Unit::class, $unit_id);

        $weight = htmlspecialchars($_POST['weight']);
        $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
        $note = htmlspecialchars($_POST['note']);

        $material = $this->entityManager->find(Material::class, $material_id);

        if ($material === null) {
            echo "Material with ID: $material_id, does not exist.\n";
            exit(1);
        }

        $material->setName($name);
        $material->setUnit($unit);
        $material->setWeight($weight);
        $material->setPrice($price);
        $material->setNote($note);

        $material->setModifiedByUser($user);

        $material->setModifiedAt(new \DateTime("now", new \DateTimeZone('GMT+2')));

        $this->entityManager->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Adds a supplier to a material.
     *
     * @param int $material_id
     *   The ID of the material to which the supplier will be added.
     *
     * Expects the following POST parameters:
     *   - supplier_id (int): The ID of the supplier (Client entity) to add. Required.
     *   - note (string): Optional note about the supplier/material relationship.
     *   - price (float, optional): Price value for the supplier/material relationship. Comma or dot as decimal separator.
     *
     * @return Response
     *   Redirects to the material details page after adding the supplier.
     *
     * @throws \Exception If required POST data is missing or invalid.
     */
    #[Route('/materials/{material_id}/add-supplier', name: 'material_add_supplier', methods: ['POST'])]
    public function addSupplier(int $material_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $material = $this->entityManager->find(Material::class, $material_id);

        $supplier_id = htmlspecialchars($_POST['supplier_id']);
        if ($supplier_id == "") die('<script>location.href = "?inc=alert&ob=4" </script>');
        $supplier = $this->entityManager->find(Client::class, $supplier_id);

        $note = htmlspecialchars($_POST['note']);

        $price = 0;
        if ($_POST['price']) {
            $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
        }

        $newMaterialSupplier = new MaterialSupplier();

        $newMaterialSupplier->setMaterial($material);
        $newMaterialSupplier->setSupplier($supplier);
        $newMaterialSupplier->setNote($note);
        $newMaterialSupplier->setPrice($price);
        $newMaterialSupplier->setCreatedAt(new \DateTime("now"));
        $newMaterialSupplier->setCreatedByUser($user);
        $newMaterialSupplier->setModifiedAt(new \DateTime("now"));

        $this->entityManager->persist($newMaterialSupplier);
        $this->entityManager->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Adds a property to a material.
     *
     * @param int $material_id
     *   The ID of the material to which the property will be added.
     *
     * Expects the following POST parameters:
     *   - property_item_id (int): The ID of the Property entity to add. Required.
     *   - min_size (string|float): The minimum size value for the property. Required.
     *   - max_size (string|float): The maximum size value for the property. Required.
     *
     * @return Response
     *   Redirects to the material details page after adding the property.
     *
     * @throws \Exception If required POST data is missing or invalid.
     */
    #[Route('/materials/{material_id}/add-property', name: 'material_add_property', methods: ['POST'])]
    public function addProperty(int $material_id): Response
    {
        $material = $this->entityManager->find(Material::class, $material_id);

        $property_item_id = htmlspecialchars($_POST['property_item_id']);
        $property = $this->entityManager->find(Property::class, $property_item_id);

        $min_size = htmlspecialchars($_POST['min_size']);
        $max_size = htmlspecialchars($_POST['max_size']);

        $newMaterialproperty = new MaterialProperty();

        $newMaterialproperty->setMaterial($material);
        $newMaterialproperty->setProperty($property);
        $newMaterialproperty->setMinSize($min_size);
        $newMaterialproperty->setMaxSize($max_size);

        $this->entityManager->persist($newMaterialproperty);
        $this->entityManager->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Edits the supplier information for a material.
     *
     * @param int $material_id The ID of the material whose supplier is being edited.
     * @param int $supplier_id The ID of the supplier (Client entity) to update.
     *
     * Expects the following POST parameters:
     *   - supplier_id (int): The new or existing supplier ID to associate. Required.
     *   - note (string): Optional note about the supplier/material relationship.
     *   - price (string|float, optional): Price value for the supplier/material relationship. Comma or dot as decimal separator.
     *   - material_supplier_id (int): The ID of the MaterialSupplier entity to update. Required.
     *
     * @return Response Redirects to the material details page after updating the supplier.
     *
     * @throws \Exception If required POST data is missing or invalid.
     */
    #[Route('/materials/{material_id}/suppliers/{supplier_id}/update', name: 'material_update_supplier', methods: ['POST'])]
    public function editSupplier(int $material_id, int $supplier_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $material = $this->entityManager->find(Material::class, $material_id);

        $supplier_id = htmlspecialchars($_POST["supplier_id"]);
        $supplier = $this->entityManager->find(Client::class, $supplier_id);

        $note = htmlspecialchars($_POST["note"]);
        $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;

        $material_supplier_id = htmlspecialchars($_POST["material_supplier_id"]);
        $material_supplier = $this->entityManager->find(MaterialSupplier::class, $material_supplier_id);

        $material_supplier->setMaterial($material);
        $material_supplier->setSupplier($supplier);
        $material_supplier->setNote($note);
        $material_supplier->setPrice($price);

        $material_supplier->setModifiedByUser($user);
        $material_supplier->setModifiedAt(new \DateTime("now"));

        $this->entityManager->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Deletes a supplier from a material.
     *
     * @param int $material_id
     *   The ID of the material from which the supplier will be removed.
     * @param int $supplier_id
     *   The ID of the MaterialSupplier entity to remove.
     *
     * @return Response
     *   Redirects to the material details page after deleting the supplier.
     *
     * @throws \Exception If the MaterialSupplier entity is not found or cannot be deleted.
     */
    #[Route('/materials/{material_id}/suppliers/{supplier_id}/delete', name: 'material_delete_supplier', methods: ['GET'])]
    public function deleteSupplier(int $material_id, int $supplier_id): Response
    {
        $material_supplier =  $this->entityManager->find(MaterialSupplier::class, $supplier_id);

        $this->entityManager->remove($material_supplier);
        $this->entityManager->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Deletes a property from a material.
     *
     * @param int $material_id
     *   The ID of the material from which the property will be removed.
     * @param int $property_id
     *   The ID of the MaterialProperty entity to remove.
     *
     * @return Response
     *   Redirects to the material details page after deleting the property.
     *
     * @throws \Exception
     *   If the MaterialProperty entity is not found or cannot be deleted.
     */
    #[Route('/materials/{material_id}/properties/{property_id}/delete', name: 'material_delete_property', methods: ['GET'])]
    public function deleteProperty(int $material_id, int $property_id): Response
    {
        $material_property = $this->entityManager->find(MaterialProperty::class, $property_id);

        $this->entityManager->remove($material_property);
        $this->entityManager->flush();;

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Searches for materials by a search term.
     *
     * @param Request $request
     *   The HTTP request object. Expects a 'term' query parameter for the search string.
     *
     * Query Parameters:
     *   - term (string, optional): The search term to filter materials by name or note. Defaults to an empty string (returns all materials).
     *
     * @return Response
     *   Renders the material search results page with the filtered materials and related data.
     */
    #[Route('/materials/search', name: 'materials_search')]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = $request->query->get('term', '');

        $materials= $this->entityManager->getRepository(Material::class)->search($term);
        $preferences = $this->entityManager->find(Preferences::class, 1);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'materials' => $materials,
            'preferences' => $preferences,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'material' => FALSE,
            ],
        ];

        return $this->render('material/search.html.twig', $data);
    }

}
