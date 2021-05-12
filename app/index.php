<!DOCTYPE html>
<html lang="sr">
<head>
  <title><?php echo VERSION ?></title>
  <?php include ('includes/pageHead.php') ?>
</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <?php include 'includes/leftSidebarMeni.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php include 'includes/topBar.php' ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800">Početna</h1>
          </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Klijenti Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <a href="clients" class="card-link">
                <div class="card border-left-info shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-sm font-weight-bold text-info text-uppercase mb-1">Klijenti</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php 
                          echo $entityManager->getRepository('\Roloffice\Entity\Client')->getNumberOfClients(); 
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
                <a href="pidb" class="card-link">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-sm font-weight-bold text-success text-uppercase mb-1">Dokumenti</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php 
                                      echo $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getNumberOfAccountingDocuments(); 
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
              <a href="cutting" class="card-link">
                <div class="card border-left-danger shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-sm font-weight-bold text-danger text-uppercase mb-1">Krojne liste</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php 
                            echo $entityManager->getRepository('\Roloffice\Entity\CuttingSheet')->getNumberOfCuttingSheets(); 
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
              <a href="materials" class="card-link">
                <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-sm font-weight-bold text-success text-uppercase mb-1">Materijal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php 
                          echo $entityManager->getRepository('\Roloffice\Entity\Material')->getNumberOfMaterials(); 
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
              <a href="orders" class="card-link">
                <div class="card border-left-primary shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-sm font-weight-bold text-primary text-uppercase mb-1">Nabavka</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php 
                          echo $entityManager->getRepository('\Roloffice\Entity\Order')->getNumberOfOrders(); 
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
              <a href="articles" class="card-link">
                <div class="card border-left-warning shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-sm font-weight-bold text-warning text-uppercase mb-1">Proizvodi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php 
                          echo $entityManager->getRepository('\Roloffice\Entity\Article')->getNumberOfArticles(); 
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
                <a href="admin" class="card-link">
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
              <a href="projects" class="card-link">
                <div class="card border-left-warning shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-sm font-weight-bold text-warning text-uppercase mb-1">Projekti</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php 
                          echo $entityManager->getRepository('\Roloffice\Entity\Project')->getNumberOfProjects(); 
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

          </div>
          <!-- End of Content Row -->

        </div>
        <!-- End of Page Content -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <?php include 'includes/mainFooter.php' ?>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- page body footer -->
  <?php include 'includes/pageBodyFooter.php' ?>
</body>
</html>