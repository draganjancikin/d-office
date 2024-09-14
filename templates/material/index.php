<!DOCTYPE html>
<html lang="sr">
<head>
  <title>Materijali</title>
  <?php include '../../templates/includes/pageHead.php'; ?>
</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    
    <!-- Sidebar -->
    <?php include '../../templates/includes/leftSidebarMeni.php'; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php include '../../templates/includes/topBar.php'; ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800">Materijali</h1>
          </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Tools Meni -->
            <?php include '../../templates/material/includes/tools_menu.php'; ?>
            <!-- End of Tools Meni -->

            <div class="col-lg-12 px-2">
              <?php 
                if (empty($_GET)): // ako je $_GET prazan
                  include '../../templates/material/includes/list_last.php';
                else:
                  if(isset($_GET['view'])) include '../../templates/material/includes/form_view.php';
                  if(isset($_GET['edit'])) include '../../templates/material/includes/form_edit.php';
                  if(isset($_GET['new'])) include '../../templates/material/includes/form_new.php';
                  
                  if(isset($_GET['search'])) include '../../templates/includes/search.php';
                endif;
              ?>
            </div>

            <!-- Modals -->
            <?php include '../../templates/material/includes/modals.php'; ?>
            <!-- End of Modals -->

          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      
      <!-- Footer -->
      <?php include '../../templates/includes/mainFooter.php'; ?>
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
  <?php include '../../templates/includes/pageBodyFooter.php'; ?>
</body>
</html>
