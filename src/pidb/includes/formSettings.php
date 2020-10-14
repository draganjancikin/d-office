<!-- Settings Data -->
<?php
if (isset($_GET['set'])):
  ?>
  <div class="card mb-4">
    <div class="card-header p-2">
      <h6 class="m-0 text-dark">Podešavanja</h6>
    </div>
    <div class="card-body p-2">
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?editSettings'; ?>" method="post">
        
        <div class="form-group row">
          <label for="inputKurs" class="col-sm-3 col-lg-2 col-form-label text-right">Kurs EUR: </label>
          <div class="col-sm-5">
            <input id="inputKurs" class="form-control" type="text" name="kurs" value="<?php echo $conf->getKurs(); ?>" required />
          </div>
        </div>

        <div class="form-group row">
          <label for="inputTax" class="col-sm-3 col-lg-2 col-form-label text-right">PDV: </label>
          <div class="col-sm-5">
            <input id="inputTax" class="form-control" type="text" name="tax" value="<?php echo $conf->getTax(); ?>" required />
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
  <?php
endif;
?>
