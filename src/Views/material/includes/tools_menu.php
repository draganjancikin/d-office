<div class="col-lg-12 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/materials/add" class="btn btn-sm btn-outline-secondary" title="Dodavanje novog materijala!">
        <i class="fas fa-plus"> <i class="fas fa-inbox"></i> </i>
      </a>
      <?php
      if (!str_contains($_GET['url'], 'materials')) {
        // check if $_GET has url key.
        if (isset($_GET['url'])) {
          $url = $_GET['url'];
          $url = explode('/', $url);

          // In view case show edit button.
          if (count($url) == 2 && $url[0] == 'material' && is_numeric($url[1])) {
            ?>
            <a href="/material/<?php echo $material_id ?>/edit">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o materijalu!">
                <i class="fas fa-edit"> </i> Izmena
              </button>
            </a>
            <?php
          }

          // in edit case show view button
          if (count($url) == 3 && $url[0] == 'material' && is_numeric($url[1]) && $url[2] == 'edit') {
            ?>
            <a href="/material/<?php echo $material_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o materijalu!">
                <i class="fas fa-eye"> </i> Pregled
              </button>
            </a>
            <?php
          }
        }
        ?>
        <!-- Button trigger modal za dodavanje dobavljača -->
        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#addSupplier" title="Dodaj novog dobavljača!">
          <i class="fas fa-plus"> </i> Dobavljač
        </button>

        <!-- Button trigger modal za dodavanje nove osobine proizvoda -->
        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#addProperty" title="Dodaj novu osobinu!">
          <i class="fa fa-plus"> </i> Osobina
        </button>
        <?php
      }
      ?>

    </div>
  </div>
  <!-- End Card -->
</div>
<!-- /#topMeni -->
