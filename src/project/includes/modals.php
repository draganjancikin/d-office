<!-- Modal: addTask (dodaj zadatak) -->
<div class="modal fade" id="addTask" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Dodavanje zadatka u projekat</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?view&project_id=' .$project_id. '&addTask'; ?>" method="post">

        <input type="hidden" name="status_id" value="1">
        
        <div class="modal-body">
          
          <div class="form-group row">
            <label for="selectTip" class="col-sm-3 col-lg-2 col-form-label text-md-right">Tip: </label>
            <div class="col-sm-6">
              <select id="selectTip" name="tip_id" class="form-control" required="required">
                <option value="">Izaberite tip zadatka</option>
                <option value="1">Merenje</option>
                <option value="2">Ponuda</option>
                <option value="3">Nabavka</option>
                <option value="4">Proizvodnja</option>
                <option value="5">Isporuka</option>
                <option value="6">Montaža</option>
                <option value="7">Reklamacija</option>
                <option value="8">Popravka</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="inputTitle" class="col-sm-3 col-lg-2 col-form-label text-md-right">Naslov: </label>
            <div class="col-sm-8">
              <input id="inputTitle" type="text" class="form-control" name="title" maxlength="64" placeholder="Unesite naslov zadatka">
            </div>
          </div>

        </div>
        
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-success">
            <i class="fas fa-save"> </i> Snimi zadatak
          </button>
        </div>
      
      </form>
      
    </div>
  </div>
</div>


<!-- Modal: addNote (dodaj belešku) -->
<div class="modal fade" id="addNote" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Beleška</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?view&project_id=' .$project_id. '&addNote'; ?>" method="post" >
        
        <div class="modal-body">
        
          <div class="form-group row">
            <div class="col-sm-12">
              <textarea class="form-control" rows="3" name="note" placeholder="Upišite belešku"></textarea>
            </div>
          </div>
          
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-success">
            <i class="fas fa-save"> </i> Snimi belešku
          </button>
        </div>

      </form>
      
    </div>
  </div>
</div>


<!-- Modal: addFile (dodaj fajl) -->
<div class="modal fade" id="addFile" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Dodaj fajl uz nalog (jpg, png, pdf)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?view&project_id=' .$project_id. '&addFile'; ?>" method="post" enctype="multipart/form-data">

        <div class="modal-body">
          <div class="form-group row">
            <div class="col-sm-12">
              <label for="inputFile">Izaberi faj:</label>
              <!-- <input type="hidden" name="project_id" value="<?php echo $project_id; ?>"> -->
              <input type="file" name="file" id="inputFile" title="Niste izabrali fajl!">
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-success" name="submit">
            <i class="fas fa-upload"> </i> Dodaj fajl
          </button>
        </div>

      </form>
        
    </div>
  </div>
</div>


<!-- Modal: addTaskNote (dodaj belešku uz zadatak) -->
<div class="modal fade" id="addTaskNote" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Beleška uz zadatak</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?view&project_id=' .$project_id. '&task_id=' .$task_id. '&addTaskNote'; ?>" method="post" >

        <div class="modal-body">

          <div class="form-group row">
            <div class="col-sm-12">
              <input type="text" class="form-control" name="note" value="" placeholder="Upišite belešku" />
            </div>
          </div>
        
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-success">
            <i class="fas fa-save"> </i> Snimi belešku
          </button>
        </div>
      </form>
        
    </div>
  </div>
</div>
