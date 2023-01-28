<?php
function basicValidation($str){
  return trim(htmlspecialchars($str));
}
require 'create_client.php';
require 'create_contact.php';
require 'create_country.php';
require 'create_city.php';
require 'create_street.php';
require 'update_client.php';
require 'update_contact.php';
require 'delete_contact.php';
?>
<div class="col-sm-12 px-2" id="topMeni">

    <div class="card mb-2">
        <div class="card-body p-1">

            <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

                <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="First group">
                    <a href="/clients/index.php?new" class="btn btn-sm btn-outline-secondary mr-1" title="Upis novog klijenta!">
                        <i class="fas fa-plus"> </i>
                        <i class="fas fa-user"> </i>
                    </a>
                </div>

                <?php
                if (isset($_GET['view']) || isset($_GET['edit'])):
                    if (isset($_GET['client_id'])) {
                        $client_id = $entityManager->getRepository('\Roloffice\Entity\Client')->checkGetClient($_GET['client_id']);
                    } else {
                        die('<script>location.href = "/clients/" </script>');
                    }
                    $client = $entityManager->getRepository('\Roloffice\Entity\Client')->getClientData($client_id);
                    ?>
                    <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="Second group">
                        <?php
                        // In view case show edit button.
                        if (isset($_GET['view'])):
                            ?>
                            <a href="?edit&client_id=<?php echo $client_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Idi na stranicu za izmenu podataka o klijentu!">
                                <i class="fas fa-edit"> </i> Izmena
                            </a>
                            <?php
                        endif;

                        // In edit case show view button.
                        if(isset($_GET['edit'])):
                            ?>
                            <a href="?view&client_id=<?php echo $client_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Idi na stranicu za pregled podataka o klijentu">
                                <i class="fas fa-eye"> </i> Pregled
                            </a>
                        <?php
                        endif;
                        ?>

                        <!-- Button trigger for modal addContact. -->
                        <a href="#" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addContact" title="Dodaj novi kontakt!">
                            <i class="fas fa-plus"> </i> Kontakt
                        </a>
                    </div>

                    <div class="btn-group" role="group" aria-label="Third group">
                        <!-- Open new project with client data. -->
                        <a href="/projects/index.php?new&client_id=<?php echo $client_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje novog projekta!">
                            <i class="fas fa-arrow-right"> </i> Projekat
                        </a>

                        <!-- Open new proforma-invoice with client data. -->
                        <a href="/pidb/index.php?new&client_id=<?php echo $client_id ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje novog predračuna!">
                            <i class="fas fa-arrow-right"> </i> Predračun
                        </a>

                        <!-- Open new cutting with client data. -->
                        <a href="/cutting/index.php?new&client_id=<?php echo $client_id ?>" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove krojne liste!">
                            <i class="fas fa-arrow-right"> </i> <i class="fas fa-cut"> </i>
                        </a>
                    </div>
                    <?php
                endif;
                ?>

            </div>

        </div>
    </div>
</div>
