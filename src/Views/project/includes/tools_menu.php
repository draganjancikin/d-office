<div class="col-lg-12 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/projects/">
        <button type="button" class="btn btn-sm btn-outline-secondary" title="Pregled Kanban table">
          <i class="fas fa-bars"></i> Kanban
        </button>
      </a>
      
      <a href="/projects/add">
        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Otvaranje novog projekta!">
          <!-- <i class="fas fa-plus"> </i> <i class="fas fa-folder"> </i> -->
          <i class="fas fa-project-diagram"></i>
        </button>
      </a>

      <?php
      if (!str_contains($_GET['url'], 'projects')) {
        if (isset($_GET['url'])) {
          $url = $_GET['url'];
          $url = explode('/', $url);

          // In view case show edit button.
          if (count($url) == 2 && $url[0] == 'project' && is_numeric($url[1])) {
            ?>
            <a href="/project/<?php echo $project_id . '/edit' ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Link ka stranici za izmenu podataka o projektu!">
                <i class="fas fa-edit"> </i> <!-- Izmena -->
              </button>
            </a>
            <?php
          }

          // In edit case show view button.
          if (count($url) == 3 && $url[0] == 'project' && is_numeric($url[1]) && $url[2] == 'edit') {
            ?>
            <a href="/project/<?php echo $project_id ?>">
              <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Link ka stranici za pregled podataka o projektu">
                <i class="fas fa-eye"> </i> <!-- Pregled -->
              </button>
            </a>
            <?php
          }

          ?>
          <!-- Button, okidač za modal addTask -->
          <a href="#">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#createTask" title="Dodavanje novog zadatka!">
              <i class="fas fa-plus"></i> <i class="fa fa-tasks"> </i>
            </button>
          </a>

          <!-- Button, okidač za modal addFile -->
          <a href="#">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addFile" title="Dodavanje fajla!">
              <i class="fas fa-plus"></i> <i class="fa fa-file"> </i>
            </button>
          </a>

          <!-- Open the proforma-invoice from project -->
          <a href="/pidbs/add?client_id=<?php echo $project_data->getClient()->getId() ?>&project_id=<?php
          echo $project_data->getId() ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje novog predračuna!">
              <i class="fas fa-arrow-right"> </i> Predračun
            </button>
          </a>

          <!-- Open the material-order from project -->
          <a href="/orders/add?project_id=<?php echo $project_data->getId() ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje nove narudžbenice za materijal!">
              <i class="fas fa-arrow-right"> </i> Narudžbenica
            </button>
          </a>

          <!-- Open the cutting from project -->
          <a href="/cuttings/add?client_id=<?php echo $project_data->getClient()->getId()
          ?>&project_id=<?php echo $project_data->getId() ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje nove krojne liste!">
              <i class="fas fa-arrow-right"> </i> <i class="fa fa-cut"> </i>
            </button>
          </a>

          <!-- Preview and printing project task -->
          <a href="/project/<?php echo $project_id ?>/printProjectTask" title="Izvoz radnog naloga u PDF
          [new window]" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1">
              <i class="fa fa-print"></i> Radni nalog
            </button>
          </a>

          <!-- Preview and printing Instalation Record (Log) -->
          <a href="/project/<?php echo $project_id ?>/printInstallationRecord" title="Štampa zapisnika o
          ugradnji (montaži)" target="_blank">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1">
              <i class="fa fa-print"></i> Zapisnik o ugradnji
            </button>
          </a>

          <?php
        }
      }
      ?>

    </div>
    <!-- End Card Body -->
  </div>
  <!-- End Card -->
</div>
