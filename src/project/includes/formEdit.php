<?php
if($project_data == 'noProject'):
  die('<script>location.href = "/project/" </script>');
else:
  $client_data = $client->getClient($project_data['client_id']);
  ?>
  <div class="card mb-4">
    <div class="card-header p-2">
      <h6 class="m-0 text-dark">
        <i class="fa fa-folder"> </i>
        # <?php echo str_pad($project_data['pr_id'], 4, "0", STR_PAD_LEFT).' - '.$project_data['title']; ?>
      </h6>
    </div>
    <div class="card-body p-2">
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?edit&project_id=' . $project_id . '&editProject'; ?>" method="post">
        
        <div class="form-group row">
          <label for="inputDate" class="col-sm-3 col-lg-2 col-form-label text-right">Datum: </label>
          <div class="col-sm-3">
            <input id="inputDate" class="form-control" type="text" value="<?php echo date("d M Y") ?>" disabled/>
          </div>
        </div>

        <div class="form-group row">
          <label for="selectClient" class="col-sm-3 col-lg-2 col-form-label text-right">Klijent: </label>
          <div class="col-sm-6">
            <select id="selectClient" name="client_id" class="form-control" required >
              <option value="<?php echo $client_data['id']; ?>"><?php echo $client_data['name']; ?></option>
              <?php
              $clients = $client->getClients();
              foreach ($clients as $client) {
                echo '<option value="' .$client['id']. '">' .$client['name']. '</option>';
              }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label for="inputTitle" class="col-sm-3 col-lg-2 col-form-label text-right">Naslov: </label>
          <div class="col-sm-8">
            <input id="inputTitle" type="text" class="form-control" name="title" value="<?php echo $project_data['title']; ?>" maxlength="64" placeholder="Unesite naslov projekta">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-lg-2 col-form-label text-right">Status:</label>
          <div class="col-sm-4">

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="active" value="1" <?php echo ( $project_data['status'] == 1 ? 'checked' : ''); ?>>
              <label class="form-check-label" for="active">Aktivan</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="wait" value="2" <?php echo ( $project_data['status'] == 2 ? 'checked' : ''); ?>>
              <label class="form-check-label" for="wait">Na čekanju</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="archived" value="3" <?php echo ( $project_data['status'] == 3 ? 'checked' : ''); ?>>
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
