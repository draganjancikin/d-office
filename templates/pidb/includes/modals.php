<!-- Modal add Article to Accounting Document -->
<div class="modal" id="addArticle" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Dodavanje proizvoda u dokument</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?addArticleToAccountingDocument&pidb_id='. $pidb_id ?>" method="post" role="form">
        <input type="hidden" name="pidb_id" value="<?php echo $pidb_id ?>">

        <div class="modal-body">

          <div class="form-group row">
            <label for="article" class="col-sm-3 col-form-label">Proizvod:</label>
            <div class="col-sm-9">
              <div id="first">
                <select class="form-control" name="article_id" id="article">
                  <option value="">izaberi proizvod</option>
                  <?php
                  foreach ($all_articles as $article) :
                      ?>
                      <option value="<?php echo $article->getId() ?>"><?php echo $article->getName() ?></option>
                      <?php
                  endforeach;
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="note" class="col-sm-3 col-form-label">Dodatni opis</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="note" id="note" value="" placeholder="Upišite belešku" >
            </div>
          </div>

          <div class="form-group row">
            <label for="pieces" class="col-sm-3 col-form-label">Komada</label>
            <div class="col-sm-4">
              <input type="text" class="form-control" name="pieces" id="pieces" value="" placeholder="Unesite količinu" />
            </div>
          </div>

          <div id="second"></div>

        </div>
        <!-- End Modal Body -->

        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary" >Dodaj proizvod</button>
        </div>

      </form>
    </div>
  </div>
</div>
<!-- End Modal -->

<!-- Modal addPayment -->
<div class="modal" id="addPayment" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Evidencija uplata</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?addPayment&pidb_id='. $pidb_id ?>" method="post" role="form">
                <input type="hidden" name="pidb_id" value="<?php echo $pidb_id ?>">
                <input type="hidden" name="client_id" value="<?php echo $pidb_data->getClient()->getId() ?>">
                <div class="modal-body">

                    <div class="form-group row">
                        <label for="pidb" class="col-sm-3 col-form-label">Dokument:</label>
                        <div class="col-sm-9">
                        <select class="form-control" name="pidb_id" id="pidb">
                            <option value="<?php echo $pidb_data->getId() ?> ">
                                <?php echo $oznaka . str_pad($pidb_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '-' . $pidb_data->getDate()->format('m') . ' - ' . $pidb_data->getClient()->getName() ?>
                            </option>
                        </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="transaction_type" class="col-sm-3 col-form-label">Vrsta uplate:</label>
                        <div class="col-sm-5">
                            <select class="form-control" name="type_id" id="transaction_type">
                                <?php
                                if($pidb_data->getType()->getId() == 1) :
                                    ?>
                                    <option value="1">Avans (gotovinski)</option>
                                    <option value="2">Avans (virmanski)</option>
                                    <?php
                                elseif($pidb_data->getType()->getId() == 2):
                                    ?>
                                    <option value="3">Uplata (gotovinska)</option>
                                    <option value="4">Uplata (virmanska)</option>
                                    <?php
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="date" class="col-sm-3 col-form-label">Datum uplate:</label>
                        <div class="col-sm-5">
                            <input type="date" id="date" name="date" value="<?php echo date('Y-m-d') ?>">
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
                <!-- End Modal Body -->

                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary" >Evidentiraj uplatu</button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- End Modal -->
