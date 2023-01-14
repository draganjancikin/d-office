<?php
$page = "projects";
require_once __DIR__.'/../../config/bootstrap.php';
session_start();
if(isset($_SESSION['username'])):
    $username = $_SESSION['username'];
    $user_role_id = $_SESSION['user_role_id'];
    ?>
    <!DOCTYPE html>
    <html lang="sr">
    <head>
      <title>Projekti</title>
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
                <h1 class="h3 mb-0 text-gray-800">Projekti</h1>
              </div>

              <!-- Content Row -->
              <div class="row">

                <!-- Tools Meni -->
                <?php include __DIR__.'/../project/includes/tools_menu.php'; ?>
                <!-- End of Tools Meni -->

                <div class="col-lg-12 px-2">
                  <?php
                  if (empty($_GET) OR isset($_GET['city_id'])): // ako je $_GET prazan ili postoji $_GET['city_id']

                    if(isset($_GET['city_id']) AND !$_GET['city_id']==""){ // ako postoji $_GET['city_id'] i nije jednak nuli
                      $city_id = $_GET['city_id'];
                      include __DIR__.'/../project/includes/list_projects_by_city.php';
                    }else{
                      include __DIR__.'/../project/includes/list_active_projects.php';
                    }

                  else:

                    if (isset($_GET['new'])) include __DIR__.'/../project/includes/form_new_project.php';
                    if (isset($_GET['view']) && isset($_GET['project_id'])) include __DIR__.'/../project/includes/form_view_project.php';
                    if (isset($_GET['edit']) && isset($_GET['project_id'])) include __DIR__.'/../project/includes/form_edit_project.php';
                    if (isset($_GET['editTask'])) include __DIR__.'/../project/includes/form_edit_task.php';

                    if (isset($_GET['search'])) include __DIR__.'/../includes/search.php';
                    if (isset($_GET['advancedSearch'])) include __DIR__.'/../project/includes/advanced_search.php';

                    if (isset($_GET['projectTasks'])) include __DIR__.'/../project/includes/project_tasks.php';

                  endif;
                  ?>
                </div>
                <!-- Modals -->
                <?php include __DIR__.'/../project/includes/modals.php'; ?>
                <!-- End of Modals -->

              </div>
              <!-- End Content Row -->

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
