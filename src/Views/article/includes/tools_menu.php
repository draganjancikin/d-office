<div class="col-lg-12 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">
      <a href="/articles/add" class="btn btn-sm btn-outline-secondary" title="Dodavanje novog proizvoda!">
        <i class="fas fa-plus"> <i class="fas fa-tag"></i> </i>
      </a>
      <?php
      if (!str_contains($_GET['url'], 'articles')) {
        // check if $_GET has url key.
        if (isset($_GET['url'])) {
          $url = $_GET['url'];
          $url = explode('/', $url);

          // In view case show edit button.
          if (count($url) == 2 && $url[0] == 'article' && is_numeric($url[1])) {
            ?>
            <a href="/article/<?php echo $article_id ?>/edit">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o proizvodu!">
                <i class="fas fa-edit"> </i> Izmena
              </button>
            </a>
            <?php
          }

          // in edit case show view button
          if (count($url) == 3 && $url[0] == 'article' && is_numeric($url[1]) && $url[2] == 'edit') {
            ?>
            <a href="/article/<?php echo $article_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o proizvodu!">
                <i class="fas fa-eye"> </i> Pregled
              </button>
            </a>
            <?php
          }
        }
        ?>
        <!-- Button trigger for modal add property -->
        <a href="#">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addProperty" title="Dodaj osobinu proizvodu!">
            <i class="fas fa-plus"> </i> Osobina
          </button>
        </a>
        <?php
      }

      if (str_contains($_GET['url'], 'articles') && !str_contains($_GET['url'], 'groups')) {
        if (isset($_GET['url'])) {
          $url = $_GET['url'];
          $url = explode('/', $url);

          // In view case show edit button.
          if (count($url) == 3 && $url[1] == 'group' && is_numeric($url[2])) {
            ?>
            <a href="/articles/group/<?php echo $group_id ?>/edit">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o grupi proizvoda!">
                <i class="fas fa-edit"> </i> Izmena
              </button>
            </a>
            <?php
          }

          // In edit case show view button.
          if (count($url) == 4 && $url[1] == 'group' && is_numeric($url[2]) && $url[3] == 'edit') {
            ?>
            <a href="/articles/group/<?php echo $group_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o grupi proizvoda!">
                <i class="fas fa-eye"> </i> Pregled
              </button>
            </a>
            <?php
          }
        }
      }
      ?>
    </div>
  </div>
</div>
<!-- /#topMeni -->
