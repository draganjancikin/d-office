<!-- New State -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Unos nove ulica </h6>
  </div>
  <div class="card-body">

    <form action="<?php echo $_SERVER['PHP_SELF'] . '?createStreet'; ?>" method="post">
      <input type="hidden" name="action" value="street">
      <div class="form-group row">
        <label for="inputName" class="col-sm-4 col-md-5 col-lg-4 col-form-label text-left text-sm-right">
            Unesi naziv ulice:
        </label>
        <div class="col-sm-8 col-md-7 col-lg-8">
          <input id="inputName" class="form-control" type="text" name="name" required />
        </div>
      </div>
      <div class="form-group row">
        <div class="col-sm-3 offset-sm-4 offset-md-5 offset-lg-4">
          <button type="submit" class="btn btn-sm btn-success">Snimi</button>
        </div>
      </div>
    </form>

  </div>
</div>
