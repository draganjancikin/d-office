<?php
$transactions = $pidb->getLastTransactions(10);
?>
<div class="row">
  <div class="col-lg-10 col-md-12">
    <div class="card mb-4">
      <div class="card-header p-2">
        <h6 class="m-0 font-weight-bold text-primary">Zadnje transakcije</h6>
      </div>
      <div class="card-body p-2">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
            <thead class="thead-light">
              <tr>
                <th class="text-center">datum</th>
                <th class="text-center">klijent</th>
                <th class="text-center">dukument</th>
                <th class="text-center">iznos</th>
              </tr>
            </thead>
            <tfoot class="thead-light">
              <tr>
                <th class="text-center">datum</th>
                <th class="text-center">klijent</th>
                <th class="text-center">dukument</th>
                <th class="text-center">iznos</th>
              </tr>
            </tfoot>
            <tbody>
              <?php
              foreach($transactions as $transaction):
                $pidb_data = $pidb->getPidb($transaction['pidb_id']);
                ?>
                <tr>
                  <td class="text-center"><?php echo date('d-m-Y', strtotime($transaction['date'])) ?></td>
                  <td><?php echo $transaction['client_name'] ?></td>
                  <td>
                    <a href="?view&pidb_id=<?php echo $pidb_data['id'] ?>">
                      <?php echo $pidb_data['type_name'] . " " . str_pad($pidb_data['y_id'], 4, "0", STR_PAD_LEFT) . ' - ' . date('m / Y', strtotime($pidb_data['date'])) ?>
                    </a>
                  </td>
                  <td class="text-right"><?php echo $transaction['amount'] ?></td>
                </tr>
                <?php
              endforeach;
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
