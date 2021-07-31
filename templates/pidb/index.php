<!DOCTYPE html>
<html lang="sr">
<head>
  <title>Dokumenti</title>
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
            <h1 class="h3 mb-0 text-gray-800">Dokumenti</h1>
          </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Tools Meni -->
            <?php include '../../templates/pidb/includes/tools_menu.php'; ?>
            <!-- End of Tools Meni -->

            

            <div class="col-lg-12 px-2">
              <?php 
                if (empty($_GET)): // ako je $_GET prazan
                  include '../../templates/pidb/includes/list_last.php';
                else:
                  if(isset($_GET['view'])) include '../../templates/pidb/includes/form_view.php';
                  if(isset($_GET['new'])) include '../../templates/pidb/includes/form_new.php';
                  if(isset($_GET['edit'])) include '../../templates/pidb/includes/form_edit.php';
                  if(isset($_GET['editArticle']) && isset($_GET['pidb_article_id'])) include '../../templates/pidb/includes/form_edit_article.php';

                  if(isset($_GET['transactions']) AND isset($_GET['pidb_id'])) include '../../templates/pidb/includes/transactions__by__accounting_document.php';
                  if(isset($_GET['transactions']) AND !isset($_GET['pidb_id'])) include '../../templates/pidb/includes/listLastTransactions.php';

                  if( isset($_GET['cashRegister']) ) include '../../templates/pidb/includes/cashRegister.php';

                  if(isset($_GET['search'])) include '../../app/includes/search.php';
                  if(isset($_GET['set'])) include '../../templates/pidb/includes/form_preferences.php';

                endif;
              ?>
              
            </div>

            <!-- Modals -->
            <?php 
            if (isset($_GET['pidb_id'])) include '../../templates/pidb/includes/modals.php';
            if (isset($_GET['cashRegister'])) include '../../templates/pidb/includes/modals2.php';
            ?>
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
