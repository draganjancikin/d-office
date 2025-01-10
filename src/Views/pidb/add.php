<?php include '../src/Views/includes/pageHead.php' ?>

<?php include '../src/Views/includes/pageHeader.php' ?>

  <!-- Tools Meni -->
  <?php include 'includes/tools_menu.php' ?>
  <!-- End of Tools Meni -->

  <!-- Main content -->
  <div class="col-lg-12 px-2">
  <?php
  include 'includes/form_new.php';
  ?>
  </div>
  <!-- Enf of Main content -->

  <!-- Modals -->
  <?php
  if (isset($_GET['pidb_id'])) include 'includes/modals.php';
  if (isset($_GET['cashRegister'])) include 'includes/modals2.php';
  if (isset($_GET['transactions']) AND isset($_GET['pidb_id'])) include 'includes/modals3.php';
  ?>
  <!-- End of Modals -->

<?php include '../src/Views/includes/pageFooter.php' ?>
