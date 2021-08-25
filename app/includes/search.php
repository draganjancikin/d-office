<?php
if($page == "clients"):
  $term = filter_input(INPUT_GET, 'search');
  $clients= $entityManager->getRepository('\Roloffice\Entity\Client')->search($term);
  ?>
  <div class="card mb-4">
    <div class="card-header p-2">
      <h6 class="m-0 font-weight-bold text-primary">Pretraga klijenata</h6>
    </div>
    <div class="card-body p-2">
      <!-- Table with list of last client. -->
      <div class="table-responsive">
        <table class="dataTable table table-bordered table-hover" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th>klijent</th>
              <th>adresa</th>
            </tr>
          </thead>
          <tfoot class="thead-light">
            <tr>
              <th>klijent</th>
              <th>adresa</th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            foreach ($clients as $client_data):
              ?>
              <tr>
                <td>
                  <a href="?view&client_id=<?php echo $client_data->getId() ?>"><?php echo $client_data->getName() ?></a>
                </td>
                <td>
                <?php echo ( $client_data->getStreet()->getName() == "" ? "" : $client_data->getStreet()->getName() . " " . $client_data->getHomeNumber() .  ", " ) . $client_data->getCity()->getName(). ', ' .$client_data->getCountry()->getName() ?>                              </td>
              </tr>
              <?php
            endforeach;
            ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- End of Card body. -->
  </div>
  <?php
endif;

