<!DOCTYPE html>
<html lang="sr">
<head>
    <title>Klijenti</title>
    <?php include '../../templates/includes/pageHead.php' ?>
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
    
        <!-- Sidebar -->
        <?php include '../../templates/includes/leftSidebarMeni.php' ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include '../../templates/includes/topBar.php' ?>
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
                        <?php include '../../templates/client/includes/tools_menu.php' ?>
                        <!-- End of Tools Meni -->
          

                        <?php
                        if (empty($_GET)): // ako je $_GET prazan
                            include '../../templates/client/includes/list_last.php';
                        else:
                            echo '<div class="col-sm-12 col-md-11 col-lg-9 col-xl-7 px-2">';
                            if(isset($_GET['view'])) include '../../templates/client/includes/form_view_client.php';
                            if(isset($_GET['new'])) include '../../templates/client/includes/form_new_client.php';
                            if(isset($_GET['edit'])) include '../../templates/client/includes/form_edit_client.php';

                            if(isset($_GET['newCountry'])) include '../../templates/client/includes/form_new_country.php';
                            if(isset($_GET['newCity'])) include '../../templates/client/includes/form_new_city.php';
                            if(isset($_GET['newStreet'])) include '../../templates/client/includes/form_new_street.php';

                            if(isset($_GET['alert'])) include '../../app/includes/alerts.php';
                            echo '</div>';

                            echo '<div class="col-lg-12 col-xl-10 px-2">';
                            if(isset($_GET['search'])) include '../../templates/includes/search.php';
                            if(isset($_GET['advancedSearch'])) include '../../templates/client/includes/form_advanced_search.php';
                            echo '</div>';
                        endif;
                        ?>


                        <!-- Modals -->
                        <?php include '../../templates/client/includes/modals.php' ?>
                        <!-- End of Modals -->

                    </div>
                    <!-- End of Content Row -->

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->
      
            <!-- Footer -->
            <?php include '../../templates/includes/mainFooter.php' ?>
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
    <?php include '../../templates/includes/pageBodyFooter.php' ?>
</body>
</html>
