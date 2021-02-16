<?php
if($project_data['id']=='0'):
  die('<script>location.href = "/projects/" </script>');
else:
  $client_data = $entityManager->find('\Roloffice\Entity\Client', $project_data['client_id']);
  $client_country = $entityManager->find('\Roloffice\Entity\Country', $client_data->getCountry());
  $client_city = $entityManager->find('\Roloffice\Entity\City', $client_data->getCity());
  $client_street = $entityManager->find('\Roloffice\Entity\Street', $client_data->getStreet());
  ?>
  <!-- ************** START OLD CODE ***************** -->
  <!-- dugme, okidač za modal addNote -->
  <!-- <a href="#"><button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#addNote" title="Dodavanje beleške uz projekat!"><i class="fa fa-plus"></i> <i class="fa fa-pencil"> </i></button></a> -->
  <!-- Pregled i štampanje radnog naloga sa beleškama uz projekat -->
  <!-- <a href="../tcpdf/examples/printProjectTaskWithNotes.php?project_id=<?php echo $project_id; ?>" title="Izvoz radnog naloga sa beleškama u PDF [new window]" target="_blank"><button type="submit" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Beleške</button></a> -->
  <!-- ************** END OLD CODE ***************** -->
  <div class="row">

    <div class="col-sm-5 pr-0">
      <div class="card mb-4">
        <div class="card-header p-2">
          <h6 class="m-0 text-dark">
            <i class="fa fa-folder"> </i>
            # <?php echo str_pad($project_data['pr_id'], 4, "0", STR_PAD_LEFT).' - '.$project_data['title']; ?>
          </h6>
        </div>
        <div class="card-body p-2 client-data">
          <dl class="row mb-0">
            <dt class="col-sm-4 col-md-3">datum</dt>
            <dd class="col-sm-8 col-md-9"><?php echo date('d-M-Y', strtotime($project_data['date'])); ?></dd>
            <dt class="col-sm-4 col-md-3">klijent</dt>
            <dd class="col-sm-8 col-md-9"><a href="/clients/index.php?viewClient&client_id=<?php echo $client_data->getId() ?>"><?php echo $client_data->getName() ?></a></dd>
            <dt class="col-sm-4 col-md-3">adresa</dt>
            <dd class="col-sm-8 col-md-9"><?php echo $client_street->getName(). ' ' . $client_data->getHomeNumber() . ($client_city->getName()<>""?", ".$client_city->getName() : "") . ', ' . $client_country->getName() . '<br/>' . $client_data->getAddressNote() ?></dd>
            <?php
            $client_contacts = $client_data->getContacts();
            $count = 0;
            foreach ($client_contacts as $client_contact):
              if ($count < 8):
              $client_contact_data = $entityManager->getRepository('\Roloffice\Entity\Contact')->findOneBy( array('id' =>$client_contact->getId()) );
              $client_contact_type = $client_contact_data->getType();
              ?>
              <dt class="col-sm-4 col-md-3"><?php echo $client_contact_type->getName() ?></dt>
              <dd class="col-sm-8 col-md-9"><?php echo $client_contact_data->getBody() . ($client_contact_data->getNote() =="" ? "" : ", " .$client_contact_data->getNote()); ?></dd>
              <?php
              $count++;
              endif;
            endforeach;
            ?>
          </dl>
        </div>
        <!-- End Card Body -->
      </div>
      <!-- End Card -->
    </div>

    <div class="col-sm-7">

      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="note-tab" data-toggle="tab" href="#note" role="tab" aria-controls="note" aria-selected="true">
            <i class="fas fa-pencil-alt"> </i> Beleške
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="document-tab" data-toggle="tab" href="#document" role="tab" aria-controls="document" aria-selected="false">
            Dokumenti
          </a>
        </li>
      </ul>
        
      <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active" id="note" role="tabpanel" aria-labelledby="note-tab">
          <div class="card mb-4">
            <div class="card-header p-2">
              <div class="card-header-menu">
                <!-- dugme, okidač za modal addNote -->
                <button type="button" class="btn btn-sm btn-outline-secondary mr-1" data-toggle="modal" data-target="#addNote" title="Dodavanje beleške uz projekat!">
                  <i class="fa fa-plus"></i> <i class="fas fa-pencil-alt"> </i>
                </button>
                <!-- Pregled i štampanje radnog naloga sa beleškama uz projekat -->
                <a href="/tcpdf/examples/printProjectTaskWithNotes.php?project_id=<?php echo $project_id; ?>" title="Izvoz radnog naloga sa beleškama u PDF [new window]" target="_blank">
                  <button type="button" class="btn btn-sm btn-outline-secondary mr-1">
                    <i class="fa fa-print"></i> Beleške
                  </button>
                </a>
              </div>

            </div>
            <div class="card-body p-2 tasks-note-scroll">
              <table class="table table-hover">
                <?php
                $date_temp = "";
                $notes = $project->getNotesByProject($project_id);
                foreach ($notes as $note):
                  ?>
                  <tr>
                    <td class="px-1 width-95">
                      <?php 
                      if(date('d-M-Y', strtotime($note['date'])) != $date_temp) {
                        echo date('d-M-Y', strtotime($note['date']));
                      }
                      ?>
                    </td>
                    <td class="px-1" <?php echo ($userlevel==1 ? 'title=" ' . $note['user_name'] . '"' : '' ); ?>>
                      <?php echo nl2br($note['note']); ?>
                    </td>
                    <td class="px-1">
                      <a onClick="javascript: return confirm('Da li ste sigurni da želite da obrišete belešku?');" href="?view&project_id=<?php echo $project_id; ?>&note_id=<?php echo $note['id']; ?>&delNote" title="Brisanje beleške">
                        <i class="fas fa-trash-alt"></i>
                      </a>
                    </td>
                  </tr>
                  <?php
                  $date_temp = date('d-M-Y', strtotime($note['date']));
                endforeach;
                ?>
              </table>
            </div>
            <!-- End Card Body -->
          </div>
          <!-- End Card -->
        </div>

        <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
          <div class="card mb-4">
            <div class="card-body p-2 tasks-note-scroll">
              <?php
              if ($order->getOrdersByProjectId($project_id)):
                echo "<h5>Narudžbenice:</h5>";
                $orders = $order->getOrdersByProjectId($project_id);
                foreach ($orders as $order):
                    ?>
                    <a href="/orders/?view&order_id=<?php echo $order['id'] ?>">
                        <?php echo str_pad($order['o_id'], 4, "0", STR_PAD_LEFT) . '_' . date('m_Y', strtotime($order['date'])) ?>
                    </a>
                    <span style="display: inline-block; width: 50px; text-align: center;">
                      <?php
                      switch ($order['status']) {
                        case 0:
                          echo '<span class="badge badge-pill badge-light">N</span>';
                          break;
                        case 1:
                          echo '<span class="badge badge-pill badge-warning">P</span>';
                          break;
                        case 2:
                          echo '<span class="badge badge-pill badge-success">S</span>';
                          break;
                        
                        default:
                          # code...
                          break;
                      }
                      if($order['is_archived'] == 1):
                          ?>
                          <span class="badge badge-pill badge-secondary">A</span>
                          <?php
                      endif;
                      ?>
                    </span>
                    <?php echo $order['supplier_name'] ?>
                    - <?php echo $order['title'] ?><br>
                    <?php
                endforeach;
              endif;

              if ($pidb->getPidbsByProjectId($project_id)):
                echo "<br><h5>Dokumenti:</h5>";
                $pidbs = $pidb->getPidbsByProjectId($project_id);
                foreach ($pidbs as $pidb):
                  ?>
                  <a href="/pidb/?view&pidb_id=<?php echo $pidb['id'] ?>">
                    <?php if($pidb['tip_id'] == 1) { echo "P";} ?>
                    <?php if($pidb['tip_id'] == 2) { echo "O";} ?>
                    <?php echo str_pad($pidb['y_id'], 4, "0", STR_PAD_LEFT) . '_' . date('m_Y', strtotime($pidb['date'])) ?>
                  </a>
                  <?php echo $pidb['client_name'] ?>
                  - <?php echo $pidb['title'] ?><br>
                  <?php
                endforeach;
              endif;
              ?>
            </div>
          </div> 
        </div>
      </div>

    </div>
  
  </div>

    <div class="row">

        <div class="col-sm-12">
            <div class="card mb-4">
                <div class="card-header p-2">
                    <h6 class="d-inline m-0 text-dark">
                        <i class="fas fa-tasks"></i> Zadaci
                    </h6>
                    <div class="card-header-menu">
                        <!-- dugme, okidač za modal addTask -->
                        <button type="button" class="btn btn-sm btn-outline-secondary mx-1" data-toggle="modal" data-target="#addTask" title="Dodavanje novog zadatka!">
                          <i class="fa fa-plus"></i> <i class="fa fa-tasks"> </i>
                        </button>

                        <!-- card body collapse link -->
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseCard" aria-expanded="true" aria-controls="collapseOne">
                          <i class="fas fa-angle-down"></i>
                        </button>
                    </div>
                </div>
                <div class="collapse show" id="collapseCard">
                    <div class="card-body p-2">

                        <div class="row row-cols-1 row-cols-md-3">
                            <?php
                            $count = 0;
                            for($i=1; $i<4; $i++):
                                if($i==1) $naslov = "ZA REALIZACIJU";
                                if($i==2) $naslov = "U REALIZACIJI";
                                if($i==3) $naslov = "REALIZOVANI";
                                ?>
                                <div class="col-sm-4">
                                    <p class="text-center"><strong><?php echo $naslov ?></strong></p>
                                    <?php
                                    $project_tasks = $project->projectTasks($project_id);
                                    foreach($project_tasks as $project_task):
                                        $count ++;
                                        if($project_task['status_id'] == $i):
                                            ?>
                                            <div class="card border-<?php echo $project_task['class']; ?> mb-2">
                                              <div class="card-header bg-<?php echo $project_task['class']; ?> p-2">
                                                <h6 class="d-inline m-0" <?php echo 'title="' . ($userlevel==1 ? $project_task['user_name'] : '') . ' " ' ?> >
                                                  <?php echo $project_task['tip']. ': '.$project_task['title'] ; ?>
                                                </h6>
                                                <div class="float-right">
                                                  <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?php echo $count ?>" aria-expanded="true" aria-controls="collapseOne">
                                                    <i class="fas fa-angle-down"></i>
                                                  </button>
                                                </div>
                                              </div>

                                              <div id="collapse<?php echo $count ?>" class="collapse" >
                                                <div class="card-body p-2">
                                                  <table>
                                                    <tr>
                                                      <td>start: </td>
                                                      <td><?php echo ($project_task['start'] == '0000-01-01 00:00:00' ? '__________' : date('d-M-Y', strtotime($project_task['start'])) ); ?></td>
                                                    </tr>
                                                    <tr>
                                                      <td>izvršilac: </td>
                                                      <td><?php echo ( !empty($project_task['employee_name']) ? $project_task['employee_name'] : '__________' ) ?></td>
                                                    </tr>
                                                    <tr>
                                                      <td>end: </td>
                                                      <td><?php echo ($project_task['end'] == '0000-01-01 00:00:00' ? '__________' : date('d-M-Y', strtotime($project_task['end'])) ); ?></td>
                                                    </tr>
                                                    <tr>
                                                      <td>beleške: </td>
                                                      <td></td>
                                                    </tr>
                                                    <?php
                                                    $date_temp = "";
                                                    $task_notes = $project->getTaskNotesByProject($project_task['id']);
                                                    foreach ($task_notes as $task_note):
                                                      ?>
                                                      <tr>
                                                        <td>
                                                          <span>
                                                            <?php
                                                            if(date('d-M-Y', strtotime($task_note['date'])) == $date_temp) {
                                                                
                                                            }else{
                                                              echo date('d-M-Y', strtotime($task_note['date']));
                                                            }
                                                            ?>
                                                          </span>
                                                        </td>
                                                        <td>
                                                          <span class="direct-chat-name">
                                                            <?php echo '- ' .$task_note['note']; ?>
                                                          </span>
                                                          <?php echo ($userlevel==1 ? '<small class="label bg-gray">' .$task_note['user_name']. '</small>' : '' ); ?>
                                                        </td>
                                                      </tr>
                                                      <?php
                                                      $date_temp = date('d-M-Y', strtotime($task_note['date']));
                                                    endforeach;
                                                    ?>
                                                  </table>
                                                </div>
                                                <!--  End Card Body -->

                                                <div class="card-footer p-2">
                                                  <!-- dugme koje briše zadatka -->
                                                  <a onClick="javascript: return confirm('Da li ste sigurni da želite da obrišete zadatak?');" href="?view&project_id=<?php echo $project_id; ?>&task_id=<?php echo $project_task['id']; ?>&delTask">
                                                    <button type="button" class="btn btn-default btn-xs btn pull-left" title="Obriši zadatak!">
                                                      <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                  </a>
                                                  <!-- dugme koje otvara formu za izmenu zadatka -->
                                                  <a href="?editTask&task_id=<?php echo $project_task['id'] ?>&project_id=<?php echo $project_id; ?>">
                                                    <button type="button" class="btn btn-default btn-xs pull-right" title="Idi na izmenu zadatka!">
                                                      <i class="fa fa-edit"></i>
                                                    </button>
                                                  </a>
                                                </div>
                                                <!-- End Card Footer -->

                                              </div>
                                              <!-- /#collapseOne -->
                        
                                            </div>
                                            <!-- End Card -->
                                            <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                                <?php
                            endfor;
                            ?>
                        </div>
                        <!-- /.row row-cols-1 row-cols-md-3 -->
          
                    </div>
                    <!-- End Card Body -->
                </div>
                
            </div>
            <!-- End Card -->
        </div>

        <!-- Files -->
        <div class="col-sm-12">

          <div class="card mb-4">
            <div class="card-header p-2">
              <h6 class="d-inline m-0 text-dark">
                <i class="fas fa-file"> </i> Fajlovi
              </h6>
            </div>
            <div class="card-body p-2">
              <div class="row row-cols-4">
                <?php
                $dir = $_SERVER["DOCUMENT_ROOT"] . '/projects/upload/project_id_'.$project_id;
                if(is_dir($dir)) :
                  if ($handle = opendir($dir)) :
                    while (false !== ($entry = readdir($handle))) :
                      if ($entry != "." && $entry != ".." && $entry != "Thumbs.db") :
                        ?>
                        <div class="col mb-4">
                          <div class="card">
                            <a href="/projects/upload/project_id_<?php echo $project_id.'/'.$entry; ?>" target="_blank" class="p-1">
                              <img src="/projects/upload/project_id_<?php echo $project_id. '/'.$entry; ?>" alt="Attachment" class="card-img-top">
                              <i class="fas fa-camera"></i> <?php echo $entry; ?>
                            </a>
                          </div>
                        </div>  
                        <?php
                      endif;
                    endwhile;
                    closedir($handle);
                  endif;
                endif;
                ?>
              </div>
            </div>
            <!-- End Card Body -->
          </div>
          <!-- End Card -->
        </div>
        <!-- /.col-sm-12 -->

    </div>
    <!-- /.row -->
    <?php
endif;
?>
