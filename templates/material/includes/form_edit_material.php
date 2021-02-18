<!-- Edit Material Data -->
<div class="card mb-4">
  <div class="card-header p-2">
  <h6 class="m-0 text-dark">Pregled materiala: <strong><?php echo $material_data->getName() ?></strong></h6>
  </div>
    <div class="card-body p-2">
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?updateMaterial&id=' .$material_id; ?>" method="post">

        <div class="form-group row">
          <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
          <div class="col-sm-9">
            <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $material_data->getName() ?>" maxlength="96">
          </div>
        </div>

        <div class="form-group row">
          <label for="selectUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
          <div class="col-sm-3">
            <select id="selectUnit" name="unit_id" class="form-control">
            <option value="<?php echo $material_unit->getId() ?>"><?php echo $material_unit->getName() ?></option>
              <?php
              $material_units = $entityManager->getRepository('\Roloffice\Entity\Unit')->FindAll();
              foreach ($material_units as $material_unit) {
                echo '<option value="' .$material_unit->getId(). '">' .$material_unit->getName(). '</option>';
              }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label for="inputWeight" class="col-sm-3 col-lg-2 col-form-label text-right">Težina:</label>
          <div class="col-sm-2">
            <input class="form-control" id="inputWeight" type="text" name="weight" value="<?php echo $material_data->getWeight() ?>" >
          </div>
          <div class="col-sm-2">g</div>
        </div>

        <div class="form-group row">
          <label for="inputPrice" class="col-sm-3 col-lg-2 col-form-label text-right">Cena:</label>
          <div class="col-sm-2">
            <input class="form-control" id="inputPrice" type="text" name="price" value="<?php echo $material_data->getPrice() ?>" >
          </div>
          <div class="col-sm-2">&#8364; bez PDV-a</div>
        </div>   

        <div class="form-group row">
          <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška: </label>
          <div class="col-sm-9">
            <textarea class="form-control" id="inputNote" rows="3" name="note" placeholder="Beleška uz materijal ..."><?php echo $material_data->getNote() ?></textarea>	
          </div>
        </div> 

        <div class="form-group row">
          <div class="col-sm-3 offset-sm-3 offset-lg-2"><button type="submit" class="btn btn-sm btn-success" title="Snimi izmene podataka o klijentu!"><i class="fas fa-save"></i> Snimi</button></div>
        </div> 

      </form>

    </div>
    <!-- End card Body -->

</div>
<!-- End Card -->

<div class="card mb-4">

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled dobavljača</h6>
  </div>

  <div class="card-body p-2">
    <?php
    foreach ($material_suppliers as $material_supplier):
      $supplier_data = $entityManager->find('\Roloffice\Entity\Client', $material_supplier->getSupplier());
      ?>
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?editMaterialSupplier&material_id=' .$material_id ?>" method="post">
        <input class="form-control" type="hidden" name="material_id" value="<?php echo $material_id ?>" />
        <input class="form-control" type="hidden" name="client_id_temp" value="<?php echo $supplier_data->getId() ?>" />

        <div class="form-group row">

          <div class="col-sm-5">
            <select class="form-control" name="client_id" required>
              <option value="<?php echo $supplier_data->getId() ?>"><?php echo $supplier_data->getName() ?></option>
              <?php
              foreach ($suppliers as $supplier) {
                echo '<option value="' .$supplier->getId(). '">' .$supplier->getName(). '</option>';
              }
              ?>
            </select>
          </div>

          <div class="col-sm-2">
            <input class="form-control" type="text" name="code" value="<?php echo $material_supplier->getNote() ?>">
          </div> 

          <div class="col-sm-3">
            <input class="form-control" type="text" name="price" value="<?php echo $material_supplier->getPrice() ?>">
          </div>

          <div class="col-sm-2">
            <button type="submit" class="btn btn-mini btn-success"><i class="fas fa-save"> </i> </button>
            <a href="<?php echo $_SERVER['PHP_SELF']. '?edit&material_id=' .$material_id. '&client_id_temp=' .$material_supplier->getId(). '&delMaterialSupplier'; ?>" class="btn btn-mini btn-danger"><i class="fas fa-trash-alt"> </i> </a>
          </div>

        </div>
      </form>
      <?php
    endforeach;
    ?>
  </div>
  <!-- End Card Body -->

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled osobina materijala</h6>
  </div>

  <div class="card-body p-2">
    <?php
    $material_propertys = $entityManager->getRepository('\Roloffice\Entity\MaterialProperty')->getMaterialProperties($material_id);
    foreach ($material_propertys as $material_property):
      $property_data = $entityManager->find('\Roloffice\Entity\Property', $material_property->getProperty());
      ?>
      <form method="post">
        <div class="form-group row">

          <div class="col-sm-4">
            <select class="form-control" name="material_id">
              <option value="<?php echo $property_data->getId() ?>"><?php echo $property_data->getName() ?></option>
            </select>
          </div>

          <div class="col-sm-2">
            <a href="<?php echo $_SERVER['PHP_SELF'] . '?delProperty&material_id=' .$material_id. '&property_id=' .$property_data->getId() ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"> </i> </a>
          </div>

        </div>
      </form>
      <?php
    endforeach;
    ?>
  </div>
  <!-- End Card Body -->

</div>
