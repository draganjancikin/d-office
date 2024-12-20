<!-- Edit Material Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Izmena materiala: <strong><?php echo $material->getName() ?></strong></h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?updateMaterial&id=' . $material_id ?>" method="post">

      <div class="row mb-2">
        <label for="disabledModified" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Izmenjen:</label>
        <div class="col-sm-5 col-sm-4 col-lg-3">
          <input class="form-control form-control-sm" id="disabledModified" type="text"
            value="<?php echo $material->getModifiedAt()->format('d M Y H:i'); ?>" disabled />
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Naziv:</label>
        <div class="col-sm-9 col-xl-8">
          <input class="form-control form-control-sm" id="inputName" type="text" name="name" value="<?php echo $material->getName(); ?>"
            maxlength="96">
        </div>
      </div>

      <div class="row mb-2">
        <label for="selectUnit" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Jedinica mere:</label>
        <div class="col-sm-3 col-lg-2">
          <select id="selectUnit" name="unit_id" class="form-select form-select-sm">
            <option value="<?php echo $material->getUnit()->getId() ?>"><?php echo $material->getUnit()->getName(); ?>
            </option>
            <?php
              $units = $entityManager->getRepository('\App\Entity\Unit')->FindAll();
              foreach ($units as $unit) {
                echo '<option value="' .$unit->getId(). '">' .$unit->getName(). '</option>';
              }
              ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputWeight" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Težina:</label>
        <div class="col-sm-3 col-lg-2">
          <input class="form-control form-control-sm" id="inputWeight" type="text" name="weight"
            value="<?php echo $material->getWeight(); ?>">
        </div>
        <div class="col-sm-2">g</div>
      </div>

      <div class="row mb-2">
        <label for="inputPrice" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Cena:</label>
        <div class="col-sm-3 col-lg-2">
          <input class="form-control form-control-sm" id="inputPrice" type="text" name="price"
            value="<?php echo $material->getPrice(); ?>">
        </div>
        <div class="col-sm-2 text-nowrap">&#8364; bez PDV-a</div>
      </div>

      <div class="row mb-2">
        <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Beleška: </label>
        <div class="col-sm-9 col-xl-8">
          <textarea class="form-control form-control-sm" id="inputNote" rows="3" name="note"
            placeholder="Beleška uz materijal ..."><?php echo $material->getNote(); ?></textarea>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-sm-3 offset-sm-3 offset-lg-2">
          <button type="submit" class="btn btn-sm btn-success" title="Snimi izmene podataka o klijentu!">
            <i class="fas fa-save"></i> Snimi
          </button>
        </div>
      </div>

    </form>

  </div>
  <!-- End card Body -->

</div>
<!-- End Card -->

<!-- Edit Material Suppliers -->
<div class="card mb-4">

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Izmena dobavljača</h6>
  </div>

  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="table table-hover" >
        <thead>
          <tr class="table-secondary">
            <th class="px-1">#</th>
            <th class="px-1 text-center">naziv</th>
            <th class="px-1 text-center">datum izmene</th>
            <th class="px-1 text-center">šifra po dobavljaču</th>
            <th class="px-1 text-center">cena</th>
            <th class="px-1"></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 0;
          foreach ($material_suppliers as $material_supplier):
            $count++;
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?updateMaterialSupplier&id=' .$material_id ?>" method="post">
              <input class="form-control" type="hidden" name="material_supplier_id"
                     value="<?php echo $material_supplier->getId() ?>" />
              <tr>
                <td class="p-1"><?php echo $count ;?></td>
                <td class="p-1">
                  <select class="form-select form-select-sm" name="supplier_id" required>
                    <option value="<?php echo $material_supplier->getSupplier()->getId(); ?>">
                      <?php echo $material_supplier->getSupplier()->getName(); ?>
                    </option>
                    <?php
                    foreach ($suppliers as $supplier) {
                      echo '<option value="' .$supplier->getId(). '">' .$supplier->getName(). '</option>';
                    }
                    ?>
                  </select>
                </td>
                <td class="p-1">
                  <input class="form-control form-control-sm" type="text" value="<?php echo $material_supplier->getModifiedAt()->format('d M Y'); ?>" disabled>
                </td>
                <td class="p-1">
                  <input class="form-control form-control-sm" type="text" name="note" value="<?php echo $material_supplier->getNote(); ?>">
                </td>
                <td class="p-1">
                  <input class="form-control form-control-sm" type="text" name="price" value="<?php echo $material_supplier->getPrice(); ?>">
                </td>
                <td class="p-1">
                  <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"> </i> </button>
                  <a href="<?php echo $_SERVER['PHP_SELF']. '?edit&id=' .$material_id. '&material_supplier_id=' .$material_supplier->getId(). '&deleteMaterialSupplier'; ?>"
                      class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"> </i> </a>
                </td>
              </tr>
            </form>
            <?php
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <!-- End Card Body -->

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Izmena osobina materijala</h6>
  </div>

  <div class="card-body p-2">
    <?php
    foreach ($material_propertys as $material_property):
      ?>
    <form method="post">
      <div class="row mb-2">

        <div class="col-sm-4">
          <select class="form-select form-select-sm" name="property_id">
            <option value="<?php echo $material_property->getProperty()->getId() ?>">
              <?php echo $material_property->getProperty()->getName() ?></option>
          </select>
        </div>

        <div class="col-sm-2">
          <a href="<?php echo $_SERVER['PHP_SELF'] . '?deleteMaterialProperty&id=' .$material_id. '&material_property_id=' .$material_property->getId() ?>"
            class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"> </i> </a>
        </div>

      </div>
    </form>
    <?php
    endforeach;
    ?>
  </div>
  <!-- End Card Body -->

</div>
