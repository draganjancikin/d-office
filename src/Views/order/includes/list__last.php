<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Zadnje narudžbenice</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th class="px-1 order-number">narudžbenica</th>
            <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>
            <th class="px-1 order-supplier">dobavljač</th>
            <th class="px-1">naslov</th>
            <th class="px-1">za projekat</th>
          </tr>
        </thead>
<!--        <tfoot class="thead-light">-->
<!--          <tr>-->
<!--            <th class="px-1">narudžbenica</th>-->
<!--            <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>-->
<!--            <th class="px-1">dobavljač</th>-->
<!--            <th class="px-1">naslov</th>-->
<!--            <th class="px-1">za projekat</th>-->
<!--          </tr>-->
<!--        </tfoot>-->
        <tbody>
          <?php
          foreach ($orders as $order):
            ?>
            <tr>
              <td class="px-1">
                <a href="/order/<?php echo $order->getId() ?>">
                  <?php echo str_pad($order->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '_' . $order->getDate()->format('m_Y') ?>
                </a>
              </td>
              <td class="px-1 order-status text-center">
                <?php
                if($order->getStatus() == 0):
                  ?>
                  <span class="badge badge-pill badge-light">N</span>
                  <?php
                endif;
                if($order->getStatus() == 1):
                  ?>
                  <span class="badge badge-pill badge-warning">P</span>
                  <?php
                endif;
                if($order->getStatus() == 2):
                  ?>
                  <span class="badge badge-pill badge-success">S</span>
                  <?php
                endif;
                if($order->getIsArchived() == 1):
                  ?>
                  <span class="badge badge-pill badge-secondary">A</span>
                  <?php
                endif;
                ?>
              </td>
              <td class="px-1"><?php echo $order->getSupplier()->getName() ?></td>
              <td class="px-1"><?php echo $order->getTitle() ?></td>
              <td class="px-1">
                <?php
                if ($project = $entityManager->getRepository('App\Entity\Order')->getProject($order->getId()) ) :
                  ?>
                  <a href="/projects/?view&project_id=<?php echo $project->getId() ?>">
                  <?php echo $project->getOrdinalNumInYear() . ' list__last.php' . $project->getClient()->getName() .' - '. $project->getTitle() ?>
                  </a>
                  <?php
                endif;
                ?>
              </td>
            </tr>
            <?php
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php
// Don't delete code below. This is for database checking
/*
$svi_materijali_na_svim_narudzbenicama = $entityManager->getRepository('App\Entity\OrderMaterial')->findAll();

foreach ($svi_materijali_na_svim_narudzbenicama as $jedan_materijal_na_narudzbenici) {
  $id_jednog_materijala_na_narudzbenici = $jedan_materijal_na_narudzbenici->getId();
  $id_materijala_na_narudzbenici = $jedan_materijal_na_narudzbenici->getMaterial()->getId();

  if ($id_jednog_materijala_na_narudzbenici > 2300) {
    echo $id_jednog_materijala_na_narudzbenici . " - " . $id_materijala_na_narudzbenici . "<br>";

    $svi_materijali = $entityManager->getRepository('App\Entity\Material')->findAll();
    $control = FALSE;
    foreach ($svi_materijali as $jedan_materijal) {
      if ($id_materijala_na_narudzbenici == $jedan_materijal->getId()) {
        $control = TRUE;
      }
    }
    if(!$control) {
      echo "Materijal na narudžbenici sa ID: " . $id_jednog_materijala_na_narudzbenici . " treba brisati";
      exit();
    }

  }
}
*/

/*
$sve_osobine_svih_materijala_na_svim_narudzbenicama = $entityManager->getRepository('App\Entity\OrderMaterialProperty')->findAll();

foreach ($sve_osobine_svih_materijala_na_svim_narudzbenicama as $jedna_osobina_materijala) {
  $id_jedne_osobine_materijala = $jedna_osobina_materijala->getId();
  $id_materijala_na_narudzbenici = $jedna_osobina_materijala->getOrderMaterial()->getId();
  
  if ($id_jedne_osobine_materijala > 2000) {

    // echo "ID Osobine materijala na narudžbenicama: " . $id_jedne_osobine_materijala . "</br>";
    // echo "ID materijala na narudžbenici: " . $id_materijala_na_narudzbenici  . "</br>";
    echo $id_jedne_osobine_materijala . "<br>";
    
    $svi_materijali_na_narudzbenicama = $entityManager->getRepository('App\Entity\OrderMaterial')->findAll();
    $control = FALSE;
    foreach ($svi_materijali_na_narudzbenicama as $jedan_materijal_na_narudzbenici) {
      if ($id_materijala_na_narudzbenici == $jedan_materijal_na_narudzbenici->getId()) {
        $control = TRUE;
      }
    }
    if(!$control) {
      echo "Osobinu materijala sa ID: " . $id_jedne_osobine_materijala . " treba brisati";
      exit();
    }
    
  }


}
*/

