<div class="col-lg-12 col-xl-10 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/cuttings/add" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove krojne
      liste!">
          <i class="fas fa-plus"> <i class="fas fa-cut"></i> </i>
      </a>
      <?php
      if (!str_contains($_GET['url'], 'cuttings')) {
        // check if $_GET has url key.
        if (isset($_GET['url'])) {
          $url = $_GET['url'];
          $url = explode('/', $url);

          // In view case show edit button.
          if (count($url) == 2 && $url[0] == 'cutting' && is_numeric($url[1])) {
            ?>
            <a href="/cutting/<?php echo $cutting_id ?>/edit">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu krojne liste!">
                <i class="fas fa-edit"> </i> Izmena
              </button>
            </a>
            <?php
          }
          // in edit case show view button
          if (count($url) == 3 && $url[0] == 'cutting' && is_numeric($url[1]) && $url[2] == 'edit') {
            ?>
            <a href="/cutting/<?php echo $cutting_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled krojne liste!">
                <i class="fas fa-eye"> </i> Pregled
              </button>
            </a>
            <?php
          }
          ?>
          <!-- Button trigger modal for addFence -->
          <a href="#">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addFence" title="Dodaj novo polje!">
              <i class="fas fa-plus"> </i> Novo polje
            </button>
          </a>

          <!-- Button trigger modal for print -->
          <a href="/cutting/<?php echo $cutting_id ?>/print" title="PDF [new window]" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary">
              <i class="fas fa-print"></i>
            </button>
          </a>
          <?php
        }
      }

      ?>

    </div><!-- End of Card Body -->
  </div><!-- End of Card -->
</div>
