<?php
if($project_data == 'noProject'):
  die('<script>location.href = "/project/" </script>');
else:
  $client_data = $entityManager->find('\Roloffice\Entity\Client', $project_data->getClient()->getId() );
  ?>
  <div class="card mb-4">
    <div class="card-header p-2">
      <h6 class="m-0 text-dark">
        <i class="fa fa-folder"> </i>
        # <?php echo str_pad($project_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).' - '.$project_data->getTitle() ?>
      </h6>
    </div>
    <div class="card-body p-2">
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?updateProject&project_id=' . $project_id ?>" method="post">
        
        <div class="form-group row">
          <label for="inputDate" class="col-sm-3 col-lg-2 col-form-label text-right">Datum: </label>
          <div class="col-sm-3">
            <input id="inputDate" class="form-control" type="text" value="<?php echo date("d M Y") ?>" disabled/>
          </div>
        </div>

        <div class="form-group row">
            <label for="selectProjectPriority" class="col-sm-3 col-lg-2 col-form-label text-sm-right">Prioritet: </label>
            <div class="col-sm-3">
              <select id="selectProjectPriority" name="project_priority_id" class="form-control" required>
                <option value="<?php echo $project_data->getPriority()->getId() ?>"><?php echo $project_data->getPriority()->getName() ?></option>
                <?php
                $priority_list = $entityManager->getRepository('\Roloffice\Entity\ProjectPriority')->findBy(array(), array('id' => "ASC"));
                foreach( $priority_list as $priority_item):
                  ?>
                  <option value="<?php echo $priority_item->getId() ?>">
                    <?php echo $priority_item->getName() ?>
                  </option>
                  <?php
                endforeach;
                ?>
              </select>
            </div>
          </div>

        <div class="form-group row">
          <label for="selectClient" class="col-sm-3 col-lg-2 col-form-label text-right">Klijent: </label>
          <div class="col-sm-6">
            <select id="selectClient" name="client_id" class="form-control" required >
              <option value="<?php echo $client_data->getId() ?>"><?php echo $client_data->getName() ?></option>
              <?php
              $clients_list = $entityManager->getRepository('\Roloffice\Entity\Client')->findBy(array(), array('name' => "ASC"));
              foreach( $clients_list as $client_item):
                ?>
                <option value="<?php echo $client_item->getId() ?>">
                  <?php echo $client_item->getName() ?>
                </option>
                <?php
              endforeach;
              ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label for="inputTitle" class="col-sm-3 col-lg-2 col-form-label text-right">Naslov: </label>
          <div class="col-sm-8">
            <input id="inputTitle" type="text" class="form-control" name="title" value="<?php echo $project_data->getTitle() ?>" maxlength="64" placeholder="Unesite naslov projekta">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-lg-2 col-form-label text-right">Status:</label>
          <div class="col-sm-4">

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status_id" id="active" value="1" <?php echo ( $project_data->getStatus()->getId() == 1 ? 'checked' : ''); ?>>
              <label class="form-check-label" for="active">Aktivan</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status_id" id="wait" value="2" <?php echo ( $project_data->getStatus()->getId() == 2 ? 'checked' : ''); ?>>
              <label class="form-check-label" for="wait">Na čekanju</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status_id" id="archived" value="3" <?php echo ( $project_data->getStatus()->getId() == 3 ? 'checked' : ''); ?>>
              <label class="form-check-label" for="archived">U arhivi</label>
            </div>
          </div>
        
        </div>
            
        <div class="form-group row">
          <div class="col-sm-3 offset-sm-3 offset-lg-2">
            <button type="submit" class="btn btn-sm btn-success">
              <i class="fas fa-save"></i> Snimi
            </button>
          </div>
        </div> 

      </form>

    </div>

  </div>
  <!-- End Card -->
  <?php
endif;
