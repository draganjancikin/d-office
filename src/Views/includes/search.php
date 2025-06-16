<?php

if ($page == "order"):
  $term = filter_input(INPUT_GET, 'search');
  $orders = $entityManager->getRepository('\App\Entity\Order')->search($term);
  ?>
  <div class="card mb-4">
    <div class="card-header p-2">
      <h6 class="m-0 font-weight-bold text-primary">Pretraga narudžbenica</h6>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-hover table-bordered" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th class="px-1 order-number">narudžbenica</th>
              <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>
              <th class="px-1 order-supplier">dobavljač</th>
              <th class="px-1">naslov</th>
              <th class="px-1">za projekat</th>
              <th class="px-1"></th>
            </tr>
          </thead>
          <tfoot class="thead-light">
            <tr>
              <th class="px-1">narudžbenica</th>
              <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>
              <th class="px-1">dobavljač</th>
              <th class="px-1">naslov</th>
              <th class="px-1">za projekat</th>
              <th class="px-1"></th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            foreach ($orders as $order_data):
              $project_data = $entityManager->getRepository('\App\Entity\Order')->getProject($order_data->getId());
              if ($order_data->getIsArchived() == 0):
                ?>
                <tr>
                  <td class="px-1">
                    <a href="/order/<?php echo $order_data->getId() ?>">
                      <?php echo str_pad($order_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '_' . $order_data->getDate()->format('m_Y') ?>
                    </a>
                  </td>
                  <td class="px-1 order-status text-center">
                  <?php
                    if ($order_data->getStatus() == 0):
                      ?>
                      <span class="badge badge-pill badge-light">N</span>
                      <?php
                    endif;
                    if ($order_data->getStatus() == 1):
                      ?>
                      <span class="badge badge-pill badge-warning">P</span>
                      <?php
                    endif;
                    if ($order_data->getStatus() == 2):
                      ?>
                      <span class="badge badge-pill badge-success">S</span>
                      <?php
                    endif;
                    if ($order_data->getStatus() == 3):
                      ?>
                      <span class="badge badge-pill badge-secondary">A</span>
                      <?php
                    endif;
                  ?>
                  </td>
                  <td class="px-1">
                    <?php echo $order_data->getSupplier()->getName() ?>
                  </td>
                  <td class="px-1">
                    <?php echo $order_data->getTitle() ?>
                  </td>
                  <td class="px-1">
                    <?php
                    if (null !== $project_data):
                      ?>
                      <a href="/project/<?php echo $project_data->getId() ?>">
                        <?php echo $project_data->getOrdinalNumInYear() .' '. $project_data->getClient()->getName() .' - '. $project_data->getTitle() ?>
                      </a>
                      <?php
                    endif;
                    ?>
                  </td>
                  <td class="px-1">
                    <?php
                    $last_order = $entityManager->getRepository('\App\Entity\Order')->getLastOrder();
                    echo ( $order_data == $last_order ? '<a onClick="javascript: return confirm(\'Da li sigurno želite obrisati narudžbenicu?\')" href="/order/' . $order_data->getId(). '/delete" class="btn btn-danger btn-mini btn-article"><i class="fas fa-trash-alt"> </i> </a>' : '');
                    ?>
                  </td>
                </tr>
                <?php
              endif;
            endforeach;
            ?>
          </tbody>
        </table>
        <h4>Arhivirano</h4>
        <table class="dataTable table table-hover table-bordered" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th class="px-1 order-number">narudžbenica</th>
              <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>
              <th class="px-1 order-supplier">dobavljač</th>
              <th class="px-1">naslov</th>
              <th class="px-1">za projekat</th>
            </tr>
          </thead>
<!--          <tfoot class="thead-light">-->
<!--            <tr>-->
<!--              <th class="px-1">narudžbenica</th>-->
<!--              <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>-->
<!--              <th class="px-1">dobavljač</th>-->
<!--              <th class="px-1">naslov</th>-->
<!--              <th class="px-1">za projekat</th>-->
<!--            </tr>-->
<!--          </tfoot>-->
          <tbody>

          <?php
          // List archived orders.
          foreach ($orders as $order_data):
            $project_data = $entityManager->getRepository('\App\Entity\Order')->getProject($order_data->getId());
            if ($order_data->getIsArchived() == 1):
              ?>
              <tr class="table-secondary">
                <td class="px-1">
                  <a href="/order/<?php echo $order_data->getId() ?>">
                    <?php echo str_pad($order_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '_' . $order_data->getDate()->format('m_Y') ?>
                  </a>
                </td>
                <td class="px-1 order-status text-center">
                  <?php
                  if ($order_data->getStatus() == 0):
                    ?>
                    <span class="badge badge-pill badge-light">N</span>
                    <?php
                  endif;
                  if ($order_data->getStatus() == 1):
                    ?>
                    <span class="badge badge-pill badge-warning">P</span>
                    <?php
                  endif;
                  if ($order_data->getStatus() == 2):
                    ?>
                    <span class="badge badge-pill badge-success">S</span>
                    <?php
                  endif;
                  if ($order_data->getIsArchived() == 1):
                    ?>
                    <span class="badge badge-pill badge-secondary">A</span>
                    <?php
                  endif;
                  ?>

                  </td>
                  <td class="px-1">
                    <?php echo $order_data->getSupplier()->getName() ?>
                  </td>
                  <td class="px-1">
                    <?php echo $order_data->getTitle() ?>
                  </td>
                  <td class="px-1">
                    <?php
                    if (null !== $project_data):
                      ?>
                      <a href="/projects/?view&project_id=<?php echo $project_data->getId() ?>">
                        <?php echo $project_data->getOrdinalNumInYear() .' '. $project_data->getClient()->getName() .' - '. $project_data->getTitle() ?>
                      </a>
                      <?php
                    endif;
                    ?>
                  </td>
              </tr>
              <?php
            endif;
          endforeach;
          ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php
endif;

if ($page == "article"):
  $term = filter_input(INPUT_GET, 'search');
  ?>

  <!--  *********** Start OLD CODE **************** -->
  <form method="get" action="/articles/price-list" class="form-horizontal" role="form" method="get">
    <div class="form-group row">
      <div class="col-md-5">
        <select class="form-select form-select-sm" name="group_id">
          <option value="0">Izaberi cenovnik</option>
          <?php
          $article_groups = $entityManager->getRepository('\App\Entity\ArticleGroup')->findAll();
          foreach ($article_groups as $article_group) {
            echo '<option value="' .$article_group->getId(). '">' .$article_group->getName(). '</option>';
          }
          ?>
        </select>
      </div>

      <div class="col-sm-5">
        <button type="submit" class="btn btn-sm btn-outline-secondary">Prikaži cenovnik</button>
      </div>
    </div>
  </form>
  <!--  *********** End OLD CODE **************** -->

  <div class="card mb-4">
    <div class="card-header p-2">
        <h6 class="m-0 font-weight-bold text-primary">Pretraga proizvoda</h6>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th>naziv artikla</th>
              <th class="text-center">jed. mere</th>
              <th class="text-center">cena <br />(RSD sa PDV-om)</th>
              <th class="text-center">cena <br />(EUR sa PDV-om)</th>
            </tr>
          </thead>
          <tfoot class="thead-light">
            <tr>
              <th>naziv artikla</th>
              <th class="text-center">jed. mere</th>
              <th class="text-center">cena <br />(RSD sa PDV-om)</th>
              <th class="text-center">cena <br />(EUR sa PDV-om)</th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            $articles = $entityManager->getRepository('\App\Entity\Article')->search($term);
            $preferences = $entityManager->find('\App\Entity\Preferences', 1);
            foreach ($articles as $articl):
              ?>
              <tr>
                <td><a href="/article/<?php echo $articl->getId() ?>" title="<?php echo
                  $articl->getNote() ?>"><?php echo $articl->getName() ?></a></td>
                <td class="text-center"><?php echo $articl->getUnit()->getName() ?></td>
                <td class="text-right"><?php echo number_format( ($articl->getPrice() * $preferences->getKurs() * ($preferences->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
                <td class="text-right"><?php echo number_format( ($articl->getPrice() * ($preferences->getTax()/100 + 1) ) , 2, ",", ".") ?></td>
              </tr>
              <?php
            endforeach;
            ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- End Card Body -->
  </div>
  <!-- End Card -->
  <?php
endif;

if ($page == "project"):
  $term = filter_input(INPUT_GET, 'search');
  $project_list = $entityManager->getRepository('\App\Entity\Project')->search($term);
  ?>
  <h3>Rezultati pretrage projekata</h3>
  <div class="card mb-4">
    <div class="card-header p-2">
        <h6 class="d-inline m-0 text-dark">Aktivni projekti</h6>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="dataTable table table-hover" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th class="w-25 text-center">projekti</th>
              <!--<th class="px-1 text-center order-status" title="Status projekta">s</th>-->
              <th class="w-25 text-center">za realizaciju</th>
              <th class="w-25 text-center">u realizaciji</th>
              <th class="w-25 text-center">realizovano</th>
            </tr>
          </thead>
          <tfoot class="thead-light">
            <tr>
              <th class="w-25 text-center">projekti</th>
              <!--<th class="px-1 text-center order-status" title="Status projekta">s</th>-->
              <th class="w-25 text-center">za realizaciju</th>
              <th class="w-25 text-center">u realizaciji</th>
              <th class="w-25 text-center">realizovano</th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            foreach ($project_list as $project_item):
              if ($project_item->getStatus()->getId() == 1 || $project_item->getStatus()->getId() == 2):
                $project_id = $project_item->getId();
                $project_tasks = $entityManager->getRepository('\App\Entity\Project')->projectTasks($project_id);
                ?>
                <tr>
                  <td>
                    <a href="/project/<?php echo $project_item->getId() ?>" class="d-block card-link"
                       title='<?php echo $project_item->getCreatedAt()->format('d M Y')?>'>
                      #<?php echo str_pad($project_item->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).' - '.$project_item->getTitle() ?>
                    </a>
                    <?php
                      echo $project_item->getClient()->getName()
                        . ($project_item->getClient()->getCity()
                          ? ', <span style="font-size: 0.9em;">' .$project_item->getClient()->getCity()->getName(). '</span>'
                          : '');
                    ?>
                  </td>
                  <!-- <td class="px-1 order-status text-center">
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
                  </td>-->
                  <td>
                    <?php
                    $count1 = 0;
                    foreach ($project_tasks as $project_task):
                      if ($project_task->getStatus()->getId() == 1):
                        ?>
                        <a href="/project/<?php echo $project_id ?>/task/<?php echo $project_task->getId() ?>/edit">
                          <span class="badge badge-<?php echo $project_task->getType()->getClass() ?>">
                            <?php echo $project_task->getType()->getName() ?>
                          </span>
                          <?php echo $project_task->getTitle() ?>
                        </a><br />
                        <?php
                        $count1 ++;
                        if ($count1 == 4):
                          ?>
                          <a class="" data-toggle="collapse" href="#collapseExample1<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample1">
                            <i class="fas fa-caret-down"></i>
                          </a>
                          <div class="collapse" id="collapseExample1<?php echo $project_id?>">
                          <?php
                        endif;
                      endif;
                    endforeach;
                    if ($count1 > 3) echo '</div>';
                    ?>
                  </td>

                  <td>
                    <?php
                    $count2 = 0;
                    foreach ($project_tasks as $project_task):
                      if ($project_task->getStatus()->getId() == 2):
                        ?>
                        <a href="/project/<?php echo $project_id ?>/task/<?php echo $project_task->getId() ?>/edit">
                          <span class="badge badge-<?php echo $project_task->getType()->getClass() ?>">
                            <?php echo $project_task->getType()->getName() ?>
                          </span>
                          <?php echo $project_task->getTitle() ?>
                        </a><br />
                        <?php
                        $count2 ++;
                        if ($count2 == 4):
                          ?>
                          <a class="" data-toggle="collapse" href="#collapseExample2<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample2">
                            <i class="fas fa-caret-down"></i>
                          </a>
                          <div class="collapse" id="collapseExample2<?php echo $project_id?>">
                          <?php
                        endif;
                      endif;
                    endforeach;
                    if ($count2 > 3) echo '</div>';
                    ?>
                  </td>

                  <td>
                    <?php
                    $count3 = 0;
                    foreach ($project_tasks as $project_task):
                      if ($project_task->getStatus()->getId() == 3):
                        ?>
                        <a href="/project/<?php echo $project_id ?>/task/<?php echo $project_task->getId() ?>/edit">
                          <span class="badge badge-<?php echo $project_task->getType()->getClass() ?>">
                            <?php echo $project_task->getType()->getName() ?>
                          </span>
                          <?php echo $project_task->getTitle() ?>
                        </a><br />
                        <?php
                        $count3 ++;
                        if ($count3 == 4):
                          ?>
                          <a class="" data-toggle="collapse" href="#collapseExample3<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample3">
                            <i class="fas fa-caret-down"></i>
                          </a>
                          <div class="collapse" id="collapseExample3<?php echo $project_id?>">
                          <?php
                        endif;
                      endif;
                    endforeach;
                    if ($count3 > 3) echo '</div>';
                    ?>
                  </td>

                </tr>
                <?php
              endif;
            endforeach;
            ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- End Card Body -->
    
    <!--
    <div class="card-header p-2">
      <h6 class="d-inline m-0 text-dark">Projekti na čekanju</h6>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="dataTable table table-hover" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th class="w-25 text-center">projekti</th>
              <th class="w-25 text-center">za realizaciju</th>
              <th class="w-25 text-center">u realizaciji</th>
              <th class="w-25 text-center">realizovano</th>
            </tr>
          </thead>
          <tfoot class="thead-light">
            <tr>
              <th class="w-25 text-center">projekti</th>
              <th class="w-25 text-center">za realizaciju</th>
              <th class="w-25 text-center">u realizaciji</th>
              <th class="w-25 text-center">realizovano</th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            foreach ($project_list as $project_item):
              if ($project_item->getStatus()->getId() == 2):
                $project_id = $project_item->getId();
                $project_tasks = $entityManager->getRepository('\App\Entity\Project')->projectTasks($project_id);
                ?>
                <tr>
                  <td>
                    <a href="?view&project_id=<?php echo $project_item->getId() ?>" class="d-block card-link" title='<?php echo $project_item->getCreatedAt()->format('d M Y')?>'>
                      #<?php echo str_pad($project_item->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).' - '.$project_item->getTitle() ?>
                    </a>
                    <?php
                    echo $project_item->getClient()->getName()
                      . ($project_item->getClient()->getCity()
                        ? ', <span style="font-size: 0.9em;">' .$project_item->getClient()->getCity()->getName(). '</span>'
                        : '');
                    ?>
                  </td>
                  <td>
                    <?php
                    $count1 = 0;
                    foreach ($project_tasks as $project_task):
                      if ($project_task->getStatus()->getId() == 1):
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
                          <a class="" data-toggle="collapse" href="#collapseExample1<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample1">
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
                    foreach ($project_tasks as $project_task):
                      if ($project_task->getStatus()->getId() == 2):
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
                          <a class="" data-toggle="collapse" href="#collapseExample2<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample2">
                            <i class="fas fa-caret-down"></i>
                          </a>
                          <div class="collapse" id="collapseExample2<?php echo $project_id?>">
                          <?php
                        endif;
                      endif;
                    endforeach;
                    if ($count2 > 3) echo '</div>';
                    ?>
                  </td>

                  <td>
                    <?php
                    $count3 = 0;
                    foreach ($project_tasks as $project_task):
                      if ($project_task->getStatus()->getId() == 3):
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
                          <a class="" data-toggle="collapse" href="#collapseExample3<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample3">
                            <i class="fas fa-caret-down"></i>
                          </a>
                          <div class="collapse" id="collapseExample3<?php echo $project_id?>">
                          <?php
                        endif;
                      endif;
                    endforeach;
                    if ($count3 > 3) echo '</div>';
                    ?>
                  </td>

                </tr>
                <?php
              endif;
            endforeach;
            ?>
          </tbody>
        </table>

      </div>
    </div>
    -->
    <!-- End Card Body -->

  <div class="card-header p-2">
    <h6 class="d-inline m-0 text-dark">Arhivirani projekti</h6>
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table class="dataTable table table-hover" id="" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th class="w-25 text-center">projekti</th>
            <th class="w-25 text-center">za realizaciju</th>
            <th class="w-25 text-center">u realizaciji</th>
            <th class="w-25 text-center">realizovano</th>
          </tr>
        </thead>
        <tfoot class="thead-light">
          <tr>
            <th class="w-25 text-center">projekti</th>
            <th class="w-25 text-center">za realizaciju</th>
            <th class="w-25 text-center">u realizaciji</th>
            <th class="w-25 text-center">realizovano</th>
          </tr>
        </tfoot>
        <tbody>
          <?php
          foreach ($project_list as $project_item):
            if ($project_item->getStatus()->getId() == 3):
              $project_id = $project_item->getId();
              $project_tasks = $entityManager->getRepository('\App\Entity\Project')->projectTasks($project_id);
              ?>
              <tr>
                <td>
                  <a href="/project/<?php echo $project_item->getId() ?>" class="d-block card-link"
                     title='<?php echo $project_item->getCreatedAt()->format('d M Y')?>'>
                    #<?php echo str_pad($project_item->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).' - '.$project_item->getTitle() ?>
                  </a>
                  <?php
                  echo $project_item->getClient()->getName()
                      . ($project_item->getClient()->getCity()
                      ? ', <span style="font-size: 0.9em;">' .$project_item->getClient()->getCity()->getName(). '</span>'
                      : '');
                  ?>
                </td>

                <td>
                  <?php
                  $count1 = 0;
                  foreach ($project_tasks as $project_task):
                    if ($project_task->getStatus()->getId() == 1):
                      ?>
                      <a href="?editTask&task_id=<?php echo $project_task->getId() ?>&project_id=<?php echo $project_id; ?>">
                        <span class="badge badge-<?php echo $project_task->getType()->getClass() ?>">
                          <?php echo $project_task->getType()->getName() ?>
                        </span>
                        <?php echo $project_task->getTitle() ?>
                      </a><br />
                      <?php
                      $count1 ++;
                      if ($count1 == 4):
                        ?>
                        <a class="" data-toggle="collapse" href="#collapseExample1<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample1">
                          <i class="fas fa-caret-down"></i>
                        </a>
                        <div class="collapse" id="collapseExample1<?php echo $project_id?>">
                        <?php
                      endif;
                    endif;
                  endforeach;
                  if ($count1 > 3) echo '</div>';
                  ?>
                </td>

                <td>
                  <?php
                  $count2 = 0;
                  foreach ($project_tasks as $project_task):
                    if ($project_task->getStatus()->getId() == 2):
                      ?>
                      <a href="?editTask&task_id=<?php echo $project_task->getId() ?>&project_id=<?php echo $project_id; ?>">
                        <span class="badge badge-<?php echo $project_task->getType()->getClass() ?>">
                          <?php echo $project_task->getType()->getName() ?>
                        </span>
                        <?php echo $project_task->getTitle() ?>
                      </a><br />
                      <?php
                      $count2 ++;
                      if ($count2 == 4):
                        ?>
                        <a class="" data-toggle="collapse" href="#collapseExample2<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample2">
                          <i class="fas fa-caret-down"></i>
                        </a>
                        <div class="collapse" id="collapseExample2<?php echo $project_id?>">
                        <?php
                      endif;
                    endif;
                  endforeach;
                  if ($count2 > 3) echo '</div>';
                  ?>
                </td>

                <td>
                  <?php
                  $count3 = 0;
                  foreach ($project_tasks as $project_task):
                    if ($project_task->getStatus()->getId() == 3):
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
                        <a class="" data-toggle="collapse" href="#collapseExample3<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample3">
                          <i class="fas fa-caret-down"></i>
                        </a>
                        <div class="collapse" id="collapseExample3<?php echo $project_id?>">
                        <?php
                      endif;
                    endif;
                  endforeach;
                  if ($count3 > 3) echo '</div>';
                  ?>
                </td>
              </tr>
              <?php
            endif;
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php
endif;
