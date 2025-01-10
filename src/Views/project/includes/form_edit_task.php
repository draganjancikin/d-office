<?php
if (isset($_GET["alertEnd"])):
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

<div class="col-lg-12 px-2">
  <div class="card border-<?php echo $task_data['class'] ?> mb-2 w-75">

    <div class="card-header bg-<?php echo $task_data['class'] ?> p-2">
      <h6 class="d-inline m-0">
        <i class="fa fa-tasks"> </i>
        <?php echo $task->getType()->getName(). ': ' .$task->getTitle() ?>
        <?php
        if ($user_role_id == 1):
          ?>
          <small class="">
            <?php echo '('. $task->getCreatedAt()->format('d-M-Y'). '), ' .$task->getCreatedByUser()->getUsername() ?>
          </small>
          <?php
        endif;
        ?>
      </h6>
      <!-- povratak na pregled projekta -->
      <a href="/project/<?php echo $project_id ?>">
        <button type="button" class="btn btn-sm btn-light float-right" title="Povratak na pregled projekta!">
          <i class="fas fa-reply"></i>
        </button>
      </a>

    </div>
    <!-- End Card Header -->

    <div class="card-body p-2">

      <form action="<?php echo '/project/' . $project_id . '/task/' . $task_id .'/edit' ?>"
            method="post">

        <div class="row mb-2">
          <label for="inputTitle" class="col-sm-3 col-lg-2 col-form-label text-right">Naslov: </label>
          <div class="col-sm-8">
            <input id="inputTitle" type="text" class="form-control form-control-sm" name="title"
                   value="<?php echo $task->getTitle() ?>" maxlength="64" placeholder="Unesite naslov zadatka">
          </div>
        </div>

        <div class="row mb-2">
          <label for="inputStart" class="col-sm-3 col-lg-2 col-form-label text-right">Start: </label>
          <div class="col-sm-4">
            <?php
            if ($task->getStartDate()->format('Y-m-d H:i:s') == "1970-01-01 00:00:00"):
              ?>
              <a href="/project/<?php echo $project_id; ?>/task/<?php echo $task_id ?>/setStartDate">
                <button type="button" class="btn btn-sm btn-secondary" title="Postavi početak realizacije zadatka!">
                  <i class="fa fa-hand-pointer-o"></i>
                  Postavi datum
                </button>
              </a>
              <input type="hidden" name="start" value="<?php echo $task->getStartDate()->format('Y-m-d H:i:s') ?>"/>
              <?php
            else:
              ?>
              <input id="inputStart" class="form-control form-control-sm" type="text" name="start"
                     value="<?php echo $task->getStartDate()->format('d-M-Y') ?>" />
              <?php
            endif;
            ?>
          </div>
        </div>

        <div class="row mb-2">
          <label for="selectEmployee" class="col-sm-3 col-lg-2 col-form-label text-right">Izvršilac: </label>
          <div class="col-sm-5">
            <select id="selectEmployee" name="employee_id" class="form-select form-select-sm">
              <?php
              if (empty($task->getEmployee())) {
                echo '<option value="">Izaberi izvršioca zadatka</option>';
              }
              else {
                echo '<option value="'.$task->getEmployee()->getId().'">'.$task->getEmployee()->getName().'</option>';
              }
              $employees_list = $entityManager->getRepository('\App\Entity\Employee')->findBy(array(), array('name' => "ASC"));
              foreach ($employees_list as $employee) {
                echo '<option value="' .$employee->getId(). '">' .$employee->getName(). '</option>';
              }
              ?>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label class="col-sm-3 col-lg-2 col-form-label text-right">End: </label>
          <div class="col-sm-4">
            <?php
            if ($task->getEndDate()->format('Y-m-d H:i:s') == "1970-01-01 00:00:00"):
              ?>
              <a href="/project/<?php echo $project_id; ?>/task/<?php echo $task_id ?>/setEndDate">
                <button type="button" class="btn btn-sm btn-secondary" title="Postavi kraj realizacije zadatka!">
                  <i class="fa fa-hand-pointer-o"></i>
                  Postavi datum
                </button>
              </a>
              <input type="hidden" name="end" value="<?php echo $task->getEndDate()->format('Y-m-d H:i:s') ?>"/>
              <?php
            else:
              ?>
              <input class="form-control form-control-sm" type="text" name="end" value="<?php echo $task->getEndDate()
                ->format('d-M-Y') ?>" />
              <?php
            endif;
            ?>
          </div>
        </div>

        <div class="row mb-2">
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
        $task_notes = $entityManager->getRepository('\App\Entity\ProjectTaskNote')->findBy(array('project_task' => $task_id), array());
        foreach ($task_notes as $task_note):
          ?>
          <tr>
            <td class="px-1 width-95">
              <?php
              if ($task_note->getCreatedAt()->format('d-M-Y') == $date_temp ) {

              }
              else {
                echo $task_note->getCreatedAt()->format('d-M-Y');
              }
              ?>
            </td>
            <td class="px-1">
              <?php echo '- ' .$task_note->getNote() ?>
              <?php echo ($user_role_id==1 ? '<span class="badge badge-secondary">'.$task_note->getCreatedByUser()->getUsername().'</span>' : '' ); ?>
            </td>
            <?php
            if($user_role_id==1):
              ?>
              <td>
                <!-- ovaj link treba da obriše belešku uz zadatak -->
                <a href="/project/<?php echo $project_id ?>/task/<?php echo $task_id ?>/note/<?php echo
                $task_note->getId() ?>/delete"><i class="fas fa-trash-alt"></i></a>
              </td>
              <?php
            endif;
            ?>
          </tr>
          <?php
          $date_temp = $task_note->getCreatedAt()->format('d-M-Y');
        endforeach;
        ?>
      </table>
    </div>
    <!-- End Card Body -->

  </div>
  <!-- End Card -->
</div>
