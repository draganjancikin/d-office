<!-- Form: new City -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Unos novog naselja </h6>
  </div>
  <div class="card-body">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?createCity'; ?>" method="post">
      <input type="hidden" name="action" value="city">
      <div class="row mb-2">
        <label for="inputName" class="col-sm-3 col-md-4 col-lg-3 col-xl-2 col-form-label text-left text-sm-right">Naselje: </label>
        <div class="col-sm-9 col-md-8 col-lg-9 col-xl-10">
          <input id="inputName" class="form-control form-control-sm" type="text" name="name"
                 placeholder="Unesite naziv naselja"
                 required />
        </div>
      </div>
      <div class="row mb-2">
        <div class="col-sm-3 offset-sm-3 offset-md-4 offset-lg-3 offset-xl-2">
          <button type="submit" class="btn btn-sm btn-success">Snimi</button>
        </div>
      </div>
    </form>
  </div>
</div>
