<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
    <div class="sidebar-brand-icon rotate-n-15">
      <i class="fas fa-tasks"></i>
    </div>
    <div class="sidebar-brand-text mx-3">d-Office 2025</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Nav Item - Klijenti Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#klijenti" aria-expanded="true" aria-controls="collapseTwo">
      <i class="fas fa-fw fa-user"></i>
      <span>Klijenti</span>
    </a>
    <div id="klijenti" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="/clients/">Klijenti</a>
        <a class="collapse-item" href="/clients/add"><i class="fas fa-plus"></i> Novi klijent</a>
        <a class="collapse-item" href="/clients/advancedSearch"><i class="fas fa-search"></i> Detajna
          pretraga</a>
        <hr class="sidebar-divider">
        <a class="collapse-item" href="/clients/addCountry"><i class="fas fa-plus"></i> Dodaj državu</a>
        <a class="collapse-item" href="/clients/addCity"><i class="fas fa-plus"></i> Dodaj naselje</a>
        <a class="collapse-item" href="/clients/addStreet"><i class="fas fa-plus"></i> Dodaj ulicu</a>
      </div>
    </div>
  </li>

  <!-- Nav Item - Knjigovodstvo Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#knjigovodstvo" aria-expanded="true" aria-controls="collapseTwo">
      <i class="fas fa-fw fa-list"></i>
      <span>Knjigovodstvo</span>
    </a>
    <div id="knjigovodstvo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="/pidbs/">Knjigovodstvo</a>
        <a class="collapse-item" href="/pidb/?transactions">Transakcije</a>
        <a class="collapse-item" href="/pidb/?cashRegister">Kasa</a>
        <a class="collapse-item" href="/pidb/index.php?new"><i class="fas fa-plus"></i> Novi dokument</a>
        <a class="collapse-item" href="/pidb/index.php?set"><i class="fas fa-cog"></i> Podešavanja</a>
      </div>
    </div>
    </li>

    <!-- Nav Item - Krojne liste Menu -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#krojne-liste" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-fw fa-cut"></i>
        <span>Krojne liste</span>
      </a>
      <div id="krojne-liste" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="/cutting/">Krojne liste</a>
          <a class="collapse-item" href="/cutting/index.php?new"><i class="fas fa-plus"></i> Nova krojna lista</a>
        </div>
      </div>
    </li>

    <!-- Nav Item - Materijal Menu -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#materijal" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-fw fa-inbox"></i>
        <span>Materijal</span>
      </a>
      <div id="materijal" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="/materials/">Materijal</a>
          <a class="collapse-item" href="/materials/index.php?new"><i class="fas fa-plus"></i> Novi materijal</a>
        </div>
      </div>
    </li>

    <!-- Nav Item - Nabavka Menu -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#nabavka" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-fw fa-th"></i>
        <span>Nabavka</span>
      </a>
      <div id="nabavka" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="/orders/">Nabavka</a>
          <a class="collapse-item" href="/orders/index.php?new"><i class="fas fa-plus"></i> Nova porudžbenica</a>
        </div>
      </div>
    </li>
    
    <!-- Nav Item - Proizvodi Menu -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#proizvodi" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-fw fa-tag"></i>
        <span>Proizvodi</span>
      </a>
      <div id="proizvodi" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="/articles/">Proizvodi</a>
          <a class="collapse-item" href="/articles/index.php?new"><i class="fas fa-plus"></i> Novi proizvod</a>
          <?php
          if ($user_role_id == 1):
            ?>
            <a class="collapse-item" href="/articles/index.php?articleGroups">Grupe proizvoda</a>
            <a class="collapse-item" href="/articles/index.php?newArticleGroup"><i class="fas fa-plus"></i> Nova grupa proizvoda</a>
            <?php
          endif;
          ?>
        </div>
      </div>
    </li>

    <?php
    if ($user_role_id == 1 OR $user_role_id == 2):
      ?>
      <!-- Nav Item - Admin Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#admin" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-wrench"></i>
          <span>Admin</span>
        </a>
        <div id="admin" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="/admin/">Admin</a>
            <a class="collapse-item" href="/admin/index.php?baseBackup"><i class="fas fa-download"></i> Bekap baze</a>
            <a class="collapse-item" href="/admin/index.php?companyInfo&view"><i class="fas fa-info"></i> Company info</a>
          </div>
        </div>
      </li>
      <?php
    endif;
    ?>
  
    <!-- Nav Item - Projects Menu -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#projekti" aria-expanded="true" aria-controls="collapseTwo">
        <!-- <i class="fas fa-fw fa-folder"></i> -->
        <i class="fas fa-fw fa-project-diagram"></i>
        <span>Projekti</span>
    </a>
    <div id="projekti" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="/projects/">Projekti</a>
        <a class="collapse-item" href="/projects/index.php?new"><i class="fas fa-plus"></i> Novi Projekat</a>
        <a class="collapse-item" href="/projects/index.php?advancedSearch"><i class="fa fa-search"></i> Detajna pretraga</a>
        <?php
        if ($user_role_id == 1):
          ?>
          <a class="collapse-item" href="/projects/index.php?projectTasks"><i class="fa fa-tasks"></i> Projektni zadaci</a>
          <?php
        endif;
        ?>
      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
