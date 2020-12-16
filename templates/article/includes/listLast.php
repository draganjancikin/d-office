<!-- List Last Article -->

<!-- *********** Start OLD CODE ********* -->
<form method="get">
  <div class="form-group row">
    <div class="col-sm-5">
      <select class="form-control" name="group_id">
        <option value="0">Izaberi cenovnik</option>
        <?php
        $article_groups = $article->getArticleGroups();
        foreach ($article_groups as $article_group) {
          echo '<option value="' .$article_group['id']. '">' .$article_group['name']. '</option>';
        }
        ?>
      </select>
    </div>
        
    <div class="col-sm-5">
      <button type="submit" class="btn btn-mini btn-outline-secondary" name="priceList">Prikaži cenovnik</button>
    </div>
  </div>
</form>
<!-- *********** End OLD CODE ********* -->


<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Zadnji upisani proizvodi</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="dataTable table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th>Naziv</th>
            <th class="text-center">jed. mere</th>
            <th class="text-center">cena <br />(RSD sa PDV-om)</th>
            <th class="text-center">cena <br />(&#8364; sa PDV-om)</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th>Naziv</th>
            <th class="text-center">jed. mere</th>
            <th class="text-center">cena <br />(RSD sa PDV-om)</th>
            <th class="text-center">cena <br />(&#8364; sa PDV-om)</th>
          </tr>
        </tfoot>
        <tbody>
          <?php
          $articles = $article->getLastArticles(10);
          foreach ($articles as $articl):
            ?>
            <tr>
              <td><a href="?view&article_id=<?php echo $articl['id'] ?>"><?php echo $articl['name'] ?></a></td>
              <td class="text-center"><?php echo $articl['unit_name'] ?></td>
              <td class="text-right"><?php echo number_format( ($articl['price'] * $article->getKurs() * ($article->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
              <td class="text-right"><?php echo number_format( ($articl['price'] * ($article->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
            </tr>
            <?php
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
