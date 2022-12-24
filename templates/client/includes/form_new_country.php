<!-- Form: new Counrty -->
<div class="card mb-4">
    <div class="card-header p-2">
        <h6 class="m-0 font-weight-bold text-primary">Unos nove države </h6>
    </div>
    <div class="card-body">
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?createCountry'; ?>" method="post">
            <input type="hidden" name="action" value="state">
            <div class="form-group row">
                <label for="inputName" class="col-sm-3 col-md-4 col-lg-3 col-xl-2 col-form-label text-left text-sm-right">Država: </label>
                <div class="col-sm-9 col-md-8 col-lg-9 col-xl-10">
                    <input id="inputName" class="form-control" type="text" name="name" placeholder="Unesite naziv drzave" required />
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3 offset-sm-3 offset-md-4 offset-lg-3 offset-xl-2">
                    <button type="submit" class="btn btn-sm btn-success">Snimi</button>
                </div>
            </div>
        </form>
    </div>
</div>
