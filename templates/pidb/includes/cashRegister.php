<?php
if(isset($_GET['date'])){
  $date = $_GET['date'];
} else {
  $date = "";
}
$daily_transactions = $pidb->getDailyCashTransactions($date);
$daily_cash_saldo = $pidb->getDailyCashSaldo($date);
?>
<div class="row">
  <div class="col-lg-10 col-md-12">
    <div class="card mb-4">
      <div class="card-header p-2">
        <h6 class="m-0 font-weight-bold text-primary">Kasa</h6>
      </div>
      <div class="card-body p-2">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
            <thead class="thead-light">
              <tr>
                <th class="text-center">vrsta transakcije</th>
                <th>vezani dokument</th>
                <th class="text-center">beleška</th>
                <th class="text-center">iznos</th>
              </tr>
            </thead>
            <tbody>
              <?php
            foreach($daily_transactions as $transaction) :
              ?>
              <tr>
                <td><?php echo $transaction['type_name'] ?></td>
                <td>
                  <?php
                  if ($transaction['pidb_y_id'] <> 0): 
                    ?>
                    <a href="/pidb?view&pidb_id=<?php echo $transaction['pidb_id']?>">
                      <?php echo str_pad($transaction['pidb_y_id'], 4, "0", STR_PAD_LEFT) ?>
                      <?php echo $transaction['client_name'] ?>
                      <?php echo $transaction['pidb_title'] ?>
                    </a>
                    <?php
                  endif;
                  ?>
                </td>
                <td><?php echo $transaction['note'] ?></td>
                <td class="text-right"><?php echo $transaction['amount'] ?></td>
              </tr>
              <?php
            endforeach;
            ?>
            </tbody>
            <tfoot class="thead-light">
              <tr>
                <th></th>
                <th></th>
                <th class="text-right">stanje</th>
                <th class="text-right"><?php echo $daily_cash_saldo  ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
