<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\Material;
use App\Entity\MaterialSupplier;
use App\Entity\MaterialProperty;

/**
 * MaterialController class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class MaterialController extends BaseController
{

    /**
     * MaterialController constructor.
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
        $materials = $this->entityManager->getRepository('\App\Entity\Material')->getLastMaterials(10);
        $preferences = $this->entityManager->find('\App\Entity\Preferences', 1);
        $data = [
            'page_title' => 'Materijali',
            'stylesheet' => '../libraries/',
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'materials',
            'entityManager' => $this->entityManager,
            'search' => $search,
            'materials' => $materials,
            'preferences' => $preferences,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('index', $data);
    }

    /**
     * Form for adding a new material.
     *
     * @return void
     */
    public function addForm(): void {
        $units = $this->entityManager->getRepository('\App\Entity\Unit')->findAll();
        $data = [
            'page_title' => 'Materijali',
            'stylesheet' => '/../libraries/',
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'material',
            'units' => $units,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('add', $data);
    }

    /**
     * Add a new material.
     *
     * @return void
     */
    public function add(){
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = htmlspecialchars($_POST['name']);
        }

        $unit_id = htmlspecialchars($_POST['unit_id']);
        $unit = $this->entityManager->find("\App\Entity\Unit", $unit_id);
        $weight = $_POST['weight'] ? htmlspecialchars($_POST['weight']) : 0;
        $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;
        $min_obrac_mera = 0;
        $note = htmlspecialchars($_POST["note"]);

        // Check if name already exist in database.
        $control_name = $this->entityManager->getRepository('\App\Entity\Material')->findBy( array('name' => $name) );

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
    public function view($material_id): void {
        $material = $this->entityManager->find('\App\Entity\Material', $material_id);
        $material_suppliers = $this->entityManager->getRepository('\App\Entity\MaterialSupplier')->getMaterialSuppliers($material_id);
        $material_propertys = $this->entityManager->getRepository('\App\Entity\MaterialProperty')->getMaterialProperties($material_id);
        $suppliers = $this->entityManager->getRepository('\App\Entity\Client')->findBy(array('is_supplier' => 1), array('name' => 'ASC') );
        $property_list = $this->entityManager->getRepository('\App\Entity\Property')->findAll();

        $data = [
            'page_title' => 'Materijali',
            'stylesheet' => '/../libraries/',
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'material',
            'material_id' => $material_id,
            'material' => $material,
            'material_suppliers' => $material_suppliers,
            'material_propertys' => $material_propertys,
            'suppliers' => $suppliers,
            'property_list' => $property_list,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('view', $data);
    }

    /**
     * Edit Material form.
     *
     * @param $material_id
     *
     * @return void
     */
    public function editForm($material_id): void {
        $material = $this->entityManager->find('\App\Entity\Material', $material_id);
        $material_suppliers = $this->entityManager->getRepository('\App\Entity\MaterialSupplier')->getMaterialSuppliers($material_id);
        $material_properties = $this->entityManager->getRepository('\App\Entity\MaterialProperty')->getMaterialProperties($material_id);
        $units = $this->entityManager->getRepository('\App\Entity\Unit')->FindAll();
        $suppliers = $this->entityManager->getRepository('\App\Entity\Client')->findBy(array('is_supplier' => 1), array('name' => 'ASC') );
        $property_list = $this->entityManager->getRepository('\App\Entity\Property')->findAll();

        $data = [
            'page_title' => 'Materijali',
            'stylesheet' => '/../libraries/',
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'material',
            'entityManager' => $this->entityManager,
            'material_id' => $material_id,
            'material' => $material,
            'material_suppliers' => $material_suppliers,
            'material_properties' => $material_properties,
            'units' => $units,
            'suppliers' => $suppliers,
            'property_list' => $property_list,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('edit', $data);
    }

    /**
     * Edit Material form.
     *
     * @param $material_id
     *
     * @return void
     */
    public function edit($material_id): void {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = htmlspecialchars($_POST['name']);
        }

        $unit_id = $_POST["unit_id"];
        $unit = $this->entityManager->find("\App\Entity\Unit", $unit_id);

        $weight = htmlspecialchars($_POST['weight']);
        $price = str_replace(",", ".", htmlspecialchars($_POST['price']));
        $note = htmlspecialchars($_POST['note']);

        $material = $this->entityManager->find('\App\Entity\Material', $material_id);

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
    public function addSupplier($material_id) {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $material = $this->entityManager->find("\App\Entity\Material", $material_id);

        $supplier_id = htmlspecialchars($_POST['supplier_id']);
        if ($supplier_id == "") die('<script>location.href = "?inc=alert&ob=4" </script>');
        $supplier = $this->entityManager->find("\App\Entity\Client", $supplier_id);

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
    public function addProperty($material_id) {
        $material = $this->entityManager->find("\App\Entity\Material", $material_id);

        $property_item_id = htmlspecialchars($_POST['property_item_id']);
        $property = $this->entityManager->find("\App\Entity\Property", $property_item_id);

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
    public function editSupplier($material_id, $supplier_id) {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $material = $this->entityManager->find('\App\Entity\Material', $material_id);

        $supplier_id = htmlspecialchars($_POST["supplier_id"]);
        $supplier = $this->entityManager->find('\App\Entity\Client', $supplier_id);

        $note = htmlspecialchars($_POST["note"]);
        $price = $_POST['price'] ? str_replace(",", ".", htmlspecialchars($_POST['price'])) : 0;

        $material_supplier_id = htmlspecialchars($_POST["material_supplier_id"]);
        $material_supplier = $this->entityManager->find('\App\Entity\MaterialSupplier', $material_supplier_id);

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
    public function deleteSupplier($material_id, $supplier_id) {
        $material_supplier =  $this->entityManager->find("\App\Entity\MaterialSupplier", $supplier_id);

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
        $material_property = $this->entityManager->find("\App\Entity\MaterialProperty", $property_id);

        $this->entityManager->remove($material_property);
        $this->entityManager->flush();;

        die('<script>location.href = "/material/' . $material_id . '" </script>');
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
        require_once __DIR__ . "/../Views/material/$view.php";
    }

}
