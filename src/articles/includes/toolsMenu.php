<?php
require 'add.php';
require 'edit.php';
require 'del.php';
?>
<div class="col-lg-12 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">
      
      <a href="/articles/index.php?new" class="btn btn-sm btn-outline-secondary" title="Dodavanje novog proizvoda!">
        <i class="fas fa-plus"> <i class="fas fa-tag"></i> </i>
      </a>
      <?php
      if(isset($_GET['view']) || isset($_GET['edit'])):
        $article_id = filter_input(INPUT_GET, 'article_id');
        $article_data = $article->getArticleById($article_id);
        
        // in view case show edit button
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&article_id=<?php echo $article_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o proizvodu!">
              <i class="fas fa-edit"> </i> Izmena
            </button>
          </a>
          <?php
        endif;

        // in edit case show view button
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&article_id=<?php echo $article_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o proizvodu!">
              <i class="fas fa-eye"> </i> Pregled
            </button>
          </a>
          <?php
        endif;
        ?>
        <!-- Button trigger for modal add property -->
        <a href="#">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addProperty" title="Dodaj osobinu proizvodu!">
            <i class="fas fa-plus"> </i> Osobina
          </button>
        </a>

      <?php
      endif;
      ?>
    </div>
  </div>
</div>
<!-- /#topMeni -->
