<!-- Predračuni -->
<?php
$proformas = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getLast(1, 0, 10);
?>
<div class="card  border-info mb-4">
  <div class="card-header bg-info p-2">
    <h6 class="m-0 font-weight-bold text-white">Predračun</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th>oznaka</th>
            <th>naziv klijenta</th>
            <th>naslov dokumenta</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th>oznaka</th>
            <th>naziv klijenta</th>
            <th>naslov dokumenta</th>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($proformas as $proforma): ?>
            <tr>
              <td>
                <a href="?view&pidb_id=<?php echo $proforma->getId() ?>">
                  <?php echo "P_" . str_pad($proforma->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' . $proforma->getCreatedAt()->format('m / Y') ?>
                </a>
              </td>
              <td>
                <?php echo $proforma->getClient()->getName() ?>
              </td>
              <td><?php echo $proforma->getTitle() ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Otpremnice -->
<?php
$delivery_notes = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getLast(2, 0, 10);
?>
<div class="card  border-secondary mb-4">
  <div class="card-header bg-secondary p-2">
    <h6 class="m-0 font-weight-bold text-white">Otpremnica</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th>oznaka</th>
            <th>naziv klijenta</th>
            <th>naslov dokumenta</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th>oznaka</th>
            <th>naziv klijenta</th>
            <th>naslov dokumenta</th>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($delivery_notes as $delivery_note): ?>
            <tr>
              <td>
                <a href="?view&pidb_id=<?php echo $delivery_note->getId() ?>">
                  <?php echo "O_" . str_pad($delivery_note->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' . $delivery_note->getCreatedAt()->format('m / Y') ?>
                </a>
              </td>
              <td>
                <?php echo $delivery_note->getClient()->getName() ?>
              </td>
              <td><?php echo $delivery_note->getTitle() ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Povratnice -->
<?php
$return_receipts = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getLast(4, 0, 10);
?>
<div class="card  border-warning mb-4">
  <div class="card-header bg-warning p-2">
    <h6 class="m-0 font-weight-bold text-white">Povratnica</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th>oznaka</th>
            <th>naziv klijenta</th>
            <th>naslov dokumenta</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th>oznaka</th>
            <th>naziv klijenta</th>
            <th>naslov dokumenta</th>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($return_receipts as $return_receipt): ?>
            <tr>
              <td>
                <a href="?view&pidb_id=<?php echo $return_receipt->getId() ?>">
                  <?php echo "POV_" . str_pad($return_receipt->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' . $return_receipt->getCreatedAt()->format('m / Y') ?>
                </a>
              </td>
              <td>
                <?php echo $return_receipt->getClient()->getName() ?>
              </td>
              <td><?php echo $return_receipt->getTitle() ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
