<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Detaljna pretraga</h6>
  </div>

  <div class="card-body">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?advancedSearch&result'; ?>" method="post">
  
      <div class="form-group row">
        <label for="inputName" class="col-sm-2 col-form-label text-right" >Kijent: </label>
        <div class="col-sm-10">
          <input class="form-control" type="text" id="inputName" name="client" value="" placeholder=" Unesite naziv klijenta" >
        </div>
      </div>
  
      <div class="form-group row">
        <label for="inputStreet" class="col-sm-2 col-form-label text-right">Ulica: </label>
        <div class="col-sm-10">
          <input id="inputStreet" class="form-control" type="text" name="street" value="" placeholder=" Unesite naziv ulice" >	 
        </div>
      </div>
  
      <div class="form-group row">
        <label for="inputCity" class="col-sm-2 col-form-label text-right">Naselje: </label>
        <div class="col-sm-10">
          <input id="inputCity" class="form-control" type="text" name="city" value="" placeholder=" Unesite naziv naselja" />	 
        </div>
      </div>
  
      <div class="form-group row">
        <div class="col-sm-3 offset-sm-2"><button type="submit" class="btn btn-sm btn-secondary" title="Snimi izmene podataka o klijentu!"><i class="fa fa-search"></i> Pretaži</button></div>
      </div>
  
    </form>

  </div>
  <!-- End Card Body -->

</div>

<?php
if (isset($_GET["result"]) ) {
  $client_name = $_POST["client"];
  $street_name = $_POST["street"];
  $city_name = $_POST["city"];

  // Create a QueryBilder instance.
  $qb = $entityManager->createQueryBuilder();
  
  $qb->select('cl')
      ->from('Roloffice\Entity\Client', 'cl')
      ->join('cl.street', 's', 'WITH', 'cl.street = s.id')
      ->join('cl.city', 'c', 'WITH', 'cl.city = c.id')
      ->where(
        $qb->expr()->orX(
          $qb->expr()->like('cl.name', $qb->expr()->literal("%$client_name%")),
          $qb->expr()->like('cl.name_note', $qb->expr()->literal("%$client_name%"))
        )
      )
      ->andWhere(
        $qb->expr()->like('s.name', $qb->expr()->literal("%$street_name%"))
      )
      ->andWhere(
        $qb->expr()->like('c.name', $qb->expr()->literal("%$city_name%"))
      ) 
      ->orderBy('cl.name', 'ASC');

  $query = $qb->getQuery();
  $clients_data = $query->getResult();
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
              $client_street = $entityManager->find('\Roloffice\Entity\Street', $client_data->getStreet());
              $client_city = $entityManager->find('\Roloffice\Entity\City', $client_data->getCity());
              $client_country = $entityManager->find('\Roloffice\Entity\Country', $client_data->getCountry());
              ?>
              <tr>
                <td><?php echo $count ?></td>  
                <td><a href="?view&client_id=<?php echo $client_data->getId() ?>"><?php echo $client_data->getName() ?></a></td>
                <td><?php echo ( $client_street->getName() == "" ? "" : $client_street->getName() . " " . $client_data->getHomeNumber() .  ", " ) . $client_city->getName() . ", ". $client_country->getAbbr() ?></td>
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
