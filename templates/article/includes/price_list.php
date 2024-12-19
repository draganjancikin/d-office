<?php
if (isset($_GET['group_id']) AND $_GET['group_id'] <> "") {

  $group_id = $_GET['group_id'];
  $group = $entityManager->find("\Roloffice\Entity\ArticleGroup", $group_id);
  $title = "Cenovnik: " . $group->getName();

  $count = 0;
  ?>

  <!-- *********** Start OLD CODE ********* -->
  <form method="get">
    <div class="form-group row">
      <div class="col-sm-5">
        <select class="form-select form-select-sm" name="group_id">
          <?php
          $article_groups = $entityManager->getRepository('\Roloffice\Entity\ArticleGroup')->findAll();
          foreach ($article_groups as $article_group) :
            ?>
            <option value="<?php echo $article_group->getId() ?>"><?php echo $article_group->getName() ?></option>
            <?php
          endforeach;
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
    <div class="card-header py-2">
      <h6 class="m-0 font-weight-bold text-primary"><?php echo $title ?></h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class=" table table-bordered table-hover" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th class="px-1">#</th>
              <th class="px-1">Naziv</th>
              <th class="px-1">jed. mere</th>
              <th class="px-1">cena <br />(RSD sa PDV-om)</th>
              <th class="px-1">cena <br />(EUR sa PDV-om)</th>
            </tr>
          </thead>
          <tfoot class="thead-light">
            <tr>
              <th class="px-1">#</th>
              <th class="px-1">Naziv</th>
              <th class="px-1">jed. mere</th>
              <th class="px-1">cena <br />(RSD sa PDV-om)</th>
              <th class="px-1">cena <br />(EUR sa PDV-om)</th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            $articles_by_group = $entityManager->getRepository('\Roloffice\Entity\Article')->getArticlesByGroup($group_id);
            $preferences = $entityManager->find('\Roloffice\Entity\Preferences', 1);
            foreach ($articles_by_group as $article_by_group):
              $count++;
              ?>
              <tr>
                <td class="px-1"><?php echo $count ?></td>  
                <td class="px-1"><a href="?view&article_id=<?php echo $article_by_group->getId() ?>"><?php echo $article_by_group->getName() ?></a></td>
                <td class="px-1"><?php echo $article_by_group->getUnit()->getName() ?></td>
                <td class="px-1"><?php echo number_format( ($article_by_group->getPrice() * $preferences->getKurs() * ($preferences->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
                <td class="px-1"><?php echo number_format( ($article_by_group->getPrice() * ($preferences->getTax()/100 + 1) ) , 4, ",", ".") ?></td>
              </tr>
              <?php
            endforeach;
            ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- End Card Body -->
  </div>
  <?php  
}
