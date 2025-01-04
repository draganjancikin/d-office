<?php include '../src/Views/includes/pageHead.php' ?>

<?php include '../src/Views/includes/pageHeader.php' ?>

  <!-- Tools Meni -->
  <?php include 'includes/tools__menu.php' ?>
  <!-- End of Tools Meni -->

  <!-- Main content -->
  <?php
  if (!isset($search)) include 'includes/form__new.php';
  ?>
  <!-- Enf of Main content -->

<?php include '../src/Views/includes/pageFooter.php' ?>
