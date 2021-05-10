<!-- Edit Article Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Izmena artkla: <strong><?php echo $article_data->getName() ?></strong></h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?editArticle&article_id=' .$article_id ?>" method="post">

      <div class="form-group row">
        <label for="selectGroup" class="col-sm-3 col-lg-2 col-form-label text-right">Grupa proizvoda:</label>
        <div class="col-sm-3">
          <select id="selectGroup" name="group_id" class="form-control">
            <?php
            if($article_group = $entityManager->find("\Roloffice\Entity\ArticleGroup", $article_data->getGroup()->getId()) ) :
              ?>
              <option value="<?php echo $article_group->getId() ?>"><?php echo $article_group->getName() ?></option>
              <?php
            endif;
            $article_groups = $entityManager->getRepository('\Roloffice\Entity\ArticleGroup')->getArticleGroups();
            foreach ($article_groups as $article_group) :
              ?>
              <option value="<?php echo $article_group->getId() ?>"><?php echo $article_group->getName() ?></option>
              <?php
            endforeach;
            ?>
          </select>
        </div>
      </div>

      <div class="form-group row">
          <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
          <div class="col-sm-8">
            <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $article_data->getName() ?>" maxlength="96">
          </div>
      </div>

      <div class="form-group row">
        <label for="selectUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
        <div class="col-sm-3">
          <select id="selectUnit" name="unit_id" class="form-control">
            <option value="<?php echo $article_data->getUnit()->getId() ?>"><?php echo $article_data->getUnit()->getName() ?></option>
            <?php
            $units = $entityManager->getRepository('\Roloffice\Entity\Unit')->findBy(array(), array('name' => 'ASC'));
            foreach ($units as $unit) :
              ?>
              <option value="<?php echo $unit->getId() ?>"><?php echo $unit->getName() ?></option>
              <?php
            endforeach;
            ?>
          </select>
        </div>
      </div>  

      <div class="form-group row">
        <label for="inputWeight" class="col-sm-3 col-lg-2 col-form-label text-right">Težina:</label>
        <div class="col-sm-2">
          <input class="form-control" id="inputWeight" type="text" name="weight" value="<?php echo $article_data->getWeight() ?>" >
        </div>
        <div class="col-sm-2">g</div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-lg-2 col-form-label text-right" for="inputMinCalcMeasure">Min obrač. mera: </label>
        <div class="col-sm-2">
          <input class="form-control" id="inputMinCalcMeasure" type="text" name="min_cals_measure" value="<?php echo $article_data->getMinCalcMeasure() ?>" >
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-lg-2 col-form-label text-right" for="inputPrice">Cena: </label>
        <div class="col-sm-2">
          <input class="form-control" id="inputPrice" type="text" name="price" value="<?php echo $article_data->getPrice() ?>">
        </div>
        <div class="col-sm-2">&#8364; bez PDV-a</div>
      </div>

      <div class="form-group row">
        <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška:</label>
        <div class="col-md-8">
          <textarea id="inputNote" class="form-control" rows="2" name="note"><?php echo $article_data->getNote() ?></textarea>
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
    foreach ($article_properties as $article_property):
      $property = $entityManager->find("\Roloffice\Entity\Property", $article_property->getProperty()->getId());
      ?>
      <form method="post">
                        
        <div class="form-group row">
          <div class="col-sm-4">
            <select class="form-control" name="material_id">
              <option value="<?php echo $property->getID() ?>"><?php echo $property->getName() ?></option>
            </select>
          </div>
          <div class="col-sm-2">
            <a href="<?php echo $_SERVER['PHP_SELF'] . '?delProperty&article_id=' .$article_id. '&property_id=' .$property->getId() ?>" class="btn btn-sm btn-danger">
              <i class="fas fa-trash-alt"> </i>
            </a>
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
