<!-- Modal addArticle -->
<div class="modal" id="addArticle" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Dodavanje proizvoda u dokument</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?addArticleInPidb&pidb_id='. $pidb_id; ?>" method="post" role="form">
        <input type="hidden" name="pidb_id" value="<?php echo $pidb_id ?>">

        <div class="modal-body">
          
          <div class="form-group row">
            <label for="article" class="col-sm-3 col-form-label">Proizvod:</label>
            <div class="col-sm-9">
              <div id="first">
                <select class="form-control" name="article_id" id="article">
                <option value="">izaberi proizvod</option>
                <?php
                foreach ($articles as $article) {
                  echo '<option value="' .$article['id']. '">' .$article['name']. '</option>';
                }
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

          <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary" >Dodaj proizvod</button>
          </div>

        </div>
        <!-- End Modal Body -->

      </form>
    </div>
  </div>
</div>
<!-- End Modal -->
