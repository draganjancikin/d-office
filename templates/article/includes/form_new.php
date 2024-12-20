<!-- New Article Data -->
<div class="card mb-4">

  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Upis podataka o novom proizvodu</h6>
  </div>

  <div class="card-body p-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?createArticle'; ?>" method="post">

      <div class="form-group row">
        <label for="inputDate" class="col-sm-3 col-lg-2 col-form-label text-right">Datum:</label>
        <div class="col-sm-3">
          <input id="inputDate" class="form-control form-control-sm" name="date" value="<?php echo date("d M Y"); ?>"
                 disabled>
        </div>
      </div>

      <div class="row mb-2">
        <label for="selectGroup" class="col-sm-3 col-lg-2 col-form-label text-right">Grupa proizvoda:</label>
        <div class="col-sm-3">
          <select id="selectGroup" class="form-select form-select-sm" name="group_id">
            <option value="4">Ostalo</option>
            <?php
            $article_groups = $entityManager->getRepository('\App\Entity\ArticleGroup')->getArticleGroups();
            foreach ($article_groups as $article_group) :
              ?>
              <option value="<?php echo $article_group->getId() ?>"><?php echo $article_group->getName() ?></option>
              <?php
            endforeach;
            ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
        <div class="col-sm-8">
          <input id="inputName" class="form-control form-control-sm" name="name" maxlength="64"
            placeholder="Broj karaktera ograničen na 64" required>
        </div>
      </div>

      <div class="row mb-2">
        <label for="selectUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
        <div class="col-sm-3">
          <select id="selectUnit" class="form-select form-select-sm" name="unit_id" required>
            <option value="">Izaberi jedinicu mere</option>
            <?php
            $units = $entityManager->getRepository('\App\Entity\Unit')->findBy(array(), array('name' => 'ASC'));
            foreach ($units as $unit) :
              ?>
              <option value="<?php echo $unit->getId() ?>"><?php echo $unit->getName() ?></option>
              <?php
            endforeach;
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
        <label for="inputMin" class="col-sm-3 col-lg-2 col-form-label text-right">Min obrač. mera:</label>
        <div class="col-sm-2">
          <input id="inputMin" class="form-control form-control-sm" name="min_calc_measure" maxlength="5" value="1">
        </div>
      </div>

      <div class="row mb-2">
        <label for="inputPrice" class="col-sm-3 col-lg-2 col-form-label text-right">Cena:</label>
        <div class="col-sm-3">
          <input id="inputPrice" class="form-control form-control-sm" name="price" maxlength="9" placeholder="u eur bez PDV-a">
        </div>
        <div class="col-sm-2">eur</div>
      </div>

      <div class="row mb-2">
        <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška:</label>
        <div class="col-md-8">
          <textarea id="inputNote" class="form-control form-control-sm" rows="2" name="note"></textarea>
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
  <!-- End Card Body -->
</div>
<!-- End Card -->
