<?php
if (isset($_GET['pidb_id'])):
  $pidb_id = $_GET['pidb_id'];
  $pidb_data = $pidb->getPidb($pidb_id);
  $client_data = $entityManager->find('\App\Entity\Client', $pidb_data['client_id']);
  $transactions = $pidb->getTransactionsByClientId($client['id']);
else:
  die('<script>location.href = "/pidb/index.php?transactions" </script>');
endif;
?>
<div class="card mb-4">
  <div class="card-header p-2">
      <h6 class="m-0 font-weight-bold text-primary">Transakcije klijenta: <?php echo $client['name'] ?></h6>
  </div>
  
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
        <tr>
            <th>datum</th>
            <th>dukument</th>
            <th>iznos</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th>datum</th>
            <th>dukument</th>
            <th>iznos</th>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($transactions as $transaction): ?>
            <tr>
              <td><?php echo date('d-m-Y', strtotime($transaction['date'])) ?></td>
              <td>ID: <?php echo $transaction['pidb_id'] ?></td>
              <td><?php echo $transaction['amount'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
