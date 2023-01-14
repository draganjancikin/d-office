<?php
$page = "orders";
require_once __DIR__.'/../../config/bootstrap.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    ?>
<!DOCTYPE html>
<html lang="sr">
<head>
  <title>Nabavka</title>
  <?php include __DIR__.'/../includes/pageHead.php'; ?>
</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    
    <!-- Sidebar -->
    <?php include __DIR__.'/../includes/leftSidebarMeni.php'; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php include __DIR__.'/../includes/topBar.php'; ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800">Nabavka</h1>
          </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Tools Meni -->
            <?php include __DIR__.'/../order/includes/tools__menu.php'; ?>
            <!-- End of Tools Meni -->

            <div class="col-lg-12 px-2">
              <?php 
                if (empty($_GET)): // ako je $_GET prazan
                  include __DIR__.'/../order/includes/list__last.php';
                else:
                  if(isset($_GET['view'])) include __DIR__.'/../order/includes/form__view.php';
                  if(isset($_GET['edit'])) include __DIR__.'/../order/includes/form__edit.php';
                  if(isset($_GET['new'])) include __DIR__.'/../order/includes/form__new.php';
                  
                  if(isset($_GET['search'])) include __DIR__.'/../includes/search.php';
                endif;
              ?>
            </div>

            <!-- Modals -->
            <?php include __DIR__.'/../order/includes/modals.php'; ?>
            <!-- End of Modals -->

          </div>
          <!-- /.row -->

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      
      <!-- Footer -->
      <?php include __DIR__.'/../includes/mainFooter.php'; ?>
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
  <?php include __DIR__.'/../includes/pageBodyFooter.php'; ?>
</body>
</html>
<?php
else:
    header('Location: /');
endif;
?>