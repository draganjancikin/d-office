<!-- New order -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Otvaranje nove narudžbenice:</h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?createOrder'; ?>" method="post">

      <div class="form-group row">
        <label for="selectSupplier" class="col-sm-3 col-lg-2 col-form-label text-right">Dobavljač: </label>
        <div class="col-sm-4">
          <select id="selectSupplier" class="form-control" name="supplier_id" required>
            <option value="">izaberi dobavljača</option>
            <?php
            $suppliers = $entityManager->getRepository('\Roloffice\Entity\Client')->findBy(array('is_supplier' => 1), array('name' => 'ASC') );
            foreach ($suppliers as $supplier) {
            echo '<option value="' .$supplier->getId(). '">' .$supplier->getName(). '</option>';
            }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label for="selectProject" class="col-sm-3 col-lg-2 col-form-label text-right">Projekat: </label>
        <div class="col-sm-4">
          <select id="selectProject" class="form-control" name="project_id">
            <?php
            if(isset($_GET['project_id'])){
                $project_id = htmlspecialchars($_GET["project_id"]);
                $project_data = $entityManager->find('\Roloffice\Entity\Project', $project_id); 
                echo '<option value="'.$project_data->getId().'">' .$project_data->getOrdinalNumInYear(). ' ' .$project_data->getClient()->getName(). ': ' .$project_data->getTitle().'</option>';
            }else{
                echo '<option value="">izaberi projekat</option>';
            }
            $projects = $entityManager->getRepository('\Roloffice\Entity\Project')->findAll();
            foreach ($projects as $project) {
                echo '<option value="' .$project->getId(). '">' .$project->getOrdinalNumInYear(). ' ' .$project->getClient()->getName(). ': ' .$project->getTitle().'</option>';
            }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group row">
        <label for="inputTitle" class="col-sm-3 col-lg-2 col-form-label text-right">Naslov: </label>
        <div class="col-sm-6">
          <input id="inputTitle" class="form-control" type="text" name="title"
            placeholder="Unesite naslov narudžbenice" />
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-lg-2 col-form-label text-right">Beleška: </label>
        <div class="col-md-6 col-sm-10">
          <textarea class="form-control" rows="3" name="note" placeholder="Unesite belešku uz narudžbenicu"></textarea>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-3 offset-sm-3 offset-lg-2">
          <button type="submit" class="btn btn-sm btn-success" title="Snimi izmene podataka o narudžbenici!">
            <i class="fas fa-save"></i> Snimi
          </button>
        </div>
      </div>

    </form>
  </div>
  <!-- End Card Body -->
</div>
<!-- End Card -->