<!-- View Material Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled materiala: <strong><?php echo $material->getName() ?></strong></h6>
  </div>
  <div class="card-body p-2">
   
    <form>
      <fieldset disabled>

        <div class="row mb-2">  
          <label for="disabledModified" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Izmenjen:</label>
          <div class="col-sm-5 col-sm-4 col-lg-3">
            <input class="form-control form-control-sm" id="disabledModified" type="text" value="<?php echo
            $material->getModifiedAt
            ()->format('d M Y H:i'); ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Naziv:</label>
          <div class="col-sm-9 col-xl-8">
            <input class="form-control form-control-sm" id="disabledInputName" type="text" name="name" value="<?php echo $material->getName(); ?>" disabled />
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledSelectUnit" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Jedinica mere:</label>
          <div class="col-sm-3 col-lg-2">
            <select id="disabledSelectUnit" name="unit_id" class="form-select form-select-sm">
              <option value="<?php echo $material->getUnit()->getId() ?>"><?php echo $material->getUnit()->getName(); ?></option>
            </select>
          </div>
        </div>
        
        <div class="row mb-2">
          <label for="disabledInputWeight" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Težina:</label>
          <div class="col-sm-3 col-lg-2">
            <input class="form-control form-control-sm" id="disabledInputWeight" type="text" name="weight" value="<?php echo $material->getWeight(); ?>" disabled />
          </div>
          <div class="col-sm-2">g</div>
        </div>
            
        <div class="row mb-2">
          <label for="disabledInputPrice" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Cena:</label>
          <div class="col-sm-3 col-lg-2">
            <input class="form-control form-control-sm" id="disabledInputPrice" type="text" name="price" value="<?php echo $material->getPrice(); ?>" disabled />
          </div>
          <div class="col-sm-3">&#8364; bez PDV-a</div>
        </div>   

        <div class="row mb-2">
          <label for="disabledInputNote" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Beleška: </label>
          <div class="col-sm-9 col-xl-8">
            <textarea class="form-control form-control-sm" id="disabledInputNote" rows="3" name="note" placeholder="Beleška uz materijal ..." disabled><?php echo $material->getNote(); ?></textarea>
          </div>
        </div>   

        <div class="row mb-2">
          <div class="col-sm-3 offset-sm-3 offset-lg-2"><button type="submit" class="btn btn-sm btn-secondary" title="Snimi izmene podataka o klijentu!" disabled><i class="fas fa-save"></i> Snimi</button></div>
        </div> 

      </fieldset>
    </form>

   </div>
  <!-- End Card Body -->
</div>
<!-- End Card -->

<div class="card mb-4">

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled dobavljača</h6>
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
            <form method="post">
              <fieldset disabled>
                <tr>
                  <td class="px-1"><?php echo $count ;?></td>
                  <td class="p-1">
                    <select class="form-select form-select-sm" name="supplier_id" title="<?php echo $material_supplier->getSupplier()->getName(); ?>" disabled>
                      <option value="<?php echo $material_supplier->getSupplier()->getId(); ?>"><?php echo $material_supplier->getSupplier()->getName(); ?></option>
                    </select>
                  </td>
                  <td class="p-1">
                      <input class="form-control form-control-sm" type="text" value="<?php echo $material_supplier->getModifiedAt()->format('d M Y') ?>" disabled>
                  </td>
                  <td class="p-1">
                    <input class="form-control form-control-sm" type="text" name="code" value="<?php echo $material_supplier->getNote();?>" disabled>
                  </td>
                  <td class="p-1">
                    <input class="form-control form-control-sm" type="text" name="price" value="<?php echo $material_supplier->getPrice(); ?>" disabled>
                  </td>
                    <td class="p-1">
                      <button type="submit" class="btn btn-sm btn-secondary" disabled><i class="fas fa-save"> </i>
                      </button>
                      <a href="#" class="btn btn-sm btn-secondary disabled"><i class="fas fa-trash-alt"> </i> </a>
                    </td>
                </tr>
              </fieldset>
            </form>
		        <?php
		      endforeach;
		      ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled osobina materijala</h6>
  </div>

  <div class="card-body p-2">

    <?php
    foreach ($material_propertys as $material_property):
      ?>
      <form method="post">
        <fieldset disabled>
          <div class="row mb-2">

            <div class="col-sm-4">
              <select class="form-select form-select-sm" name="property_id">
                <option value="<?php echo $material_property->getProperty()->getId() ?>"><?php echo $material_property->getProperty()->getName() ?></option>
              </select>
            </div>

            <div class="col-sm-2">
              <a href="#" class="btn btn-sm btn-secondary disabled"><i class="fas fa-trash-alt"> </i> </a>
            </div>

          </div>
        </fieldset>
      </form>

      <?php
    endforeach;
    ?>
  </div>
  <!-- End Card Body -->

</div>
