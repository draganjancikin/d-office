<?php
require 'create.php';
require 'delete.php';
require 'add__article.php';
require 'update__article.php';
require 'remove__article.php';

require 'export__to__accounting_document.php';
?>
<div class="col-lg-12 col-xl-10 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/cutting/index.php?new" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove krojne liste!">
        <i class="fas fa-plus"> <i class="fas fa-cut"></i> </i>
      </a>
      <?php
      if(isset($_GET['view']) || isset($_GET['edit'])):
        
        if ( isset($_GET['id']) ) {
          $id = filter_input(INPUT_GET, 'id') ;
        } else {
          $id = htmlspecialchars($_POST['id']);
        }
        if (!$cutting_data = $entityManager->find('\Roloffice\Entity\CuttingSheet', $id)) {
          die('<script>location.href = "/cutting/"</script>');
        }
        $client_data = $entityManager->find('\Roloffice\Entity\Client', $cutting_data->getClient());
        $client_country = $entityManager->find('\Roloffice\Entity\Country', $client_data->getCountry());
        $client_city = $entityManager->find('\Roloffice\Entity\City', $client_data->getCity());
        $client_street = $entityManager->find('\Roloffice\Entity\Street', $client_data->getStreet());

        $fence_models = $entityManager->getRepository('\Roloffice\Entity\FenceModel')->findBy(array(), array('name' => 'ASC'));
        
        // in view case show edit button
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&id=<?php echo $id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu krojne liste!">
              <i class="fas fa-edit"> </i> Izmena
            </button>
          </a>
          <?php
        endif;
        
        // in edit case show view button
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&id=<?php echo $id ?>">
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
        <a href="printCutting.php?cutting_id=<?php echo $id ?>" title="PDF [new window]" target="_blank">
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
