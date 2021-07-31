<?php
require 'update_preferences.php';
require 'create__accounting_document.php';
require 'update__accounting_document.php';
require 'add__article__to__accounting_document.php';
require 'add__payment__to__accounting_document.php';
require 'edit__article__in__accounting_document.php';
require 'change__article__in__accounting_document.php';
require 'duplicate__article__in__accounting_document.php';
require 'remove__article__from__accounting_document.php';
require 'export__proforma__to__dispatch.php';

require 'del.php';
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
        $pidb_data = $entityManager->find('\Roloffice\Entity\AccountingDocument', $pidb_id);
        $client_data = $entityManager->find('\Roloffice\Entity\Client', $pidb_data->getClient());
        $client_country = $entityManager->find('\Roloffice\Entity\Country', $client_data->getCountry());
        $client_city = $entityManager->find('\Roloffice\Entity\City', $client_data->getCity());
        $client_street = $entityManager->find('\Roloffice\Entity\Street', $client_data->getStreet());
        $all_articles = $entityManager->getRepository('\Roloffice\Entity\Article')->findAll();
        
        // In view case show edit button.
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&pidb_id=<?php echo $pidb_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o projektu!">
              <i class="fas fa-edit"> </i> <!-- Izmena -->
            </button>
          </a>
          <?php
        endif;

        // in edit case show view button
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&pidb_id=<?php echo $pidb_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o projektu!">
              <i class="fas fa-eye"> </i> <!-- Pregled -->
            </button>
          </a>
          <?php
        endif;
        
        if(isset($_GET['view']) || isset($_GET['edit'])):
          ?>
          <!-- Button trigger modal za dodavanje proizvoda u dokument -->
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addArticle" title="Dodaj novi proizvod!">
            <i class="fas fa-plus"> </i> Proizvod
          </button>

          <!-- Print Buttons -->
          <a href="/tcpdf/examples/printAccountingDocument.php?accounting_document__id=<?php echo $pidb_id ?>" title="PDF [new window]" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
              <i class="fas fa-print"> </i>
            </button>
          </a>
          <a href="/tcpdf/examples/printAccountingDocumentW.php?accounting_document__id=<?php echo $pidb_id ?>" title="PDF [new window]" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
              <i class="fas fa-print"> </i> w
            </button>
          </a>

          <?php
          if($pidb_data->getType()->getId() == 2):
            ?>
            <a href="/tcpdf/examples/printAccountingDocumentI.php?accounting_document__id=<?php echo $pidb_id ?>" title="PDF [new window]" target="_blank">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
                <i class="fas fa-print"> </i> I
              </button>
            </a>
            <a href="/tcpdf/examples/printAccountingDocumentIW.php?accounting_document__id=<?php echo $pidb_id ?>" title="PDF [new window]" target="_blank">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
                <i class="fas fa-print"> </i> IW
              </button>
            </a>
            <?php
          endif;

          // export proforma
          if($pidb_data->getType()->getId() == 1):
            ?>
            <a href="?edit&pidb_id=<?php echo $pidb_id ?>&exportProformaToDispatch">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" <?php echo ($pidb_data->getIsArchived() == 1 ? 'title="Predračun je arhiviran i nije ga moguće izvesti u otpremnicu" disabled' : 'title="Izvezi u otpremnicu" ' ) ?> >
                <i class="fas fa-share"> </i> Otpremnica
              </button>
            </a>

            <a href="/projects/index.php?new&client_id=<?php echo $pidb_data->getClient()->getId() ?>&pidb_id=<?php echo $pidb_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Novi projekat!">
                <i class="fas fa-share"> </i> <i class="fas fa-project-diagram"></i> <!-- Projekat -->
              </button>
            </a>
            <?php
          endif;
          
        endif;
          
        // Next and Previuos button
        if($user_role_id == 1 && isset($_GET['edit'])):
          $previous = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getPrevious( $pidb_id, $pidb_data->getType()->getId() );
          $next = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getNext( $pidb_id, $pidb_data->getType()->getId() );
          ?>
          <a href="?edit&pidb_id=<?php echo $previous->getId() ?>">
            <button type="submit" class="btn btn-sm btn-outline-secondary mr-1">
              <i class="fas fa-arrow-left"> </i>
            </button>
          </a>
          <?php
          if($next) :
            ?>
            <a href="?edit&pidb_id=<?php echo $next->getId() ?>">
              <button type="submit" class="btn btn-sm btn-outline-secondary mr-1">
                <i class="fas fa-arrow-right"> </i>
              </button>
            </a>
            <?php
          endif;                   
        endif;
          
      endif;

      if( isset($_GET['view']) || isset($_GET['edit'])):
        ?>
        <!-- Button trigger modal za evidentiranje transakcije -->
        <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addPayment" <?php echo ($pidb_data->getIsArchived() == 1 ? 'title="Predračun je arhiviran i nije moguće videntiranje transakcija" disabled' : 'title="Evidentiranje transakcija" ' ) ?>>
          <i class="fas fa-hand-holding-usd"></i>
        </button>
        <a href="/pidb/index.php?transactions&pidb_id=<?php echo $pidb_id ?>">
          <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" title="Pregled transakcija">
            <i class="fas fa-eye"></i> <i class="fas fa-dollar-sign"></i>
          </button>
        </a>
        <?php
      endif;

      if( isset($_GET['cashRegister']) ):
        ?>
        <!-- Button triger modal za cash input -->
        <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#cashInput" title="Ulaz gotovine u kasu">
          <i class="fas fa-euro-sign"></i>
          ->
          <i class="fas fa-cash-register"></i>
        </button>
        <!-- Button triger modal za cash output -->
        <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#cashOutput" title="Izlaz gotovine iz kase">
          <i class="fas fa-cash-register"></i>
          ->
          <i class="fas fa-euro-sign"></i>
        </button>

        <!-- Print Buttons -->
        <?php
        if (isset($_GET['date'])) {
          $date = $_GET['date'];
        } else {
          $date = $pidb->getDate();
        }
        ?>
        <!--
        <a href="/tcpdf/examples/printDailyCashReport.php&" title="PDF [new window]" target="_blank">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
            <i class="fas fa-print"> </i>
            <i class="fas fa-cash-register"></i>
          </button>
        </a>
        -->
        <form class="d-inline" target="_blank" action="/tcpdf/examples/printDailyCashReport.php">
          <input type="hidden" name="date" value="<?php echo $date ?>">
          <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
            <i class="fas fa-print"> </i>
            <i class="fas fa-cash-register"></i>
          </button>
        </form>
        <?php
      endif;
        ?>
    </div>
  </div>

</div>
<!-- /#topMeni -->
