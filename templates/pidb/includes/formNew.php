<!-- New Document Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Otvaranje novog dokumenta:</h6>
  </div>
  <div class="card-body p-2">
    <form class="form-horizontal" role="form" action="<?php echo $_SERVER['PHP_SELF'] . '?new&addPidb'; ?>" method="post">
      <?php 
      if(isset($_GET['project_id'])):
        ?>
        <input type="hidden" name="project_id" value="<?php echo $_GET['project_id'] ?>">
        <?php
      endif;
      ?>

      <div class="form-group row">
        <label for="select_pidb_tip_id" class="col-sm-3 col-lg-2 col-form-label text-right">Vrsta dokumenta:</label>
        <div class="col-sm-4">
          <div id="vps">
            <select id="select_pidb_tip_id" name="pidb_tip_id" class="form-control">
              <option value="1">Predračun</option>
              <option value="2">Otpremnica</option>
              <option value="4">Povratnica</option>
            </select>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <label for="select_client_id" class="col-sm-3 col-lg-2 col-form-label text-right">Klijent: </label>
        <div class="col-sm-4">
          <select id="select_client_id" class="form-control" name="client_id" required="required">
          <?php
          if(isset($_GET['client_id'])){
            $client_id = htmlspecialchars($_GET["client_id"]);
            $client_data = $client->getClient($client_id);
            echo '<option value="'.$client_data['id'].'">'.$client_data['name'].'</option>';
          }else{
            echo '<option value="">naziv klijenta</option>';
          }
          $clients = $client->getClients();
          foreach ($clients as $client) {
            echo '<option value="' .$client['id']. '">' .$client['name']. '</option>';
          }
          ?>
          </select>
        </div>
      </div>  
    
      <div class="form-group row">
        <label for="input_title" class="col-sm-3 col-lg-2 col-form-label text-right" >Naslov: </label>
        <div class="col-sm-6">
          <input id="input_title" class="form-control" type="text" name="title" value="" />
        </div>
      </div>

      <div class="form-group row">
        <label for="input_note" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška: </label>
        <div class="col-md-10">
          <textarea id="input_note" class="form-control" rows="3" name="note" placeholder="Unesite belešku uz dokument"></textarea>
        </div>
        </div>

        <div class="form-group row">
        <div class="col-sm-3 offset-sm-3 offset-lg-2">
          <button type="submit" class="btn btn-sm btn-success" title="Snimi izmene podataka o klijentu!">
            <i class="fas fa-save"></i> Snimi
          </button>
        </div>
        </div>

    </form>
  </div>
</div>
