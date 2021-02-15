<!-- New Project -->
<div class="row">
  <div class="col-lg-8 col-md-12">
    
    <div class="card mb-4">
      <div class="card-header p-2">
        <h6 class="m-0 text-dark">Otvaranje novog projekta:</h6>
      </div>

      <div class="card-body p-2">
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?new&add'; ?>" method="post">

          <?php
          if( isset($_GET['pidb_id']) ):
            // hiden input for $pidb_id
            ?>
            <input type="hidden" name="pidb_id" value="<?php echo $_GET['pidb_id'] ?>">
            <?php
            endif;
          ?>

          <div class="form-group row">
            <label for="InputDate" class="col-sm-3 col-lg-2 col-form-label text-sm-right">Datum: </label>
            <div class="col-sm-3">
              <input id="inputDate" class="form-control" type="text" value="<?php echo date("d M Y") ?>" disabled>
            </div>
          </div>

          <div class="form-group row">
            <label for="selectClient" class="col-sm-3 col-lg-2 col-form-label text-sm-right">Klijent: </label>
            <div class="col-sm-9">
              <select id="selectClient" name="client_id" class="form-control" required>
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
            <label for="inputTitle" class="col-sm-3 col-lg-2 col-form-label text-sm-right">Naslov: </label>
            <div class="col-sm-9">
              <input id="inputTitle" type="text" class="form-control" name="title" maxlength="64" placeholder="Unesite naslov projekta" required>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-3 offset-sm-3 offset-lg-2">
              <button type="submit" class="btn btn-sm btn-success">
                  <i class="fas fa-save"></i> Snimi
              </button>
            </div>
          </div> 

        </form>
      </div>
      <!-- End Body -->

    </div>
    <!-- End Card -->
  </div>
</div>
