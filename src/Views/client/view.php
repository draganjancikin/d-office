<?php include '../src/Views/includes/pageHead.php' ?>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    
    <!-- Sidebar -->
    <?php include '../src/Views/includes/leftSidebarMeni.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php
        include '../src/Views/includes/topBar.php';
        ?>
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
            <?php include 'includes/tools_menu.php' ?>
            <!-- End of Tools Meni -->

            <?php

            echo '<div class="col-sm-12 col-md-11 col-lg-9 col-xl-7 px-2">';
            include 'includes/form_view_client.php';
            echo '</div>';

            if (isset($_GET['search'])) include '../src/Views/includes/search.php';
            ?>

            <!-- Modals -->
            <?php include 'includes/modals.php' ?>
            <!-- End of Modals -->

          </div>
          <!-- End of Content Row -->

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      
      <!-- Footer -->
      <?php include '../src/Views/includes/mainFooter.php' ?>
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
  <?php include '../src/Views/includes/pageBodyFooter.php' ?>
</body>
</html>
