<?php
if(isset($_GET['pidb_id'])) :
    $pidb_id = $_GET['pidb_id'];
    $pidb_data = $pidb->getPidb($pidb_id);
    $client = $pidb->getClientByPidbId($pidb_id);
    $transactions = $pidb->getTransactionsByPidbId($pidb_id);
    $total = $pidb->getTotalAmountsByPidbId($pidb_id)['total'];
    $total_income = $pidb->getAvansIncome($pidb_id) + $pidb->getIncome($pidb_id);
else :
    die('<script>location.href = "/pidb/index.php?transactions" </script>');
endif;
?>
<div class="card mb-4">
    <div class="card-header p-2">
        <h6 class="m-0 font-weight-bold text-primary">
            Transakcije dokumenta: 
            <a href="?view&pidb_id=<?php echo $pidb_id ?>">
                <?php echo $pidb_data['type_name'] ?>
                <?php echo str_pad($pidb_data['y_id'], 4, "0", STR_PAD_LEFT). ' - ' .date('m', strtotime($pidb_data['date'])) ?>
                <?php echo $client['name'] ?>
            </a>
        </h6>
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th>datum</th>
                        <th>duguje</th>
                        <th>potražuje</th>
                        <th></th>
                    </tr>
                </thead>
                
                <tbody>
                    <tr>
                        <td class="text-center"><?php echo date('d-m-Y', strtotime($pidb_data['date'])) ?></td>
                        <td class="text-right"><?php echo number_format($total, 4, ",", ".") ?></td>
                        <td class="text-right"></td>
                        <td></td>
                    </tr>
                    <?php
                    foreach($transactions as $transaction) :
                        ?>
                        <tr>
                            <td class="text-center"><?php echo date('d-m-Y', strtotime($transaction['date'])) ?></td>
                            <td class="text-right">
                               
                            </td>
                            <td class="text-right">
                                <?php
                                echo number_format($transaction['amount'], 4, ",", ".");
                                ?>
                            </td>
                            <td></td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right">ukupno</th>
                        <th class="text-right"><?php echo number_format($total, 4, ",", ".") ?></th>
                        <th class="text-right"><?php echo number_format($total_income, 4, ",", ".") ?></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">saldo</th>
                        <th class="text-right <?php echo ( ($total-$total_income) <= 0 ? "bg-success" : "bg-danger text-white" ) ?>">
                            <?php echo number_format($total-$total_income, 4, ",", ".") ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
