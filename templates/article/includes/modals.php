<!-- Modal -->
<div class="modal fade" id="addProperty" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Dodavanje osobine proizvoda</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?edit&article_id=' . $article_id . '&newProperty'; ?>" method="post" >
        <input type="hidden" name="article_id" value="<?php echo $article_id ?>">
        <div class="modal-body">

          <div class="form-group row">
            <label for="selectProperty" class="col-sm-3 col-form-label text-right">Osobina</label>
            <div class="col-sm-5">
                <select id="selectProperty" class="form-control" name="property_item_id" required>
                <option value="">izaberi osobinu</option>
                  <?php
                  // $property_list = $article->getPropertys();
                  // TODO
                  $property_list = []; 
                  foreach ($property_list as $property_item) {
                    echo '<option value="' .$property_item['id']. '">' .$property_item['name']. '</option>';
                  }
                  ?>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="InputMin" class="col-sm-3 col-form-label text-right">Min</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="inputMin" name="min" value="0" />
            </div>
          </div>
              
          <div class="form-group row">
            <label for="max" class="col-sm-3 col-form-label text-right">Max</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="max" value="0" />
            </div>
          </div>

        </div>
        <!-- End Modal Body -->

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
          <button type="submit" class="btn btn-primary">Dodaj osobinu</button>
        </div>

      </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
