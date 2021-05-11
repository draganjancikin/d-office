<?php
if(isset($_GET['id'])):
  ?>
  <!-- Modal addSupplier -->
  <div class="modal fade" id="addSupplier" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Dodavanje dobavljača</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form action="<?php echo $_SERVER['PHP_SELF'] . '?addSupplier&id='.$id ; ?>" method="post">
          <input type="hidden" name="id" value="<?php echo $id ?>">

          <div class="modal-body">

            <div class="form-group row">
              <label for="selectClient" class="col-sm-3 col-form-label text-right">Dobavljač</label>
              <div class="col-sm-8">
                <select id="selectClient" class="form-control" name="supplier_id" required="required">
                  <option value="">izaberi dobavljača</option>
                  <?php
                  foreach ($suppliers as $supplier) {
                    echo '<option value="' .$supplier->getId(). '">' .$supplier->getName(). '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="inputCode" class="col-sm-3 col-form-label text-right">Beleška</label>
              <div class="col-sm-6">
                <input id="inputCode" type="text" class="form-control" name="note" value="" placeholder="Upišite šifru materijala" />
              </div>
            </div>

            <div class="form-group row">
              <label for="inputPrice" class="col-sm-3 col-form-label text-right">Cena</label>
              <div class="col-sm-6">
                <input id="inputPrice" type="text" class="form-control" name="price" value="" placeholder="Upišite cenu" />
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
              <button type="submit" class="btn btn-primary" >Dodaj dobavljača</button>
            </div>

          </div>
          <!-- End Modal Body -->

        </form>
      </div>
    </div>
  </div>
  <!-- End Modal addSupplier -->

  <!-- Modal addProperty -->
  <div class="modal fade" id="addProperty" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Dodavanje osobine proizvoda</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form action="<?php echo $_SERVER['PHP_SELF'] . '?addProperty&id='.$id; ?>" method="post">
          <input type="hidden" name="id" value="<?php echo $id ?>">

          <div class="modal-body">

            <div class="form-group row">
              <label for="selectPropertyItem" class="col-sm-3 col-form-label text-right">Osobina</label>
              <div class="col-sm-5">
                <select id="selectPropertyItem" class="form-control" name="property_item_id" required="required">
                  <option value="">izaberi osobinu</option>
                  <?php
                  $property_list = $entityManager->getRepository('\Roloffice\Entity\Property')->findAll();
                  foreach ($property_list as $property_item) :
                    ?>
                    <option value="<?php echo $property_item->getId() ?>"><?php echo $property_item->getName() ?></option>
                    <?php
                  endforeach;
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="inputMin" class="col-sm-3 col-form-label text-right">Min</label>
              <div class="col-sm-8">
                <input id="inputMin" type="text" class="form-control" name="min_size" value="0" placeholder="Minimalna moguća vrednost osobine" />
              </div>
            </div>

            <div class="form-group row">
              <label for="inputMax" class="col-sm-3 col-form-label text-right">Max</label>
              <div class="col-sm-8">
                <input id="inputMax" type="text" class="form-control" name="max_size" value="0" placeholder="Maksimalna moguća vrednost osobine" />
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
              <button type="submit" class="btn btn-primary" >Dodaj osobinu</button>
            </div>
          </div>
            
        </form>

      </div>
      <!-- /.modal-content -->
    
    </div>
    <!-- /.modal-dialog -->

  </div>
  <!-- End Modal addProperty -->
  <?php
endif;
