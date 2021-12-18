<?php
require 'create.php';
require 'update.php';
require 'delete.php';
require 'add__material.php';
require 'edit__material.php';
require 'remove__material.php';
require 'duplicate__material.php';
?>
<div class="col-lg-12 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/orders/index.php?new" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove narudžbenice!">
        <i class="fas fa-plus"> </i> <i class="fas fa-th"> </i>
      </a>

      <?php
      if(isset($_GET['view']) || isset($_GET['edit'])):
        $order_id = filter_input(INPUT_GET, 'id');
        $order_data = $entityManager->find('\Roloffice\Entity\Order', $order_id);
        $supplier_data = $entityManager->find('\Roloffice\Entity\Client', $order_data->getSupplier());
        $project_data = $entityManager->getRepository('Roloffice\Entity\Order')->getProject($order_id);
        $materials = $entityManager->getRepository('\Roloffice\Entity\Material')->getSupplierMaterials($supplier_data->getId());
        // In view case show edit button.
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&id=<?php echo $order_id ?>" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu narudžbenice!">
            <i class="fas fa-edit"> </i>
          </a>
          <?php
        endif;

        // In edit case show view button.
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&id=<?php echo $order_id ?>" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled narudžbenice!">
            <i class="fas fa-eye"> </i>
          </a>
          <?php
        endif;
        ?>
        
        <!-- Button trigger modal za dodavanje proizvoda u dokument -->
        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#addMaterial" title="Dodaj materijal!">
          <i class="fa fa-plus"> </i> Materijal
        </button>

        <a href="printOrder.php?order_id=<?php echo $order_id ?>" title="PDF [new window]" target="_blank" class="btn btn-sm btn-outline-secondary mr-1">
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
