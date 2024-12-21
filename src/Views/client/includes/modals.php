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

      <form action="/client/<?php echo $client_id ?>/addContact" method="post" role="form">
        <input type="hidden" name="client_id" value="<?php echo $client_id ?>">

        <div class="modal-body">

          <div class="row mb-2">
            <label for="selectContactType" class="col-sm-3 col-form-label text-left text-sm-right">Tip kontakta:</label>
            <div class="col-sm-5">
              <select id="selectContactType" class="form-select form-select-sm" name="contact_type_id" required>
                <option value="">izaberi tip kontakta</option>
                <?php
                $contact_types = $entityManager->getRepository('\App\Entity\ContactType')->findAll();
                foreach ($contact_types as $contact_type) :
                  ?>
                  <option value="<?php echo  $contact_type->getId() ?>"><?php echo $contact_type->getName() ?></option>
                  <?php
                endforeach;
                ?>
              </select>
            </div>
          </div>

          <div class="row mb-2">
            <label for="inputContact" class="col-sm-3 col-form-label text-left text-sm-right">Kontakt: </label>
            <div class="col-sm-9">
              <input class="form-control form-control-sm" id="inputContact" type="text" name="body" value="" >
            </div>
          </div>

          <div class="row mb-2">
            <label for="inputNote" class="col-sm-3 col-form-label text-left text-sm-right">Beleška: </label>
            <div class="col-sm-9">
              <input class="form-control form-control-sm" id="inputNote" type="text" name="note" value="" >
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Dodaj kontakt</button>
        </div>

      </form>

    </div>
  </div>
</div><!-- End of Modal addContact -->
