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
        <tfoot class="thead-light">
          <tr>
            <th class="px-1">narudžbenica</th>
            <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>
            <th class="px-1">dobavljač</th>
            <th class="px-1">naslov</th>
            <th class="px-1">za projekat</th>
          </tr>
        </tfoot>
        <tbody>
          <?php
          $orders = $entityManager->getRepository('\Roloffice\Entity\Order')->getLastOrders(10);
          foreach ($orders as $order):
            ?>
            <tr>
              <td class="px-1">
                <a href="?view&order_id=<?php echo $order->getId() ?>">
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
                if ($project = $entityManager->getRepository('Roloffice\Entity\Order')->getProject($order->getId()) ) :
                  ?>
                  <a href="/projects/?view&project_id=<?php echo $project->getId() ?>">
                  <?php echo $project->getOrdinalNumInYear() .' '. $project->getClient()->getName() .' - '. $project->getTitle() ?>
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

//get all OrderMaterial
$orders_materials = $entityManager->getRepository('Roloffice\Entity\Order')->getAllMaterialsInAllOrders();
foreach ($orders_materials as $orders_material) {
  // Check if $orders_material exist in Material
  if ($mat = $orders_material->getMaterial()) {
echo "ima ID: " . $mat->getId() . " </br>";
  } else {
    echo "nema materijala za: " . $orders_material->getId() . "</br>";
  }
  
}

