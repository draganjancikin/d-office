<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-primary">Zadnje krojne liste</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
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
          $cuttings = $cutting->getLastCuttings(10);
          foreach ($cuttings as $cutting):
            ?>
            <tr>
              <td class="centar">
                <a href="?view&cutting_id=<?php echo $cutting['id'] ?>">KL_<?php echo str_pad($cutting['c_id'], 4, "0", STR_PAD_LEFT) ?></a>
              </td>
              <td>
                <?php echo $cutting['client_name'] ?>
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
