<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Detaljna pretraga</h6>
  </div>

  <div class="card-body">
<!--    <form action="--><?php //echo $_SERVER['PHP_SELF'] . '?advancedSearch&result'; ?><!--" method="post">-->
    <form action="/clients/advancedSearch" method="post">

      <div class="row mb-2">
        <label for="inputName" class="col-sm-3 col-form-label text-left text-sm-right" >Kijent: </label>
        <div class="col-sm-9">
          <input class="form-control form-control-sm" type="text" id="inputName" name="client" value="<?php echo (
              isset($_POST["client"]) ? $_POST["client"] : '') ?>" placeholder=" Unesite naziv klijenta" >
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputStreet" class="col-sm-3 col-form-label text-left text-sm-right">Ulica: </label>
        <div class="col-sm-9">
          <input id="inputStreet" class="form-control form-control-sm" type="text" name="street" value="<?php echo ( isset($_POST["street"]) ? $_POST["street"] : '' )?>" placeholder=" Unesite naziv ulice" >
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputCity" class="col-sm-3 col-form-label text-left text-sm-right">Naselje: </label>
        <div class="col-sm-9">
          <input id="inputCity" class="form-control form-control-sm" type="text" name="city" value="<?php echo ( isset($_POST["city"]) ? $_POST["city"] : '') ?>" placeholder=" Unesite naziv naselja" />
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-sm-3 offset-sm-3"><button type="submit" class="btn btn-sm btn-secondary" title="Snimi izmene podataka o klijentu!"><i class="fa fa-search"></i> Pretaži</button></div>
      </div>
  
    </form>

  </div>
  <!-- End Card Body -->

</div>

<?php

if ($_POST && isset($_GET["url"]) && str_contains($_GET["url"], 'advancedSearch') ) {
  $term = $_POST["client"];
  $street = $_POST["street"];
  $city = $_POST["city"];

  $clients_data = $entityManager->getRepository('\App\Entity\Client')->advancedSearch($term, $street, $city);
  ?>
  
  <div class="card mb-4">

    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Rezultati detaljne pretrage</h6>
    </div>

    <div class="card-body">

      <!-- Table with list of last client. -->
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>#</th>
              <th>Naziv klijenta</th>
              <th>Adresa</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>#</th>
              <th>Naziv klijenta</th>
              <th>Adresa</th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            $count = 0;
            foreach ($clients_data as $client_data){
              $count++;
              $client_street = $client_city = $client_country = NULL;
              $client_street_name = $cient_city_name = $client_country_name = "";
              if ($client_data->getStreet()) {
                $client_street = $entityManager->find('\App\Entity\Street', $client_data->getStreet());
                $client_street_name = $client_street ? $client_street->getName() . " " . $client_data->getHomeNumber() . ", " : "";
              }
              if ($client_data->getCity()) {
                $client_city = $entityManager->find('\App\Entity\City', $client_data->getCity());
                $cient_city_name = $client_city ? $client_city->getName() . ", " : "";
              }
              if ($client_data->getCountry()) {
                $client_country = $entityManager->find('\App\Entity\Country', $client_data->getCountry());
                $client_country_name = $client_country ? $client_country->getAbbr() : "";
              }
              ?>
              <tr>
                <td><?php echo $count ?></td>  
                <td><a href="?view&client_id=<?php echo $client_data->getId() ?>"><?php echo $client_data->getName() ?></a></td>
                <td><?php echo $client_street_name . $cient_city_name . $client_country_name ?></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>

    </div>
    <!-- End Card Body -->

  </div>
        
  <?php   
  
}
?>
