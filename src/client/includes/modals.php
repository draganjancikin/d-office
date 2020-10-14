<!-- Modal addContact -->
<div class="modal" id="addContact" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Dodavanje kontakta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?newContact'; ?>" method="post" role="form">
        <input type="hidden" name="client_id" value="<?php echo $client_id ?>">

        <div class="modal-body">

          <div class="form-group row">
            <label for="selectContactType" class="col-sm-3 col-form-label text-right">Tip kontakta:</label>
            <div class="col-sm-5">
              <select id="selectContactType" class="form-control" name="contacttype_id">
              <option value="">izaberi tip kontakta</option>
              <?php
                foreach ($contacttypes as $contacttype) {
                  echo '<option value="' .$contacttype['id']. '">' .$contacttype['name']. '</option>';
                }
              ?>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="inputContact" class="col-sm-3 col-form-label text-right">Kontakt: </label>
            <div class="col-sm-9"> 
              <input class="form-control" id="inputContact" type="text" name="number" value="" >
            </div>
          </div>

          <div class="form-group row">
            <label for="inputNote" class="col-sm-3 col-form-label text-right">Beleška: </label>
            <div class="col-sm-9">
              <input class="form-control" id="inputNote" type="text" name="note" value="" >
            </div>
          </div>
              
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
          <button type="submit" class="btn btn-primary">Dodaj kontakt</button>
        </div>

      </form>

    </div>
  </div>
</div>
<!-- End Modal addContact -->
