<!-- List Last Article -->

<!-- *********** Start OLD CODE ********* -->
<form method="get">
  <div class="form-group row">
    <div class="col-sm-5">
      <select class="form-select form-select-sm" name="group_id">
        <?php
        $article_groups = $entityManager->getRepository('\Roloffice\Entity\ArticleGroup')->findAll();
        foreach ($article_groups as $article_group) {
          echo '<option value="' .$article_group->getId(). '">' .$article_group->getName(). '</option>';
        }
        ?>
      </select>
    </div>
        
    <div class="col-sm-5">
      <button type="submit" class="btn btn-sm btn-outline-secondary" name="priceList">Prikaži cenovnik</button>
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
          $last_articles = $entityManager->getRepository('\Roloffice\Entity\Article')->getLastArticles(15);
          $preferences = $entityManager->find('\Roloffice\Entity\Preferences', 1);
          foreach ($last_articles as $article_data):
            ?>
            <tr>
              <td><a href="?view&article_id=<?php echo $article_data->getId() ?>"><?php echo $article_data->getName() ?></a></td>
              <td class="text-center"><?php echo $article_data->getUnit()->getName() ?></td>
              <td class="text-right"><?php echo number_format( ($article_data->getPrice() * $preferences->getKurs() * ($preferences->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
              <td class="text-right"><?php echo number_format( ($article_data->getPrice() * ($preferences->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
            </tr>
            <?php
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
