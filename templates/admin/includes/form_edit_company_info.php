<?php
$company = $entityManager->find('\Roloffice\Entity\CompanyInfo', '1');
$company_country = $company->getCountry() ? $entityManager->find('\Roloffice\Entity\Country', $company->getCountry()) : null;
$company_city = $company->getCity() ? $entityManager->find('\Roloffice\Entity\City', $company->getCity()) : null;
$company_street = $company->getStreet() ? $entityManager->find('\Roloffice\Entity\Street', $company->getStreet()) : null;
?>
<!-- View Company Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">
      Izmena podataka o kompaniji:
    </h6>
  </div>

  <div class="card-body px-2">
    <form action="<?php echo $_SERVER['PHP_SELF']. '?updateCompanyInfo'?>" method="post">

      <div class="form-group row">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-6">
          <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $company->getName() ?>" >
        </div>
      </div>

      <div class="form-group row">
        <label for="inputPIB" class="col-sm-3 col-lg-2 col-form-label text-right">PIB: </label>
        <div class="col-sm-4">
          <input class="form-control" id="inputPIB" type="text" name="pib" value="<?php echo $company->getPib() ?>"  maxlength="9" >
        </div>
      </div>

      <div class="form-group row">
        <label for="inputMb" class="col-sm-3 col-lg-2 col-form-label text-right">MB: </label>
        <div class="col-sm-4">
          <input class="form-control" id="inputMb" type="text" name="mb" value="<?php echo $company->getMb() ?>"  maxlength="9" >
        </div>
      </div>

      <div class="form-group row">
        <label for="selectCountry" class="col-sm-3 col-lg-2 col-form-label text-right">Država:</label>
        <div class="col-sm-4">
          <select id="selectCountry" class="form-control" name="country_id">
            <?php
            if ($company_country === null) {
              ?>
              <option>Izaberite drzavu</option>
              <?php
            } else {
              ?>
              <option value="<?php echo $company_country->getId() ?>"><?php echo $company_country->getName() ?></option>
              <?php
            }
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
            <?php
            if ($company_city === null) {
              ?>
              <option>Izaberite naselje</option>
              <?php
            } else {
              ?>
              <option value="<?php echo $company_city->getId() ?>"><?php echo $company_city->getName() ?></option>
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
        <label for="selectStreet" class="col-sm-3 col-lg-2 col-form-label text-right">Ulica:</label>
        <div class="col-sm-4">
          <select id="selectStreet" class="form-control" name="street_id">>
            <?php
            if ($company_street === null) {
              ?>
              <option>Izaberite naselje</option>
              <?php
            } else {
              ?>
              <option value="<?php echo $company_street->getId() ?>"><?php echo $company_street->getName() ?></option>
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
        <label for="inputNum" class="col-sm-3 col-lg-2 col-form-label text-right">Broj:</label>
        <div class="col-sm-2">
          <input class="form-control" id="inputNum" type="text" name="home_number" value="<?php echo $company->getHomeNumber() ?>" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputBankAccount1" class="col-sm-3 col-lg-2 col-form-label text-right">Broj ziro racuna 1:</label>
        <div class="col-sm-4">
          <input class="form-control" id="inputBankAccount1" type="text" name="bank_account_1" value="<?php echo $company->getBankAccount1() ?>" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputBankAccount2" class="col-sm-3 col-lg-2 col-form-label text-right">Broj ziro racuna 2:</label>
        <div class="col-sm-4">
          <input class="form-control" id="inputBankAccount2" type="text" name="bank_account_2" value="<?php echo $company->getBankAccount2() ?>" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputPhone1" class="col-sm-3 col-lg-2 col-form-label text-right">Telefon 1:</label>
        <div class="col-sm-4">
          <input class="form-control" id="inputPhone1" type="text" name="phone_1" value="<?php
          echo $company->getPhone1() ?>" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputPhone2" class="col-sm-3 col-lg-2 col-form-label text-right">Telefon 2:</label>
        <div class="col-sm-4">
          <input class="form-control" id="inputPhone2" type="text" name="phone_2" value="<?php
					echo $company->getPhone2() ?>" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputEmail1" class="col-sm-3 col-lg-2 col-form-label text-right">Email 1:</label>
        <div class="col-sm-4">
          <input class="form-control" id="inputEmail1" type="email" name="email_1" value="<?php
					echo $company->getEmail1() ?>" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputEmail2" class="col-sm-3 col-lg-2 col-form-label text-right">Email 2:</label>
        <div class="col-sm-4">
          <input class="form-control" id="inputEmail2" type="email" name="email_2" value="<?php
					echo $company->getEmail2() ?>" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputWebsite1" class="col-sm-3 col-lg-2 col-form-label text-right">Website 1:</label>
        <div class="col-sm-4">
          <input class="form-control" id="inputWebsite1" type="text" name="website_1" value="<?php
					echo $company->getWebsite1() ?>" />
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-3 offset-sm-3 offset-lg-2">
          <button type="submit" class="btn btn-sm btn-success" title="Snimi izmene podataka o companiji!">
            <i class="fas fa-save"></i> Snimi
          </button>
        </div>
      </div>

    </form>
  </div>

</div>
