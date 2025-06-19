<?php

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
