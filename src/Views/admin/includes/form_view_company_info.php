<?php
$company = $entityManager->find('\App\Entity\CompanyInfo', '1');
$company_country = $company->getCountry() ? $entityManager->find('\App\Entity\Country', $company->getCountry()) : null;
$company_city = $company->getCity() ? $entityManager->find('\App\Entity\City', $company->getCity()) : null;
$company_street = $company->getStreet() ? $entityManager->find('\App\Entity\Street', $company->getStreet()) : null;
?>
<!-- View Company Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">
      Pregled podataka o kompaniji:
    </h6>
  </div>

  <div class="card-body px-2">
    <form>
      <fieldset disabled>
        <div class="row mb-2">
          <label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
          <div class="col-sm-6">
            <input class="form-control form-control-sm" id="disabledInputName" type="text" value="<?php echo
            $company->getName() ?>"
              disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputPIB" class="col-sm-3 col-lg-2 col-form-label text-right">PIB: </label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputPIB" type="text" value="<?php echo $company->getPib() ?>"
              maxlength="9" disabled>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputMb" class="col-sm-3 col-lg-2 col-form-label text-right">MB: </label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputMb" type="text" value="<?php echo $company->getMb() ?>"
              maxlength="9" disabled>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledSelectCountry" class="col-sm-3 col-lg-2 col-form-label text-right">Država:</label>
          <div class="col-sm-4">
            <select id="disabledSelectCountry" class="form-select form-select-sm">
              <option><?php echo $company_country ? $company_country->getName() : '' ?></option>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledSelectCity" class="col-sm-3 col-lg-2 col-form-label text-right">Naselje:</label>
          <div class="col-sm-4">
            <select id="disabledSelectCity" class="form-select form-select-sm">
              <option><?php echo $company_city ? $company_city->getName() : '' ?></option>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledSelectStreet" class="col-sm-3 col-lg-2 col-form-label text-right">Ulica:</label>
          <div class="col-sm-4">
            <select id="disabledSelectStreet" class="form-select form-select-sm">
              <option><?php echo $company_street ? $company_street->getName() : '' ?></option>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputNum" class="col-sm-3 col-lg-2 col-form-label text-right">Broj:</label>
          <div class="col-sm-2">
            <input class="form-control form-control-sm" id="disabledInputNum" type="text"
              value="<?php echo $company->getHomeNumber() ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputNum" class="col-sm-3 col-lg-2 col-form-label text-right text-nowrap">Broj žiro
            računa
            1:</label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputNum" type="text"
              value="<?php echo $company->getBankAccount1() ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputNum" class="col-sm-3 col-lg-2 col-form-label text-right text-nowrap">Broj žiro
            računa 2:</label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputNum" type="text"
              value="<?php echo $company->getBankAccount2() ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputPhone1" class="col-sm-3 col-lg-2 col-form-label text-right text-nowrap">Telefon
            1:</label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputPhone1" type="text"
                   value="<?php echo $company->getPhone1() ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputPhone2" class="col-sm-3 col-lg-2 col-form-label text-right text-nowrap">Telefon
            2:</label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputPhone2" type="text"
                   value="<?php echo $company->getPhone2() ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputEmail1" class="col-sm-3 col-lg-2 col-form-label text-right text-nowrap">Email
            1:</label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputEmail1" type="text"
                   value="<?php echo $company->getEmail1() ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputEmail2" class="col-sm-3 col-lg-2 col-form-label text-right text-nowrap">Email
            2:</label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputEmail2" type="text"
                   value="<?php echo $company->getEmail2() ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputWebsite1" class="col-sm-3 col-lg-2 col-form-label text-right text-nowrap">
            Website 1:
          </label>
          <div class="col-sm-4">
            <input class="form-control form-control-sm" id="disabledInputWebsite1" type="text"
                   value="<?php echo $company->getWebsite1() ?>" disabled />
          </div>
        </div>

      </fieldset>
    </form>
  </div>

</div>