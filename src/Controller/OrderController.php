<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\CompanyInfo;
use App\Entity\Material;
use App\Entity\MaterialProperty;
use App\Entity\Order;
use App\Entity\OrderMaterial;
use App\Entity\OrderMaterialProperty;
use App\Entity\Preferences;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TCPDF;

/**
 * OrderController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class OrderController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private string $page;
    private string $page_title;
    protected string $stylesheet;
    protected string $app_version;

    /**
     * OrderController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->page = 'order';
        $this->page_title = 'Narudžbenice';
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
        $this->app_version = $this->loadAppVersion();
    }

    /**
     * Displays the list of recent orders and related data.
     *
     * - Requires user to be logged in (checks $_SESSION['username']).
     * - Fetches the 10 most recent materials, preferences, and orders.
     * - For each order, collects ID, title, date, status, ordinal number, supplier name, archive status, and project (if any).
     * - Prepares order status classes for display.
     * - Passes all data to the 'order/index.html.twig' template for rendering.
     *
     * @return Response
     *   Renders the orders index page with all relevant data.
     */
    #[Route('/orders/', name: 'orders_index', methods: ['GET'])]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $materials = $this->entityManager->getRepository(Material::class)->getLastMaterials(10);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $orders = $this->entityManager->getRepository(Order::class)->getLastOrders(10);

        $orders_data = [];
        foreach ($orders as $order) {

            $order_data = [
                'id' => $order->getId(),
                'title' => $order->getTitle(),
                'date' => $order->getDate()->format('m_Y'),
                'status' => $order->getStatus(),
                'ordinal_num_in_year' => $order->getOrdinalNumInYear(),
                'supplier_name' => $order->getSupplier() ? $order->getSupplier()->getName() : '',
                'is_archived' => $order->getIsArchived(),
                'project' => 0,
            ];
            if ($project = $this->entityManager->getRepository(Order::class)->getProject($order->getId())){
                $order_data['project'] = $project;
            }
            $orders_data[] = $order_data;
        }

        $order_status_classes = [
            0 => [
                'class' => 'badge-light',
                'icon' => 'N',
                'title' => 'Nacrt',
            ],
            1 => [
                'class' => 'badge-warning',
                'icon' => 'P',
                'title' => 'Poručeno',
            ],
            2 => [
                'class' => 'badge-success',
                'icon' => 'S',
                'title' => 'Stiglo',
            ],
        ];

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'materials' => $materials,
            'preferences' => $preferences,
            'orders_data' => $orders_data,
            'tools_menu' => [
                'order' => FALSE,
            ],
            'order_status_classes' => $order_status_classes,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('order/index.html.twig', $data);
    }

    /**
     * Displays the form for adding a new order.
     *
     * @param int|null $project_id
     *   Optional project ID to pre-fill project data in the form.
     *
     * - Requires user to be logged in (checks $_SESSION['username']).
     * - Fetches all suppliers and projects for selection in the form.
     * - If a project_id is provided, fetches the corresponding project data.
     * - Passes all data to the 'order/order_new.html.twig' template for rendering.
     *
     * @return Response
     *   Renders the new order form page with all relevant data.
     */
    #[Route('/orders/new/{project_id?}', name: 'order_new_form', methods: ['GET'])]
    public function new(?int $project_id = NULL): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $suppliers = $this->entityManager->getRepository(Client::class)->findBy(['is_supplier' => 1], ['name' => 'ASC']);
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        if (isset($_GET['project_id'])) {
            $project_id = (int) htmlspecialchars($_GET['project_id']);
        }

        $project_data = NULL;
        if (NULL != $project_id) {
            $project_data = $this->entityManager->find(Project::class, $project_id);
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'suppliers' => $suppliers,
            'projects' => $projects,
            'project_data' => $project_data,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            "tools_menu" => [
                'order' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('order/order_new.html.twig', $data);
    }

    /**
     * Handles creation of a new order from POST data.
     *
     * - Requires user to be logged in (checks $_SESSION['user_id']).
     * - Expects the following POST parameters:
     *   - supplier_id (int): The ID of the supplier for the order. Required.
     *   - title (string): The title of the order. Required.
     *   - note (string): Optional note for the order.
     *   - project_id (int, optional): If provided, associates the order with a project.
     *
     * - Creates a new Order entity, sets its fields, and persists it to the database.
     * - Sets the ordinal number in year for the new order.
     * - If a project is specified, associates the order with the project.
     * - Redirects to the order details page after creation.
     *
     * @return Response
     *   Redirects to the order details page for the newly created order.
     */
    #[Route('/orders/create', name: 'order_create', methods: ['POST'])]
    public function create(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $ordinal_num_in_year = 0;

        $supplier_id = htmlspecialchars($_POST["supplier_id"]);
        $supplier = $this->entityManager->find(Client::class, $supplier_id);

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
        $this->entityManager->getRepository(Order::class)->setOrdinalNumInYear($new_order_id);

        // If exist project in Order, then add $newOrder to table v6_projects_orders.
        if (NULL != $_POST["project_id"] ) {
            $project_id = htmlspecialchars($_POST["project_id"]);
            $project = $this->entityManager->find(Project::class, $project_id);

            $project->getOrders()->add($newOrder);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('order_show', ['order_id' => $new_order_id]);
    }

    /**
     * Displays the details of a specific order.
     *
     * @param int $order_id
     *   The ID of the order to display.
     *
     * - Requires user to be logged in (checks $_SESSION['username']).
     * - Fetches the order, related project, supplier, supplier contacts, and materials.
     * - Retrieves preferences and currency exchange rate.
     * - Calculates order material data, tax base, tax amount, and totals in RSD and EUR.
     * - Passes all relevant data to the 'order/order_view.html.twig' template for rendering.
     *
     * @return Response
     *   Renders the order details page with all relevant data.
     */
    #[Route('/orders/{order_id}', name: 'order_show', requirements: ['order_id' => '\d+'], methods: ['GET'])]
    public function show(int $order_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $order_data = $this->entityManager->find(Order::class, $order_id);

        $project_data = $this->entityManager->getRepository(Order::class)->getProject($order_id);
        $supplier_data = $this->entityManager->getRepository(Client::class)->getClientData($order_data->getSupplier());
        $supplier_contacts = $supplier_data['contacts'];
        $materials = $this->entityManager->getRepository(Material::class)->getSupplierMaterials($supplier_data['id']);
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $materials_on_order_data = $this->getOrderMaterialsData($order_id);

        $total_tax_base_rsd = $this->getOrderTotalTaxBaseRSD($order_id);
        $total_tax_amount_rsd = $this->getOrderTotalTaxAmountRSD($order_id);

        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'order_id' => $order_id,
            'order_data' => $order_data,
            'project_data' => $project_data,
            'supplier_data' => $supplier_data,
            'supplier_contacts' => $supplier_contacts,
            'materials' => $materials,
            'preferences' => $preferences,
            'materials_on_order_data' => $materials_on_order_data,
            'tools_menu' => [
                'order' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
            'total_tax_base_rsd' => $total_tax_base_rsd,
            'total_tax_amount_rsd' => $total_tax_amount_rsd,
            'total_rsd' => $total_rsd,
            'total_eur' => $total_rsd / $kurs,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('order/order_view.html.twig', $data);
    }

    /**
     * Displays the form for editing an existing order.
     *
     * @param int $order_id
     *   The ID of the order to edit.
     *
     * - Requires user to be logged in (checks $_SESSION['username']).
     * - Fetches the order, related supplier, supplier contacts, project, and materials.
     * - Retrieves preferences and currency exchange rate.
     * - Calculates order material data, tax base, tax amount, and totals in RSD and EUR.
     * - Fetches the list of all active projects for selection.
     * - Passes all relevant data to the 'order/order_edit.html.twig' template for rendering.
     *
     * @return Response
     *   Renders the order edit form page with all relevant data.
     */
    #[Route('/orders/{order_id}/edit', name: 'order_edit_form', methods: ['GET'])]
    public function edit(int $order_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $order_data = $this->entityManager->find(Order::class, $order_id);
        $supplier_data = $this->entityManager->getRepository(Client::class)->getClientData($order_data->getSupplier());
        $supplier_contacts = $supplier_data['contacts'];
        $project_data = $this->entityManager->getRepository(Order::class)->getProject($order_id);

        $materials = $this->entityManager->getRepository(Material::class)->getSupplierMaterials($supplier_data['id']);

        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $materials_on_order_data = $this->getOrderMaterialsData($order_id);

        $total_tax_base_rsd = $this->getOrderTotalTaxBaseRSD($order_id);
        $total_tax_amount_rsd = $this->getOrderTotalTaxAmountRSD($order_id);

        $total_rsd = $total_tax_base_rsd + $total_tax_amount_rsd;

        $project_list = $this->entityManager->getRepository(Project::class)->getAllActiveProjects();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'order_id' => $order_id,
            'order_data' => $order_data,
            'supplier_data' => $supplier_data,
            'supplier_contacts' => $supplier_contacts,
            'project_data' => $project_data,
            'materials' => $materials,
            'preferences' => $preferences,
            'materials_on_order_data' => $materials_on_order_data,
            'tools_menu' => [
                'order' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
            'total_tax_base_rsd' => $total_tax_base_rsd,
            'total_tax_amount_rsd' => $total_tax_amount_rsd,
            'total_rsd' => $total_rsd,
            'total_eur' => $total_rsd / $kurs,
            'project_list' => $project_list,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('order/order_edit.html.twig', $data);
    }

    /**
     * Updates an existing order with data from a POST request.
     *
     * @param int $order_id
     *   The ID of the order to update.
     *
     * - Requires user to be logged in (checks $_SESSION['user_id']).
     * - Expects the following POST parameters:
     *   - title (string): The updated title of the order. Required.
     *   - status (int): The updated status of the order. Required.
     *   - is_archived (int, optional): Whether the order is archived (1 or 0).
     *   - note (string): Optional note for the order.
     *   - project_id (int, optional): The new project ID to associate with the order.
     *   - old_project_id (int, optional): The previous project ID for project reassignment logic.
     *
     * - Updates the order entity and persists changes to the database.
     * - If the project association changes, updates the order's project relationship accordingly.
     * - Redirects to the order details page after updating.
     *
     * @return Response
     *   Redirects to the order details page for the updated order.
     */
    #[Route('/orders/{order_id}/update', name: 'order_update', methods: ['POST'])]
    public function update(int $order_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);
        $order = $this->entityManager->find(Order::class, $order_id);

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

            $new_project = $this->entityManager->find(Project::class, $new_project_id);
            $old_project = $this->entityManager->find(Project::class, $old_project_id);

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

        return $this->redirectToRoute('order_show', ['order_id' => $order_id]);
    }

    /**
     * Deletes an order and all its associated materials and material properties.
     *
     * - Checks if the order exists for the given order_id.
     * - If the order has materials, removes all associated material properties and materials from the database.
     * - Removes the order itself from the database.
     * - Redirects to the order search page after deletion.
     *
     * @param int $order_id
     *   The ID of the order to delete.
     *
     * @return Response
     *   Redirects to the order search page after deletion.
     */
    #[Route('/orders/{order_id}/delete', name: 'order_delete', methods: ['GET'])]
    public function delete(int $order_id): Response
    {
        // Check if exist Order.
        if ($order = $this->entityManager->find(Order::class, $order_id)) {

            // Check if exist Materials in Order.
            if ($order_materials = $this->entityManager->getRepository(OrderMaterial::class)->getOrderMaterials($order_id)) {

                // Loop trough all materials.
                foreach ($order_materials as $order_material) {

                    // Check if exist Properties in Order Material.
                    if (
                      $order_material_properties = $this->entityManager
                        ->getRepository(OrderMaterialProperty::class)
                        ->getOrderMaterialProperties($order_material->getId())
                    ) {
                        // Remove Properties.
                        foreach ($order_material_properties as $order_material_property) {
                            $orderMaterialProperty = $this->entityManager
                                ->find(OrderMaterialProperty::class, $order_material_property->getId());
                            $this->entityManager->remove($orderMaterialProperty);
                            $this->entityManager->flush();
                        }
                    }

                    // Remove Material.
                    $this->entityManager->remove($order_material);
                    $this->entityManager->flush();
                }

            }

            // Remove Order.
            $this->entityManager->remove($order);
            $this->entityManager->flush();

        }

        return $this->redirectToRoute('order_search', ['term' => '']);
    }

    /**
     * Adds a material to an order.
     *
     * @param int $order_id
     *   The ID of the order to which the material will be added.
     *
     * Expects the following POST parameters:
     *   - material_id (int): The ID of the material to add. Required.
     *   - pieces (int, optional): The number of pieces to add. Defaults to 0 if not provided.
     *   - note (string, optional): An optional note for the order material.
     *
     * - Fetches the order and material entities.
     * - Uses the material's price and weight, and the default tax from preferences.
     * - Creates a new OrderMaterial entity and persists it.
     * - For each property of the material, creates a corresponding OrderMaterialProperty with quantity 0.
     * - Redirects to the order edit form after adding the material.
     *
     * @return Response
     *   Redirects to the order edit form for the order.
     */
    #[Route('/orders/{order_id}/add-material', name: 'order_add_material', methods: ['POST'])]
    public function addMaterial(int $order_id): Response
    {
        $order = $this->entityManager->find(Order::class, $order_id);

        $material_id = htmlspecialchars($_POST["material_id"]);
        $material = $this->entityManager->find(Material::class, $material_id);

        $price = $material->getPrice();
        $weight = $material->getWeight();

        $pieces = $_POST["pieces"] ? htmlspecialchars($_POST["pieces"]) : 0;

        $preferences = $this->entityManager->find(Preferences::class, 1);
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
        $material_properties = $this->entityManager
            ->getRepository(MaterialProperty::class)->getMaterialProperties($material->getId());
        foreach ($material_properties as $material_property) {
            // Insert to table v6_orders_materials_properties.
            $newOrderMaterialProperty = new OrderMaterialProperty();
            $newOrderMaterialProperty->setOrderMaterial($newOrderMaterial);
            $newOrderMaterialProperty->setProperty($material_property->getProperty());
            $newOrderMaterialProperty->setQuantity(0);

            $this->entityManager->persist($newOrderMaterialProperty);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('order_edit_form', ['order_id' => $order_id]);
    }

    /**
     * Updates a material in an order with new data from a POST request.
     *
     * @param int $order_id
     *   The ID of the order containing the material.
     * @param int $order_material_id
     *   The ID of the material in the order to update.
     *
     * Expects the following POST parameters:
     *   - material_id (int): The new material ID to set (if changed).
     *   - note (string, optional): Note for the order material.
     *   - pieces (int, optional): Number of pieces.
     *   - price (float|string, optional): Price for the material.
     *   - discount (float|string, optional): Discount for the material.
     *   - [property_name] (float|string, optional): For each property, the new quantity value.
     *
     * - If the material is changed, removes old properties, sets new material, and adds new properties with quantity 0.
     * - If the material is not changed, updates note, pieces, price, discount, and property quantities.
     * - Redirects to the order edit form after updating.
     *
     * @return Response
     *   Redirects to the order edit form for the order.
     */
    #[Route('/orders/{order_id}/materials/{order_material_id}/update', name: 'order_material_update', methods:
      ['POST'])]
    public function updateMaterial(int $order_id, int $order_material_id): Response
    {
        // Old material on Order.
        $old_material = $this->entityManager->find(OrderMaterial::class, $order_material_id);
        $old_material_id = $old_material->getMaterial()->getId();

        // New material from form.
        $new_material_id = (int) htmlspecialchars($_POST["material_id"]);

        if ($old_material_id != $new_material_id) {
            $order_material = $this->entityManager->find(OrderMaterial::class, $order_material_id);
            $new_material = $this->entityManager->find(Material::class, $new_material_id);

            // Check if material_id in Order changed.
            if ($old_material_id != $new_material_id){
                // Remove the Properties of the old Material. (from table v6__order__materials__properties).
                if (
                  $order__material__properties = $this->entityManager
                    ->getRepository(OrderMaterialProperty::class)
                    ->findBy(['order_material' => $order_material_id], [])
                ) {
                    foreach ($order__material__properties as $order__material__property) {
                        $orderMaterialProperty = $this->entityManager
                            ->find(OrderMaterialProperty::class, $order__material__property->getId());
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
                $material_properties = $this->entityManager
                    ->getRepository(MaterialProperty::class)
                    ->getMaterialProperties($new_material->getId());
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

            $orderMaterial = $this->entityManager->find(OrderMaterial::class, $order_material_id);
            // $orderMaterial->setOrder($order);
            // $orderMaterial->setMaterial($material);
            $orderMaterial->setNote($note);
            $orderMaterial->setPieces($pieces);
            $orderMaterial->setPrice($price);
            $orderMaterial->setDiscount($discount);
            $this->entityManager->flush();

            // Properties update in table v6_orders_materials_properties.
            $order_material_properties = $this->entityManager
                ->getRepository(OrderMaterialProperty::class)
                ->getOrderMaterialProperties($order_material_id);
            foreach ($order_material_properties as $order_material_property) {
                // Get property name from $order_material_property.
                $property_name = $order_material_property->getProperty()->getName();
                // Get property value from $_POST
                $property_value = str_replace(",", ".", htmlspecialchars($_POST["$property_name"]));

                $orderMaterialProperty = $this->entityManager
                    ->find(OrderMaterialProperty::class, $order_material_property->getId());

                $orderMaterialProperty->setQuantity($property_value);
                $this->entityManager->flush();
            }
        }

        return $this->redirectToRoute('order_edit_form', ['order_id' => $order_id]);
    }

    /**
     * Displays the form for changing a material in an order.
     *
     * @param int $order_id
     *   The ID of the order containing the material.
     * @param int $order_material_id
     *   The ID of the material in the order to change.
     *
     * - Requires user to be logged in (checks $_SESSION['username']).
     * - Fetches the order, the specific material in the order, and all materials available from the order's supplier.
     * - Passes all relevant data to the 'order/material_in_order_change.html.twig' template for rendering.
     *
     * @return Response
     *   Renders the material change form for the order.
     */
    #[Route('/orders/{order_id}/material/{order_material_id}/change', name: 'order_material_change_form', methods:
      ['GET'])]
    public function changeMaterial(int $order_id, int $order_material_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $material_on_order_id = $order_material_id;
        $material_data = $this->entityManager->find(OrderMaterial::class, $material_on_order_id);
        $order_data = $this->entityManager->find(Order::class, $order_id);
        $supplier_id = $order_data->getSupplier()->getId();
        $materials_by_supplier = $this->entityManager->getRepository(Material::class)->getSupplierMaterials($supplier_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'material_on_order_id' => $material_on_order_id,
            'material_data' => $material_data,
            'materials_by_supplier' => $materials_by_supplier,
            'order_data' => $order_data,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'order' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('order/material_in_order_change.html.twig', $data);
    }

    /**
     * Deletes a material from an order, including its associated properties.
     *
     * @param int $order_id
     *   The ID of the order from which the material will be deleted.
     * @param int $order_material_id
     *   The ID of the material in the order to delete.
     *
     * - Removes all properties associated with the order material from the database.
     * - Removes the order material itself from the database.
     * - Redirects to the order edit form after deletion.
     *
     * @return Response
     *   Redirects to the order edit form for the order.
     */
    #[Route('/orders/{order_id}/materials/{order_material_id}/delete', name: 'order_material_delete', methods:
      ['GET'])]
    public function deleteMaterial(int $order_id, int $order_material_id): Response
    {
        $order_material = $this->entityManager->find(OrderMaterial::class, $order_material_id);

        // First remove properties from table v6_orders_materials_properties.
        if ($order_material_properties = $this->entityManager
            ->getRepository(OrderMaterialProperty::class)
            ->getOrderMaterialProperties($order_material_id)) {
            foreach ($order_material_properties as $order_material_property) {
                $orderMaterialProperty = $this->entityManager
                    ->find(OrderMaterialProperty::class, $order_material_property->getId());
                $this->entityManager->remove($orderMaterialProperty);
                $this->entityManager->flush();
            }
        }

        // Second remove materials from table v6_orders_materials.
        $this->entityManager->remove($order_material);
        $this->entityManager->flush();

        return $this->redirectToRoute('order_edit_form', ['order_id' => $order_id]);
    }

    /**
     * Duplicates a material in an order, including its properties.
     *
     * @param int $order_id
     *   The ID of the order in which to duplicate the material.
     * @param int $order_material_id
     *   The ID of the material in the order to duplicate.
     *
     * - Creates a new OrderMaterial entity with the same order, material, pieces, price, tax, weight, and note as the original.
     * - Sets discount to 0 for the duplicated material.
     * - Duplicates all properties from the original OrderMaterial, setting their quantity to 0 in the new entity.
     * - Persists the new OrderMaterial and its properties to the database.
     * - Redirects to the order edit form after duplication.
     *
     * @return Response
     *   Redirects to the order edit form for the order.
     */
    #[Route('/orders/{order_id}/materials/{order_material_id}/duplicate', name: 'order_material_duplicate', methods:
      ['GET'])]
    public function duplicateMaterial(int $order_id, int $order_material_id): Response
    {
        $orderMaterial = $this->entityManager->find(OrderMaterial::class, $order_material_id);

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
        $material_on_order_properties = $this->entityManager
            ->getRepository(OrderMaterial::class)->getProperties($order_material_id);

        foreach ($material_on_order_properties as $material_on_order_property) {
            // Insert to table v6__orders__materials__properties.
            $newOrderMaterialProperty = new OrderMaterialProperty();

            $newOrderMaterialProperty->setOrderMaterial($newOrderMaterial);
            $newOrderMaterialProperty->setProperty($material_on_order_property->getProperty());
            $newOrderMaterialProperty->setQuantity(0);

            $this->entityManager->persist($newOrderMaterialProperty);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('order_edit_form', ['order_id' => $order_id]);
    }

    /**
     * Generates and returns a PDF document for the specified order.
     *
     * - Requires user to be logged in (checks $_SESSION['username']).
     * - Fetches company info, order data, supplier data, and materials on the order.
     * - Renders the order data as HTML using the 'order/print.html.twig' template.
     * - Uses TCPDF to generate a PDF from the rendered HTML.
     * - Sets PDF metadata (author, title, subject, keywords) based on company and order info.
     * - Attempts to save the PDF to a supplier-specific folder on the local machine if it exists; otherwise, returns the PDF as a download.
     * - Sets appropriate HTTP headers for PDF output.
     *
     * @param int $order_id
     *   The ID of the order to print.
     *
     * @return Response
     *   Returns a PDF file as an HTTP response, either inline or as a download, depending on folder availability.
     */
    #[Route('/orders/{order_id}/print', name: 'order_print', methods: ['GET'])]
    public function print(int $order_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);

        $order_data = $this->entityManager->find(Order::class, $order_id);
        $supplier_data = $this->entityManager
            ->getRepository(Client::class)->getClientData($order_data->getSupplier());

        $materials_on_order_data = $this->getOrderMaterialsData($order_id);

        $data = [
            'order_id' => $order_id,
            'order_data' => $order_data,
            'supplier_data' => $supplier_data,
            'company_info' => $company_info,
            'materials_on_order_data' => $materials_on_order_data,
        ];

        // Render HTML content from a Twig template (or similar)
        $html = $this->renderView('order/print.html.twig', $data);

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Narudzbenica');
        $pdf->SetSubject($company_info['name']);
        $pdf->SetKeywords($company_info['name'] . ', PDF, Narudzbenica');

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

        $root = 'D:/ROLOSTIL/PORUDZBINE';
        $folder = '';
        $folder = match ($order_data->getSupplier()->getId()) {
            12 => "ALUMIL NS",
            126 => "ALUPLAST",
            1 => "ALUROLL BG",
            150 => "AURA DEKOR",
            3 => "EKV",
            289 => "ENTUZIAST",
            86 => "FEROLNOR",
            10 => "GU",
            20 => "HELISA",
            7 => "INFOMARKET",
            9 => "LALIC LINE",
            844 => "LIBELA",
            110 => "MIGRO",
            4776 => "NS-GLASS",
            18 => "MIREX",
            725 => "PORTA ROYAL",
            19 => "PRIVREDNO DRUSTVO METRO",
            58 => "PROFINE",
            774 => "ROLLPLAST",
            16 => "ROLOEXPRES",
            4 => "ROLOPLAST",
            15 => "ROLO REMONT",
            125 => "ROLOSTIL plus",
            84 => "ROLO-TIM",
            5 => "ROLO-TIM NS",
            648 => "SI-LINE",
            57 => "STAKLORAM plus",
            113 => "STUBLINA",
            81 => "TEHNI",
            116 => "TEHNOMARKET",
            131 => "TOMOVIC PLAST",
            526 => "VABIS",
            4567 => "VIDOVIC ALU-PLAST",
            91 => "WURTH",
            default => '',
        };

        $file_name = $supplier_data['name'] . ' - '
            . str_pad($order_data->getOrdinalNumInYear(), 3, "0", STR_PAD_LEFT) . '-'
            . $order_data->getDate()->format('m'). ' - '
            . $order_data->getDate()->format('d M') . '.pdf';

        // Check if folder exist on local machine.
        if (is_dir($root . '/' . $folder)) {
            // Close and output PDF document and save PDF to "$root.$folder./" .
            $pdfContent = $pdf->Output($root . '/' . $folder . '/' . $file_name, 'FS');
        }
        else {
            // Close and output PDF document.
            $pdfContent = $pdf->Output($file_name, 'S');
        }
        // Remove leading __ from filename for the response
        $cleanFilename = ltrim($file_name, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

    /**
     * Get materials data for the order.
     *
     * @param int $order_id
     *
     * @return array
     */
    protected function getOrderMaterialsData(int $order_id): array
    {
        $preferences = $this->entityManager->find(Preferences::class, 1);
        $kurs = $preferences->getKurs();

        $materials_on_order = $this->entityManager->getRepository(Order::class)->getMaterialsOnOrder($order_id);

        $materials_on_order_data = [];
        foreach ($materials_on_order as $index => $material_on_order) {
            $materials_on_order_data[$index]['material']['id'] = $material_on_order->getId();
            $materials_on_order_data[$index]['material']['material']['id'] = $material_on_order->getMaterial()->getId();
            $materials_on_order_data[$index]['material']['name'] = $material_on_order->getMaterial()->getName();
            $materials_on_order_data[$index]['material']['pieces'] = $material_on_order->getPieces();

            $material_on_order_properties = $this->entityManager
                ->getRepository(OrderMaterial::class)
                ->getProperties($material_on_order->getId());

            foreach ($material_on_order_properties as $property_key => $material_on_order_property) {
                $materials_on_order_data[$index]['material']['properties'][$property_key]['name']
                    = $material_on_order_property->getProperty()->getName();
                $materials_on_order_data[$index]['material']['properties'][$property_key]['quantity']
                    = $material_on_order_property->getQuantity();
            }

            $materials_on_order_data[$index]['material']['note'] = $material_on_order->getNote();
            $materials_on_order_data[$index]['material']['unit'] = $material_on_order->getMaterial()->getUnit()->getName();
            $materials_on_order_data[$index]['material']['quantity'] = $this->entityManager
                ->getRepository(OrderMaterial::class)
                ->getQuantity(
                    $material_on_order->getId(),
                    $material_on_order->getMaterial()->getMinCalcMeasure(),
                    $material_on_order->getPieces()
                );
            $materials_on_order_data[$index]['material']['price'] = $material_on_order->getPrice();
            $materials_on_order_data[$index]['material']['discount'] = $material_on_order->getDiscount();
            $materials_on_order_data[$index]['material']['tax_base_rsd'] =  $this->entityManager
                ->getRepository(OrderMaterial::class)
                ->getTaxBase(
                    $material_on_order->getPrice(),
                    $material_on_order->getDiscount(),
                    $materials_on_order_data[$index]['material']['quantity']
                ) * $kurs;
            $materials_on_order_data[$index]['material']['tax'] = $material_on_order->getTax();
            $materials_on_order_data[$index]['material']['tax_amount_rsd'] = $this->entityManager
                ->getRepository(OrderMaterial::class)
                ->getTaxAmount(
                    $materials_on_order_data[$index]['material']['tax_base_rsd'],
                    $materials_on_order_data[$index]['material']['tax']
                );
            $materials_on_order_data[$index]['material']['sub_total_rsd'] = $this->entityManager
                ->getRepository(OrderMaterial::class)
                ->getSubTotal(
                    $materials_on_order_data[$index]['material']['tax_base_rsd'],
                    $materials_on_order_data[$index]['material']['tax_amount_rsd']
            );
        }

        return $materials_on_order_data;
    }

    /**
     * @param int $order_id
     *
     * @return float
     */
    private function getOrderTotalTaxBaseRSD(int $order_id): float
    {
        $order_materials_data = $this->getOrderMaterialsData($order_id);
        $total_tax_base_rsd = 0;
        foreach ($order_materials_data as $index => $order_material_data) {
            $total_tax_base_rsd += $order_material_data['material']['tax_base_rsd'];
        }
        return $total_tax_base_rsd;
    }

    /**
     * @param int $order_id
     *
     * @return float
     */
    private function getOrderTotalTaxAmountRSD(int $order_id): float
    {
        $order_materials_data = $this->getOrderMaterialsData($order_id);
        $total_tax_amount_rsd = 0;
        foreach ($order_materials_data as $index => $order_material_data) {
            $total_tax_amount_rsd += $order_material_data['material']['tax_amount_rsd'];
        }
        return $total_tax_amount_rsd;
    }

    /**
     * Searches for orders and archived orders by a search term.
     *
     * - Requires user to be logged in (checks $_SESSION['username']).
     * - Accepts a 'term' query parameter from the request (string, optional).
     * - Searches both active and archived orders using the repository's search method.
     * - For each order, collects ID, ordinal number, title, supplier name, date, status, archive status, project, and whether it is the last order.
     * - Prepares order status classes for display.
     * - Passes all data to the 'order/search.html.twig' template for rendering.
     *
     * @param Request $request
     *   The HTTP request object containing the search term as a query parameter.
     *
     * @return Response
     *   Renders the search results page with all relevant data.
     */
    #[Route('/orders/search', name: 'order_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = htmlspecialchars($request->query->get('term', ''));

        $orders = $this->entityManager->getRepository(Order::class)->search($term, 0);
        $orders_data = [];
        foreach ($orders as $order) {
            $project = $this->entityManager->getRepository(Order::class)->getProject($order->getId());
            $last_order = $this->entityManager->getRepository(Order::class)->getLastOrder();
            $orders_data[] = [
                'id' => $order->getId(),
                'ordinal_num_in_year' => $order->getOrdinalNumInYear(),
                'title' => $order->getTitle(),
                'supplier_name' => $order->getSupplier()->getName(),
                'date' => $order->getDate()->format('m_Y'),
                'status' => $order->getStatus(),
                'is_archived' => $order->getIsArchived(),
                'project' => $project,
                'is_last' => $order == $last_order ? TRUE : FALSE,
            ];
        }

        $orders_archived = $this->entityManager->getRepository(Order::class)->search($term, 1);
        $orders_archived_data = [];
        foreach ($orders_archived as $order_archived) {
            $project_archived = $this->entityManager->getRepository(Order::class)->getProject($order_archived->getId());
            $last_order_archived = $this->entityManager->getRepository(Order::class)->getLastOrder();
            $orders_archived_data[] = [
                'id' => $order_archived->getId(),
                'ordinal_num_in_year' => $order_archived->getOrdinalNumInYear(),
                'title' => $order_archived->getTitle(),
                'supplier_name' => $order_archived->getSupplier()->getName(),
                'date' => $order_archived->getDate()->format('m_Y'),
                'status' => $order_archived->getStatus(),
                'is_archived' => $order_archived->getIsArchived(),
                'project' => $project_archived,
                'is_last' => $order_archived == $last_order_archived ? TRUE : FALSE,
            ];
        }

        $order_status_classes = [
            0 => [
                'class' => 'badge-light',
                'icon' => 'N',
                'title' => 'Nacrt',
            ],
            1 => [
                'class' => 'badge-warning',
                'icon' => 'P',
                'title' => 'Poručeno',
            ],
            2 => [
                'class' => 'badge-success',
                'icon' => 'S',
                'title' => 'Stiglo',
            ],
        ];

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'orders_data' => $orders_data,
            'orders_archived_data' => $orders_archived_data,
            'order_status_classes' => $order_status_classes,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            "tools_menu" => [
              'order' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('order/search.html.twig', $data);
    }

    /**
     * Loads the application version from composer.json.
     *
     * @return string
     *   The app version, or 'unknown' if not found.
     */
    private function loadAppVersion(): string
    {
        $composerJsonPath = __DIR__ . '/../../composer.json';
        if (file_exists($composerJsonPath)) {
            $composerData = json_decode(file_get_contents($composerJsonPath), true);
            return $composerData['version'] ?? 'unknown';
        }
        return 'unknown';
    }
}
