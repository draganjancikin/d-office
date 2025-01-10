<?php include '../src/Views/includes/pageHead.php' ?>

<?php include '../src/Views/includes/pageHeader.php' ?>

  <!-- Tools Meni -->
  <?php include 'includes/tools__menu.php' ?>
  <!-- End of Tools Meni -->

  <!-- Main content -->
  <?php
  if (!isset($search)) include 'includes/form_edit_material.php';
  if (isset($_GET['search'])) include '../src/Views/includes/search.php';
  ?>
  <!-- Enf of Main content -->

<?php include '../src/Views/includes/pageFooter.php' ?>
