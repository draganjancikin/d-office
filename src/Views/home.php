<?php include '../src/Views/includes/pageHead.php' ?>

<?php include '../src/Views/includes/pageHeader.php' ?>

  <!-- Klijenti Card -->
  <div class="col-xl-3 col-md-6 mb-4">
    <a href="/clients/" class="card-link">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-sm font-weight-bold text-info text-uppercase mb-1">Klijenti</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?php
                echo $entityManager->getRepository('\App\Entity\Client')->count([]);
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-user fa-2x text-info"></i>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Dokumenti Card -->
  <div class="col-xl-3 col-md-6 mb-4">
    <a href="/pidbs/" class="card-link">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-sm font-weight-bold text-success text-uppercase mb-1">Dokumenti</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
              <?php
                echo $entityManager->getRepository('\App\Entity\AccountingDocument')->count([]);
              ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-list fa-2x text-success"></i>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Krojne liste Card -->
  <div class="col-xl-3 col-md-6 mb-4">
    <a href="/cuttings/" class="card-link">
      <div class="card border-left-danger shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-sm font-weight-bold text-danger text-uppercase mb-1">Krojne liste</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?php
                  echo $entityManager->getRepository('\App\Entity\CuttingSheet')->count([]);
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-cut fa-2x text-danger"></i>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Materijal Card -->
  <div class="col-xl-3 col-md-6 mb-4">
    <a href="materials/" class="card-link">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-sm font-weight-bold text-success text-uppercase mb-1">Materijal</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?php
                echo $entityManager->getRepository('\App\Entity\Material')->count([]);
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-inbox fa-2x text-success"></i>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Nabavka Card -->
  <div class="col-xl-3 col-md-6 mb-4">
    <a href="/orders/" class="card-link">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-sm font-weight-bold text-primary text-uppercase mb-1">Nabavka</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?php
                echo $entityManager->getRepository('\App\Entity\Order')->count([]);
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-th fa-2x text-primary"></i>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Proizvodi Card -->
  <div class="col-xl-3 col-md-6 mb-4">
    <a href="articles/" class="card-link">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-sm font-weight-bold text-warning text-uppercase mb-1">Proizvodi</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?php
                echo $entityManager->getRepository('\App\Entity\Article')->count([]);
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-tag fa-2x text-warning"></i>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
  <?php
  if($user_role_id==1 OR $user_role_id==2):
    ?>
    <!-- Card Admin -->
    <div class="col-xl-3 col-md-6 mb-4">
      <a href="admin/" class="card-link">
        <div class="card border-left-secondary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1">Admin</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">. . .</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-wrench fa-2x text-secondary"></i>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <!-- End of Card Admin -->
    <?php
  endif;
  ?>

  <!-- Card Projects -->
  <div class="col-xl-3 col-md-6 mb-4">
    <a href="projects/" class="card-link">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-sm font-weight-bold text-warning text-uppercase mb-1">Projekti</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?php
                echo $entityManager->getRepository('\App\Entity\Project')->count([]);
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-project-diagram fa-2x text-warning"></i>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
  <!-- End of Card Projects -->

<?php include '../src/Views/includes/pageFooter.php' ?>
