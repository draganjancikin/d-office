<!-- Edit Client Data -->
<div class="card mb-4">
    <div class="card-header p-2">
        <h6 class="m-0 text-dark">
            Izmena podataka o klijentu: <strong><?php echo $client_data['name']; ?></strong>
        </h6>
    </div>

    <div class="card-body px-2">

        <form action="<?php echo $_SERVER['PHP_SELF']. '?editClient&client_id=' .$client_id; ?>" method="post">
            <input type="hidden" name="client_id" value="<?php echo $client_id ?>">
            <input type="hidden" name="type_id" value="<?php echo $client_data['type_id'] ?>">

            <div class="form-group row">
                <label for="selectTip" class="col-sm-3 col-lg-2 col-form-label text-right">Vrsta klijenta:</label>
                <div class="col-sm-4">
                    <select id="selectTip" class="form-control" name="type_id" >
                        <option value="<?php echo $client_data['type_id']; ?>"><?php echo $client_data['type_name']; ?></option>
                        <?php
                        $types = $client->getTypes();
                        foreach ($types as $type):
                            ?>
                            <option value="<?php echo $type['id'] ?>"><?php echo $type['name'] ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
                <div class="col-sm-6">
                    <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $client_data['name'] ?>" >
                </div>
                <div class="col-sm-4">
                    <input class="form-control" id="inputName" type="text" name="name_note" value="<?php echo $client_data['name_note'] ?>" >
                </div>
            </div>

            <?php
            if ($client_data['type_id'] == 2):
                ?>
                <div class="form-group row">
                    <label for="inputPIB" class="col-sm-3 col-lg-2 col-form-label text-right">PIB: </label>
                    <div class="col-sm-4">
                        <input class="form-control" id="inputPIB" type="text" name="pib" value="<?php echo $client_data['lb'] ?>"  maxlength="9" >	
                    </div>
                </div>
                <?php
            endif;
            ?>

            <div class="form-group row">
                <label for="is_supplier" class="col-sm-3 col-lg-2 col-form-label text-right">Dobavljač:</label>
                <div class="col-sm-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_supplier" name="is_supplier" value="1" <?php echo ($client_data['is_supplier'] == 0 ? '' : 'checked') ?> >
                        <label class="form-check-label" for="is_supplier">Jeste</label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="selectState" class="col-sm-3 col-lg-2 col-form-label text-right">Država:</label>
                <div class="col-sm-4">
                    <select id="selectState" class="form-control" name="state_id">
                        <option value="<?php echo $client_data['state_id'] ?>"><?php echo $client_data['state_name'] ?></option>
                        <?php
                        $states = $client->getStates();
                        foreach ($states as $state):
                            ?>
                            <option value="<?php echo $state['id'] ?>"><?php echo $state['name'] ?></option>
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
                        <option value="<?php echo $client_data['city_id'] ?>"><?php echo $client_data['city_name'] ?></option>
                        <?php
                        $citys = $client->getCitys();
                        foreach ($citys as $city) :
                            ?>
                            <option value="<?php echo $city['id'] ?>"><?php echo $city['name'] ?></option>
                            <?php
                        endforeach
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="selectStreet" class="col-sm-3 col-lg-2 col-form-label text-right">Ulica:</label>
                <div class="col-sm-4">
                    <select id="selectStreet" class="form-control" name="street_id">>
                        <option value="<?php echo $client_data['street_id'] ?>"><?php echo $client_data['street_name'] ?></option>
                        <?php
                        $streets = $client->getStreets();
                        foreach ($streets as $street):
                            ?>
                            <option value="<?php echo $street['id'] ?>"><?php echo $street['name'] ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>  

            <div class="form-group row">
                <label for="inputNum" class="col-sm-3 col-lg-2 col-form-label text-right">Broj:</label>
                <div class="col-sm-2">
                    <input class="form-control" id="inputNum" type="text" name="home_number" value="<?php echo $client_data['home_number'] ?>" >
                </div>
                <div class="col-sm-7">
                    <input class="form-control" id="inputNum" type="text" name="address_note" value="<?php echo $client_data['address_note'] ?>" >
                </div>
            </div>

            <div class="form-group row">
                <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška: </label>
                <div class="col-sm-6">
                    <textarea class="form-control" id="inputNote" rows="3" name="note"><?php echo $client_data['note'] ?></textarea>	
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
        // Ovde idu kontakti
        $contacts = $contact->getContactsById($client_id);
        $contacttypes = $contact->getContactTypes();
        foreach ($contacts as $contact):
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']. '?editContact&client_id=' .$client_id; ?>" method="post">
                <input type="hidden" name="contact_id" value="<?php echo $contact['contact_id'] ?>">

                <div class="form-group row">
                    <div class="col-sm-3">
                        <select class="form-control" name="contacttype_id">>
                            <option value="<?php echo $contact['type_id']; ?>"><?php echo $contact['name'] ?></option>
                            <?php
                            foreach ($contacttypes as $contacttype):
                                ?>
                                <option value="<?php echo $contacttype['id'] ?>"><?php echo $contacttype['name'] ?></option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="number" value="<?php echo $contact['number'] ?>" placeholder="Unesi kontakt" >
                    </div>

                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="note" value="<?php echo $contact['note'] ?>" placeholder="Unesi belešku" >
                    </div>  

                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-mini btn-secondary" title="Snimi izmenu kontakta!">
                            <i class="fas fa-save"> </i>
                        </button>
                        <a onClick="javascript: return confirm('Da li ste sigurni da želite da obrišete kontakt?');" href="<?php echo '?delContact&client_id=' .$client_id. '&contact_id=' .$contact['contact_id'] ?>" class="btn btn-mini btn-danger " title="Obriši kontakt!">
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
