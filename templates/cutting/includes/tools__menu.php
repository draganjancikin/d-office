<?php
require 'create.php';
require 'delete.php';
require 'add__article.php';
require 'update__article.php';
require 'remove__article.php';

require 'export__to__accounting_document.php';
?>
<div class="col-lg-12 col-xl-10 px-2" id="topMeni">
    <div class="card mb-2">
        <div class="card-body py-1 px-2">

            <a href="/cutting/?new" class="btn btn-sm btn-outline-secondary" title="Otvaranje nove krojne liste!">
              <i class="fas fa-plus"></i> 
              <i class="fas fa-cut"></i> 
            </a>
            <?php
            if (isset($_GET['view']) || isset($_GET['edit'])):
        
                if (isset($_GET['id'])) {
                    $id = filter_input(INPUT_GET, 'id') ;
                } else {
                    $id = htmlspecialchars($_POST['id']);
                }
                if (!$cutting_data = $entityManager->find('\App\Entity\CuttingSheet', $id)) {
                    die('<script>location.href = "/cutting/"</script>');
                }
                $client = $entityManager->getRepository('\App\Entity\Client')->getClientData($cutting_data->getClient()->getId());
                $fence_models = $entityManager->getRepository('\App\Entity\FenceModel')->findBy(array(), array('name' => 'ASC'));

                // In view case show edit button.
                if(isset($_GET['view'])):
                    ?>
                    <a href="?edit&id=<?php echo $id ?>" class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za izmenu krojne liste!">
                        <i class="fas fa-edit"></i> Izmena
                    </a>
                    <?php
                endif;

                // in edit case show view button
                if(isset($_GET['edit'])):
                    ?>
                    <a href="?view&id=<?php echo $id ?> "class="btn btn-sm btn-outline-secondary mx-1" title="Idi na stranicu za pregled krojne liste!">
                        <i class="fas fa-eye"></i> Pregled
                    </a>
                    <?php
                endif;
                ?>

                <!-- Button trigger modal for addFence -->
                <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addFence" title="Dodaj novo polje!">
                    <i class="fas fa-plus"></i> Novo polje
                </button>

                <!-- Button trigger modal for print -->
                <a href="printCutting?cutting_id=<?php echo $id ?>" class="btn btn-sm btn-outline-secondary" title="Štampaj!" target="_blank">
                    <i class="fas fa-print"></i>
                </a>

                <?php
            endif;
            ?>

        </div><!-- End of Card Body -->
    </div><!-- End of Card -->
</div>
