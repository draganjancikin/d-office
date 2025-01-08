<?php include '../src/Views/includes/pageHead.php' ?>

<?php include '../src/Views/includes/pageHeader.php' ?>

  <!-- Tools Meni -->
  <?php include 'includes/tools_menu.php' ?>
  <!-- End of Tools Meni -->

  <!-- Main content -->
  <?php
  if (!isset($search)) include 'includes/advanced_search.php';
  if (isset($_GET['search'])) include '../src/Views/includes/search.php';
  ?>
  <!-- Enf of Main content -->

<?php include '../src/Views/includes/pageFooter.php' ?>
