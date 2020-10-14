<?php
  $documents_home = $pidb->getLastDocuments(10);
  foreach ($documents_home as $key=>$pidbs):
    if($key == 3):
                      
    else:

      $count = 0;
      if($key == 1){ 
          $vrsta = "predračun";
          $prefix = "P_";
          $style = 'info';
      }
      if($key == 2){
          $vrsta = "otpremnica";
          $prefix = "O_";
          $style = 'secondary';
      }
      if($key == 4){
          $vrsta = "povratnica";
          $prefix = "";
          $style = 'warning';
      }
      ?>
      <div class="card  border-<?php echo $style; ?> mb-4">
        <div class="card-header bg-<?php echo $style; ?> p-2">
          <h6 class="m-0 font-weight-bold text-white"><?php echo $vrsta;?></h6>
        </div>
        <div class="card-body p-2">
          <div class="table-responsive">
            <table class="table table-bordered table-hover" id="" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr>
                  <th>oznaka</th>
                  <th>naziv klijenta</th>
                  <th>naslov dokumenta</th>
                </tr>
              </thead>
              <tfoot class="thead-light">
                <tr>
                  <th>oznaka</th>
                  <th>naziv klijenta</th>
                  <th>naslov dokumenta</th>
                </tr>
              </tfoot>
              <tbody>
                <?php
                // prvo izlistavamo dokumente koji nisu arhivirani
                foreach ($pidbs as $pidb):
                  if ($pidb['archived'] == 0):
                    ?>
                    <tr>
                      <td>
                        <a href="?view&pidb_id=<?php echo $pidb['id']; ?>&pidb_tip_id=<?php echo $pidb['tip_id']; ?>">
                          <?php echo $prefix . str_pad($pidb['y_id'], 4, "0", STR_PAD_LEFT) . ' - ' . date('m / Y', strtotime($pidb['date'])); ?>
                        </a>
                      </td>
                      <td>
                        <?php echo $pidb['client_name']; ?>
                      </td>
                      <td><?php echo $pidb['title']; ?></td>
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
  endforeach;
  ?>
