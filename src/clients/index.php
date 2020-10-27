<!DOCTYPE html>
<html lang="sr">
<head>
  <title>Klijenti</title>
  <?php include '../../app/includes/pageHead.php'; ?>
</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    
    <!-- Sidebar -->
    <?php include '../../app/includes/leftSidebarMeni.php'; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php include '../../app/includes/topBar.php'; ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800">Klijenti</h1>
          </div>

          <!-- Content Row -->
          <div class="row">
            
            <!-- Tools Meni -->
            <?php include '../../src/clients/includes/toolsMenu.php'; ?>
            <!-- End of Tools Meni -->
          
            <div class="col-lg-12 col-xl-10 px-2">
            <?php
              if (empty($_GET)): // ako je $_GET prazan
                include '../../src/clients/includes/listLast.php';
              else:
                if(isset($_GET['view'])) include '../../src/clients/includes/formView.php';
                if(isset($_GET['new'])) include '../../src/clients/includes/formNew.php';
                if(isset($_GET['edit'])) include '../../src/clients/includes/formEdit.php';
                
                if(isset($_GET['search'])) include '../../app/includes/search.php';
                if(isset($_GET['advancedSearch'])) include '../../src/clients/includes/advancedSearch.php';

                if(isset($_GET['addstate'])) include '../../src/clients/includes/formAddState.php';
                if(isset($_GET['addcity'])) include '../../src/clients/includes/formAddCity.php';
                if(isset($_GET['addstreet'])) include '../../src/clients/includes/formAddStreet.php';

                if(isset($_GET['alert'])) include '../../app/includes/alerts.php';
                
              endif;
            ?>
            </div>

            <!-- Modals -->
            <?php include '../../src/clients/includes/modals.php'; ?>
            <!-- End of Modals -->

          </div>
          <!-- End of Content Row -->

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      
      <!-- Footer -->
      <?php include '../../app/includes/mainFooter.php'; ?>
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
  <?php include '../../app/includes/pageBodyFooter.php'; ?>
</body>
</html>
