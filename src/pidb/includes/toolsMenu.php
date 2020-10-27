<?php
require 'add.php';
require 'edit.php';
require 'del.php';
require 'export.php';
?>
<div class="col-lg-12 px-2" id="topMeni">

  <div class="card mb-2">
    <div class="card-body py-1 px-2">
      <a href="/pidb/index.php?new" class="btn btn-sm btn-outline-secondary" title="Otvaranje novog dokumenta!">
        <i class="fas fa-plus"> </i> Dokument
      </a>
      <?php
      if( isset($_GET['view']) || isset($_GET['edit']) || isset($_GET['editArticle']) ):
        $pidb_id = filter_input(INPUT_GET, 'pidb_id');
        $pidb_data = $pidb->getPidb($pidb_id);
        $client_data = $client->getClient($pidb_data['client_id']);
        $articles = $article->getArticles();
        // in view case show edit button
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&pidb_id=<?php echo $pidb_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o klijentu!">
              <i class="fas fa-edit"> </i> <!-- Izmena -->
            </button>
          </a>
          <?php
        endif;

        // in edit case show view button
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&pidb_id=<?php echo $pidb_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o klijentu!">
              <i class="fas fa-eye"> </i> <!-- Pregled -->
            </button>
          </a>
          <?php
        endif;
        ?>
        <?php
        if(isset($_GET['view']) || isset($_GET['edit'])):
          ?>
          <!-- Button trigger modal za dodavanje proizvoda u dokument -->
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addArticle" title="Dodaj novi proizvod!">
            <i class="fas fa-plus"> </i> Proizvod
          </button>

          <!-- Print Buttons -->
          <a href="/tcpdf/examples/printPidb.php?pidb_id=<?php echo $pidb_id ?>" title="PDF [new window]" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
              <i class="fas fa-print"> </i>
            </button>
          </a>
          <a href="/tcpdf/examples/printPidbWC.php?pidb_id=<?php echo $pidb_id ?>" title="PDF [new window]" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
              <i class="fas fa-print"> </i> w
            </button>
          </a>

          <?php
          if($pidb_data['tip_id'] == 2):
            ?>
            <a href="/tcpdf/examples/printPidbI.php?pidb_id=<?php echo $pidb_id ?>" title="PDF [new window]" target="_blank">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
                <i class="fas fa-print"> </i> I
              </button>
            </a>
            <a href="/tcpdf/examples/printPidbIW.php?pidb_id=<?php echo $pidb_id ?>" title="PDF [new window]" target="_blank">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
                <i class="fas fa-print"> </i> IW
              </button>
            </a>
            <?php
          endif;

          // export proforma
          if($pidb_data['tip_id'] == 1):
            ?>
            <a href="?edit&pidb_id=<?php echo $pidb_id ?>&exportProformaToDispatch">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Izvezi u otpremnicu!">
                <i class="fas fa-share"> </i> Otpremnica
              </button>
            </a>

            <a href="/projects/index.php?new&client_id=<?php echo $pidb_data['client_id'] ?>&pidb_id=<?php echo $pidb_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Novi projekat!">
                <i class="fas fa-share"> </i> <i class="fas fa-project-diagram"></i> <!-- Projekat -->
              </button>
            </a>
            <?php
          endif;
          
        endif;
        
        // Next and Previuos button
        if($userlevel == 1 && isset($_GET['edit'])):
          ?>
          
          <a href="?edit&pidb_id=<?php echo $pidb->getPreviousPidb($pidb_id, $pidb_data['tip_id']) ?>">
            <button type="submit" class="btn btn-sm btn-outline-secondary mr-1">
              <i class="fas fa-arrow-left"> </i>
            </button>
          </a>
          <?php
          if($pidb->getNextPidb($pidb_id, $pidb_data['tip_id']) != ""){
            ?>
            <a href="?edit&pidb_id=<?php echo $pidb->getNextPidb($pidb_id, $pidb_data['tip_id']) ?>">
              <button type="submit" class="btn btn-sm btn-outline-secondary mr-1">
                <i class="fas fa-arrow-right"> </i>
              </button>
            </a>
            <?php
          }                      
        endif;

        // Payment option
        ?>
        <!-- Button trigger modal za evidenciju uplata -->
        <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addPayment" title="Evidencija plaćanja">
          <i class="fas fa-hand-holding-usd"></i>
        </button>
        <?php
      endif;
      ?>
    </div>
  </div>

</div>
<!-- /#topMeni -->
