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
          $orders = $order->getLastOrders(10);
          foreach ($orders as $order):
            $project_id = $order['project_id'];
            $project_data = $project->getProject($project_id);
            ?>
            <tr>
              <td class="px-1">
                <a href="?view&order_id=<?php echo $order['id'] ?>">
                  <?php echo str_pad($order['o_id'], 4, "0", STR_PAD_LEFT) . '_' . date('m_Y', strtotime($order['date'])) ?>
                </a>
              </td>
              <td class="px-1 order-status text-center">
                <?php
                if($order['status'] == 0):
                  ?>
                  <span class="badge badge-pill badge-light">N</span>
                  <?php
                endif;
                if($order['status'] == 1):
                  ?>
                  <span class="badge badge-pill badge-warning">P</span>
                  <?php
                endif;
                if($order['status'] == 2):
                  ?>
                  <span class="badge badge-pill badge-success">S</span>
                  <?php
                endif;
                if($order['is_archived'] == 1):
                  ?>
                  <span class="badge badge-pill badge-secondary">A</span>
                  <?php
                  endif;
                ?>
              </td>
              <td class="px-1"><?php echo $order['supplier_name'] ?></td>
              <td class="px-1"><?php echo $order['title'] ?></td>
              <td class="px-1">
                <a href="/projects/?view&project_id=<?php echo $project_data['id'] ?>">
                  <?php echo $project_data['pr_id'] .' '. $project_data['client_name'] .' - '. $project_data['title']; ?>
                </a>
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
