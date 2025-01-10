<div class="card border-secondary mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold">Promena materijala</h6>
  </div>
  <div class="card-body p-2">

    <form action="<?php echo '/order/' . $material_data->getOrder()->getId().'/material/' . $material_on_order_id . '/edit' ?>"
          class="form-horizontal" role="form" method="post">
      <!--            <input type="hidden" name="material_id" value="-->
      <?php //echo $material_data->getArticle()->getId() ?><!--" />-->

        <div class="form-group row">

          <label for="article_id" class="col-sm-3 col-lg-2 col-form-label text-right">Izaberite materijal:</label>
          <div class="col-sm-9 col-lg-10">
            <select name="material_id" id="material_id" class="form-control">
              <option value="<?php echo $material_data->getMaterial()->getId() ?>"><?php echo $material_data->getMaterial()->getName() ?></option>
              <?php
              foreach ($materials_by_supplier as $material) {
                echo '<option value="' . $material->getMaterial()->getId() . '" title="' . $material->getMaterial()->getNote() . '">' . $material->getMaterial()->getName() . '</option>';
              }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-sm-3 offset-sm-3 offset-lg-2">
            <button type="submit" class="btn btn-sm btn-success" title="Snimi izmenu materijala!">
              <i class="fas fa-save" title="Snimi izmenu"> </i> Snimi izmenu
            </button>
          </div>
        </div>

    </form>
  </div>
</div>
