<!-- Edit Client Data -->
<div class="card mb-4">
      <div class="card-header p-2">
            <h6 class="m-0 text-dark">
                Izmena podataka o klijentu: <strong><?php echo $client['name'] ?></strong>
            </h6>
      </div>

    <div class="card-body px-2">

        <form action="<?php echo $_SERVER['PHP_SELF']. '?updateClient&client_id=' . $client_id; ?>" method="post">
            <input type="hidden" name="client_id" value="<?php echo $client_id ?>">
            <input type="hidden" name="type_id" value="<?php echo $client['type_id'] ?>">

            <div class="form-group row">
                <label for="selectTip" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Vrsta klijenta:</label>
                <div class="col-sm-4">
                    <select id="selectTip" class="form-control" name="type_id" >
                        <option value="<?php echo $client['type_id'] ?>"><?php echo $client['type'] ?></option>
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
                <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Naziv:</label>
                <div class="col-sm-6 col-lg-6">
                    <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $client['name'] ?>" >
                </div>
                <div class="col-sm-3 col-lg-4">
                    <input class="form-control" id="inputName" type="text" name="name_note" value="<?php echo $client['name_note'] ?>" >
                </div>
            </div>

            <?php
            if ($client['type_id'] == 2):
                ?>
                <div class="form-group row">
                    <label for="inputPIB" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">PIB: </label>
                    <div class="col-sm-4">
                        <input class="form-control" id="inputPIB" type="text" name="lb" value="<?php echo $client['lb'] ?>"  maxlength="9" >
                    </div>
                </div>
                <?php
            endif;
            ?>

            <div class="form-group row">
                <label for="is_supplier" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Dobavljač:</label>
                <div class="col-sm-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_supplier" name="is_supplier" value="1" <?php echo ($client['is_supplier'] ? 'checked' : '') ?> >
                        <label class="form-check-label" for="is_supplier">Jeste</label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="selectCountry" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Država:</label>
                <div class="col-sm-7 col-md-6 col-lg-5 col-xl-4">
                    <select id="selectCountry" class="form-control" name="country_id">
                        <?php
                        if ($client['country'] === null) {
                            ?>
                            <option>Izaberite drzavu</option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $client['country_id'] ?>"><?php echo $client['country'] ?></option>
                            <?php
                        }
                        $countries = $entityManager->getRepository('\Roloffice\Entity\Country')->findBy(array(), array('name' => 'ASC'));
                        foreach ($countries as $country) :
                            ?>
                            <option value="<?php echo $country->getId() ?>">
                                <?php echo $country->getName() ?>
                            </option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="selectCity" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Naselje:</label>
                <div class="col-sm-7 col-md-6 col-lg-5 col-xl-4">
                    <select id="selectCity" class="form-control" name="city_id">
                        <?php
                        if ($client['city'] === null) {
                            ?>
                            <option>Izaberite naselje</option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $client['city_id'] ?>"><?php echo $client['city'] ?></option>
                            <?php
                        }
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
                <label for="selectStreet" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Ulica:</label>
                <div class="col-sm-7 col-md-6 col-lg-5 col-xl-4">
                    <select id="selectStreet" class="form-control" name="street_id">>
                        <?php
                        if ($client['street'] === null) {
                            ?>
                            <option>Izaberite naselje</option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $client['street_id'] ?>"><?php echo $client['street'] ?></option>
                            <?php
                        }
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
                <label for="inputNum" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Broj:</label>
                <div class="col-sm-2">
                    <input class="form-control" id="inputNum" type="text" name="home_number" value="<?php echo $client['home_number'] ?>" >
                </div>
                <div class="col-sm-7 col-lg-8">
                    <input class="form-control" id="inputNum" type="text" name="address_note" value="<?php echo $client['address_note'] ?>" >
                </div>
            </div>

            <div class="form-group row">
                <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Beleška: </label>
                <div class="col-sm-6">
                    <textarea class="form-control" id="inputNote" rows="3" name="note"><?php echo $client['note'] ?></textarea>
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
        foreach ($client['contacts'] as $contact):
            $client_contact_data = $entityManager->getRepository('\Roloffice\Entity\Contact')
                                                    ->findOneBy( array('id' =>$contact->getId()) );
            $client_contact_type = $client_contact_data->getType();
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']. '?updateContact&client_id=' . $client_id; ?>" method="post">
                <input type="hidden" name="contact_id" value="<?php echo $contact->getId() ?>">

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
                        <input type="text" class="form-control" name="body" value="<?php echo $contact->getBody() ?>" placeholder="Unesi kontakt" >
                    </div>

                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="note" value="<?php echo $contact->getNote() ?>" placeholder="Unesi belešku" >
                    </div>

                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-mini btn-secondary" title="Snimi izmenu kontakta!">
                            <i class="fas fa-save"> </i>
                        </button>
                        <a onClick="javascript: return confirm('Da li ste sigurni da želite da obrišete kontakt?');"
                           href="<?php echo '?deleteContact&client_id=' . $client_id . '&contact_id=' . $contact->getId() ?>"
                           class="btn btn-mini btn-danger "
                           title="Obriši kontakt!">
                            <i class="fas fa-trash"> </i>
                        </a>
                    </div>
                </div>
            </form>
            <?php
        endforeach;
        ?>
    </div><!-- End of Card Body -->

</div><!-- End of Card -->
