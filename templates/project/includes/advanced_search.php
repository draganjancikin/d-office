<!-- Advanced search -->
<div class="card mb-4 w-75">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">Detaljna pretraga projekata</h6>
  </div>
  <div class="card-body p-2">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?advancedSearch' ?>" method="post">

      <div class="form-group row">
        <label for="inputClient" class="col-sm-3 col-lg-2 col-form-label text-right">Kijent: </label>
        <div class="col-sm-6">
          <input id="inputClient" class="form-control" type="text" name="client" value=""
            placeholder=" Unesite naziv klijenta" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputProjectTitle" class="col-sm-3 col-lg-2 col-form-label text-right">Naslov: </label>
        <div class="col-sm-6">
          <input id="inputProjectTitle" class="form-control" type="text" name="project_title" value=""
            placeholder=" Unesite naslov projekta" />
        </div>
      </div>

      <div class="form-group row">
        <label for="inputCity" class="col-sm-3 col-lg-2 col-form-label text-right">Naselje: </label>
        <div class="col-sm-6">
          <input id="inputCity" class="form-control" type="text" name="city" value=""
            placeholder=" Unesite naziv naselja" />
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-3 offset-sm-3 offset-lg-2">
          <button type="submit" class="btn btn-sm btn-outline-secondary text-nowrap"><i class="fa fa-search"></i>
            Pretaži</button>
        </div>
      </div>

    </form>
  </div>
</div>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["advancedSearch"])) :
  $client = $_POST["client"];
  var_dump($client);
  $project_title = $_POST["project_title"];
  $city = $_POST["city"];
  $project_advanced_search_list = $entityManager->getRepository('\Roloffice\Entity\Project')->advancedSearch($client, $project_title, $city);
  ?>

<!-- <h3>Rezultati pretrage projekata</h3> -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="d-inline m-0 text-dark">Rezultati pretrage projekata</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="dataTable table table-hover" id="" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th class="px-1">projekat</th>
            <th class="px-1 text-center order-status" title="Status projekta">s</th>
            <th class="px-1">za realizaciju</th>
            <th class="px-1">u realizaciji</th>
            <th class="px-1">realizovano</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th class="px-1">narudžbenica</th>
            <th class="px-1 text-center order-status" title="Status projekta">s</th>
            <th class="px-1">za realizaciju</th>
            <th class="px-1">u realizaciji</th>
            <th class="px-1">realizovano</th>
          </tr>
        </tfoot>
        <tbody>
          <?php
                        foreach($project_advanced_search_list as $project_item):
                            $project_id = $project_item->getId();
                            $project_tasks = $entityManager->getRepository('\Roloffice\Entity\Project')->projectTasks($project_id);
                            ?>
          <tr>
            <td>
              <a href="?view&project_id=<?php echo $project_item->getId() ?>" class="d-block card-link"
                title='<?php echo $project_item->getCreatedAt()->format('M Y')?>'>
                #<?php echo str_pad($project_item->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).' - '.$project_item->getTitle() ?>
              </a>
              <?php echo $project_item->getClient()->getName(). ', <span style="font-size: 0.9em;">' .$project_item->getClient()->getCity()->getName(). '</span>'; ?>
            </td>
            <td class="px-1 order-status text-center">
              <?php
                                    switch ($project_item->getStatus()->getId()) {
                                        case 1:
                                            echo '<span class="badge badge-pill badge-light">A</span>';
                                            break;
                                        case 2:
                                            echo '<span class="badge badge-pill badge-warning">Č</span>';
                                            break;
                                        case 3:
                                            echo '<span class="badge badge-pill badge-secondary">Z</span>';
                                            break;
                                        default:
                                            break;
                                    }
                                    ?>
            </td>
            <td>
              <?php
                                    $count1 = 0;
                                    foreach($project_tasks as $project_task):
                                        if($project_task->getStatus()->getId() == 1):
                                            ?>
              <a href="?editTask&task_id=<?php echo $project_task->getId() ?>&project_id=<?php echo $project_id; ?>">
                <span class="badge badge-<?php echo $project_task->getType()->getClass() ?>">
                  <?php echo $project_task->getType()->getName() ?>
                </span>
                <?php echo $project_task->getTitle() ?>
              </a>
              <br />
              <?php
                                            $count1 ++;
                                            if ($count1 == 4):
                                                ?>
              <a class="" data-toggle="collapse" href="#collapseExample1<?php echo $project_id?>" role="button"
                aria-expanded="false" aria-controls="collapseExample1">
                <i class="fas fa-caret-down"></i>
              </a>
              <div class="collapse" id="collapseExample1<?php echo $project_id?>">
                <?php
                                            endif;
                                        endif;
                                    endforeach;
                                    if($count1 > 3) echo '</div>';
                                    ?>
            </td>

            <td>
              <?php
                                    $count2 = 0;
                                    foreach($project_tasks as $project_task):
                                      if($project_task->getStatus()->getId() == 2):
                                            ?>
              <a href="?editTask&task_id=<?php echo $project_task->getId() ?>&project_id=<?php echo $project_id; ?>">
                <span class="badge badge-<?php echo $project_task->getType()->getClass() ?>">
                  <?php echo $project_task->getType()->getName() ?>
                </span>
                <?php echo $project_task->getTitle() ?>
              </a>
              <br />
              <?php
                                            $count2 ++;
                                            if ($count2 == 4):
                                                ?>
              <a class="" data-toggle="collapse" href="#collapseExample2<?php echo $project_id?>" role="button"
                aria-expanded="false" aria-controls="collapseExample2">
                <i class="fas fa-caret-down"></i>
              </a>
              <div class="collapse" id="collapseExample2<?php echo $project_id?>">
                <?php
                                            endif;
                                        endif;
                                    endforeach;
                                    if($count2 > 3) echo '</div>';
                                    ?>
            </td>

            <td>
              <?php
                                    $count3 = 0;
                                    foreach($project_tasks as $project_task):
                                      if($project_task->getStatus()->getId() == 3):
                                            ?>
              <a href="?editTask&task_id=<?php echo $project_task->getId() ?>&project_id=<?php echo $project_id; ?>">
                <span class="badge badge-<?php echo $project_task->getType()->getClass() ?>">
                  <?php echo $project_task->getType()->getName() ?>
                </span>
                <?php echo $project_task->getTitle() ?>
              </a><br />
              <?php
                                            $count3 ++;
                                            if ($count3 == 4):
                                                ?>
              <a class="" data-toggle="collapse" href="#collapseExample3<?php echo $project_id?>" role="button"
                aria-expanded="false" aria-controls="collapseExample3">
                <i class="fas fa-caret-down"></i>
              </a>
              <div class="collapse" id="collapseExample3<?php echo $project_id?>">
                <?php
                                            endif;
                                        endif;
                                    endforeach;
                                    if($count3 > 3) echo '</div>';
                                    ?>
            </td>

          </tr>
          <?php
                        endforeach;
                        ?>
        </tbody>
      </table>

    </div>
  </div>
  <!-- End Card Body -->
  <?php
endif;