<?php
require 'add.php';
require 'edit.php';
require 'del.php';
require 'export.php';
?>
<div class="col-lg-12 col-xl-10 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/cutting/index.php?new" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove krojne liste!">
        <i class="fas fa-plus"> <i class="fas fa-cut"></i> </i>
      </a>
      <?php
      if(isset($_GET['view']) || isset($_GET['edit'])):
        
        if ( isset($_GET['cutting_id']) ) {
          $cutting_sheet_id = filter_input(INPUT_GET, 'cutting_id') ;
        } else {
          $cutting_sheet_id = htmlspecialchars($_POST['cutting_id']);
        }
        // TODO:
        $cutting_data = $entityManager->find('\Roloffice\Entity\CuttingSheet', $cutting_sheet_id);
        $client_data = $entityManager->find('\Roloffice\Entity\Client', $cutting_data->getClient());
        $client_country = $entityManager->find('\Roloffice\Entity\Country', $client_data->getCountry());
        $client_city = $entityManager->find('\Roloffice\Entity\City', $client_data->getCity());
        $client_street = $entityManager->find('\Roloffice\Entity\Street', $client_data->getStreet());
        
        // in view case show edit button
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&cutting_id=<?php echo $cutting_sheet_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu krojne liste!">
              <i class="fas fa-edit"> </i> Izmena
            </button>
          </a>
          <?php
        endif;
        
        // in edit case show view button
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&cutting_id=<?php echo $cutting_sheet_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled krojne liste!">
              <i class="fas fa-eye"> </i> Pregled
            </button>
          </a>
          <?php
        endif;
        ?>

        <!-- Button trigger modal for addFence -->
        <a href="#">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addFence" title="Dodaj novo polje!">
            <i class="fas fa-plus"> </i> Novo polje
          </button>
        </a>

        <!-- Button trigger modal for print -->
        <a href="../tcpdf/examples/printCutting.php?cutting_id=<?php echo $cutting_sheet_id ?>" title="PDF [new window]" target="_blank">
          <button type="button" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-print"></i>
          </button>
        </a>

        <?php
      endif;  
      ?>

    </div>
    <!-- End of Card Body -->
  </div>
  <!-- End of Card -->
</div>
