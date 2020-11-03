<!-- View Material Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled materiala: <strong><?php echo $material_data['name']; ?></strong></h6>
  </div>
  <div class="card-body p-2">
   
    <form>
      <fieldset disabled>

        <div class="form-group row">
          <label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
          <div class="col-sm-6">
            <input class="form-control" id="disabledInputName" type="text" name="name" value="<?php echo $material_data['name']; ?>" disabled />
          </div>
        </div>

        <div class="form-group row">
          <label for="disabledSelectUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
          <div class="col-sm-3">
            <select id="disabledSelectUnit" name="unit_id" class="form-control">
              <option value="<?php echo $material_data['unit_id'];  ?>"><?php echo $material_data['unit_name'];  ?></option>
            </select>
          </div>
        </div>
        
        <div class="form-group row">
          <label for="disabledInputWeight" class="col-sm-3 col-lg-2 col-form-label text-right">Težina:</label>
          <div class="col-sm-2">
            <input class="form-control" id="disabledInputWeight" type="text" name="weight" value="<?php echo $material_data['weight']; ?>" disabled />
          </div>
          <div class="col-sm-2">g</div>
        </div>
            
        <div class="form-group row">
          <label for="disabledInputPrice" class="col-sm-3 col-lg-2 col-form-label text-right">Cena:</label>
          <div class="col-sm-2">
            <input class="form-control" id="disabledInputPrice" type="text" name="price" value="<?php echo $material_data['price']; ?>" disabled />
          </div>
          <div class="col-sm-2">EUR bez PDV-a</div>
        </div>   
               
        <div class="form-group row">
          <label for="disabledInputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška: </label>
          <div class="col-sm-9">
            <textarea class="form-control" id="disabledInputNote" rows="3" name="note" placeholder="Beleška uz materijal ..." disabled><?php echo $material_data['note']; ?></textarea>	
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
    foreach ($material_suppliers as $supplier):
      ?>
      <form method="post">
        <fieldset disabled>
          <div class="form-group row">
            
            <div class="col-sm-5">
              <select class="form-control" name="client_id">
                <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
              </select>
            </div>

            <div class="col-sm-2">
              <input class="form-control" type="text" name="code" value="<?php echo $supplier['code']; ?>">
            </div>

            <div class="col-sm-3">
              <input class="form-control" type="text" name="price" value="<?php echo $supplier['price']; ?>">
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
    $propertys = $material->getPropertyById($material_id);
    foreach ($propertys as $property):
      ?>
      <form method="post">
        <fieldset disabled>
          <div class="form-group row">

            <div class="col-sm-4">
              <select class="form-control" name="material_id">
                <option value="<?php echo $property['id']; ?>"><?php echo $property['name']; ?></option>
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
