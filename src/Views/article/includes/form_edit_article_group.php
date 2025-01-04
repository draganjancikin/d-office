<!-- View Article Group Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Izmena grupe artikala: <strong><?php echo $article_group_data->getName() ?></strong></h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo '/articles/group/' . $group_id . '/edit' ?>" method="post">

      <div class="form-group row">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-8">
          <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $article_group_data->getName() ?>" maxlength="96">
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-3 offset-sm-3 offset-lg-2">
          <button type="submit" class="btn btn-sm btn-success">
            <i class="fas fa-save"></i> Snimi
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
