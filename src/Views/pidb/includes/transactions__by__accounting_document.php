<?php
//if(isset($_GET['pidb_id'])) :
//    $accounting_document_id = $_GET['pidb_id'];
  $pidb_data = $entityManager->find('\App\Entity\AccountingDocument', $accounting_document_id);
  $client_data = $entityManager->find('\App\Entity\Client',$pidb_data->getClient());
  $transactions = $pidb_data->getPayments();
  $total = $entityManager->getRepository('\App\Entity\AccountingDocument')->getTotalAmountsByAccountingDocument($accounting_document_id);
  $avans = $entityManager->getRepository('\App\Entity\AccountingDocument')->getAvans($accounting_document_id);
  $income = $entityManager->getRepository('\App\Entity\AccountingDocument')->getIncome($accounting_document_id);
  $total_income = $avans + $income;
//else :
//    die('<script>location.href = "/pidbs/transactions" </script>');
//endif;
?>
<div class="row">
  <div class="col-lg-9 col-md-12">
    <div class="card mb-4">
      <div class="card-header p-2">
          <h6 class="m-0 font-weight-bold text-primary">
              Transakcije dokumenta:
              <a href="/pidb/<?php echo $pidb_data->getId() ?>">
                  <?php echo $pidb_data->getType()->getName() ?>
                  <?php echo str_pad($pidb_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT). ' - ' .$pidb_data->getDate()->format('m') ?>
                  <?php echo $client_data->getName() ?>
              </a>
          </h6>
      </div>
      <div class="card-body p-2">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
            <thead class="thead-light">
              <tr class="text-center">
                <th>datum</th>
                <th>vrsta</th>
                <th>beleška</th>
                <th>duguje</th>
                <th>potražuje</th>
                <th></th>
              </tr>
            </thead>

            <tbody>
              <tr>
                  <td class="text-center"><?php echo $pidb_data->getDate()->format('d-m-Y'); ?></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-right"><?php echo number_format($total, 4, ",", "."); ?></td>
                  <td class="text-right"></td>
                  <td></td>
              </tr>
              <?php foreach ($transactions as $transaction): ?>
                <tr>
                  <td class="text-center"><?php echo $transaction->getDate()->format('d-m-Y'); ?></td>
                  <td class="text-center" title="<?php echo $transaction->getNote() ?>"><?php echo $transaction->getType()->getName(); ?></td>
                  <td class="text-center"><?php echo $transaction->getNote(); ?></td>
                  <td class="text-right"></td>
                  <td class="text-right">
                    <?php echo number_format($transaction->getAmount(), 4, ",", "."); ?>
                  </td>
                  <td>
                    <?php if ($user_role_id==1 OR $user_role_id==2): ?>
                      <a href="<?php echo '/pidb/' . $pidb_id . '/transaction/' . $transaction->getId() . '/edit'?>"
                         class="btn btn-success btn-sm"

                         title="Izmeni transakciju!">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a onClick="javascript: return confirm('Da li ste sigurni da želite da obrišete transakciju?');
" href="<?php echo '/pidb/' . $pidb_data->getId() . '/transaction/' .$transaction->getId() . '/delete' ?>"
                         title="Brisanje
Transakcije" class="btn btn-danger btn-sm">
                        <i class="far fa-trash-alt"></i>
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th class="text-right">ukupno</th>
                <th class="text-right"><?php echo number_format($total, 4, ",", ".") ?></th>
                <th class="text-right"><?php echo number_format($total_income, 4, ",", ".") ?></th>
                <th></th>
              </tr>
              <tr>
                <th></th>
                <th></th>
                <th colspan="2" class="text-right">saldo</th>
                <th class="text-right <?php echo ( (round($total, 4) - round($total_income, 4)) <= 0 ? "bg-success" : "bg-danger text-white" ) ?>">
                  <?php echo number_format($total-$total_income, 4, ",", ".") ?>
                </th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
