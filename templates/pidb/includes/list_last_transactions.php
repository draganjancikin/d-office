<?php
$transactions = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getLastTransactions(10);
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
                $accounting_document = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getAccountingDocumentByTransaction($transaction->getId());
                ?>
                <tr>
                  <td class="text-center"><?php echo $transaction->getDate()->format('d-m-Y') ?></td>
                  <td><?php // echo $accounting_document->getClient()->getName() ?></td>
                  <td>
                    <a href="/pidb/index.php?transactions&pidb_id=<?php echo $accounting_document->getId() ?>">
                      <?php echo $accounting_document->getType()->getName() . " " . str_pad($accounting_document->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' . $accounting_document->getDate()->format('m / Y') ?>
                    </a>
                  </td>
                  <td class="text-right"><?php echo $transaction->getAmount() ?></td>
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
