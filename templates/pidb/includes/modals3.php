<!-- Modal edit Payment -->
<div class="modal" id="editTransaction" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Izmena transakcije</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?editTransaction' ?>" method="post" role="form">
        <input type="hidden" name="transaction_id" value="<?php echo $transaction->getId() ?>">
        <input type="hidden" name="pidb_id" value="<?php echo $pidb_data->getId() ?>">

        <div class="modal-body">

          <div class="form-group row">
            <label for="article" class="col-sm-3 col-form-label">Dokument:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" value="<?php echo $pidb_data->getType()->getName() . ' ' . str_pad($pidb_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT). ' - ' .$pidb_data->getDate()->format('m') . ' ' . $client_data->getName() ?>" disabled/>
            </div>
          </div>
          
          <div class="form-group row">
              <label for="transaction_type" class="col-sm-3 col-form-label">Vrsta transakcije:</label>
              <div class="col-sm-5">
                <select class="form-control" name="type_id" id="transaction_type">
                  <?php if ($pidb_data->getType()->getId() == 1): ?>
                    <option value="1">Avans (gotovinski)</option>
                    <option value="2">Avans (virmanski)</option>
                  <?php elseif ($pidb_data->getType()->getId() == 2): ?>
                    <option value="3">Uplata (gotovinska)</option>
                    <option value="4">Uplata (virmanska)</option>
                  <?php endif; ?>
                </select>
              </div>
          </div>
          
          <div class="form-group row">
            <label for="date" class="col-sm-3 col-form-label">Datum transakcije:</label>
            <div class="col-sm-5">
              <input type="date" id="date" name="date" value="<?php echo $transaction->getDate()->format('Y-m-d') ?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="amount" class="col-sm-3 col-form-label">Iznos:</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="amount" id="amount" value="<?php echo $transaction->getAmount() ?>" />
            </div>
          </div>

          <div class="form-group row">
            <label for="note" class="col-sm-3 col-form-label">Beleška:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="note" id="note" value="<?php echo $transaction->getNote() ?>" >
            </div>
          </div>

          
          

        </div>
        <!-- End Modal Body -->

        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary" >Izmeni transakciju</button>
        </div>
      
      </form>

    </div>
  </div>
</div>
<!-- End Modal -->