<?php
$page = "pidb";
?>
<!DOCTYPE html>
<html lang="sr">
<head>
  <title>Dokumenti</title>
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
            <h1 class="h3 mb-0 text-gray-800">Knjigovodstvo</h1>
          </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Tools Meni -->
            <?php include __DIR__.'/../pidb/includes/tools_menu.php'; ?>
            <!-- End of Tools Meni -->

            <div class="col-lg-12 px-2">
              <?php
                if (empty($_GET)): // ako je $_GET prazan
                    include __DIR__.'/../pidb/includes/list_last.php';
                else:
                  if (isset($_GET['view'])) include __DIR__.'/../pidb/includes/form_view.php';
                  if (isset($_GET['new'])) include __DIR__.'/../pidb/includes/form_new.php';
                  if (isset($_GET['edit'])) include __DIR__.'/../pidb/includes/form_edit.php';
                  if (isset($_GET['editArticle']) && isset($_GET['pidb_article_id'])) include __DIR__.'/../pidb/includes/form_edit_article.php';

                  if (isset($_GET['transactions']) AND isset($_GET['pidb_id'])) include __DIR__.'/../pidb/includes/transactions__by__accounting_document.php';
                  if (isset($_GET['transactions']) AND !isset($_GET['pidb_id'])) include __DIR__.'/../pidb/includes/list_last_transactions.php';

                  if (isset($_GET['cashRegister']) ) include __DIR__.'/../pidb/includes/cashRegister.php';

                  if (isset($_GET['search'])) include __DIR__.'/../includes/search.php';
                  if (isset($_GET['set'])) include __DIR__.'/../pidb/includes/form_preferences.php';

                endif;
              ?>

            </div>

            <!-- Modals -->
            <?php
            if (isset($_GET['pidb_id'])) include __DIR__.'/../pidb/includes/modals.php';
            if (isset($_GET['cashRegister'])) include __DIR__.'/../pidb/includes/modals2.php';
            if (isset($_GET['transactions']) AND isset($_GET['pidb_id'])) include __DIR__.'/../pidb/includes/modals3.php';
            ?>
            <!-- End of Modals -->

          </div>
          <!-- End of Content Row -->

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
