<!-- Form: new Client -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Upis podataka o novom klijentu:</h6>
  </div>
  <div class="card-body px-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?createClient' ?>" method="post">
      <div class="row mb-2">
        <label for="selectTip" class="col-sm-3 col-form-label text-left text-sm-right">Vrsta klijenta:</label>
        <div class="col-sm-4">
          <select id="selectTip" class="form-select form-select-sm" name="type_id">
            <?php
            $types = $entityManager->getRepository('\App\Entity\ClientType')->findAll();
            foreach ($types as $type) :
              ?>
              <option value="<?php echo $type->getId() ?>">
                <?php echo $type->getName() ?>
              </option>
              <?php
            endforeach;
            ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputName" class="col-sm-3 col-form-label text-left text-sm-right">Naziv:</label>
        <div class="col-sm-9 mb-2">
          <input class="form-control form-control-sm" id="inputName" type="text" name="name"
                 placeholder="Unesite naziv klijenta" required>
        </div>
        <div class="col-sm-9 offset-sm-3">
          <input class="form-control form-control-sm" id="inputName" type="text" name="name_note"
                 placeholder="Unesite belešku uz naziv klijenta">
        </div>
        <div class="col-sm-12">
          <?php if (isset($_GET['name_error'])) echo 'Ime mora biti upisano' ?>
        </div>
        </div>

        <div id="pib"></div>

      <div class="row mb-2">
        <label for="is_supplier" class="col-sm-3 col-form-label text-left text-sm-right">Dobavljač:</label>
        <div class="col-sm-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_supplier" name="is_supplier" value="1">
            <label class="form-check-label" for="is_supplier">Jeste</label>
          </div>
        </div>
      </div>

      <div class="row mb-2">
        <label for="selectCountry" class="col-sm-3 col-form-label text-left text-sm-right">Država:</label>
        <div class="col-sm-5">
          <select id="selectCountry" class="form-select form-select-sm" name="country_id">
            <option value="1">Srbija</option>
            <?php
            $states = $entityManager->getRepository('\App\Entity\Country')->findBy(array(), array('name' => 'ASC'));
            foreach ($states as $state) :
              ?>
              <option value="<?php echo $state->getId() ?>">
                <?php echo $state->getName() ?>
              </option>
              <?php
            endforeach;
            ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <label for="selectCity" class="col-sm-3 col-form-label text-left text-sm-right">Naselje:</label>
        <div class="col-sm-5">
          <select id="selectCity" class="form-select form-select-sm" name="city_id">
            <option>Izaberite naselje</option>
            <?php
            $citys = $entityManager->getRepository('\App\Entity\City')->findBy(array(), array('name' => 'ASC'));
            foreach ($citys as $city) :
              ?>
              <option value="<?php echo $city->getId() ?>">
                <?php echo $city->getName() ?>
              </option>
              <?php
            endforeach;
            ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <label for="sSelectStreet" class="col-sm-3 col-form-label text-left text-sm-right">Ulica:</label>
        <div class="col-sm-5">
          <select id="selectStreet" class="form-select form-select-sm" name="street_id">
            <option>Izaberite ulicu</option>
            <?php
            $citys = $entityManager->getRepository('\App\Entity\Street')->findBy(array(), array('name' => 'ASC'));
            foreach ($citys as $street) :
              ?>
              <option value="<?php echo $street->getId() ?>">
                <?php echo $street->getName() ?>
              </option>
              <?php
            endforeach;
            ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <label for="disabledInputNum" class="col-sm-3 col-form-label text-left text-sm-right">Broj:</label>
        <div class="col-sm-2 mb-2">
          <input class="form-control form-control-sm" id="disabledInputNum" type="text" name="home_number"
                 maxlength="8">
        </div>
        <div class="col-sm-7">
          <input class="form-control form-control-sm" id="disabledInputNum" type="text" name="address_note"
                 placeholder="Unesite belešku uz adresu">
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputNote" class="col-sm-3 col-form-label text-left text-sm-right">Beleška: </label>
        <div class="col-sm-9">
          <textarea class="form-control form-control-sm" id="inputNote" rows="3" name="note" placeholder="Unesite belešku uz klijenta"></textarea>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-sm-3 offset-sm-3">
          <button type="submit" class="btn btn-sm btn-success" title="Snimi podatake o klijentu!">
            <i class="fas fa-save"> </i> Snimi
          </button>
        </div>
      </div>
    </form>
  </div><!-- End of Card body -->
</div>
