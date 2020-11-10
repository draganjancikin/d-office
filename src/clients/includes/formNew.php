<!-- New Client Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Upis podataka o novom klijentu:</h6>
  </div>

  <div class="card-body px-2">
    
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?newClient'; ?>" method="post">

      <div class="form-group row">
        <label for="selectTip" class="col-sm-3 col-lg-2 col-form-label text-right">Vrsta klijenta:</label>
        <div class="col-sm-4">
          <select id="selectTip" class="form-control" name="vps_id">
            <?php
            $vpses = $client->getVpses();
            foreach ($vpses as $vps) {
              echo '<option value="' .$vps['id']. '">' .$vps['name']. '</option>';
            }
            ?>
          </select>
        </div>
      </div>
    
      <div class="form-group row">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-6">
          <input class="form-control" id="inputName" type="text" name="name" placeholder="Unesite naziv klijenta" required >
        </div>
        <div class="col-sm-4">
          <input class="form-control" id="inputName" type="text" name="name_note" placeholder="Unesite belešku uz naziv klijenta" >
        </div>
        <div class="col-sm-12">
          <?php if (isset($_GET['name_error'])) echo 'Ime mora biti upisano' ?>
        </div>
      </div>
      
      <div id="pib"></div>

      <div class="form-group row">
        <label for="is_supplier" class="col-sm-3 col-lg-2 col-form-label text-right">Dobavljač:</label>
        <div class="col-sm-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_supplier" name="is_supplier" value="1">
            <label class="form-check-label" for="is_supplier">Jeste</label>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <label for="selectState" class="col-sm-3 col-lg-2 col-form-label text-right">Država:</label>
        <div class="col-sm-4">
          <select id="selectState" class="form-control" name="state_id">
            <option value="1">Srbija</option>
            <?php
            $states = $client->getStates();
            foreach ($states as $state) {
              echo '<option value="' .$state['id']. '">' .$state['name']. '</option>';
            }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label for="selectCity" class="col-sm-3 col-lg-2 col-form-label text-right">Naselje:</label>
        <div class="col-sm-4">
          <select id="selectCity" class="form-control" name="city_id">
            <option value="1">Izaberi naselje</option>
            <?php
            $citys = $client->getCitys();
            foreach ($citys as $city) {
              echo '<option value="' .$city['id']. '">' .$city['name']. '</option>';
            }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label for="sSelectStreet" class="col-sm-3 col-lg-2 col-form-label text-right">Ulica:</label>
        <div class="col-sm-4">
          <select id="selectStreet" class="form-control" name="street_id">
            <option value="1">Izaberi ulicu</option>
            <?php
            $streets = $client->getStreets();
            foreach ($streets as $street) {
              echo '<option value="' .$street['id']. '">' .$street['name']. '</option>';
            }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label for="disabledInputNum" class="col-sm-3 col-lg-2 col-form-label text-right">Broj:</label>
        <div class="col-sm-2">
          <input class="form-control" id="disabledInputNum" type="text" name="home_number" maxlength="8" placeholder="Unesite kućni broj">
        </div>
        <div class="col-sm-7">
          <input class="form-control" id="disabledInputNum" type="text" name="address_note" placeholder="Unesite belešku uz adresu" >
        </div>
      </div>

      <div class="form-group row">
        <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška: </label>
        <div class="col-sm-6">
          <textarea class="form-control" id="inputNote" rows="3" name="note" placeholder="Unesite belešku uz klijenta"></textarea>	
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-3 offset-sm-3 offset-lg-2"><button type="submit" class="btn btn-sm btn-success" title="Snimi podatake o klijentu!"><i class="fas fa-save"> </i> Snimi</button></div>
      </div>
    
    </form>
  </div>
  <!-- End of Card body -->
</div>
