<?php
function basicValidation($str){
  return trim(htmlspecialchars($str));
}
require 'add.php';
require 'edit.php';
require 'del.php';
?>
<div class="col-lg-12 col-xl-10 px-2" id="topMeni">

  <div class="card mb-2">
    <div class="card-body p-1">
    
      <a href="/clients/index.php?new">
        <button type="button" class="btn btn-sm btn-outline-secondary" title="Upis novog klijenta!">
          <i class="fas fa-plus"> </i><i class="fas fa-user"> </i>
        </button>
      </a>
      <?php
      if(isset($_GET['view']) || isset($_GET['edit'])):
        $client_id = filter_input(INPUT_GET, 'client_id');
        $client_data = $client->getClient($client_id);
        
        // in view case show edit button 
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&client_id=<?php echo $client_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o klijentu!">
              <i class="fas fa-edit"> </i> Izmena
            </button>
          </a>
          <?php
        endif;
        
        // in edit case show view button
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&client_id=<?php echo $client_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o klijentu">
            <i class="fas fa-eye"> </i> Pregled
            </button>
          </a>
          <?php
        endif;
        ?>

        <!-- Button trigger for modal addContact -->
        <a href="#">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addContact" title="Dodaj novi kontakt!">
            <i class="fas fa-plus"> </i> Kontakt
          </button>
        </a>

        <!-- Open new project with client data -->
        <a href="/project/index.php?new&client_id=<?php echo $client_id ?>">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje novog projekta!">
            <i class="fas fa-arrow-right"> </i> Projekat
          </button>
        </a>
        
        <!-- Open new proforma-invoice with client data -->
        <a href="/pidb/index.php?new&client_id=<?php echo $client_id ?>">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje novog predračuna!">
            <i class="fas fa-arrow-right"> </i> Predračun
          </button>
        </a>

        <!-- Open new cutting with client data -->
        <a href="/cutting/index.php?new&client_id=<?php echo $client_id ?>">
          <button type="submit" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove krojne liste!">
            <i class="fas fa-arrow-right"> </i> <i class="fas fa-cut"> </i> 
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
