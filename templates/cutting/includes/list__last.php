<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Zadnje krojne liste</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive text-nowrap">
      <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th>krojna lista</th>
            <th>klijent</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th>krojna lista</th>
            <th>klijent</th>
          </tr>
        </tfoot>
        <tbody>
          <?php
          $cutting_sheets = $entityManager->getRepository('\Roloffice\Entity\CuttingSheet')->getLastCuttingSheets(10);
          foreach ($cutting_sheets as $cutting_sheet):
            ?>
          <tr>
            <td class="centar">
              <a
                href="?view&id=<?php echo $cutting_sheet->getId() ?>">KL_<?php echo str_pad($cutting_sheet->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) ?></a>
            </td>
            <td>
              <?php echo $cutting_sheet->getClient()->getName() ?>
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