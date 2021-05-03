<?php
require 'create_order.php';
require 'update_order.php';
require 'add_material_to_order.php';
require 'edit_material_in_order.php';
require 'remove_material_from_order.php';
require 'duplicate_material_in_order.php';
?>
<div class="col-lg-12 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/orders/index.php?new" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove narudžbenice!">
        <i class="fas fa-plus"> </i> <i class="fas fa-th"> </i>
      </a>

      <?php
      if(isset($_GET['view']) || isset($_GET['edit'])):
        $order_id = filter_input(INPUT_GET, 'order_id');
        $order_data = $entityManager->find('\Roloffice\Entity\Order', $order_id);
        $supplier_data = $entityManager->find('\Roloffice\Entity\Client', $order_data->getSupplier());
        $project_data = $entityManager->getRepository('Roloffice\Entity\Order')->getProject($order_id);
        $materials = $entityManager->getRepository('\Roloffice\Entity\Material')->findBy(array(), array('name' => "ASC"));

        // in view case show edit button
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&order_id=<?php echo $order_id ?>" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu narudžbenice!">
            <i class="fas fa-edit"> </i>
          </a>
          <?php
        endif;

        // in edit case show view button
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&order_id=<?php echo $order_id ?>" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled narudžbenice!">
            <i class="fas fa-eye"> </i>
          </a>
          <?php
        endif;
        ?>
        
        <!-- Button trigger modal za dodavanje proizvoda u dokument -->
        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#addMaterial" title="Dodaj materijal!">
          <i class="fa fa-plus"> </i> Materijal
        </button>

        <a href="/tcpdf/examples/printOrder.php?order_id=<?php echo $order_id ?>" title="PDF [new window]" target="_blank" class="btn btn-sm btn-outline-secondary mr-1">
          <i class="fa fa-print"> </i>
        </a>

        <?php
      endif;
      ?>

    </div>
    <!-- End Card Body -->
  </div>
  <!-- End Card -->
</div>
<!-- /#topMeni -->
