<div class="col-lg-12 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/orders/add" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove narudžbenice!">
        <i class="fas fa-plus"> </i> <i class="fas fa-th"> </i>
      </a>
      <?php
      if (!str_contains($_GET['url'], 'orders')) {
        // check if $_GET has url key.
        if (isset($_GET['url'])) {
          $url = $_GET['url'];
          $url = explode('/', $url);

          // In view case show edit button.
          if (count($url) == 2 && $url[0] == 'order' && is_numeric($url[1])) {
            ?>
            <a href="/order/<?php echo $order_id ?>/edit" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu narudžbenice!">
              <i class="fas fa-edit"> </i>
            </a>
            <?php
          }

          // In edit case show view button.
          if (count($url) == 3 && $url[0] == 'order' && is_numeric($url[1]) && $url[2] == 'edit') {
            ?>
            <a href="/order/<?php echo $order_id ?>" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled narudžbenice!">
              <i class="fas fa-eye"> </i>
            </a>
            <?php
          }
        }

        ?>
        <!-- Button trigger modal za dodavanje materijala u narudzbenicu -->
        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#addMaterial" title="Dodaj materijal!">
          <i class="fa fa-plus"> </i> Materijal
        </button>

        <a href="/order/<?php echo $order_id ?>/print" title="PDF [new window]" target="_blank"
           class="btn btn-sm btn-outline-secondary mr-1">
          <i class="fa fa-print"> </i>
        </a>

        <?php
      }
      ?>

    </div>
    <!-- End Card Body -->
  </div>
  <!-- End Card -->
</div>
<!-- /#topMeni -->
