<!-- Edit Client Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">
      Izmena podataka o klijentu: <strong><?php echo $client_data->getName() ?></strong>
    </h6>
  </div>

  <div class="card-body px-2">

    <form action="<?php echo $_SERVER['PHP_SELF']. '?updateClient&client_id=' .$client_id; ?>" method="post">
      <input type="hidden" name="client_id" value="<?php echo $client_id ?>">
      <input type="hidden" name="type_id" value="<?php echo $client_type->getId() ?>">

      <div class="form-group row">
        <label for="selectTip" class="col-sm-3 col-lg-2 col-form-label text-right">Vrsta klijenta:</label>
        <div class="col-sm-4">
          <select id="selectTip" class="form-control" name="type_id" >
            <option value="<?php echo $client_type->getId() ?>"><?php echo $client_type->getName() ?></option>
            <?php
            $types = $entityManager->getRepository('\Roloffice\Entity\ClientType')->findAll();
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

      <div class="form-group row">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-6">
          <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $client_data->getName() ?>" >
        </div>
        <div class="col-sm-4">
          <input class="form-control" id="inputName" type="text" name="name_note" value="<?php echo $client_data->getNameNote() ?>" >
        </div>
      </div>

      <?php
      if ($client_type->getId() == 2):
        ?>
        <div class="form-group row">
          <label for="inputPIB" class="col-sm-3 col-lg-2 col-form-label text-right">PIB: </label>
          <div class="col-sm-4">
            <input class="form-control" id="inputPIB" type="text" name="pib" value="<?php echo $client_data->getLb() ?>"  maxlength="9" >	
          </div>
        </div>
        <?php
      endif;
      ?>

      <div class="form-group row">
        <label for="is_supplier" class="col-sm-3 col-lg-2 col-form-label text-right">Dobavljač:</label>
        <div class="col-sm-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_supplier" name="is_supplier" value="1" <?php echo ($client_data->getIsSupplier() == 0 ? '' : 'checked') ?> >
            <label class="form-check-label" for="is_supplier">Jeste</label>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <label for="selectCountry" class="col-sm-3 col-lg-2 col-form-label text-right">Država:</label>
        <div class="col-sm-4">
          <select id="selectCountry" class="form-control" name="country_id">
            <option value="<?php echo $client_country->getId() ?>"><?php echo $client_country->getName() ?></option>
            <?php
            $states = $entityManager->getRepository('\Roloffice\Entity\Country')->findBy(array(), array('name' => 'ASC'));
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

      <div class="form-group row">
        <label for="selectCity" class="col-sm-3 col-lg-2 col-form-label text-right">Naselje:</label>
        <div class="col-sm-4">
          <select id="selectCity" class="form-control" name="city_id">
            <option value="<?php echo $client_city->getId() ?>"><?php echo $client_city->getName() ?></option>
            <?php
            $citys = $entityManager->getRepository('\Roloffice\Entity\City')->findBy(array(), array('name' => 'ASC'));
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

      <div class="form-group row">
        <label for="selectStreet" class="col-sm-3 col-lg-2 col-form-label text-right">Ulica:</label>
        <div class="col-sm-4">
          <select id="selectStreet" class="form-control" name="street_id">>
            <option value="<?php echo $client_street->getId() ?>"><?php echo $client_street->getName() ?></option>
            <?php
            $streets = $entityManager->getRepository('\Roloffice\Entity\Street')->findBy(array(), array('name' => 'ASC'));
            foreach ($streets as $street) :
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

      <div class="form-group row">
        <label for="inputNum" class="col-sm-3 col-lg-2 col-form-label text-right">Broj:</label>
        <div class="col-sm-2">
          <input class="form-control" id="inputNum" type="text" name="home_number" value="<?php echo $client_data->getHomeNumber() ?>" >
        </div>
        <div class="col-sm-7">
          <input class="form-control" id="inputNum" type="text" name="address_note" value="<?php echo $client_data->getAddressNote() ?>" >
        </div>
      </div>

      <div class="form-group row">
        <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška: </label>
        <div class="col-sm-6">
          <textarea class="form-control" id="inputNote" rows="3" name="note"><?php echo $client_data->getNote() ?></textarea>	
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

    <hr>

    <!-- ========== Contacts ========== -->
    <h5>Kontakti</h5>
    <?php
    $client_contacts = $client_data->getContacts();
    foreach ($client_contacts as $client_contact):
      $client_contact_data = $entityManager->getRepository('\Roloffice\Entity\Contact')->findOneBy( array('id' =>$client_contact->getId()) );
      $client_contact_type = $client_contact_data->getType();
      ?>
      <form action="<?php echo $_SERVER['PHP_SELF']. '?updateContact&client_id=' .$client_id; ?>" method="post">
        <input type="hidden" name="contact_id" value="<?php echo $client_contact->getId() ?>">

        <div class="form-group row">
          <div class="col-sm-3">
            <select class="form-control" name="contact_type_id">>
              <option value="<?php echo $client_contact_type->getId() ?>"><?php echo $client_contact_type->getName() ?></option>
              <?php
              $contact_types = $entityManager->getRepository('\Roloffice\Entity\ContactType')->findAll();
              foreach ($contact_types as $contact_type):
                ?>
                <option value="<?php echo $contact_type->getId() ?>"><?php echo $contact_type->getName() ?></option>
                <?php
              endforeach;
              ?>
            </select>
          </div>

          <div class="col-sm-3">
            <input type="text" class="form-control" name="body" value="<?php echo $client_contact->getBody() ?>" placeholder="Unesi kontakt" >
          </div>

          <div class="col-sm-4">
            <input type="text" class="form-control" name="note" value="<?php echo $client_contact->getNote() ?>" placeholder="Unesi belešku" >
          </div>  

          <div class="col-sm-2">
            <button type="submit" class="btn btn-mini btn-secondary" title="Snimi izmenu kontakta!">
              <i class="fas fa-save"> </i>
            </button>
            <a onClick="javascript: return confirm('Da li ste sigurni da želite da obrišete kontakt?');" href="<?php echo '?deleteContact&client_id=' .$client_id. '&contact_id=' .$client_contact->getId() ?>" class="btn btn-mini btn-danger " title="Obriši kontakt!">
              <i class="fas fa-trash"> </i>
            </a>
          </div>

        </div>

      </form>
      <?php
    endforeach;
    ?>

  </div>
  <!-- End Card Body -->

</div>
