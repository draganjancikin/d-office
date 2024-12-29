<?php include '../src/Views/includes/pageHead.php' ?>

<?php include '../src/Views/includes/pageHeader.php' ?>

  <!-- Tools Meni -->
  <?php include 'includes/tools__menu.php' ?>
  <!-- End of Tools Meni -->

  <!-- Main content -->
  <?php
  if (!isset($search)) include 'includes/list__last.php';
  if (isset($_GET['search'])) include '../src/Views/includes/search.php';
  ?>
  <!-- Enf of Main content -->

  <!-- Modals -->
  <?php include 'includes/modals.php' ?>
  <!-- End of Modals -->

<?php include '../src/Views/includes/pageFooter.php' ?>
