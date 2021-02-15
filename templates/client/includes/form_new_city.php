<!-- New City Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Unos novog naselja </h6>
  </div>
  <div class="card-body">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?createCity'; ?>" method="post">
      <input type="hidden" name="action" value="city">
      <div class="form-group row">
        <label for="inputName" class="col-sm-3 col-form-label text-right">Unesi naziv naselja: </label>
        <div class="col-sm-5">
          <input id="inputName" class="form-control" type="text" name="name" required />
        </div>
      </div>
      <div class="form-group row">
        <div class="col-sm-3 offset-sm-3">
          <button type="submit" class="btn btn-sm btn-success">Snimi</button>
        </div>
      </div>
    </form>
  </div>
</div>
