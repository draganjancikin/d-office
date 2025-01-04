<!-- View Article Data -->
<div class="card mb-4">

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled artikla: <strong><?php echo $article_data->getName() ?></strong></h6>
  </div>

  <div class="card-body p-2">
    <form>
      <fieldset disabled>

        <div class="row mb-2">
          <label for="disabledSelectGroup" class="col-sm-3 col-lg-2 col-form-label text-right text-nowrap">Grupa
            proizvoda:</label>
          <div class="col-sm-3">
            <select id="disabledSelectGroup" name="group_id" class="form-select form-select-sm">
              <?php
                if ($article_data->getGroup() ) :
                  ?>
                  <option value="<?php echo $article_data->getGroup()->getId() ?>">
                    <?php echo $article_data->getGroup()->getName() ?>
                  </option>
                <?php
                endif;
              ?>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
          <div class="col-sm-8">
            <input class="form-control form-control-sm" id="disabledInputName" type="text" name="name"
              value="<?php echo $article_data->getName() ?>" maxlength="96">
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledSelectUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
          <div class="col-sm-3">
            <select id="disabledSelectUnit" name="unit_id" class="form-select form-select-sm">
              <option value="<?php echo $article_data->getUnit()->getId() ?>">
                <?php echo $article_data->getUnit()->getName() ?></option>
            </select>
          </div>
        </div>

        <div class="row mb-2">
          <label for="disabledInputWeight" class="col-sm-3 col-lg-2 col-form-label text-right">Težina:</label>
          <div class="col-sm-2">
            <input class="form-control form-control-sm" id="disabledInputWeight" type="text" name="weight"
              value="<?php echo $article_data->getWeight() ?>">
          </div>
          <div class="col-sm-2">g</div>
        </div>

        <div class="row mb-2">
          <label class="col-sm-3 col-lg-2 col-form-label text-right" for="disabledInputMinMera">Min obrač. mera:
          </label>
          <div class="col-sm-2">
            <input class="form-control form-control-sm" id="disabledInputMinMera" type="text" name="min_obrac_mera"
              value="<?php echo $article_data->getMinCalcMeasure() ?>">
          </div>
        </div>

        <div class="row mb-2">
          <label class="col-sm-3 col-lg-2 col-form-label text-right" for="disabledInputPrice">Cena: </label>
          <div class="col-sm-2">
            <input class="form-control form-control-sm" id="disabledInputPrice" type="text" name="price"
              value="<?php echo $article_data->getPrice() ?>">
          </div>
          <div class="col-sm-2 text-nowrap">&#8364; bez PDV-a</div>
        </div>

        <div class="row mb-2">
          <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška:</label>
          <div class="col-md-8">
            <textarea id="inputNote" class="form-control form-control-sm" rows="2"
              name="note"><?php echo $article_data->getNote() ?></textarea>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-sm-3 offset-sm-3 offset-lg-2">
            <button type="submit" class="btn btn-sm btn-secondary">
              <i class="fas fa-save"></i> Snimi
            </button>
          </div>
        </div>

      </fieldset>
    </form>
  </div>
  <!-- End Card Body -->

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Pregled osobina artikla</h6>
  </div>
  <div class="card-body p-2">

    <?php
    foreach ($article_properties as $article_property):
      ?>
      <form method="post">
        <fieldset disabled>

          <div class="row mb-2">
            <div class="col-sm-4">
              <select class="form-select form-select-sm">
                <option value="<?php echo $article_property->getProperty()->getId() ?>">
                  <?php echo $article_property->getProperty()->getName() ?></option>
              </select>
            </div>
            <div class="col-sm-2">
              <a href="#" class="btn btn-sm btn-secondary disabled">
                <i class="fas fa-trash-alt"> </i>
              </a>
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
