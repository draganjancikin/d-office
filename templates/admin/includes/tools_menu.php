<?php
require 'update_company_info.php';
?>
<div class="col-lg-12 col-xl-10 px-2" id="topMeni">
    <div class="card mb-2">
        <div class="card-body p-1">
            <?php
            if(isset($_GET['view']) || isset($_GET['edit'])):

                // In view case show edit button.
                if(isset($_GET['view'])):
                    ?>
                    <a href="?companyInfo&edit">
                        <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu podataka o klijentu!">
                            <i class="fas fa-edit"> </i> Izmena
                        </button>
                    </a>
                <?php
                endif;

                // In edit case show view button.
                if(isset($_GET['edit'])):
                    ?>
                    <a href="?companyInfo&view">
                        <button type="button" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled podataka o klijentu">
                            <i class="fas fa-eye"> </i> Pregled
                        </button>
                    </a>
                <?php
                endif;

            endif;
            ?>
        </div>
        <!-- End of Card Body. -->
    </div>
    <!-- End of Card. -->
</div>
