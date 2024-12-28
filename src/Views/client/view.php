<?php include '../src/Views/includes/pageHead.php' ?>

<?php include '../src/Views/includes/pageHeader.php' ?>

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

<?php include '../src/Views/includes/pageFooter.php' ?>
