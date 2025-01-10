<!-- Modal add Article to CuttingSheet -->
<div class="modal fade" id="addFence" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Dodaj polje u krojnu listu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo '/cutting/' . $cutting_id . '/addArticle' ?>" method="post">
        <div class="modal-body">
        
          <input type="hidden" name="id" value="<?php echo $cutting_id; ?>" />

          <div class="row mb-2">
            <label for="selectFenceModel" class="col-sm-4 col-form-label text-right">Model:</label>
            <div class="col-sm-5">
              <select id="selectFenceModel" class="form-select form-select-sm" name="fence_model_id" required>
                <option value="">izaberite model</option>
                <?php
                foreach ($fence_models as $fence_model):
                  ?>
                  <option value="<?php echo $fence_model->getId() ?>"><?php echo $fence_model->getName() ?></option> 
                  <?php
                endforeach;
                ?>
              </select>
            </div>
          </div>

          <div class="row mb-2">
            <label for="selectPicketWidth" class="col-sm-4 col-form-label text-right">Širina letvice: </label>
            <div class="col-sm-5">
            <select id="selectPicketWidth" class="form-select form-select-sm" name="picket_width" required>
              <option value="35">35</option>
              <option value="60">60</option>
              <option value="80">80</option>
              <option value="100">100</option>
            </select>
            </div> 
          </div>

          <div class="row mb-2">
            <label for="inputWidth" class="col-sm-4 col-form-label text-right">Širina polja: </label>
            <div class="col-sm-3"> 
              <input class="form-control form-control-sm" id="inputWidth" type="text" name="width" value="0">
            </div>
          </div>

          <div class="row mb-2">
            <label for="inputHeight" class="col-sm-4 col-form-label text-right">Visina polja: </label>
            <div class="col-sm-3"> 
              <input class="form-control form-control-sm" id="inputHeight" type="text" name="height" value="0">
            </div>
          </div>

          <div class="row mb-2">
            <label for="inputMidHeight" class="col-sm-4 col-form-label text-right">Visina sredine polja: </label>
            <div class="col-sm-3"> 
              <input class="form-control form-control-sm" id="inputMidHeight" type="text" name="mid_height" value="0">
            </div>
          </div>

          <div class="row mb-2">
            <label for="inputSpace" class="col-sm-4 col-form-label text-right">Razmak medju letvicama: </label>
            <div class="col-sm-2"> 
              <input class="form-control form-control-sm" id="inputSpace" type="text" name="space" value="0">
            </div>
          </div>

          <div class="row mb-2">
            <label for="inputNumberOfFields" class="col-sm-4 col-form-label text-right"> Broj polja: </label>
            <div class="col-sm-2"> 
              <input class="form-control form-control-sm" id="inputNumberOfFields" type="text" name="number_of_fields"
                     value="0">
            </div>
          </div>
    
        </div>
      
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Dodaj Polje</button>
        </div>

      </form>
    </div>
  </div>
</div>
