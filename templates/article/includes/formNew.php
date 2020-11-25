<!-- New Article Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Upis podataka o novom proizvodu</h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?new&newArticle'; ?>" method="post" >
    
      <div class="form-group row">
        <label for="inputDate" class="col-sm-3 col-lg-2 col-form-label text-right">Datum:</label>
        <div class="col-sm-3">
            <input id="inputDate" class="form-control" name="date" value="<?php echo date("d M Y"); ?>" disabled>
        </div>
      </div>

      <div class="form-group row">
        <label for="selectGroup" class="col-sm-3 col-lg-2 col-form-label text-right">Grupa proizvoda:</label>
        <div class="col-sm-3">
          <select id="selectGroup" class="form-control" name="group_id">
            <option value="">Izaberi grupu proizvoda</option>
            <?php
            $article_groups = $article->getArticleGroups();
            foreach ($article_groups as $article_group) {
              echo '<option value="' .$article_group['id']. '">' .$article_group['name']. '</option>';
            }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-8">
          <input id="inputName" class="form-control" name="name" maxlength="64" placeholder="Broj karaktera ograničen na 64" required>
        </div>
      </div>

      <div class="form-group row">
        <label for="selectUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
        <div class="col-sm-3">
          <select id="selectUnit" class="form-control" name="unit_id" required>
            <option value="">Izaberi jedinicu mere</option>
            <?php
            $units = $article->getUnits();
            foreach ($units as $unit) {
              echo '<option value="' .$unit['id']. '">' .$unit['name']. '</option>';
            }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label for="inputWeight" class="col-sm-3 col-lg-2 col-form-label text-right">Težina:</label>
        <div class="col-sm-3">
          <input id="inputWeight" class="form-control" name="weight" maxlength="5" placeholder="u gramima">
        </div>
        <div class="col-sm-2">g</div>
      </div>

      <div class="form-group row">
        <label for="inputMin" class="col-sm-3 col-lg-2 col-form-label text-right">Min obrač. mera:</label>
        <div class="col-sm-2">
          <input id="inputMin" class="form-control" name="min_obrac_mera" maxlength="5" value="1">
        </div>
      </div>

      <div class="form-group row">
        <label for="inputPrice" class="col-sm-3 col-lg-2 col-form-label text-right">Cena:</label>
        <div class="col-sm-3">
          <input id="inputPrice" class="form-control" name="price" maxlength="5" placeholder="u eur bez PDV-a">
        </div>
        <div class="col-sm-2">eur</div>
      </div>

      <div class="form-group row">
        <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška:</label>
        <div class="col-md-8">
            <textarea id="inputNote" class="form-control" rows="2" name="note"></textarea>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-3 offset-sm-3 offset-lg-2">
            <button type="submit" class="btn btn-sm btn-success" title="Snimi podatake o proizvodu!"><i class="fas fa-save"></i> Snimi</button>
            <button type="reset" class="btn btn-sm btn-default">Poništi</button>
        </div>
      </div>

    </form>
  </div>
  <!-- End Card Body -->
</div>
<!-- End Card -->
