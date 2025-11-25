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

    private EntityManagerInterface $em;
    private string $page = 'materials';
    private string $pageTitle = 'Materijali';
    protected string $stylesheet;

    /**
     * MaterialController constructor.
     *
     * Initializes controller properties and loads the application version.
     *
     * @param EntityManagerInterface $em
     *   The Doctrine entity manager for database operations.
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
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
     * @return Response The HTTP response with the rendered materials list or a redirect to the login page.
     */
    #[Route('/materials/', name: 'materials_index', methods: ['GET'])]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $materials = $this->em->getRepository(Material::class)->getLastMaterials(10);
        $preferences = $this->em->find(Preferences::class, 1);

        $data = $this->getDefaultData();
        $data += [
            'materials' => $materials,
            'preferences' => $preferences,
            'tools_menu' => [
              'material' => FALSE,
            ],
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
     * @return Response The HTTP response with the rendered new material form or a redirect to the login page.
     */
    #[Route('/materials/new', name: 'material_new_form', methods: ['GET'])]
    public function new(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $units = $this->em->getRepository(Unit::class)->findAll();

        $data = $this->getDefaultData();
        $data += [
            'units' => $units,
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
     * @return Response The HTTP response that redirects to the new material's details page or displays an error.
     */
    #[Route('/materials/create', name: 'material_create', methods: ['POST'])]
    public function create(): Response
    {
        session_start();
        $user = $this->em->find(User::class, $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = htmlspecialchars($_POST['name']);
        }

        $unitId = htmlspecialchars($_POST['unit_id']);
        $unit = $this->em->find(Unit::class, $unitId);
        $weight = $_POST['weight'] ? htmlspecialchars($_POST['weight']) : 0;
        $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;
        $min_obrac_mera = 0;
        $note = htmlspecialchars($_POST["note"]);

        // Check if name already exist in database.
        $controlName = $this->em->getRepository(Material::class)->findBy( array('name' => $name) );

        if ($controlName) {
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

        $this->em->persist($newMaterial);
        $this->em->flush();

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
     * @param int $material_id The ID of the material to display.
     *
     * @return Response The HTTP response with the rendered material details or a redirect to the login page.
     */
    #[Route('/materials/{material_id}', name: 'materials_show', requirements: ['material_id' => '\d+'], methods: ['GET'])]
    public function show(int $material_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $material = $this->em->find(Material::class, $material_id);
        $materialSuppliers = $this->em->getRepository(MaterialSupplier::class)->getMaterialSuppliers($material_id);
        $materialProperties = $this->em->getRepository(MaterialProperty::class)->getMaterialProperties($material_id);
        $suppliers = $this->em->getRepository(Client::class)->findBy(['is_supplier' => 1], ['name' => 'ASC']);
        $propertyList = $this->em->getRepository(Property::class)->findAll();

        $data = $this->getDefaultData();
        $data += [
            'material_id' => $material_id,
            'material' => $material,
            'material_suppliers' => $materialSuppliers,
            'material_properties' => $materialProperties,
            'suppliers' => $suppliers,
            'property_list' => $propertyList,
            'tools_menu' => [
                'material' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
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
     * @param int $material_id The ID of the material to edit.
     *
     * @return Response The HTTP response with the rendered edit material form or a redirect to the login page.
     */
    #[Route('/materials/{material_id}/edit', name: 'material_edit_form', methods: ['GET'])]
    public function edit(int $material_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $material = $this->em->find(Material::class, $material_id);
        $materialSuppliers = $this->em->getRepository(MaterialSupplier::class)->getMaterialSuppliers($material_id);
        $materialProperties = $this->em->getRepository(MaterialProperty::class)->getMaterialProperties($material_id);
        $units = $this->em->getRepository(Unit::class)->FindAll();
        $suppliers = $this->em->getRepository(Client::class)->findBy(['is_supplier' => 1], ['name' => 'ASC']);
        $propertyList = $this->em->getRepository(Property::class)->findAll();

        $data = $this->getDefaultData();
        $data += [
            'material_id' => $material_id,
            'material' => $material,
            'material_suppliers' => $materialSuppliers,
            'material_properties' => $materialProperties,
            'units' => $units,
            'suppliers' => $suppliers,
            'property_list' => $propertyList,
            'tools_menu' => [
                'material' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
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
     * @param int $material_id The ID of the material to update.
     *
     * @return Response The HTTP response that redirects to the updated material's details page or displays an error.
     */
    #[Route('/materials/{material_id}/update', name: 'material_update', methods: ['POST'])]
    public function update(int $material_id): Response
    {
        session_start();
        $user = $this->em->find(User::class, $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = htmlspecialchars($_POST['name']);
        }

        $unitId = $_POST["unit_id"];
        $unit = $this->em->find(Unit::class, $unitId);

        $weight = htmlspecialchars($_POST['weight']);
        $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
        $note = htmlspecialchars($_POST['note']);

        $material = $this->em->find(Material::class, $material_id);

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

        $this->em->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Adds a supplier to a material.
     *
     * @param int $material_id The ID of the material to which the supplier will be added.
     *
     * Expects the following POST parameters:
     *   - supplier_id (int): The ID of the supplier (Client entity) to add. Required.
     *   - note (string): Optional note about the supplier/material relationship.
     *   - price (float, optional): Price value for the supplier/material relationship. Comma or dot as decimal separator.
     *
     * @return Response Redirects to the material details page after adding the supplier.
     *
     * @throws \Exception If required POST data is missing or invalid.
     */
    #[Route('/materials/{material_id}/add-supplier', name: 'material_add_supplier', methods: ['POST'])]
    public function addSupplier(int $material_id): Response
    {
        session_start();
        $user = $this->em->find(User::class, $_SESSION['user_id']);

        $material = $this->em->find(Material::class, $material_id);

        $supplierId = htmlspecialchars($_POST['supplier_id']);
        if ($supplierId == "") die('<script>location.href = "?inc=alert&ob=4" </script>');
        $supplier = $this->em->find(Client::class, $supplierId);

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

        $this->em->persist($newMaterialSupplier);
        $this->em->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Adds a property to a material.
     *
     * @param int $material_id The ID of the material to which the property will be added.
     *
     * Expects the following POST parameters:
     *   - property_item_id (int): The ID of the Property entity to add. Required.
     *   - min_size (string|float): The minimum size value for the property. Required.
     *   - max_size (string|float): The maximum size value for the property. Required.
     *
     * @return Response Redirects to the material details page after adding the property.
     *
     * @throws \Exception If required POST data is missing or invalid.
     */
    #[Route('/materials/{material_id}/add-property', name: 'material_add_property', methods: ['POST'])]
    public function addProperty(int $material_id): Response
    {
        $material = $this->em->find(Material::class, $material_id);

        $propertyItemId = htmlspecialchars($_POST['property_item_id']);
        $property = $this->em->find(Property::class, $propertyItemId);

        $minSize = htmlspecialchars($_POST['min_size']);
        $maxSize = htmlspecialchars($_POST['max_size']);

        $newMaterialproperty = new MaterialProperty();

        $newMaterialproperty->setMaterial($material);
        $newMaterialproperty->setProperty($property);
        $newMaterialproperty->setMinSize($minSize);
        $newMaterialproperty->setMaxSize($maxSize);

        $this->em->persist($newMaterialproperty);
        $this->em->flush();

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
        $user = $this->em->find(User::class, $_SESSION['user_id']);

        $material = $this->em->find(Material::class, $material_id);

        $supplierId = htmlspecialchars($_POST["supplier_id"]);
        $supplier = $this->em->find(Client::class, $supplierId);

        $note = htmlspecialchars($_POST["note"]);
        $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;

        $materialSupplierId = htmlspecialchars($_POST["material_supplier_id"]);
        $materialSupplier = $this->em->find(MaterialSupplier::class, $materialSupplierId);

        $materialSupplier->setMaterial($material);
        $materialSupplier->setSupplier($supplier);
        $materialSupplier->setNote($note);
        $materialSupplier->setPrice($price);

        $materialSupplier->setModifiedByUser($user);
        $materialSupplier->setModifiedAt(new \DateTime("now"));

        $this->em->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Deletes a supplier from a material.
     *
     * @param int $material_id The ID of the material from which the supplier will be removed.
     * @param int $supplier_id The ID of the MaterialSupplier entity to remove.
     *
     * @return Response Redirects to the material details page after deleting the supplier.
     *
     * @throws \Exception If the MaterialSupplier entity is not found or cannot be deleted.
     */
    #[Route('/materials/{material_id}/suppliers/{supplier_id}/delete', name: 'material_delete_supplier', methods: ['GET'])]
    public function deleteSupplier(int $material_id, int $supplier_id): Response
    {
        $materialSupplier =  $this->em->find(MaterialSupplier::class, $supplier_id);

        $this->em->remove($materialSupplier);
        $this->em->flush();

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Deletes a property from a material.
     *
     * @param int $material_id The ID of the material from which the property will be removed.
     * @param int $property_id The ID of the MaterialProperty entity to remove.
     *
     * @return Response Redirects to the material details page after deleting the property.
     *
     * @throws \Exception If the MaterialProperty entity is not found or cannot be deleted.
     */
    #[Route('/materials/{material_id}/properties/{property_id}/delete', name: 'material_delete_property', methods: ['GET'])]
    public function deleteProperty(int $material_id, int $property_id): Response
    {
        $materialProperty = $this->em->find(MaterialProperty::class, $property_id);

        $this->em->remove($materialProperty);
        $this->em->flush();;

        return $this->redirectToRoute('materials_show', ['material_id' => $material_id]);
    }

    /**
     * Searches for materials by a search term.
     *
     * @param Request $request The HTTP request object. Expects a 'term' query parameter for the search string.
     *
     * Query Parameters:
     *   - term (string, optional): The search term to filter materials by name or note. Defaults to an empty string (returns all materials).
     *
     * @return Response Renders the material search results page with the filtered materials and related data.
     */
    #[Route('/materials/search', name: 'materials_search')]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = $request->query->get('term', '');

        $materials= $this->em->getRepository(Material::class)->search($term);
        $preferences = $this->em->find(Preferences::class, 1);

        $data = $this->getDefaultData();
        $data += [
            'materials' => $materials,
            'preferences' => $preferences,
            'tools_menu' => [
                'material' => FALSE,
            ],
        ];

        return $this->render('material/search.html.twig', $data);
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
