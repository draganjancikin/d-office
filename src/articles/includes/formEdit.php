<!-- Edit Article Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Izmena artkla: <strong><?php echo $article_data['name']; ?></strong></h6>
  </div>
  
  <div class="card-body p-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?editArticle&article_id=' .$article_id; ?>" method="post">
    
      <div class="form-group row">
        <label for="selectGroup" class="col-sm-3 col-lg-2 col-form-label text-right">Grupa proizvoda:</label>
        <div class="col-sm-3">
          <select id="selectGroup" name="group_id" class="form-control">
            <option value="<?php echo $article_data['group_id'];  ?>"><?php echo $article->getArticleGroupById($article_data['group_id']);  ?></option>
            <?php
            $article_groups = $article->getArticleGroups();
            foreach ($article_groups as $article_group) {
              echo '<option value="' .$article_group['id']. '">' .$article_group['name']. '</option>';
            }
            ?>
          </select>
        </div>
      </div>
    
      <div class="form-group row">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-8">
          <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $article_data['name']; ?>" maxlength="96">
        </div>
      </div>
    
      <div class="form-group row">
        <label for="selectUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
        <div class="col-sm-3">
          <select id="selectUnit" name="unit_id" class="form-control">
            <option value="<?php echo $article_data['unit_id'];  ?>"><?php echo $article_data['unit_name'];  ?></option>
            <?php
            $units = $article->getUnits();
            foreach ($units as $unit) {
              echo '<option value="' .$unit['id']. '">' .$unit['name']. '</option>';
            }
            ?>
          </select>
        </div>
      </div>  

      <div class="form-group row">
        <label for="inputWeight" class="col-sm-3 col-lg-2 col-form-label text-right">Težina:</label>
        <div class="col-sm-2">
          <input class="form-control" id="inputWeight" type="text" name="weight" value="<?php echo $article_data['weight']; ?>" >
        </div>
        <div class="col-sm-2">g</div>
      </div>
     
      <div class="form-group row">
        <label class="col-sm-3 col-lg-2 col-form-label text-right" for="inputMinMera">Min obrač. mera: </label>
        <div class="col-sm-2">
          <input class="form-control" id="inputMinMera" type="text" name="min_obrac_mera" value="<?php echo $article_data['min_obrac_mera']; ?>" >
        </div>
      </div>
     
      <div class="form-group row">
        <label class="col-sm-3 col-lg-2 col-form-label text-right" for="inputPrice">Cena: </label>
        <div class="col-sm-2">
          <input class="form-control" id="inputPrice" type="text" name="price" value="<?php echo $article_data['price']; ?>">
        </div>
        <div class="col-sm-2">eur</div>
      </div>

      <div class="form-group row">
        <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška:</label>
        <div class="col-md-8">
            <textarea id="inputNote" class="form-control" rows="2" name="note"><?php echo $article_data['note'] ?></textarea>
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
  <!-- End Card Body -->

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Izmena osobina artkla</h6>
  </div>
  <div class="card-body p-2">
    <?php
    $propertys = $article->getPropertyById($article_id);
    foreach ($propertys as $property):
      ?>
      <form method="post">
                  
        <div class="form-group row">
            
          <div class="col-sm-4">
            <select class="form-control" name="material_id">
              <option value="<?php echo $property['id']; ?>"><?php echo $property['name']; ?></option>
            </select>
          </div>
                    
          <div class="col-sm-2">
            <a href="<?php echo $_SERVER['PHP_SELF'] . '?delProperty&article_id=' .$article_id. '&property_id=' .$property['id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"> </i> </a>
          </div>
        
        </div>
        
      </form>
      <?php
    endforeach;
    ?>
  </div>
  <!-- End Card Body -->

  

</div>
<!-- End Card -->
