<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\Order;
use App\Entity\OrderMaterial;
use App\Entity\OrderMaterialProperty;

/**
 * OrderController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class OrderController extends BaseController
{

    private $page = 'order';
    private $page_title = 'Narudžbenice';
    private $stylesheet = '/../libraries/';

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
        $orders = $this->entityManager->getRepository('\App\Entity\Order')->getLastOrders(10);
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'search' => $search,
            'materials' => $materials,
            'preferences' => $preferences,
            'orders' => $orders,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('index', $data);
    }

    /**
     * Form for adding a new order.
     *
     * @param $project_id
     *
     * @return void
     */
    public function addForm($project_id = NULL): void {
        $suppliers = $this->entityManager->getRepository('\App\Entity\Client')->findBy(array('is_supplier' => 1), array('name' => 'ASC') );
        $projects = $this->entityManager->getRepository('\App\Entity\Project')->findAll();

        $project_data = NULL;
        if (NULL != $project_id) {
            $project_data = $this->entityManager->find('\App\Entity\Project', $project_id);
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'suppliers' => $suppliers,
            'projects' => $projects,
            'project_data' => $project_data,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('add', $data);
    }

    /**
     * Add a new Order.
     *
     * @return void
     */
    public function add(): void {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $ordinal_num_in_year = 0;

        $supplier_id = htmlspecialchars($_POST["supplier_id"]);
        $supplier = $this->entityManager->find("\App\Entity\Client", $supplier_id);

        $title = htmlspecialchars($_POST["title"]);
        $note = htmlspecialchars($_POST["note"]);

        // Save a new order.
        $newOrder = new Order();

        $newOrder->setOrdinalNumInYear($ordinal_num_in_year);
        $newOrder->setSupplier($supplier);
        $newOrder->setTitle($title);
        $newOrder->setNote($note);
        $newOrder->setStatus(0);
        $newOrder->setIsArchived(0);

        $newOrder->setDate(new \DateTime("now"));
        $newOrder->setCreatedAt(new \DateTime("now"));
        $newOrder->setCreatedByUser($user);
        $newOrder->setModifiedAt(new \DateTime("0000-01-01 00:00:00"));

        $this->entityManager->persist($newOrder);
        $this->entityManager->flush();

        // Get id of last Order.
        $new_order_id = $newOrder->getId();

        // Set Ordinal Number In Year.
        $this->entityManager->getRepository('App\Entity\Order')->setOrdinalNumInYear($new_order_id);

        // If exist project in Order, then add $newOrder to table v6_projects_orders.
        if (NULL != $_POST["project_id"] ) {
            $project_id = htmlspecialchars($_POST["project_id"]);
            $project = $this->entityManager->find("\App\Entity\Project", $project_id);

            $project->getOrders()->add($newOrder);
            $this->entityManager->flush();
        }

        die('<script>location.href = "/order/' . $new_order_id . '" </script>');
    }

    /**
     * View order form.
     *
     * @param $order_id
     *
     * @return void
     */
    public function view($order_id): void {
        $order_data = $this->entityManager->find('\App\Entity\Order', $order_id);
        $project_data = $this->entityManager->getRepository('App\Entity\Order')->getProject($order_id);
        $supplier_data = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($order_data->getSupplier());
        $supplier_contacts = $supplier_data['contacts'];
        $materials = $this->entityManager->getRepository('\App\Entity\Material')->getSupplierMaterials($supplier_data['id']);
        $preferences = $this->entityManager->find('App\Entity\Preferences', 1);
        $materials_on_order = $this->entityManager->getRepository('\App\Entity\Order')->getMaterialsOnOrder($order_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'order_id' => $order_id,
            'order_data' => $order_data,
            'project_data' => $project_data,
            'supplier_data' => $supplier_data,
            'supplier_contacts' => $supplier_contacts,
            'materials' => $materials,
            'preferences' => $preferences,
            'materials_on_order' => $materials_on_order,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('view', $data);
    }

    /**
     * Edit Order form.
     *
     * @param $order_id
     *
     * @return void
     */
    public function editForm($order_id): void {
        $order_data = $this->entityManager->find('\App\Entity\Order', $order_id);
        $supplier_data = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($order_data->getSupplier());
        $project_data = $this->entityManager->getRepository('App\Entity\Order')->getProject($order_id);

        $materials = $this->entityManager->getRepository('\App\Entity\Material')->getSupplierMaterials($supplier_data['id']);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'order_id' => $order_id,
            'order_data' => $order_data,
            'supplier_data' => $supplier_data,
            'project_data' => $project_data,
            'materials' => $materials,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('edit', $data);
    }

    /**
     * Edit Order.
     *
     * @param $order_id
     *
     * @return void
     */
    public function edit($order_id): void {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);
        $order = $this->entityManager->find("\App\Entity\Order", $order_id);

        $title = htmlspecialchars($_POST["title"]);
        $status = htmlspecialchars($_POST["status"]);

        $is_archived = 0;
        if ( isset($_POST["is_archived"]) && $_POST["is_archived"] == 1 ) {
            $is_archived = htmlspecialchars($_POST["is_archived"]);
        }

        $note = htmlspecialchars($_POST["note"]);

        $order->setTitle($title);
        $order->setStatus($status);
        $order->setIsArchived($is_archived);
        $order->setNote($note);

        $order->setModifiedByUser($user);
        $order->setModifiedAt(new \DateTime("now"));

        $this->entityManager->flush();

        // Update order in project if project exist
        if (NULL != $_POST["project_id"]) {
            $old_project_id = $_POST["old_project_id"];
            $new_project_id = htmlspecialchars($_POST["project_id"]);

            $new_project = $this->entityManager->find("\App\Entity\Project", $new_project_id);
            $old_project = $this->entityManager->find("\App\Entity\Project", $old_project_id);

            if ($old_project_id != $new_project_id) {
                if ($old_project_id  != 0) {
                    // Delete order form old project.
                    $old_project->getOrders()->removeElement($order);
                }

                // Add order to new project.
                $new_project->getOrders()->add($order);
                $this->entityManager->flush();
            }
        }
        die('<script>location.href = "/order/' . $order_id . '" </script>');
    }

    /**
     * Delete Order.
     *
     * @param $order_id
     *
     * @return void
     */
    public function delete($order_id) {
        // Check if exist Order.
        if ($order = $this->entityManager->find("\App\Entity\Order", $order_id)) {

            // Check if exist Materials in Order.
            if ($order_materials = $this->entityManager->getRepository('\App\Entity\OrderMaterial')->getOrderMaterials($order_id)) {

                // Loop trough all materials
                foreach ($order_materials as $order_material) {

                    // Check if exist Properties in Order Material
                    if (
                      $order_material_properties = $this->entityManager
                        ->getRepository('\App\Entity\OrderMaterialProperty')
                        ->getOrderMaterialProperties($order_material->getId())
                    ) {
                        // Remove Properties.
                        foreach ($order_material_properties as $order_material_property) {
                            $orderMaterialProperty = $this->entityManager->find("\App\Entity\OrderMaterialProperty", $order_material_property->getId());
                            $this->entityManager->remove($orderMaterialProperty);
                            $this->entityManager->flush();
                        }
                    }

                    // Remove Material.
                    $this->entityManager->remove($order_material);
                    $this->entityManager->flush();
                }

            }

            // Remove Order
            $this->entityManager->remove($order);
            $this->entityManager->flush();

        }

        die('<script>location.href = "/orders/?search=" </script>');
    }

    /**
     * Add Material to Order.
     *
     * @param $order_id
     *
     * @return void
     */
    public function addMaterial($order_id): void {
        $order = $this->entityManager->find("\App\Entity\Order", $order_id);

        $material_id = htmlspecialchars($_POST["material_id"]);
        $material = $this->entityManager->find("\App\Entity\Material", $material_id);

        $price = $material->getPrice();
        $weight = $material->getWeight();

        $pieces = $_POST["pieces"] ? htmlspecialchars($_POST["pieces"]) : 0;

        $preferences = $this->entityManager->find('App\Entity\Preferences', 1);
        $tax = $preferences->getTax();

        $note = htmlspecialchars($_POST["note"]);

        $newOrderMaterial = new OrderMaterial();

        $newOrderMaterial->setOrder($order);
        $newOrderMaterial->setMaterial($material);
        $newOrderMaterial->setPieces($pieces);
        $newOrderMaterial->setPrice($price);
        $newOrderMaterial->setDiscount(0);
        $newOrderMaterial->setTax($tax);
        $newOrderMaterial->setWeight($weight);
        $newOrderMaterial->setNote($note);

        $this->entityManager->persist($newOrderMaterial);
        $this->entityManager->flush();

        // Last inserted order material.
        $last_order_material_id = $newOrderMaterial->getId();

        // Insert material properties in table v6_orders_materials_properties.
        $material_properties = $this->entityManager->getRepository('\App\Entity\MaterialProperty')->getMaterialProperties($material->getId());
        foreach ($material_properties as $material_property) {
            // Insert to table v6_orders_materials_properties.
            $newOrderMaterialProperty = new OrderMaterialProperty();
            $newOrderMaterialProperty->setOrderMaterial($newOrderMaterial);
            $newOrderMaterialProperty->setProperty($material_property->getProperty());
            $newOrderMaterialProperty->setQuantity(0);

            $this->entityManager->persist($newOrderMaterialProperty);
            $this->entityManager->flush();
        }

        die('<script>location.href = "/order/' . $order_id . '/edit" </script>');
    }

    /**
     * Edit Material in Order.
     *
     * @param $order_id
     * @param $order_material_id
     * @return void
     */
    public function editMaterial($order_id, $order_material_id): void {
        // Old material on Order.
        $old_material = $this->entityManager->find('\App\Entity\OrderMaterial', $order_material_id);
        $old_material_id = $old_material->getMaterial()->getId();

        // New material from form.
        $new_material_id = (int) htmlspecialchars($_POST["material_id"]);

        if ($old_material_id != $new_material_id) {
            $order_material = $this->entityManager->find('\App\Entity\OrderMaterial', $order_material_id);
            $new_material = $this->entityManager->find('\App\Entity\Material', $new_material_id);

            // Check if material_id in Order changed.
            if ($old_material_id != $new_material_id){
                // Remove the Properties of the old Material. (from table v6__order__materials__properties).
                if (
                  $order__material__properties = $this->entityManager
                    ->getRepository('\App\Entity\OrderMaterialProperty')
                    ->findBy(['order_material' => $order_material_id], [])
                ) {
                    foreach ($order__material__properties as $order__material__property) {
                        $orderMaterialProperty = $this->entityManager->find("\App\Entity\OrderMaterialProperty", $order__material__property->getId());
                        $this->entityManager->remove($orderMaterialProperty);
                        $this->entityManager->flush();
                    }
                }

                // Change Material from old to new.
                $order_material->setMaterial($new_material);
                $order_material->setPrice($new_material->getPrice());
                $order_material->setDiscount(0);
                $order_material->setNote("");
                $order_material->setPieces(1);
                $this->entityManager->flush();

                // insert Material properties in table v6__order__materials__properties
                $material_properties = $this->entityManager->getRepository('\App\Entity\MaterialProperty')->getMaterialProperties($new_material->getId());
                foreach ($material_properties as $material_property) {
                    // Insert to v6__order__materials__properties.
                    $newOrderMaterialProperty = new OrderMaterialProperty();

                    $newOrderMaterialProperty->setOrderMaterial($order_material);
                    $newOrderMaterialProperty->setProperty($material_property->getProperty());
                    $newOrderMaterialProperty->setQuantity(0);

                    $this->entityManager->persist($newOrderMaterialProperty);
                    $this->entityManager->flush();
                }
            }
        }
        else {

            $note = $_POST["note"] ?? $old_material->getNote();
            $note = htmlspecialchars($note, ENT_QUOTES, 'UTF-8');

            if (isset($_POST["pieces"]) && is_numeric($_POST["pieces"])) {
                $pieces = floor((float)$_POST["pieces"]);
            }
            else {
                $pieces = (int) $old_material->getPieces();
            }
            $pieces = max(0, $pieces);

            if (isset($_POST["price"])) {
                $sanitized_price = htmlspecialchars($_POST["price"]);
                $price = str_replace(",", ".", $sanitized_price);

                if (!is_numeric($price)) {
                    $price = $old_material->getPrice(); // Fallback to the old price
                }
            }
            else {
               $price = $old_material->getPrice();
            }

            if (isset($_POST["discount"])) {
                $sanitized_discount = htmlspecialchars($_POST["discount"]);
                $discount = str_replace(",", ".", $sanitized_discount);
                if (!is_numeric($discount)) {
                    $discount = $old_material->getDiscount();
                }
            }
            else {
               $discount = $old_material->getDiscount();
            }

            $orderMaterial = $this->entityManager->find("\App\Entity\OrderMaterial", $order_material_id);
            // $orderMaterial->setOrder($order);
            // $orderMaterial->setMaterial($material);
            $orderMaterial->setNote($note);
            $orderMaterial->setPieces($pieces);
            $orderMaterial->setPrice($price);
            $orderMaterial->setDiscount($discount);
            $this->entityManager->flush();

            // Properties update in table v6_orders_materials_properties.
            $order_material_properties = $this->entityManager->getRepository('\App\Entity\OrderMaterialProperty')->getOrderMaterialProperties($order_material_id);
            foreach ($order_material_properties as $order_material_property) {
                // Get property name from $order_material_property.
                $property_name = $order_material_property->getProperty()->getName();
                // Get property value from $_POST
                $property_value = str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));

                $orderMaterialProperty = $this->entityManager->find("\App\Entity\OrderMaterialProperty", $order_material_property->getId());

                $orderMaterialProperty->setQuantity($property_value);
                $this->entityManager->flush();
            }
        }

        die('<script>location.href = "/order/' . $order_id . '/edit" </script>');
    }

    /**
     * Edit Material form.
     *
     * @param $order_id
     * @param $order_material_id
     *
     * @return void
     */
    public function editMaterialForm($order_id, $order_material_id): void {
        $material_on_order_id = $order_material_id;
        $material_data = $this->entityManager->find('\App\Entity\OrderMaterial', $material_on_order_id);
        $order_data = $this->entityManager->find('\App\Entity\Order', $order_id);
        $supplier_id = $order_data->getSupplier()->getId();
        $materials_by_supplier = $this->entityManager->getRepository('\App\Entity\Material')->getSupplierMaterials($supplier_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'material_on_order_id' => $material_on_order_id,
            'material_data' => $material_data,
            'materials_by_supplier' => $materials_by_supplier,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('edit_material', $data);
    }

    /**
     * Delete Material from Order.
     *
     * @param $order_id
     * @param $order_material_id
     *
     * @return void
     */
    public function deleteMaterial($order_id, $order_material_id) {
        $order_material = $this->entityManager->find("\App\Entity\OrderMaterial", $order_material_id);

        // First remove properties from table v6_orders_materials_properties.
        if ($order_material_properties = $this->entityManager->getRepository('\App\Entity\OrderMaterialProperty')->getOrderMaterialProperties($order_material_id)) {
            foreach ($order_material_properties as $order_material_property) {
                $orderMaterialProperty = $this->entityManager->find("\App\Entity\OrderMaterialProperty", $order_material_property->getId());
                $this->entityManager->remove($orderMaterialProperty);
                $this->entityManager->flush();
            }
        }

        // Second remove materials from table v6_orders_materials.
        $this->entityManager->remove($order_material);
        $this->entityManager->flush();

        die('<script>location.href = "/order/' . $order_id . '" </script>');
    }

    /**
     * Duplicate Material on Order.
     *
     * @param $order_id
     * @param $order_material_id
     *
     * @return void
     */
    public function duplicateMaterial($order_id, $order_material_id): void {
        $orderMaterial = $this->entityManager->find("\App\Entity\OrderMaterial", $order_material_id);

        $newOrderMaterial = new OrderMaterial();

        $newOrderMaterial->setOrder($orderMaterial->getOrder());
        $newOrderMaterial->setMaterial($orderMaterial->getMaterial());
        $newOrderMaterial->setPieces($orderMaterial->getPieces());
        $newOrderMaterial->setPrice($orderMaterial->getPrice());
        $newOrderMaterial->setDiscount(0);
        $newOrderMaterial->setTax($orderMaterial->getTax());
        $newOrderMaterial->setWeight($orderMaterial->getWeight());
        $newOrderMaterial->setNote($orderMaterial->getNote());

        $this->entityManager->persist($newOrderMaterial);
        $this->entityManager->flush();

        // Get Properties from old OrderMaterial and add to newOrderMaterial.
        $material_on_order_properties = $this->entityManager->getRepository('\App\Entity\OrderMaterial')->getProperties($order_material_id);

        foreach ($material_on_order_properties as $material_on_order_property) {
            // Insert to table v6__orders__materials__properties.
            $newOrderMaterialProperty = new OrderMaterialProperty();

            $newOrderMaterialProperty->setOrderMaterial($newOrderMaterial);
            $newOrderMaterialProperty->setProperty($material_on_order_property->getProperty());
            $newOrderMaterialProperty->setQuantity(0);

            $this->entityManager->persist($newOrderMaterialProperty);
            $this->entityManager->flush();
        }

        die('<script>location.href = "/order/' . $order_id . '/edit" </script>');
    }

    /**
     * Print Order.
     *
     * @param $order_id
     *
     * @return void
     */
    public function print($order_id): void {
        $data = [
            'order_id' => $order_id,
            'entityManager' => $this->entityManager,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printOrder', $data);
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
        require_once __DIR__ . "/../Views/$this->page/$view.php";
    }

}
