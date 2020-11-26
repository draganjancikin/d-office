<!DOCTYPE html>
<html lang="sr">
<head>
  <title>Admin</title>
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
            <h1 class="h3 mb-0 text-gray-800">Admin</h1>
          </div>

          <!-- Content Row -->
          <div class="row">

            <div class="col-lg-12 px-2" id="topMeni">
              <div class="card mb-2">
                <div class="card-body py-1 px-2">

                </div>
              </div>
            </div>
            <!-- /#topMeni -->

            <div class="col-lg-12 px-2">
              <?php 
              if (empty($_GET)): // ako je $_GET prazan
                // include '../../src/article/includes/listLast.php';
              else:
                if(isset($_GET['baseBackup'])) include '../../templates/admin/includes/baseBackup.php';
                
              endif;
              ?>
            </div>



          </div>

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
