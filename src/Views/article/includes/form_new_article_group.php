<!-- New Article Group Data -->
<div class="card mb-4">

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Upis podataka o novoj grupi proizvoda</h6>
  </div>

  <div class="card-body p-2">
    <form action="<?php echo '/articles/groups/add' ?>" method="post">

      <div class="row mb-2">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-8">
          <input id="inputName" class="form-control form-control-sm" name="name" maxlength="64"
                 placeholder="Broj karaktera ograničen na 64" required>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-sm-3 col-md-6 offset-sm-3 offset-lg-2">
          <button type="submit" class="btn btn-sm btn-success" title="Snimi podatake o proizvodu!">
            <i class="fas fa-save"></i> Snimi
          </button>
          <button type="reset" class="btn btn-sm btn-default">Poništi</button>
        </div>
      </div>

    </form>
  </div>
</div>
