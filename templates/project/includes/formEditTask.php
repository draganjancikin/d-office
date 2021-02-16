<?php

$task_id = filter_input(INPUT_GET, 'task_id');
$project_id = filter_input(INPUT_GET, 'project_id');
$task_data = $project->getTask($task_id);

if($task_data=='noProject'):
  // ako nema task ne postoji
  die('<script>location.href = "/project/" </script>');
else:

  if(isset($_GET["alertEnd"])):
    ?>
    <div class="row">
      <div class="col-md-12">
                
        <div class="alert alert-warning alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-warning"></i> PAŽNJA!</h4>
          Pokušali ste da postavite datum završetka realizacije zadatka, 
          a to nije moguće jer realizacija zadatka nije ni započeta!
        </div>
                
      </div>
    </div>
    <?php
  endif;
  ?>
  
  <div class="card border-<?php echo $task_data['class']; ?> mb-2 w-75">
  
    <div class="card-header bg-<?php echo $task_data['class']; ?> p-2">
      <h6 class="d-inline m-0">
        <i class="fa fa-tasks"> </i>
        <?php echo $task_data['tip']. ': ' .$task_data['title']; ?>
        <?php
        if($user_role_id==1):
          ?>
          <small class="">
            <?php echo '('. date('d-M-Y', strtotime($task_data['date'])). '), ' .$task_data['user_name']; ?>
          </small>
          <?php
        endif;
        ?>
      </h6>
      <!-- povratak na pregled projekta -->
      <a href="?view&project_id=<?php echo $project_id; ?>">
        <button type="button" class="btn btn-sm btn-light float-right" title="Povratak na pregled projekta!">
          <i class="fas fa-reply"></i>
        </button>
      </a>

    </div>
    <!-- End Card Header -->
  
    <div class="card-body p-2">

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?editTask&task_id='.$task_id.'&project_id='.$project_id; ?>" method="post">

        <input type="hidden" name="start" value="<?php echo $task_data['start'] ?>"/>
        <input type="hidden" name="end" value="<?php echo $task_data['end'] ?>"/>

        <div class="form-group row">
          <label for="inputTitle" class="col-sm-3 col-lg-2 col-form-label text-right">Naslov: </label>
          <div class="col-sm-8">
            <input id="inputTitle" type="text" class="form-control" name="title" value="<?php echo $task_data['title']; ?>" maxlength="64" placeholder="Unesite naslov zadatka">
          </div>
        </div>

        <div class="form-group row">
          <label for="inputStart" class="col-sm-3 col-lg-2 col-form-label text-right">Start: </label>
          <div class="col-sm-4">
            <?php 
            if ($task_data['start'] == '0000-01-01 00:00:00'):
              ?>
              <a href="?editTask&project_id=<?php echo $project_id; ?>&task_id=<?php echo $task_id; ?>&setTaskStart">
                <button type="button" class="btn btn-sm btn-secondary" title="Postavi početak realizacije zadatka!">
                  <i class="fa fa-hand-pointer-o"></i> 
                  Postavi datum
                </button>
              </a>
              <?php
            else:
              ?>
              <input id="inputStart" class="form-control" type="text" name="start" value="<?php echo date('d-M-Y', strtotime($task_data['start'])); ?>" />
              <?php
            endif;
            ?>
          </div>
        </div>

        <div class="form-group row">
          <label for="selectEmployee" class="col-sm-3 col-lg-2 col-form-label text-right">Izvršilac: </label>
          <div class="col-sm-5">
            <select id="selectEmployee" name="employee_id" class="form-control">
              <?php
              echo '<option value="'.$task_data['employee_id'].'">'.$task_data['employee_name'].'</option>';
              $employees = $project->getEmployees();
              foreach ($employees as $employee) {
                echo '<option value="' .$employee['id']. '">' .$employee['name']. '</option>';
              }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-lg-2 col-form-label text-right">End: </label>
          <div class="col-sm-4">
            <?php 
            if ($task_data['end'] == '0000-01-01 00:00:00'):
              ?>
              <a href="?editTask&project_id=<?php echo $project_id; ?>&task_id=<?php echo $task_id; ?>&setTaskEnd">
                <button type="button" class="btn btn-sm btn-secondary" title="Postavi kraj realizacije zadatka!">
                  <i class="fa fa-hand-pointer-o"></i> 
                  Postavi datum
                </button>
              </a>
              <?php
            else:
              ?>
              <input class="form-control" type="text" name="end" value="<?php echo date('d-M-Y', strtotime($task_data['end'])); ?>" />
              <?php
            endif;
            ?>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-2 control-label"></label>
          <div class="col-sm-5">
            <button type="submit" class="btn btn-sm btn-success">
              <i class="fas fa-save"> </i> Snimi izmene
            </button>
          </div>
        </div>

      </form>
      
    </div>
    <!-- End Card Body -->
    
    <div class="card-header p-2">
      <h6 class="d-inline m-0">
        <i class="fas fa-pencil-alt"> </i>
        Beleške uz zadatak 
      </h6>
      <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Dodaj belešku uz zadatak!" data-toggle="modal" data-target="#addTaskNote">
        <i class="fas fa-plus"> </i> 
        <i class="fas fa-pencil-alt"> </i>
      </button>
    </div>
    <!-- End Card Header -->

    <div class="card-body p-2">
      <table class="table table-hover">
        <?php
        $date_temp = "";
        $task_notes = $project->getTaskNotesByProject($task_id);
        foreach ($task_notes as $task_note):
          ?>
          <tr>
            <td class="px-1 width-95">
              <?php 
              if(date('d-M-Y', strtotime($task_note['date'])) == $date_temp) {

              }else{
                echo date('d-M-Y', strtotime($task_note['date']));
              }
              ?>
            </td>
            <td class="px-1">
              <?php echo '- ' .$task_note['note']; ?>
              <?php echo ($user_role_id==1 ? '<span class="badge badge-secondary">'.$task_note['user_name'].'</span>' : '' ); ?>
            </td>
            <?php
            if($user_role_id==1):
              ?>
              <td>
                <!-- ovaj link treba da obriše belešku uz zadatak -->
                <a href="?view&project_id=<?php echo $project_id; ?>&task_note_id=<?php echo $task_note['id']; ?>&task_id=<?php echo $task_id; ?>&delTaskNote"><i class="fas fa-trash-alt"></i></a>
              </td>
              <?php
            endif;
            ?>
          </tr>
          <?php
          $date_temp = date('d-M-Y', strtotime($task_note['date']));
        endforeach;
        ?>
      </table>
    </div>
    <!-- End Card Body -->

  </div>
  <!-- End Card -->
  
  <?php
endif;
