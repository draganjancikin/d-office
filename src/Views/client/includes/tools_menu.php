<div class="col-sm-12 px-2" id="topMeni">

  <div class="card mb-2">
    <div class="card-body p-1">

      <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

        <div class="btn-group mb-1 mb-sm-0" role="group" aria-label="First group">
          <a href="/clients/add" class="btn btn-sm btn-outline-secondary mr-1" title="Upis novog klijenta!">
            <i class="fas fa-plus"> </i>
            <i class="fas fa-user"> </i>
          </a>
        </div>

        <?php

        if (!str_contains($_GET['url'], 'clients')) {

          // check if $_GET has url key.
          if (isset($_GET['url'])) {
            $url = $_GET['url'];
            $url = explode('/', $url);

            if (count($url) == 2 && $url[0] == 'client' && is_numeric($url[1])) {
              $client_id = $entityManager->getRepository('\App\Entity\Client')->checkGetClient($url[1]);
              $client = $entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);
              ?>
              <a href="/client/<?php echo $client_id ?>/edit" class="btn btn-sm btn-outline-secondary
              mr-1" title="Idi na stranicu za izmenu podataka o klijentu!">
                <i class="fas fa-edit"> </i> Izmena
              </a>
              <?php
            }
            if (count($url) == 3 && $url[0] == 'client' && is_numeric($url[1]) && $url[2] == 'edit') {
              $client_id = $entityManager->getRepository('\App\Entity\Client')->checkGetClient($url[1]);
              $client = $entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);
              ?>
              <a href="/client/<?php echo $client_id ?>" class="btn btn-sm btn-outline-secondary
              mr-1" title="Idi na stranicu za pregled podataka o klijentu">
                <i class="fas fa-eye"> </i> Pregled
              </a>
              <?php
            }

          }
          ?>
          <!-- Button trigger for modal addContact. -->
          <a href="#" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addContact" title="Dodaj novi kontakt!">
            <i class="fas fa-plus"> </i> Kontakt
          </a>
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

          <?php
        }
        ?>

      </div>

    </div>
  </div>
</div>
