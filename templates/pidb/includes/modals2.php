<!-- Modal cashInput -->
<div class="modal" id="cashInput" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Ulaz gotovine</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?addPayment' ?>" method="post" role="form">

        <!-- <input type="hidden" name="pidb_id" value="0"> -->
        <!-- <input type="hidden" name="client_id" value="0"> -->
        <!-- <input type="hidden" name="date" value="<?php // echo $pidb->getDate() ?>"> -->

        <div class="modal-body">

          <div class="form-group row">
            <label for="transaction_type" class="col-sm-3 col-form-label">Vrsta:</label>
            <div class="col-sm-5">
              <select class="form-control" name="type_id" id="transaction_type">
                <option value="5">Početno stanje kase</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="amount" class="col-sm-3 col-form-label">Iznos:</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="amount" id="amount" value="" placeholder="Unesite iznos" />
            </div>
          </div>

          <div class="form-group row">
            <label for="note" class="col-sm-3 col-form-label">Beleška:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="note" id="note" value="" >
            </div>
          </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary" >Evidentiraj</button>
        </div>

      </form>

    </div>
  </div>
</div>
<!-- End Modal -->

<!-- Modal cashOutput -->
<div class="modal" id="cashOutput" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Izlaz gotovine</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?addPayment' ?>" method="post" role="form">
        <!-- <input type="hidden" name="pidb_id" value="0"> -->
        <!-- <input type="hidden" name="client_id" value="0"> -->
        <!-- <input type="hidden" name="date" value="<?php // echo $pidb->getDate() ?>"> -->
        
        <div class="modal-body">

          <div class="form-group row">
            <label for="transaction_type" class="col-sm-3 col-form-label">Vrsta:</label>
            <div class="col-sm-5">
              <select class="form-control" name="type_id" id="transaction_type">
                <option value="7">Izlaz gotovine</option>
                <option value="6">Izlaz gotovine na kraju dana (smene)</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="amount" class="col-sm-3 col-form-label">Iznos:</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="amount" id="amount" value="" placeholder="Unesite iznos" />
            </div>
          </div>

          <div class="form-group row">
            <label for="note" class="col-sm-3 col-form-label">Beleška:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="note" id="note" value="" >
            </div>
          </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary" >Evidentiraj</button>
        </div>
      
      </form>

    </div>
  </div>
</div>
<!-- End Modal -->
