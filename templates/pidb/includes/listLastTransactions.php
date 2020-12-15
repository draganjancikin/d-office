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
                        <th class="text-center">iznos</th>
                    </tr>
                </thead>
                <tfoot class="thead-light">
                    <tr>
                        <th>datum</th>
                        <th>klijent</th>
                        <th>dukument</th>
                        <th class="text-center">iznos</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    foreach($transactions as $transaction) :
                        ?>
                        <tr>
                            <td><?php echo date('d-m-Y', strtotime($transaction['date'])) ?></td>
                            <td><?php echo $transaction['client_name'] ?></td>
                            <td>
                                <a href="?view&pidb_id=<?php echo $transaction['pidb_id'] ?>">
                                    <?php echo str_pad($transaction['pidb_y_id'], 4, "0", STR_PAD_LEFT) . ' - ' . date('m / Y', strtotime($transaction['date'])); ?>
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
