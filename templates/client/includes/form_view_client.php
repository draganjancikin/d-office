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

                <div class="form-group row">
                    <label for="disabledSelectTip" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Vrsta klijenta:</label>
                    <div class="col-sm-4">
                        <select id="disabledSelectTip" class="form-control">
                            <option><?php echo $client['type'] ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Naziv:</label>
                    <div class="col-sm-6 col-lg-6">
                        <input class="form-control" id="disabledInputName" type="text" value="<?php echo $client['name'] ?>" disabled />
                    </div>
                    <div class="col-sm-3 col-lg-4">
                        <input class="form-control" id="disabledInputName" type="text" value="<?php echo $client['name_note'] ?>" disabled />
                    </div>
                </div>

                <?php
                if ($client['type_id'] == 2):
                    ?>
                    <div class="form-group row">
                        <label for="disabledInputPIB" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">PIB: </label>
                        <div class="col-sm-4">
                            <input class="form-control" id="disabledInputPIB" type="text" value="<?php echo $client['lb'] ?>"  maxlength="9" disabled >
                        </div>
                    </div>
                    <?php
                endif;
                ?>

                <div class="form-group row">
                    <label for="is_supplier" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Dobavljač:</label>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_supplier" name="is_supplier" <?php echo ($client['is_supplier'] ? 'checked' : '') ?> >
                            <label class="form-check-label" for="is_supplier">Jeste</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledSelectCountry" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Država:</label>
                    <div class="col-sm-7 col-md-6 col-lg-5 col-xl-4">
                        <select id="disabledSelectCountry" class="form-control">
                            <option><?php echo $client['country'] ?? '' ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledSelectCity" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Naselje:</label>
                    <div class="col-sm-7 col-md-6 col-lg-5 col-xl-4">
                        <select id="disabledSelectCity" class="form-control">
                            <option><?php echo $client['city'] ?? '' ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledSelectStreet" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Ulica:</label>
                    <div class="col-sm-7 col-md-6 col-lg-5 col-xl-4">
                        <select id="disabledSelectStreet" class="form-control">
                            <option><?php echo $client['street'] ?? '' ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledInputNum" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Broj:</label>
                     <div class="col-sm-2">
                        <input class="form-control" id="disabledInputNum" type="text" value="<?php echo $client['home_number'] ?>" disabled />
                    </div>
                    <div class="col-sm-7 col-lg-8">
                        <input class="form-control" id="disabledInputNum" type="text" value="<?php echo $client['address_note'] ?>" disabled />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledInputNote" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Beleška: </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="disabledInputNote" rows="3" disabled><?php echo $client['note'] ?></textarea>
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
        foreach ($client['contacts'] as $contact):
            $client_contact_data = $entityManager->getRepository('\Roloffice\Entity\Contact')->findOneBy( array('id' =>$contact->getId()) );
            $client_contact_type = $client_contact_data->getType();
            ?>
            <form>
                <fieldset disabled>
                    <div class="form-group row">

                        <div class="col-sm-3">
                            <select class="form-control">
                                <option><?php echo $client_contact_type->getName() ?></option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <input class="form-control" type="text" value="<?php echo $contact->getBody() ?>"  placeholder="unesi kontakt" disabled >
                        </div>

                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="<?php echo $contact->getNote() ?>" placeholder="unesi belešku" >
                        </div>

                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-mini btn-secondary disabled" title="Snimi izmenu kontakta!">
                                <i class="fas fa-save"> </i>
                            </button>
                            <a href="<?php echo $_SERVER['PHP_SELF']. '?edit&client_id=' . $client_id . '&contact_id=' . $contact->getId() . '&deleteContact'; ?>"
                               class="btn btn-mini btn-secondary disabled" title="Obriši kontakt!">
                                <i class="fas fa-trash"> </i>
                            </a>
                        </div>

                    </div>

                </fieldset>
            </form>

            <?php
        endforeach;
        ?>
    </div><!-- End of Card body -->
</div><!-- End of Card -->
