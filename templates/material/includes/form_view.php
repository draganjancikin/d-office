<!-- View Material Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled materiala: <strong><?php echo $material->getName() ?></strong></h6>
  </div>
  <div class="card-body p-2">
   
    <form>
      <fieldset disabled>

        <div class="form-group row">  
          <label for="disabledModified" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Izmenjen:</label>
          <div class="col-sm-5 col-sm-4 col-lg-3">
            <input class="form-control" id="disabledModified" type="text" value="<?php echo $material->getModifiedAt()->format('d M Y H:i'); ?>" disabled />
          </div>
        </div>

        <div class="form-group row">
          <label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Naziv:</label>
          <div class="col-sm-9 col-xl-8">
            <input class="form-control" id="disabledInputName" type="text" name="name" value="<?php echo $material->getName(); ?>" disabled />
          </div>
        </div>

        <div class="form-group row">
          <label for="disabledSelectUnit" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Jedinica mere:</label>
          <div class="col-sm-3 col-lg-2">
            <select id="disabledSelectUnit" name="unit_id" class="form-control">
              <option value="<?php echo $material->getUnit()->getId() ?>"><?php echo $material->getUnit()->getName(); ?></option>
            </select>
          </div>
        </div>
        
        <div class="form-group row">
          <label for="disabledInputWeight" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Težina:</label>
          <div class="col-sm-3 col-lg-2">
            <input class="form-control" id="disabledInputWeight" type="text" name="weight" value="<?php echo $material->getWeight(); ?>" disabled />
          </div>
          <div class="col-sm-2">g</div>
        </div>
            
        <div class="form-group row">
          <label for="disabledInputPrice" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Cena:</label>
          <div class="col-sm-3 col-lg-2">
            <input class="form-control" id="disabledInputPrice" type="text" name="price" value="<?php echo $material->getPrice(); ?>" disabled />
          </div>
          <div class="col-sm-3">&#8364; bez PDV-a</div>
        </div>   
               
        <div class="form-group row">
          <label for="disabledInputNote" class="col-sm-3 col-lg-2 col-form-label text-left text-sm-right">Beleška: </label>
          <div class="col-sm-9 col-xl-8">
            <textarea class="form-control" id="disabledInputNote" rows="3" name="note" placeholder="Beleška uz materijal ..." disabled><?php echo $material->getNote(); ?></textarea>	
          </div>
        </div>   
        
        <div class="form-group row">
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
    <?php
    foreach ($material_suppliers as $material_supplier):
      ?>
      <form method="post">
        <fieldset disabled>
          <div class="form-group row">
            
            <div class="col-sm-4 col-lg-3 col-xl-2">
              <select class="form-control" name="supplier_id">
                <option value="<?php echo $material_supplier->getSupplier()->getId(); ?>"><?php echo $material_supplier->getSupplier()->getName(); ?></option>
              </select>
            </div>

            <div class="col-sm-2">
              <input class="form-control" type="text" value="<?php echo $material_supplier->getModifiedAt()->format('d M Y') ?>">
            </div>

            <div class="col-sm-2 col-lg-3">
              <input class="form-control" type="text" name="code" value="<?php echo $material_supplier->getNote();?>">
            </div>

            <div class="col-sm-2">
              <input class="form-control" type="text" name="price" value="<?php echo $material_supplier->getPrice(); ?>">
            </div>

            <div class="col-sm-2">
              <button type="submit" class="btn btn-mini btn-secondary"><i class="fas fa-save"> </i> </button>
              <a href="#" class="btn btn-mini btn-secondary disabled"><i class="fas fa-trash-alt"> </i> </a>
            </div>

          </div>
        </fieldset>
      </form>
      <?php
    endforeach;
    ?>
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
          <div class="form-group row">

            <div class="col-sm-4">
              <select class="form-control" name="property_id">
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
