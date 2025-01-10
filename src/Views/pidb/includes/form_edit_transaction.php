<!-- Form: edit Transaction -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Izmena transakcije:</h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo '/pidb/' . $pidb_data->getId() . '/transaction/' . $transaction->getId() . '/edit' ?>"
          method="post" role="form">

      <div class="row mb-2">
        <label for="article" class="col-sm-3 col-form-label">Dokument:</label>
        <div class="col-sm-9">
          <input type="text" class="form-control form-control-sm" value="<?php echo $pidb_data->getType()->getName() . ' ' .
            str_pad($pidb_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT). ' - ' .$pidb_data->getDate()->format('m') . ' ' . $client_data['name'] ?>" disabled/>
        </div>
      </div>

      <div class="row mb-2">
        <label for="transaction_type" class="col-sm-3 col-form-label">Vrsta transakcije:</label>
        <div class="col-sm-4">
          <select class="form-select form-select-sm" name="type_id" id="transaction_type">
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

      <div class="row mb-2">
        <label for="date" class="col-sm-3 col-form-label">Datum transakcije:</label>
        <div class="col-sm-4">
          <input type="date" class="form-control form-control-sm" id="date" name="date" value="<?php echo $transaction->getDate()->format('Y-m-d') ?>">
        </div>
      </div>

      <div class="row mb-2">
        <label for="amount" class="col-sm-3 col-form-label">Iznos:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control form-control-sm" name="amount" id="amount" value="<?php echo $transaction->getAmount() ?>" />
        </div>
      </div>

      <div class="row mb-2">
        <label for="note" class="col-sm-3 col-form-label">Beleška:</label>
        <div class="col-sm-9">
          <input type="text" class="form-control form-control-sm" name="note" id="note" value="<?php echo
          $transaction->getNote
          () ?>" >
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-sm-3 offset-sm-3">
          <button type="submit" class="btn btn-sm btn-primary" >Izmeni transakciju</button>
        </div>
      </div>

    </form>

  </div>
</div>