if($page == "pidb"):
  require '../../templates/pidb/includes/del.php';
  $term = filter_input(INPUT_GET, 'search');
  $last_pidb = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getLastAccountingDocument();
  ?>
  <div class="card border-info mb-4">

    <div class="card-header bg-info p-2">
      <h6 class="m-0 font-weight-bold text-white">Predračun</h6>
    </div>

    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-hover" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th>oznaka</th>
              <th>naziv klijenta</th>
              <th>opis dokumenta</th>
              <th></th>
            </tr>
          </thead>
          <tfoot class="thead-light">
            <tr>
              <th>oznaka</th>
              <th>naziv klijenta</th>
              <th>opis dokumenta</th>
              <th></th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            $proformas = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->search( array( 1, $term, 0 ));
            foreach ($proformas as $proforma):
              ?>
              <tr>
                <td>
                  <a href="?view&pidb_id=<?php echo $proforma->getId() ?>">
                    P_<?php echo str_pad($proforma->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' . $proforma->getDate()->format('m / Y') ?>
                  </a>
                </td>
                <td><?php echo $proforma->getClient()->getName() ?></td>
                <td><?php echo $proforma->getTitle() ?></td>
                <td>
                  <?php 
                  echo ( $proforma->getId() == $last_pidb->getId() ? '<a href="' .$_SERVER['PHP_SELF']. '?search&delPidb&pidb_id=' .$proforma->getId(). '&pidb_type_id=' . $proforma->getType()->getId().'" class="btn btn-mini btn-danger"><i class="fas fa-trash-alt"></i> </a>' : '');
                  ?>
                </td>
              </tr>
              <?php
            endforeach;
            ?> 
          </tbody>
        </table>
        <h5>Arhivirano</h5>
        <table class="table table-hover dataTable" id="" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th>oznaka</th>
              <th>naziv klijenta</th>
              <th>opis dokumenta</th>
              <th></th>
            </tr>
          </thead>
          <tfoot class="thead-light">
            <tr>
              <th>oznaka</th>
              <th>naziv klijenta</th>
              <th>opis dokumenta</th>
              <th></th>
            </tr>
          </tfoot>
          <tbody>
            <?php      
            // Archived proforma.
            $proformas = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->search( array( 1, $term, 1 ));
            foreach ($proformas as $proforma):
              ?>
              <tr class="table-secondary">
                <td>
                  <a href="?view&pidb_id=<?php echo $proforma->getId() ?>">
                    P_<?php echo str_pad($proforma->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' .$proforma->getDate()->format('m / Y') ?>
                  </a>
                </td>
                <td><?php echo $proforma->getClient()->getName() ?></td>
                <td><?php echo $proforma->getTitle() ?></td>
                <td>
                  <?php 
                  echo ( $proforma->getId() == $last_pidb->getId() ? '<a href="' .$_SERVER['PHP_SELF']. '?search&delPidb&pidb_id=' .$proforma->getId(). '" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> </a>' : '');
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
  </div>

  <div class="card border-secondary mb-4">

    <div class="card-header bg-secondary p-2">
      <h6 class="m-0 font-weight-bold text-white">Otpremnica</h6>
    </div>

        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-hover" id="" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr>
                      <th>oznaka</th>
                      <th>naziv klijenta</th>
                      <th>opis dokumenta</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tfoot class="thead-light">
                    <tr>
                      <th>oznaka</th>
                      <th>naziv klijenta</th>
                      <th>opis dokumenta</th>
                      <th></th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php
                    $proformas = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->search( array( 2, $term, 0 ));
                    foreach ($proformas as $proforma):
                      ?>
                      <tr>
                        <td>
                          <a href="?view&pidb_id=<?php echo $proforma->getId() ?>">
                            O_<?php echo str_pad($proforma->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' . $proforma->getDate()->format('m / Y') ?>
                          </a>
                        </td>
                        <td><?php echo $proforma->getClient()->getName() ?></td>
                        <td><?php echo $proforma->getTitle() ?></td>
                        <td>
                          <?php 
                          echo ( $proforma->getId() == $last_pidb->getId() ? '<a href="' .$_SERVER['PHP_SELF']. '?search&delPidb&pidb_id=' .$proforma->getId(). '&pidb_type_id=' .$proforma->getType()->getId().'" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> </a>' : '');
                          ?>
                        </td>
                      </tr>
                      <?php
                    endforeach;
                    ?>
                  </tbody>
                </table>
                <h5>Arhivirano</h5>
                <table class="table table-hover dataTable" id="" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr>
                      <th>oznaka</th>
                      <th>naziv klijenta</th>
                      <th>opis dokumenta</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tfoot class="thead-light">
                    <tr>
                      <th>oznaka</th>
                      <th>naziv klijenta</th>
                      <th>opis dokumenta</th>
                      <th></th>
                    </tr>
                  </tfoot>
                  <?php
                  // Archived dispatch.
                  $proformas = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->search( array( 2, $term, 1 ));
                  foreach ($proformas as $proforma):
                    ?>
                    <tr class="table-secondary">
                      <td>
                      <a href="?view&pidb_id=<?php echo $proforma->getId() ?>">
                        O_<?php echo str_pad($proforma->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' . $proforma->getDate()->format('m / Y') ?>
                      </a>
                      </td>
                      <td><?php echo $proforma->getClient()->getName() ?></td>
                      <td><?php echo $proforma->getTitle() ?></td>
                      <td>
                        <?php 
                        echo ( $proforma->getId() == $last_pidb->getId() ? '<a href="' .$_SERVER['PHP_SELF']. '?search&delPidb&pidb_id=' .$proforma['id']. '" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> </a>' : '');
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
    </div>

    <div class="card border-warning mb-4">

        <div class="card-header bg-warning p-2">
            <h6 class="m-0 font-weight-bold text-white">Povratnica</h6>
        </div>

        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-hover" id="" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr>
                      <th>oznaka</th>
                      <th>naziv klijenta</th>
                      <th>opis dokumenta</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tfoot class="thead-light">
                    <tr>
                      <th>oznaka</th>
                      <th>naziv klijenta</th>
                      <th>opis dokumenta</th>
                      <th></th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php
                    $proformas = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->search( array( 4, $term, 0 ));
                    foreach ($proformas as $proforma):
                      ?>
                      <tr>
                        <td>
                          <a href="?view&pidb_id=<?php echo $proforma['id']; ?>">
                            POV_<?php echo str_pad($proforma['y_id'], 4, "0", STR_PAD_LEFT) . ' - ' . date('m / Y', strtotime($proforma['date'])); ?>
                          </a>
                        </td>
                        <td><?php echo $proforma['client_name'] ; ?></td>
                        <td><?php echo $proforma['title']; ?></td>
                        <td>
                          <?php 
                          echo ( $proforma->getId() == $last_pidb->getId() ? '<a href="' .$_SERVER['PHP_SELF']. '?search&delPidb&pidb_id=' .$proforma['id']. '&pidb_tip_id=' .$proforma['tip_id'].'" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> </a>' : '');
                          ?>
                        </td>
                      </tr>
                      <?php
                    endforeach;
                        
                    // Archived.
                    $proformas = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->search( array( 4, $term, 1 ));
                    foreach ($proformas as $proforma):
                      ?>
                      <tr class="table-secondary">
                        <td>
                          <a href="?view&pidb_id=<?php echo $proforma->getId() ?>">
                              POV_<?php echo str_pad($proforma->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - ' . $proforma->getDate()->format('m / Y') ?>
                          </a>
                        </td>
                        <td><?php echo $proforma->getClient()->getName() ?></td>
                        <td><?php echo $proforma->getTitle() ?></td>
                        <td>
                          <?php 
                          echo ( $proforma->getId() == $last_pidb->getId() ? '<a href="' .$_SERVER['PHP_SELF']. '?search&delPidb&pidb_id=' .$proforma['id']. '" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> </a>' : '');
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
    </div>
    <?php
endif;

if($page == "cutting"):
    $term = filter_input(INPUT_GET, 'search');
    $cuttings = $entityManager->getRepository('\Roloffice\Entity\CuttingSheet')->search($term);
    ?>
    <div class="card mb-4">
        <div class="card-header p-2">
            <h6 class="m-0 font-weight-bold text-primary">Pretraga krojnih lista</h6>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="dataTable table table-hover" id="" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>krojna lista</th>
                            <th>klijent</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tfoot class="thead-light">
                        <tr>
                            <th>krojna lista</th>
                            <th>klijent</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        foreach ($cuttings as $cutting):
                          ?>
                          <tr>
                              <td class="centar">
                                  <a href="?view&cutting_sheet_id=<?php echo $cutting->getId() ?>">KL_<?php echo str_pad($cutting->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) ?></a>
                              </td>
                              <td><?php echo $cutting->getClient()->getName() ?></td>
                              <td>
                                  <?php
                                  $last_cutting_sheet = $entityManager->getRepository('\Roloffice\Entity\CuttingSheet')->getLastCuttingSheet();
                                  echo ( $cutting->getId() == $last_cutting_sheet->getId() ? '<a href="' .$_SERVER['PHP_SELF']. '?deleteCS&cs_id=' .$cutting->getId(). '" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"> </i> </a>' : '');
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
    </div>
    <?php
endif;

if($page == "materials"):
  $term = filter_input(INPUT_GET, 'search');
  $materials= $entityManager->getRepository('\Roloffice\Entity\Material')->search($term);
  $preferences = $entityManager->find('\Roloffice\Entity\Preferences', 1);
  ?>
  <div class="card mb-4">
    <div class="card-header p-2">
      <h6 class="m-0 font-weight-bold text-primary">Pretraga materijala</h6>
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
            foreach ($materials as $material_data):
              ?>
              <tr>
                <td>
                  <a href="?view&id=<?php echo $material_data->getId() ?>" title="<?php echo $material_data->getNote() ?>"><?php echo $material_data->getName() ?></a>
                </td>
                <td class="text-center"><?php echo $material_data->getUnit()->getName() ?></td>
                <td class="text-right">
                  <?php echo number_format( ($material_data->getPrice() * $preferences->getKurs() * ($preferences->getTax()/100 + 1) ) , 2, ",", ".") ?>
                </td>
                <td class="text-right">
                  <?php echo number_format( ($material_data->getPrice() * ($preferences->getTax()/100 + 1) ) , 2, ",", ".") ?>
                </td>
              </tr>
              <?php
            endforeach;
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php
endif;

if($page == "orders"):
    require '../../templates/order/includes/delete_order.php';
    $term = filter_input(INPUT_GET, 'search');
    $orders = $entityManager->getRepository('\Roloffice\Entity\Order')->search($term);
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
                        </tr>
                    </thead>
                    <tfoot class="thead-light">
                        <tr>
                            <th class="px-1">narudžbenica</th>
                            <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>
                            <th class="px-1">dobavljač</th>
                            <th class="px-1">naslov</th>
                            <th class="px-1">za projekat</th>
                        </tr>
                    </tfoot>
                    <tbody>
                    <?php
                    foreach ($orders as $order_data):
                        $project_data = $entityManager->getRepository('\Roloffice\Entity\Order')->getProject($order_data->getId());
                        if ($order_data->getIsArchived() == 0):
                            ?>
                            <tr>
                                <td class="px-1">
                                    <a href="?view&order_id=<?php echo $order_data->getId() ?>">
                                        <?php echo str_pad($order_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '_' . $order_data->getDate()->format('m_Y') ?>
                                    </a>
                                </td>
                                <td class="px-1 order-status text-center">
                                <?php
                                    if($order_data->getStatus() == 0):
                                    ?>
                                    <span class="badge badge-pill badge-light">N</span>
                                    <?php
                                    endif;
                                    if($order_data->getStatus() == 1):
                                    ?>
                                    <span class="badge badge-pill badge-warning">P</span>
                                    <?php
                                    endif;
                                    if($order_data->getStatus() == 2):
                                    ?>
                                    <span class="badge badge-pill badge-success">S</span>
                                    <?php
                                    endif;
                                    if($order_data->getStatus() == 3):
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
                                    if(null !== $project_data):
                                        ?>
                                        <a href="/projects/?view&project_id=<?php echo $project_data->getId() ?>">
                                            <?php echo $project_data->getOrdinalNumInYear() .' '. $project_data->getClient()->getName() .' - '. $project_data->getTitle() ?>
                                        </a>
                                        <?php 
                                    endif;
                                    ?>
                                    <?php 
                                    $last_order = $entityManager->getRepository('\Roloffice\Entity\Order')->getLastOrder();
                                    echo ( $order_data == $last_order ? '<a onClick="javascript: return confirm(\'Da li sigurno želite obrisati narudžbenicu?\')" href="' .$_SERVER['PHP_SELF']. '?name=&search&deleteOrder&order_id=' .$order_data->getId(). '" class="btn btn-danger btn-mini btn-article"><i class="fas fa-trash-alt"> </i> </a>' : ''); 
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
                    <tfoot class="thead-light">
                        <tr>
                            <th class="px-1">narudžbenica</th>
                            <th class="px-1 text-center order-status" title="Status narudžbenice">s</th>
                            <th class="px-1">dobavljač</th>
                            <th class="px-1">naslov</th>
                            <th class="px-1">za projekat</th>
                        </tr>
                    </tfoot>
                    <tbody>
                      
                    <?php
                    // zatim izlistavamo narudžbenice koje su arhivirane
                    foreach ($orders as $order_data):
                        $project_data = $entityManager->getRepository('\Roloffice\Entity\Order')->getProject($order_data->getId());
                        // $project_id = $order['project_id'];
                        // $project_data = $project -> getProject($project_id);
                        if ($order_data->getIsArchived() == 1):
                            ?>
                            <tr class="table-secondary">
                                <td class="px-1">
                                    <a href="?view&order_id=<?php echo $order_data->getId() ?>">
                                        <?php echo str_pad($order_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '_' . $order_data->getDate()->format('m_Y') ?>
                                    </a>
                                </td>
                                <td class="px-1 order-status text-center">

                                <?php
                                    if($order_data->getStatus() == 0):
                                    ?>
                                    <span class="badge badge-pill badge-light">N</span>
                                    <?php
                                    endif;
                                    if($order_data->getStatus() == 1):
                                    ?>
                                    <span class="badge badge-pill badge-warning">P</span>
                                    <?php
                                    endif;
                                    if($order_data->getStatus() == 2):
                                    ?>
                                    <span class="badge badge-pill badge-success">S</span>
                                    <?php
                                    endif;
                                    if($order_data->getIsArchived() == 1):
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
                                    if(null !== $project_data):
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

if($page == "articles"):
    $term = filter_input(INPUT_GET, 'search');
    ?>
    
    <!--  *********** Start OLD CODE **************** -->
    <form class="form-horizontal" role="form" method="get">
        <div class="form-group row">
          <div class="col-md-5">
            <select class="form-control" name="group_id">
              <option value="0">Izaberi cenovnik</option>
              <?php
              $article_groups = $entityManager->getRepository('\Roloffice\Entity\ArticleGroup')->findAll();
              foreach ($article_groups as $article_group) {
                  echo '<option value="' .$article_group->getId(). '">' .$article_group->getName(). '</option>';
              }
              ?>
            </select>
          </div>
                
            <div class="col-sm-5">
                <button type="submit" class="btn btn-mini btn-outline-secondary" name="priceList" >Prikaži cenovnik</button>
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
                    $articles = $entityManager->getRepository('\Roloffice\Entity\Article')->search($term);
                    $preferences = $entityManager->find('\Roloffice\Entity\Preferences', 1);
                    foreach ($articles as $articl):
                      ?>
                      <tr>
                        <td><a href="?view&article_id=<?php echo $articl->getId() ?>" title="<?php echo $articl->getNote() ?>"><?php echo $articl->getName() ?></a></td>
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

if($page == "projects"):
    $term = filter_input(INPUT_GET, 'search');
    $project_list = $entityManager->getRepository('\Roloffice\Entity\Project')->search($term);
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
                    foreach( $project_list as $project_item):
                        if ($project_item->getStatus()->getId() == 1 || $project_item->getStatus()->getId() == 2):
                            $project_id = $project_item->getId();
                            $project_tasks = $entityManager->getRepository('\Roloffice\Entity\Project')->projectTasks($project_id);
                            ?>
                            <tr>
                                <td>
                                <a href="?view&project_id=<?php echo $project_item->getId(); ?>" class="d-block card-link" title='<?php echo $project_item->getCreatedAt()->format('d M Y')?>'>
                                    #<?php echo str_pad($project_item->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).' - '.$project_item->getTitle() ?>
                                </a>
                                <?php echo $project_item->getClient()->getName(). ', <span style="font-size: 0.9em;">' .$project_item->getClient()->getCity()->getName(). '</span>' ?>
                                </td>
                                <!--<td class="px-1 order-status text-center">
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
                                    foreach($project_tasks as $project_task):
                                        if($project_task->getStatus()->getId() == 1):
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
                                                <a class="" data-toggle="collapse" href="#collapseExample3<?php echo $project_id?>" role="button" aria-expanded="false" aria-controls="collapseExample3">
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
                    foreach( $project_list as $project_item):
                        if ($project_item['status'] == 2):
                            $project_id = $project_item['id'];
                            $project_tasks = $project->projectTasks($project_id);
                            ?>
                            <tr>
                                <td>
                                    <a href="?view&project_id=<?php echo $project_item['id']; ?>" class="d-block card-link" title='<?php echo date('d M Y', strtotime($project_item['date']));?>'>
                                        #<?php echo str_pad($project_item['pr_id'], 4, "0", STR_PAD_LEFT).' - '.$project_item['title']; ?>
                                    </a>
                                    <?php echo $project_item['client_name']. ', <span style="font-size: 0.9em;">' .$project_item['client_city_name']. '</span>'; ?>
                                </td>

                                <td>
                                    <?php
                                    $count1 = 0;
                                    foreach($project_tasks as $project_task):
                                        if($project_task['status_id'] == 1):
                                          ?>
                                          <a href="?editTask&task_id=<?php echo $project_task['id']; ?>&project_id=<?php echo $project_id; ?>">
                                              <span class="badge badge-<?php echo $project_task['class']; ?>">
                                                  <?php echo $project_task['tip']; ?>
                                              </span>
                                            <?php echo $project_task['title']; ?>
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
                                    foreach($project_tasks as $project_task):
                                        if($project_task['status_id'] == 2):
                                            ?>
                                            <a href="?editTask&task_id=<?php echo $project_task['id']; ?>&project_id=<?php echo $project_id; ?>">
                                                <span class="badge badge-<?php echo $project_task['class']; ?>">
                                                    <?php echo $project_task['tip']; ?>
                                                </span>
                                              <?php echo $project_task['title']; ?>
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
                                    if($count2 > 3) echo '</div>';
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $count3 = 0;
                                    foreach($project_tasks as $project_task):
                                        if($project_task['status_id'] == 3):
                                            ?>
                                            <a href="?editTask&task_id=<?php echo $project_task['id']; ?>&project_id=<?php echo $project_id; ?>">
                                                <span class="badge badge-<?php echo $project_task['class']; ?>">
                                                    <?php echo $project_task['tip']; ?>
                                                </span>
                                                <?php echo $project_task['title']; ?>
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
                                    if($count3 > 3) echo '</div>';
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
                    foreach( $project_list as $project_item):
                        if ($project_item['status'] == 3):
                            $project_id = $project_item['id'];
                            $project_tasks = $project->projectTasks($project_id);
                            ?>
                            <tr>
                                <td>
                                    <a href="?view&project_id=<?php echo $project_item['id']; ?>" class="d-block card-link" title='<?php echo date('d M Y', strtotime($project_item['date']));?>'>
                                        #<?php echo str_pad($project_item['pr_id'], 4, "0", STR_PAD_LEFT).' - '.$project_item['title']; ?>
                                    </a>
                                    <?php echo $project_item['client_name']. ', <span style="font-size: 0.9em;">' .$project_item['client_city_name']. '</span>'; ?>
                                </td>

                                <td>
                                    <?php
                                    $count1 = 0;
                                    foreach($project_tasks as $project_task):
                                        if($project_task['status_id'] == 1):
                                            ?>
                                            <a href="?editTask&task_id=<?php echo $project_task['id']; ?>&project_id=<?php echo $project_id; ?>">
                                                <span class="badge badge-<?php echo $project_task['class']; ?>">
                                                    <?php echo $project_task['tip']; ?>
                                                </span>
                                                <?php echo $project_task['title']; ?>
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
                                    if($count1 > 3) echo '</div>';
                                    ?>
                                </td>

                              <td>
                                  <?php
                                  $count2 = 0;
                                  foreach($project_tasks as $project_task):
                                      if($project_task['status_id'] == 2):
                                          ?>
                                          <a href="?editTask&task_id=<?php echo $project_task['id']; ?>&project_id=<?php echo $project_id; ?>">
                                              <span class="badge badge-<?php echo $project_task['class']; ?>">
                                                  <?php echo $project_task['tip']; ?>
                                              </span>
                                              <?php echo $project_task['title']; ?>
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
                                  if($count2 > 3) echo '</div>';
                                  ?>
                              </td>

                              <td>
                                  <?php
                                  $count3 = 0;
                                  foreach($project_tasks as $project_task):
                                      if($project_task['status_id'] == 3):
                                          ?>
                                          <a href="?editTask&task_id=<?php echo $project_task['id']; ?>&project_id=<?php echo $project_id; ?>">
                                              <span class="badge badge-<?php echo $project_task['class']; ?>">
                                                  <?php echo $project_task['tip']; ?>
                                              </span>
                                              <?php echo $project_task['title']; ?>
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
                                  if($count3 > 3) echo '</div>';
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
