<!-- View Client Data -->
<div class="card mb-4">
    <div class="card-header p-2">
        <h6 class="m-0 text-dark">
            Pregled podataka o klijentu: <strong><?php echo $client_data['name'] ?></strong>
        </h6>
    </div>

    <div class="card-body px-2">

        <form>
            <fieldset disabled>

                <div class="form-group row">
                    <label for="disabledSelectTip" class="col-sm-3 col-lg-2 col-form-label text-right">Vrsta klijenta:</label>
                    <div class="col-sm-4">
                        <select id="disabledSelectTip" class="form-control">
                            <option><?php echo $client_data['vps_name'] ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
                    <div class="col-sm-6">
                        <input class="form-control" id="disabledInputName" type="text" value="<?php echo $client_data['name'] ?>" disabled />
                    </div>
                    <div class="col-sm-3">
                        <input class="form-control" id="disabledInputName" type="text" value="<?php echo $client_data['name_note'] ?>" disabled />
                    </div>
                </div>

                <?php
                if ($client_data['vps_id'] == 2):
                    ?>
                    <div class="form-group row">
                        <label for="disabledInputPIB" class="col-sm-3 col-lg-2 col-form-label text-right">PIB: </label>
                        <div class="col-sm-4">
                            <input class="form-control" id="disabledInputPIB" type="text" value="<?php echo $client_data['pib'] ?>"  maxlength="9" disabled >	
                        </div>
                    </div>
                    <?php
                endif;
                ?>

                <div class="form-group row">
                    <label for="is_supplier" class="col-sm-3 col-lg-2 col-form-label text-right">Dobavljač:</label>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_supplier" name="is_supplier" <?php echo ($client_data['is_supplier'] == 0 ? '' : 'checked') ?> >
                            <label class="form-check-label" for="is_supplier">Jeste</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledSelectState" class="col-sm-3 col-lg-2 col-form-label text-right">Država:</label>
                    <div class="col-sm-4">
                        <select id="disabledSelectState" class="form-control">
                            <option><?php echo $client_data['state_name'] ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledSelectCity" class="col-sm-3 col-lg-2 col-form-label text-right">Naselje:</label>
                    <div class="col-sm-4">
                        <select id="disabledSelectCity" class="form-control">
                            <option><?php echo $client_data['city_name'] ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledSelectStreet" class="col-sm-3 col-lg-2 col-form-label text-right">Ulica:</label>
                    <div class="col-sm-4">
                        <select id="disabledSelectStreet" class="form-control">
                            <option><?php echo $client_data['street_name'] ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledInputNum" class="col-sm-3 col-lg-2 col-form-label text-right">Broj:</label>
                    <div class="col-sm-2">
                        <input class="form-control" id="disabledInputNum" type="text" value="<?php echo $client_data['home_number'] ?>" disabled />
                    </div>
                    <div class="col-sm-7">
                        <input class="form-control" id="disabledInputNum" type="text" value="<?php echo $client_data['address_note'] ?>" disabled />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledInputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška: </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="disabledInputNote" rows="3" disabled><?php echo $client_data['note'] ?></textarea>	
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-3 offset-sm-3 offset-lg-2">
                        <button type="submit" class="btn btn-sm btn-secondary disabled" title="Snimi izmene podataka o klijentu!">
                        <i class="fas fa-save"></i> Snimi
                        </button>
                    </div>
                </div>

            </fieldset>
        </form>

        <hr>

        <h5>Kontakti</h5>
        <?php
        $contacts = $contact->getContactsById($client_id);
        $contacttypes = $contact->getContactTypes();
        foreach ($contacts as $contact):
            ?>
            <form>
                <fieldset disabled>
                    <div class="form-group row">

                        <div class="col-sm-3">
                            <select class="form-control">
                                <option><?php echo $contact['name'] ?></option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <input class="form-control" type="text" value="<?php echo $contact['number'] ?>"  placeholder="unesi kontakt" disabled >	
                        </div>

                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="<?php echo $contact['note'] ?>" placeholder="unesi belešku" >
                        </div>

                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-mini btn-secondary disabled" title="Snimi izmenu kontakta!">
                                <i class="fas fa-save"> </i>
                            </button>
                            <a href="<?php echo $_SERVER['PHP_SELF']. '?edit&client_id=' .$client_id. '&contact_id=' .$contact['id']. '&delContact'; ?>" class="btn btn-mini btn-secondary disabled" title="Obriši kontakt!">
                                <i class="fas fa-trash"> </i>
                            </a>
                        </div>

                    </div>

                </fieldset>
            </form>

            <?php
        endforeach;
        ?>
    </div>
    <!-- End of Card body -->
</div>
