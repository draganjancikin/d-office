<?php
$transactions = $pidb->getLastTransactions(10);
?>
<div class="card mb-4">
    <div class="card-header p-2">
        <h6 class="m-0 font-weight-bold text-primary">Zadnje transakcije</h6>
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>datum</th>
                        <th>klijent</th>
                        <th>dukument</th>
                        <th>iznos</th>
                    </tr>
                </thead>
                <tfoot class="thead-light">
                    <tr>
                        <th>datum</th>
                        <th>klijent</th>
                        <th>dukument</th>
                        <th>iznos</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach($transactions as $transaction) :
                        ?>
                        <tr>
                            <td><?php echo date('d-m-Y', strtotime($transaction['date'])) ?></td>
                            <td>ID: <?php echo $transaction['client_id'] ?></td>
                            <td>ID: <?php echo $transaction['pidb_id'] ?></td>
                            <td><?php echo $transaction['amount'] ?></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
