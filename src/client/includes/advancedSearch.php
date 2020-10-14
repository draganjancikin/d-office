<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Detaljna pretraga</h6>
  </div>

  <div class="card-body">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?advancedSearch&p=1'; ?>" method="post">
  
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
if($_SERVER["REQUEST_METHOD"] == "POST" AND $_GET["p"] == 1) {
    
  $client = $_POST["client"];
  $street = $_POST["street"];
  $city = $_POST["city"];
  ?>
  
  <div class="card mb-4">

    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Rezultati detaljne pretrage</h6>
    </div>

    <div class="card-body">

      <!-- table with list of last client -->
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
            //metoda koja vraća naziv klijenta-e u zavisnosti od datog pojma u pretrazi
            if(!$client==""){
              // postoji upis u polje ime klijenta
              $where_client = " AND (client.name LIKE '%$client%' OR client.name_note LIKE '%$client%' )";
                        
              // proveravamo da li je upisano nešto u polje ulica
              if(!$street==""){
                $where_street = " AND street.street_name LIKE '%$street%' ";
                if(!$city==""){
                  $where_city = " AND city.city_name LIKE '%$city%' ";
                }else{
                  $where_city = "";
                }
              }else{
                $where_street = "";
                if(!$city==""){
                  $where_city = " AND city.city_name LIKE '%$city%' ";
                }else{
                  $where_city = "";
                }
              }
            }else{
              // ne postoji upis u polje ime klijenta
              $where_client = "";
                        
              // proveravamo da li je upisano nešto u polje ulica
              if(!$street==""){
                $where_street = " AND street.street_name LIKE '%$street%' ";
                if(!$city==""){
                  $where_city = " AND city.city_name LIKE '%$city%' ";
                }else{
                  $where_city = "";
                }
              }else{
                $where_street = "";
                if(!$city==""){
                  $where_city = " AND city.city_name LIKE '%$city%' ";
                }else{
                  $where_city = "";
                }
              }
            }
                
            $where = "WHERE (vps_id = 1 OR vps_id = 2) " . $where_client . $where_street . $where_city;
                    
            $db = new DB();
            $connection = $db->connectDB();
            $count = 0;
                    
            // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
            $result = $connection->query("SELECT client.id, client.name, city.city_name, street.street_name, client.home_number "
                                        . "FROM client "
                                        . "JOIN (street, city)"
                                        . "ON (client.city_id = city.id AND client.street_id = street.id)"
                                        . $where
                                        . "ORDER BY client.name ") or die(mysqli_error($connection));
                    
            while($row = mysqli_fetch_array($result)):
              $id = $row['id'];
              $name = $row['name'];
              $street_name = $row['street_name'];
              $home_number = $row['home_number'];
              $city_name = $row['city_name'];
                        
              $count++;
              ?>
                <tr>
                  <td><?php echo $count ?></td>  
                  <td><a href="?view&client_id=<?php echo $id ?>"><?php echo $name ?></a></td>
                  <td><?php echo ( $street_name == "" ? "" : $street_name . " " . $home_number .  ", " ) . $city_name ?></td>
                </tr>
              <?php
            endwhile;
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
