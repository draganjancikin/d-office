<?php

namespace App\Controller;

use App\Core\BaseController;
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
use TCPDF;

/**
 * OrderController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class OrderController extends BaseController
{

    private $page;
    private $page_title;

    /**
     * OrderController constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->page = 'order';
        $this->page_title = 'Narudžbenice';
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function index(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
        ];

        $this->render('order/index.html.twig', $data);
    }

    /**
     * Form for adding a new order.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function orderNewForm(?int $project_id = NULL): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $suppliers = $this->entityManager->getRepository(Client::class)->findBy(['is_supplier' => 1], ['name' => 'ASC']);
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

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
        ];

        $this->render('order/order_new.html.twig', $data);
    }

    /**
     * Add a new Order.
     *
     * @return void
     */
    public function orderAdd(): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

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

        die('<script>location.href = "/order/' . $new_order_id . '" </script>');
    }

    /**
     * View order form.
     *
     * @param int $order_id
     *
     * @return void
     */
    public function orderViewForm(int $order_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
        ];

        $this->render('order/order_view.html.twig', $data);
    }

    /**
     * Edit Order form.
     *
     * @param int $order_id
     *
     * @return void
     */
    public function orderEditForm(int $order_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
        ];

        $this->render('order/order_edit.html.twig', $data);
    }

    /**
     * Edit Order.
     *
     * @param int $order_id
     *
     * @return void
     */
    public function orderEdit(int $order_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);
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
        die('<script>location.href = "/order/' . $order_id . '" </script>');
    }

    /**
     * Delete Order.
     *
     * @param int $order_id
     *
     * @return void
     */
    public function orderDelete(int $order_id): void
    {
        // Check if exist Order.
        if ($order = $this->entityManager->find(Order::class, $order_id)) {

            // Check if exist Materials in Order.
            if ($order_materials = $this->entityManager->getRepository(OrderMaterial::class)->getOrderMaterials($order_id)) {

                // Loop trough all materials
                foreach ($order_materials as $order_material) {

                    // Check if exist Properties in Order Material
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

            // Remove Order
            $this->entityManager->remove($order);
            $this->entityManager->flush();

        }

        die('<script>location.href = "/orders/?search=" </script>');
    }

    /**
     * Add Material to Order.
     *
     * @param int $order_id
     *
     * @return void
     */
    public function addMaterial(int $order_id): void
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

        die('<script>location.href = "/order/' . $order_id . '/edit" </script>');
    }

    /**
     * Edit Material in Order.
     *
     * @param int $order_id
     * @param int $order_material_id
     * @return void
     */
    public function editMaterial(int $order_id, int $order_material_id): void
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

        die('<script>location.href = "/order/' . $order_id . '/edit" </script>');
    }

    /**
     * Edit Material form.
     *
     * @param int $order_id
     * @param int $order_material_id
     *
     * @return void
     */
    public function editMaterialForm(int $order_id, int $order_material_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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

        ];

      $this->render('order/material_in_order_change.html.twig', $data);
    }

    /**
     * Delete Material from Order.
     *
     * @param int $order_id
     * @param int $order_material_id
     *
     * @return void
     */
    public function deleteMaterial(int $order_id, int $order_material_id): void
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

        die('<script>location.href = "/order/' . $order_id . '" </script>');
    }

    /**
     * Duplicate Material on Order.
     *
     * @param int $order_id
     * @param int $order_material_id
     *
     * @return void
     */
    public function duplicateMaterial(int $order_id, int $order_material_id): void
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

        die('<script>location.href = "/order/' . $order_id . '/edit" </script>');
    }

    /**
     * Print Order.
     *
     * @param int $order_id
     *
     * @return void
     */
    public function print(int $order_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
        ob_start();
        $this->render('order/print.html.twig', $data);
        $html = ob_get_clean();

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
            $pdf->Output($root . '/' . $folder . '/' . $file_name, 'FI');
        }
        else {
            // Close and output PDF document.
            $pdf->Output($file_name, 'I');
        }
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
     * Search for orders.
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
        ];

        $this->render('order/search.html.twig', $data);
    }

}
