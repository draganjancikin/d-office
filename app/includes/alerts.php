<!-- Alerts -->


    <?php
    if (isset($_GET['ob'])):
        $ob = $_GET['ob'];
        
        switch ($ob) {
            case "1":
                $name = ($_GET['name']);
                ?>
                <div class="col-lg-8">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Obaveštenje!</strong>  Izvršen je "Back up" baze u fajl: C:/<?php echo $name;?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <?php
                break;
            case "2":
                ?>
                <!-- NEW CODE -->
                <div class="col-lg-8">
                    <div class="alert alert-warning" role="alert">
                        <strong>Pažnja!</strong> Unos postoji u bazi.
                    </div>
                </div>
                <?php
                break;
            case "3":
                ?>
                <!--  OLD CODE -->
                <div class="alert alert-warning alert-dismissable">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <strong>Pažnja!</strong> Artikal postoji u narudžbenici.
                </div>
                <?php
                break;
            case "4":
                ?>
                <!--  OLD CODE -->
                <div class="alert alert-warning alert-dismissable">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <strong>Pažnja!</strong> Morate uneti naziv.
                </div>
                <?php
                break;
        }

    endif;

    if (isset($_GET['nouser'])):
      ?>
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Ups!</strong> Ne postoji kombinacija korisničkog imena i password-a koje ste ukucali! Pokušajte ponovo.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <?php
    endif;

?>
