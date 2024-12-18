<!-- View Client Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">
      Pregled podataka o klijentu: <strong><?php echo $client['name'] ?></strong>
    </h6>
  </div>

  <div class="card-body px-2">

    <form>
      <fieldset disabled>

        <div class="row mb-2">
          <label for="disabledSelectTip" class="col-sm-3 col-form-label text-left text-sm-right">Vrsta klijenta:</label>
          <div class="col-sm-4">
            <select id="disabledSelectTip" class="form-select form-select-sm disabled">
              <option><?php echo $client['type'] ?></option>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputName" class="col-sm-3 col-form-label text-left text-sm-right">Naziv:</label>
          <div class="col-sm-9 mb-2">
            <input class="form-control form-control-sm" id="disabledInputName" type="text" value="<?php echo $client['name'] ?>" disabled />
          </div>
          <div class="col-sm-9 offset-sm-3">
            <input class="form-control form-control-sm" id="disabledInputName" type="text" value="<?php echo $client['name_note'] ?>"
                   title="Beleška uz naziv klijenta" disabled />
          </div>
        </div>

          <?php
          if ($client['type_id'] == 2):
            ?>
            <div class="row mb-2">
              <label for="disabledInputPIB" class="col-sm-3 col-form-label text-left text-sm-right">PIB: </label>
              <div class="col-sm-4">
                <input class="form-control form-control-sm" id="disabledInputPIB" type="text" value="<?php echo $client['lb'] ?>"  maxlength="9" disabled >
              </div>
            </div>
            <?php
          endif;
          ?>

        <div class="row mb-2">
          <label for="is_supplier" class="col-sm-3 col-form-label text-left text-sm-right">Dobavljač:</label>
          <div class="col-sm-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="is_supplier" name="is_supplier" <?php echo ($client['is_supplier'] ? 'checked' : '') ?> >
              <label class="form-check-label" for="is_supplier">Jeste</label>
            </div>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledSelectCountry" class="col-sm-3  col-form-label text-left text-sm-right">Država:</label>
          <div class="col-sm-5">
            <select id="disabledSelectCountry" class="form-select form-select-sm">
              <option><?php echo $client['country'] ?? '' ?></option>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledSelectCity" class="col-sm-3 col-form-label text-left text-sm-right">Naselje:</label>
          <div class="col-sm-5">
            <select id="disabledSelectCity" class="form-select form-select-sm">
              <option><?php echo $client['city'] ?? '' ?></option>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledSelectStreet" class="col-sm-3 col-form-label text-left text-sm-right">Ulica:</label>
          <div class="col-sm-5">
            <select id="disabledSelectStreet" class="form-select form-select-sm">
              <option><?php echo $client['street'] ?? '' ?></option>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputNum" class="col-sm-3 col-form-label text-left text-sm-right">Broj:</label>
          <div class="col-sm-2 mb-2">
            <input class="form-control form-control-sm" id="disabledInputNum" type="text" value="<?php echo $client['home_number'] ?>" disabled />
          </div>
          <div class="col-sm-7">
            <input class="form-control form-control-sm" id="disabledInputNum" type="text" value="<?php echo $client['address_note'] ?>"
                   title="Beleška uz adresu klijenta" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputNote" class="col-sm-3 col-form-label text-left text-sm-right">Beleška: </label>
          <div class="col-sm-9">
            <textarea class="form-control form-control-sm" id="disabledInputNote" rows="3" disabled><?php echo $client['note'] ?></textarea>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-sm-3 offset-sm-3">
            <button type="submit" class="btn btn-sm btn-secondary disabled" title="Snimi izmene podataka o klijentu!">
              <i class="fas fa-save"></i> Snimi
            </button>
          </div>
        </div>

      </fieldset>
    </form>
  </div> <!-- End of Card Body -->
  <div class="card-footer px-2">
    <p class="h5">Kontakti</p>
    <?php
    foreach ($client['contacts'] as $contact):
      $client_contact_data = $entityManager->getRepository('\Roloffice\Entity\Contact')->findOneBy( array('id' =>$contact->getId()) );
      $client_contact_type = $client_contact_data->getType();
      ?>
      <form>
        <fieldset >
          <div class="row mb-2">

            <div class="col-sm-3">
              <select class="form-select form-select-sm" disabled>
                <option><?php echo $client_contact_type->getName() ?></option>
              </select>
            </div>

            <div class="col-sm-3">
              <?php echo $client_contact_type->getId() == 2 ? '<a href="tel: ' . $contact->getBody() . '">' : '' ?>
                <input class="form-control form-control-sm" type="text" id="contact" value="<?php echo $contact->getBody() ?>"  placeholder="unesi kontakt" disabled>
              <?php echo $client_contact_type->getId() == 2 ? '</a>' : '' ?>
            </div>

            <div class="col-sm-4">
              <input class="form-control form-control-sm" type="text" id="contactNote" value="<?php echo $contact->getNote() ?>" placeholder="unesi belešku" disabled >
            </div>

            <div class="col-sm-2">
              <button type="submit" class="btn btn-secondary btn-sm disabled" title="Snimi izmenu kontakta!">
                <i class="fas fa-save"></i>
              </button>
              <a href="<?php echo $_SERVER['PHP_SELF']. '?edit&client_id=' . $client_id . '&contact_id=' . $contact->getId() . '&deleteContact'; ?>"
                 class="btn btn-secondary btn-sm disabled" title="Obriši kontakt!">
                <i class="fas fa-trash"></i>
              </a>
            </div>

          </div>

        </fieldset>
      </form>

      <?php
    endforeach;
    ?>
  </div><!-- End of Card Footer -->
</div><!-- End of Card -->
