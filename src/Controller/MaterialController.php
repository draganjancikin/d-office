<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\Client;
use App\Entity\Material;
use App\Entity\MaterialSupplier;
use App\Entity\MaterialProperty;
use App\Entity\Preferences;
use App\Entity\Property;
use App\Entity\Unit;
use App\Entity\User;

/**
 * MaterialController class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class MaterialController extends BaseController
{

    private string $page;
    private string $page_title;

    /**
     * MaterialController constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->page = 'materials';
        $this->page_title = 'Materijali';
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

        $materials = $this->entityManager->getRepository(Material::class)->getLastMaterials(10);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'search' => $search,
            'materials' => $materials,
            'preferences' => $preferences,
            'tools_menu' => [
              'material' => FALSE,
            ],
        ];

        $this->render('material/index.html.twig', $data);
    }

    /**
     * Form for adding a new material.
     *
     * @return void
     */
    public function addForm(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();
        $units = $this->entityManager->getRepository(Unit::class)->findAll();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'units' => $units,
        ];

        $this->render('material/add.html.twig', $data);
    }

    /**
     * Add a new material.
     *
     * @return void
     */
    public function add(): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

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
        die('<script>location.href = "/material/' . $new_id . '" </script>');
    }

    /**
     * View material form.
     *
     * @param $material_id
     *
     * @return void
     */
    public function view($material_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $material = $this->entityManager->find(Material::class, $material_id);
        $material_suppliers = $this->entityManager->getRepository(MaterialSupplier::class)->getMaterialSuppliers
        ($material_id);
        $material_properties = $this->entityManager->getRepository(MaterialProperty::class)->getMaterialProperties
        ($material_id);
        $suppliers = $this->entityManager->getRepository(Client::class)->findBy(array('is_supplier' => 1), array('name' =>
          'ASC') );
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
        ];

        $this->render('material/view.html.twig', $data);
    }

    /**
     * Edit Material form.
     *
     * @param $material_id
     *
     * @return void
     */
    public function editForm($material_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
        ];

        $this->render('material/edit.html.twig', $data);
    }

    /**
     * Edit Material form.
     *
     * @param $material_id
     *
     * @return void
     */
    public function edit($material_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

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

        die('<script>location.href = "/material/' . $material_id . '" </script>');
    }

    /**
     * Add supplier to material.
     *
     * @param $material_id
     *
     * @return void
     */
    public function addSupplier($material_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

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

        die('<script>location.href = "/material/' . $material_id . '" </script>');
    }

    /**
     * Add property to material.
     *
     * @param $material_id
     *
     * @return void
     */
    public function addProperty($material_id): void
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

        die('<script>location.href = "/material/'.$material_id.'" </script>');
    }

    /**
     * Edit supplier.
     *
     * @param $material_id
     * @param $supplier_id
     *
     * @return void
     */
    public function editSupplier($material_id, $supplier_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

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

        die('<script>location.href = "/material/' . $material_id . '" </script>');
    }

    /**
     * Delete supplier.
     *
     * @param $material_id
     * @param $supplier_id
     *
     * @return void
     */
    public function deleteSupplier($material_id, $supplier_id): void
    {
        $material_supplier =  $this->entityManager->find(MaterialSupplier::class, $supplier_id);

        $this->entityManager->remove($material_supplier);
        $this->entityManager->flush();

        die('<script>location.href = "/material/' . $material_id . '" </script>');
    }

    /**
     * Delete property from material.
     *
     * @param $material_id
     * @param $property_id
     *
     * @return void
     */
    public function deleteProperty($material_id, $property_id) {
        $material_property = $this->entityManager->find(MaterialProperty::class, $property_id);

        $this->entityManager->remove($material_property);
        $this->entityManager->flush();;

        die('<script>location.href = "/material/' . $material_id . '" </script>');
    }

    /**
     * Search for materials.
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

        $materials= $this->entityManager->getRepository(Material::class)->search($term);
        $preferences = $this->entityManager->find(Preferences::class, 1);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'materials' => $materials,
            'preferences' => $preferences,
        ];

        $this->render('material/search.html.twig', $data);
    }

}
