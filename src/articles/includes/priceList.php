<?php
if(isset($_GET['group_id']) AND $_GET['group_id']<>""  ){
    
  $group_id = $_GET['group_id'];
    
  if ($group_id == 0) $title = "Cenovnik: Svi proizvodi";
  if ($group_id == 1) $title = "Cenovnik: Agro program";
  if ($group_id == 2) $title = "Cenovnik: PVC ograda";
  if ($group_id == 3) $title = "Cenovnik: Građevinski program";
  if ($group_id == 4) $title = "Cenovnik: Ostalo";
  if ($group_id == 5) $title = "Cenovnik: Transport";
    
  $count = 0;
  ?>
  
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
            $articles_by_group = $article->getArticlesByGroup($group_id);
            foreach ($articles_by_group as $article_by_group):
              $count++;
              ?>
              <tr>
                <td class="px-1"><?php echo $count ?></td>  
                <td class="px-1"><a href="?view&article_id=<?php echo $article_by_group['id'] ?>"><?php echo $article_by_group['name'] ?></a></td>
                <td class="px-1"><?php echo $article_by_group['unit_name'] ?></td>
                <td class="px-1"><?php echo number_format( ($article_by_group['price'] * $article->getKurs() * ($article->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
                <td class="px-1"><?php echo number_format( ($article_by_group['price'] * ($article->getTax()/100 + 1) ) , 4, ",", ".") ?></td>
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
