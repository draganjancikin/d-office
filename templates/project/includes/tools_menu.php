<?php
require 'add.php';
require 'create_project.php';
require 'update_project.php';
require 'create_project_note.php';
require 'delete_project_note.php';
require 'create_task.php';
require 'update_task.php';
require 'update_task_start_end.php';
require 'delete_task.php';
require 'create_task_note.php';
require 'delete_task_note.php';

?>
<div class="col-lg-12 px-2" id="topMeni">
  <div class="card mb-2">
    <div class="card-body py-1 px-2">

      <a href="/projects/">
        <button type="button" class="btn btn-sm btn-outline-secondary" title="Pregled Kanban table">
          <i class="fas fa-bars"></i> Kanban
        </button>
      </a>
      
      <a href="/projects/index.php?new">
        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Otvaranje novog projekta!">
          <!-- <i class="fas fa-plus"> </i> <i class="fas fa-folder"> </i> -->
          <i class="fas fa-project-diagram"></i>
        </button>
      </a>

      <?php
      if( ( isset($_GET['view']) || isset($_GET['edit']) ) && isset($_GET['project_id']) ):
      
        $project_id = filter_input(INPUT_GET, 'project_id');
        
        $project_data = $entityManager->find('\Roloffice\Entity\Project', $project_id);
        
        // in view case show edit button 
        if(isset($_GET['view'])):
          ?>
          <a href="?edit&project_id=<?php echo $project_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Link ka stranici za izmenu podataka o projektu!">
              <i class="fas fa-edit"> </i> <!-- Izmena -->
            </button>
          </a>
          <?php
        endif;

        // in edit case show view button
        if(isset($_GET['edit'])):
          ?>
          <a href="?view&project_id=<?php echo $project_id ?>">
            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Link ka stranici za pregled podataka o projektu">
              <i class="fas fa-eye"> </i> <!-- Pregled -->
            </button>
          </a>
          <?php
        endif;
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
        <a href="/pidb/index.php?new&client_id=<?php echo $project_data->getClient()->getId() ?>&project_id=<?php echo $project_data->getId() ?>">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje novog predračuna!">
            <i class="fas fa-arrow-right"> </i> Predračun
          </button>
        </a>

        <!-- Open the material-order from project -->
        <a href="/orders/index.php?new&project_id=<?php echo $project_data->getId() ?>">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje nove narudžbenice za materijal!">
            <i class="fas fa-arrow-right"> </i> Narudžbenica
          </button>
        </a>

        <!-- Open the cutting from project -->
        <a href="/cutting/index.php?new&client_id=<?php echo $project_data->getClient()->getId() ?>&project_id=<?php echo $project_data->getId() ?>">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1" title="Otvaranje nove krojne liste!">
            <i class="fas fa-arrow-right"> </i> <i class="fa fa-cut"> </i> 
          </button>
        </a>

        <!-- Preview and printing project task -->
        <a href="printProjectTask.php?project_id=<?php echo $project_id ?>" title="Izvoz radnog naloga u PDF [new window]" target="_blank">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1">
            <i class="fa fa-print"></i> Radni nalog
          </button>
        </a>

        <!-- Preview and printing Instalation Record (Log) -->
        <a href="/tcpdf/examples/printInstallationRecord.php?project_id=<?php echo $project_id ?>" title="Štampa zapisnika o ugradnji (montaži)" target="_blank">
          <button type="button" class="btn btn-sm btn-outline-secondary mr-1">
            <i class="fa fa-print"></i> Zapisnik o ugradnji
          </button>
        </a>
      
      <?php
      endif;
      ?>

    </div>
    <!-- End Card Body -->
  </div>
  <!-- End Card -->
</div>
