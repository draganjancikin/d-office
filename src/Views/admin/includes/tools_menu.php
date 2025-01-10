<div class="col-lg-12 col-xl-10 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body p-1">
      <?php
      if (isset($_GET['url'])) {
      $url = $_GET['url'];
      $url = explode('/', $url);

        // In view case show edit button.
        if (count($url) == 2 && $url[1] == 'company-info') {
          ?>
          <a href="/admin/company-info/edit">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o klijentu!">
              <i class="fas fa-edit"> </i> Izmena
            </button>
          </a>
          <?php
        }

        // In edit case show view button.
        if (count($url) == 3 && $url[1] == 'company-info' && $url[2] == 'edit') {

          ?>
          <a href="/admin/company-info">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o klijentu">
              <i class="fas fa-eye"> </i> Pregled
            </button>
          </a>
          <?php
        }
      }
      ?>
    </div>
    <!-- End of Card Body. -->
  </div>
  <!-- End of Card. -->
</div>
