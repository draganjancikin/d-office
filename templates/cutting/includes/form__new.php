<!-- New Cutting -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Otvaranje nove krojne liste:</h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?create'; ?>" method="post">

      <div class="form-group row">
        <label for="date" class="col-sm-3 col-lg-2 col-form-label text-right">Datum: </label>
        <div class="col-sm-3"><input class="form-control" id="date" type="text" value="<?php echo date("d M Y"); ?>"" ></div>
      </div>

      <div class=" form-group row">
          <label for="selectClientId" class="col-sm-3 col-lg-2 col-form-label text-right">Izaberi klijenta: </label>
          <div class="col-sm-5">
            <select id="selectClientId" class="form-control" name="client_id" required>
              <?php
            if(isset($_GET['client_id'])){
              $client_id = htmlspecialchars($_GET["client_id"]);
              $client_data = $entityManager->find('\Roloffice\Entity\Client', $client_id);
              echo '<option value="'.$client_data->getId().'">'.$client_data->getName().'</option>';
            }else{
              echo '<option value="">Izaberi klijenta</option>';
            }
          
            $clients_list = $entityManager->getRepository('\Roloffice\Entity\Client')->findBy(array(), array('name' => "ASC"));
            foreach( $clients_list as $client_item):
              ?>
              <option value="<?php echo $client_item->getId() ?>">
                <?php echo $client_item->getName() ?>
              </option>
              <?php
            endforeach;
            ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-sm-3 offset-sm-3 offset-lg-2">
            <button type="submit" class="btn btn-sm btn-success text-nowrap">Otvori novu krojnu listu</button>
          </div>
        </div>

    </form>
  </div>
</div>