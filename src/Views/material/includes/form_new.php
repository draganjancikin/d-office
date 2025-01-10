<!-- New Material -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Upis podataka o novom materijalu</strong></h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo '/materials/add' ?>" method="post">

      <div class="row mb-2">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-9 col-lg-10">
          <input id="inputName" class="form-control form-control-sm" name="name" maxlength="96"
            placeholder="broj karaktera ograničen na 64" required="required" />
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
        <div class="col-sm-3">
          <select id="inputUnit" class="form-select form-select-sm" name="unit_id" required>
            <option value="">izaberi jedinicu mere</option>
            <?php
            foreach ($units as $unit) {
              echo '<option value="' . $unit->getId() . '">' . $unit->getName() . '</option>';
            }
            ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputWeight" class="col-sm-3 col-lg-2 col-form-label text-right">Težina:</label>
        <div class="col-sm-3">
          <input id="inputWeight" class="form-control form-control-sm" name="weight" maxlength="5" placeholder="u gramima">
        </div>
        <div class="col-sm-2">g</div>
      </div>

      <div class="row mb-2">
        <label for="inputPrice" class="col-sm-3 col-lg-2 col-form-label text-right">Cena:</label>
        <div class="col-sm-3">
          <input id="inputPrice" class="form-control form-control-sm" name="price" maxlength="5" placeholder="u eur bez PDV-a" />
        </div>
        <div class="col-sm-2">eur</div>
      </div>

      <div class="row mb-2">
        <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Napomena:</label>
        <div class="col-sm-8">
          <textarea id="inputNote" class="form-control form-control-sm" rows="2" name="note"></textarea>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-md-6 col-sm-3 offset-sm-3 offset-lg-2">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Snimi
          </button>
          <button type="reset" class="btn btn-default">Poništi</button>
        </div>
      </div>

    </form>
  </div>
  <!-- End Card Body -->
</div>
