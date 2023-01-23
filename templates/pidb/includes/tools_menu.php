<?php
require 'update_preferences.php';
require 'create__accounting_document.php';
require 'update__accounting_document.php';
require 'add__article__to__accounting_document.php';
require 'edit__article__in__accounting_document.php';
require 'add__payment__to__accounting_document.php';
require 'edit__transaction.php';
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

            <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

                <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="First group">
                    <a href="/pidb/?new" class="btn btn-sm btn-outline-secondary" title="Otvaranje novog dokumenta!">
                        <i class="fas fa-plus"></i> Dokument
                    </a>
                </div>

                <?php
                if ( isset($_GET['view']) || isset($_GET['edit']) || isset($_GET['editArticle']) ):
                    $pidb_id = filter_input(INPUT_GET, 'pidb_id');
                    $pidb_data = $entityManager->find('\App\Entity\AccountingDocument', $pidb_id);
                    if (!$pidb_data) {
                        die('<script>location.href = "/pidb/"</script>');
                    }
                    $client = $entityManager->getRepository('\App\Entity\Client')->getClientData($pidb_data->getClient()->getId());

                    $all_articles = $entityManager->getRepository('\App\Entity\Article')->findAll();
                    ?>
                    <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="Second group">
                        <?php
                        // In view case show edit button.
                        if (isset($_GET['view'])):
                            ?>
                            <a href="?edit&pidb_id=<?php echo $pidb_id ?>" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o projektu!">
                              <i class="fas fa-edit"></i> <!-- Izmena -->
                            </a>
                            <?php
                        endif;

                        // in edit case show view button.
                        if (isset($_GET['edit'])):
                            ?>
                            <a href="?view&pidb_id=<?php echo $pidb_id ?>" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o projektu!">
                              <i class="fas fa-eye"></i> <!-- Pregled -->
                            </a>
                            <?php
                        endif;
                        ?>
                    </div>
                    <?php
                    if (isset($_GET['view']) || isset($_GET['edit'])):
                        ?>
                        <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="Third group">
                            <!-- Button trigger modal za dodavanje proizvoda u dokument -->
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addArticle" title="Dodaj novi proizvod!">
                                <i class="fas fa-plus"></i> Proizvod
                            </button>
                        </div>

                        <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="Fifth group">
                            <!-- Print Buttons -->
                            <a href="printAccountingDocument?accounting_document__id=<?php echo $pidb_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            <a href="printAccountingDocumentW?accounting_document__id=<?php echo $pidb_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!" target="_blank">
                                <i class="fas fa-print"></i> w
                            </a>

                            <?php
                            if ($pidb_data->getType()->getId() == 2):
                                ?>
                                <a href="printAccountingDocumentI?accounting_document__id=<?php echo $pidb_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!" target="_blank">
                                    <i class="fas fa-print"></i> I
                                </a>
                                <a href="printAccountingDocumentIW?accounting_document__id=<?php echo $pidb_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!" target="_blank">
                                    <i class="fas fa-print"></i> IW
                                </a>
                                <?php
                            endif;
                            ?>
                        </div>
                        <?php
                        // Export proforma.
                        if ($pidb_data->getType()->getId() == 1):
                            ?>
                            <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="Sixth group">
                                <a href="?edit&pidb_id=<?php echo $pidb_id ?>&exportProformaToDispatch" class="btn btn-sm btn-outline-secondary mr-1" <?php echo ($pidb_data->getIsArchived() == 1 ? 'title="Predračun je arhiviran i nije ga moguće izvesti u otpremnicu" disabled' : 'title="Izvezi u otpremnicu" ' ) ?> >
                                    <i class="fas fa-share"></i> Otpremnica
                                </a>
                                <a href="/projects/index.php?new&client_id=<?php echo $pidb_data->getClient()->getId() ?>&acc_doc_id=<?php echo $pidb_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Novi projekat!">
                                    <i class="fas fa-share"></i>
                                    <i class="fas fa-project-diagram"></i> <!-- Projekat -->
                                </a>
                            </div>
                            <?php
                        endif;

                    endif;

                    // Next and Previous button.
                    if ($user_role_id == 1 && isset($_GET['edit'])):
                        if ($previous = $entityManager->getRepository('\App\Entity\AccountingDocument')->getPrevious($pidb_id, $pidb_data->getType()->getId())) :
                            ?>
                            <a href="?edit&pidb_id=<?php echo $previous->getId() ?> "class="btn btn-sm btn-outline-secondary mr-1" title="Predhodna!">
                              <i class="fas fa-arrow-left"></i>
                            </a>
                            <?php
                        endif;
                        if ($next = $entityManager->getRepository('\App\Entity\AccountingDocument')->getNext($pidb_id, $pidb_data->getType()->getId())) :
                            ?>
                            <a href="?edit&pidb_id=<?php echo $next->getId() ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Sledeca!">
                              <i class="fas fa-arrow-right"></i>
                            </a>
                            <?php
                        endif;
                    endif;

                endif;

                if (isset($_GET['view']) || isset($_GET['edit'])):
                    ?>
                    <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="Seventh group">
                        <!-- Button trigger modal za evidentiranje transakcije -->
                        <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addPayment" <?php echo ($pidb_data->getIsArchived() == 1 ? 'title="Predračun je arhiviran i nije moguće videntiranje transakcija" disabled' : 'title="Evidentiranje transakcija" ' ) ?>>
                            <i class="fas fa-hand-holding-usd"></i>
                        </button>
                        <a href="/pidb/?transactions&pidb_id=<?php echo $pidb_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Pregled transakcija">
                            <i class="fas fa-eye"></i>
                            <i class="fas fa-dollar-sign"></i>
                        </a>
                    </div>
                    <?php
                endif;

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
                    $date = $_GET['date'] ?? date('Y-m-d');
                    ?>
                    <!--
                    <a href="/tcpdf/examples/printDailyCashReport.php&" title="PDF [new window]" target="_blank">
                        <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
                            <i class="fas fa-print"> </i>
                            <i class="fas fa-cash-register"></i>
                        </button>
                    </a>
                    -->
                    <form class="d-inline" target="_blank" action="printDailyCashReport">
                        <input type="hidden" name="date" value="<?php echo $date ?>">
                        <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" title="Štampaj!">
                            <i class="fas fa-print"></i>
                            <i class="fas fa-cash-register"></i>
                        </button>
                    </form>
                    <?php
                endif;
                ?>
            </div>
        </div><!-- End of Card body -->
    </div>

</div>
<!-- /#topMeni -->
