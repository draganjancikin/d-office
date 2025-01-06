<?php
require 'add__payment__to__accounting_document.php';
require 'delete__transaction.php';
require 'change__article__in__accounting_document.php';
require 'duplicate__article__in__accounting_document.php';
require 'remove__article__from__accounting_document.php';
require 'export__proforma__to__dispatch.php';
require 'delete_accounting_document.php';
?>
<div class="col-lg-12 px-2" id="topMeni">

  <div class="card mb-2">
    <div class="card-body py-1 px-2">
      <a href="/pidbs/add" class="btn btn-sm btn-outline-secondary" title="Otvaranje novog dokumenta!">
          <i class="fas fa-plus"> </i> Dokument
      </a>
      <?php
      if (!str_contains($_GET['url'], 'pidbs')) {
        // check if $_GET has url key.
        if (isset($_GET['url'])) {
          $url = $_GET['url'];
          $url = explode('/', $url);
//          if (!$pidb_data) {
//            die('<script>location.href = "/pidbs/"</script>');
//          }
          // In view case show edit button.
          if (count($url) == 2 && $url[0] == 'pidb' && is_numeric($url[1]) && !isset($url[2])) {
            ?>
            <a href="/pidb/<?php echo $pidb_id ?>/edit">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1"
                      title="Idi na stranicu za izmenu podataka o dokumentu!">
                <i class="fas fa-edit"> </i> <!-- Izmena -->
              </button>
            </a>
            <?php
          }
          // In edit case show view button.
          if (count($url) == 3 && $url[0] == 'pidb' && is_numeric($url[1]) && $url[2] == 'edit') {
            ?>
            <a href="/pidb/<?php echo $pidb_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mx-1"
                      title="Idi na stranicu za pregled podataka o dokumentu!">
                <i class="fas fa-eye"> </i> <!-- Pregled -->
              </button>
            </a>
            <?php
          }

          if (!isset($url[2]) || (isset($url[2]) && $url[2] != 'transactions')) {
          ?>
          <!-- Button trigger modal za dodavanje proizvoda u dokument -->
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addArticle" title="Dodaj novi proizvod!">
            <i class="fas fa-plus"> </i> Proizvod
          </button>
          <!-- Print Buttons -->
          <a href="/pidb/<?php echo $pidb_id ?>/print" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
              <i class="fas fa-print"> </i>
            </button>
          </a>
          <a href="/pidb/<?php echo $pidb_id ?>/printW" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
              <i class="fas fa-print"> </i> w
            </button>
          </a>
          <?php
          if ($pidb_data->getType()->getId() == 2):
            ?>
            <a href="/pidb/<?php echo $pidb_id ?>/printI" target="_blank">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
                <i class="fas fa-print"> </i> I
              </button>
            </a>
            <a href="/pidb/<?php echo $pidb_id ?>/printIW" target="_blank">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
                <i class="fas fa-print"> </i> IW
              </button>
            </a>
            <?php
          endif;
          // Export proforma.
          if ($pidb_data->getType()->getId() == 1):
            ?>
            <a href="/pidb/<?php echo $pidb_id ?>/exportProformaToDispatch">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" <?php echo ($pidb_data->getIsArchived() == 1 ? 'title="Predračun je arhiviran i nije ga moguće izvesti u otpremnicu" disabled' : 'title="Izvezi u otpremnicu" ' ) ?> >
                <i class="fas fa-share"> </i> Otpremnica
              </button>
            </a>

            <a href="/projects/add?client_id=<?php echo $pidb_data->getClient()->getId() ?>&acc_doc_id=<?php echo
            $pidb_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Novi projekat!">
                <i class="fas fa-share"> </i> <i class="fas fa-project-diagram"></i>
              </button>
            </a>
            <?php
          endif;
          // Next and Previous button.
          if ($user_role_id == 1):
            if ($previous = $entityManager->getRepository('\App\Entity\AccountingDocument')->getPrevious($pidb_id, $pidb_data->getType()->getId())) :
              ?>
              <a href="/pidb/<?php echo $previous->getId() ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary mr-1">
                  <i class="fas fa-arrow-left"> </i>
                </button>
              </a>
            <?php
            endif;
            if ($next = $entityManager->getRepository('\App\Entity\AccountingDocument')->getNext($pidb_id, $pidb_data->getType()->getId())) :
              ?>
              <a href="/pidb/<?php echo $next->getId() ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary mr-1">
                  <i class="fas fa-arrow-right"> </i>
                </button>
              </a>
            <?php
            endif;
          endif;
          ?>
          <!-- Button trigger modal za evidentiranje transakcije -->
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addPayment" <?php echo ($pidb_data->getIsArchived() == 1 ? 'title="Predračun je arhiviran i nije moguće videntiranje transakcija" disabled' : 'title="Evidentiranje transakcija" ' ) ?>>
            <i class="fas fa-hand-holding-usd"></i>
          </button>
          <a href="/pidb/<?php echo $pidb_id ?>/transactions">
            <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" title="Pregled transakcija">
              <i class="fas fa-eye"></i> <i class="fas fa-dollar-sign"></i>
            </button>
          </a>
          <?php
          }
        }
      }

        if (isset($_GET['cashRegister'])):
            ?>
            <!-- Button trigger modal za cash input -->
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
            }
            else {
                // $date = $pidb->getDate();
                $date = date('Y-m-d');
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
            <form class="d-inline" target="_blank" action="printDailyCashReport.php">
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
