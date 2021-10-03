<!-- Modal addMaterial -->
<div class="modal fade" id="addMaterial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Dodavanje materijala u narudžbenicu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?edit&order_id='. $order_id .'&addMaterialToOrder'; ?>" method="post">
        <div class="modal-body">
        
          <div class="form-group row">
            <label for="selectMaterial" class="col-sm-3 col-form-label text-right">Materijal:</label>
            <div class="col-sm-9">
              <div id="first">
                <select class="form-control" name="material_id" id="selectMaterial">
                <option value="">izaberi materijal</option>
                <?php
                foreach ($materials as $material) {
                  echo '<option value="' .$material->getMaterial()->getId(). '" title="' .$material->getMaterial()->getNote(). '">' .$material->getMaterial()->getName().'</option>';
                }
                ?>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="inputNote" class="col-sm-3 col-form-label text-right">Dodatni opis</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="note" id="inputNote" value="" placeholder="Upišite belešku" >
            </div>
          </div>   
      
          <div class="form-group row">
            <label for="inputPieces" class="col-sm-3 col-form-label text-right">Količina</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="pieces" id="inputPieces" value="" placeholder="Unesite količinu" />
            </div>
          </div>          
          
          <div id="second"></div>

        </div>
        <!-- /.modal-body -->
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
          <button type="submit" class="btn btn-primary" >Dodaj materijal</button>
        </div>

      </form>
    </div>
  </div>
</div>
