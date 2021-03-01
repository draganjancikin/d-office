<!-- Edit Procuring Data -->
<div class="card mb-4 border-secondary">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">
      Narudžbenica:
      <strong><?php echo str_pad($order_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT). ' - ' .$order_data->getDate()->format('m')  . ' <span class="font-weight-normal">(' . $order_data->getDate()->format('d-M-Y'). ')</span>'; ?> </strong>
      - za projekat: <?php  echo ( NULL != $project_data->getId()  ? '<a href="/projects/?view&project_id='.$project_data->getId().'">'.$project_data->getOrdinalNumInYear().' '.$project_data->getClient()->getName().' - '.$project_data->getTitle().'</a>' : '___' ) ?>
    </h6>
  </div>
  <div class="card-body p-2">
    <dl class="row mb-0">
      <dt class="col-sm-3 col-md-2">dobavljać:</dt>
      <dd class="col-sm-9 col-md-10"><?php echo $supplier_data->getName() ?></dd>

      <dt class="col-sm-3 col-md-2">adresa:</dt>
      <dd class="col-sm-9 col-md-10"><?php echo $supplier_data->getStreet()->getName(). ' ' .$supplier_data->getHomeNumber(). ', ' .$supplier_data->getCity()->getName(). ', ' .$supplier_data->getCountry()->getName() ?></dd>

      <?php
      $supplier_contacts = $supplier_data->getContacts();
      $contactsCount = 0;
      foreach ($supplier_contacts as $supplier_contact):
        $supplier_contact_data = $entityManager->getRepository('\Roloffice\Entity\Contact')->findOneBy( array('id' =>$supplier_contact->getId()) );
        $supplier_contact_type = $supplier_contact_data->getType();
        $contactsCount ++;
        if($contactsCount < 5){
        ?>
        <dt class="col-sm-3 col-md-2"><?php echo $supplier_contact_type->getName() ?>:</dt>
                    <dd class="col-sm-9 col-md-10"><?php echo $supplier_contact_data->getBody() . ($supplier_contact_data->getNote() =="" ? "" : ", " .$supplier_contact_data->getNote()); ?></dd>
        <?php
         }
      endforeach;
      ?>
    </dl>
    <div class="table-responsive">
      <table class="table table-hover" >
        <thead>
          <tr class="table-secondary">
            <th class="px-1">#</th>
            <th class="px-1">naziv materijala</th>
            <th class="px-1 text-center">jed.<br />mere</th>
            <th class="px-1 text-center">količina</th>
            <th class="px-1 text-center">cena</th>
            <th class="px-1 text-center">popust<br />%</th>
            <th class="px-1 text-center">poreska<br />osnovica</th>
            <th class="px-1 text-center">PDV<br />%</th>
            <th class="px-1 text-center">iznos PDV-a</th>
            <th class="px-1 text-center">ukupno</th>
            <th class="px-1"></th>
          </tr>
        </thead>
        
        <tbody>

        <?php
        $preferences = $entityManager->find('Roloffice\Entity\Preferences', 1);
        $kurs = $preferences->getKurs();

        $count = 0;
        $total_tax_base = 0;
        $total_tax_amount = 0;
        $total = 0;
        $materials_on_order = $entityManager->getRepository('\Roloffice\Entity\Order')->getMaterialsOnOrder($order_id);
          foreach ($materials_on_order as $material_on_order):
          $count++;
          ?>
          <form action="<?php echo $_SERVER['PHP_SELF']. '?editMaterialInOrder&order_id='.$order_id.'&orderm_material_id=' .$material_on_order->getId() ?>" method="POST">
            <input type="hidden" name="material_id" value="<?php echo $material_on_order->getMaterial()->getId() ?>" />
                
            <tr>
              <td class="px-1"><?php echo $count ?></td>
              <td class="px-1">
              <?php echo $material_on_order->getMaterial()->getName() ?>
                <br />
                kol. <input class="input-box-45" type="text" name="pieces" value="<?php echo $material_on_order->getPieces() ?>" placeholder="(kom)" />
                <?php
                $material_on_order_properties = $entityManager->getRepository('\Roloffice\Entity\OrderMaterial')->getPropertiesOnOrderMaterial($material_on_order->getId());
                foreach ($material_on_order_properties as $material_on_order_property):
                  echo $material_on_order_property->getProperty()->getName() . ' <input class="input-box-50" type="text" name="' .$material_on_order_property->getProperty()->getName(). '" value="' .number_format($material_on_order_property->getQuantity(), 2, ",", "."). '" placeholder="(cm)" disabled > ';
                endforeach;
                ?>
                <br />
                <?php echo ( $material_on_order->getNote() == "" ? "" : $material_on_order->getNote() ) ?>
              </td>
              <td class="px-1 text-center">
                <?php echo $material_on_order->getMaterial()->getUnit()->getName() ?>
              </td>
              <td class="px-1 input-box-45">
                <!-- količina artikla, treba da se izračunava kao proizvod property-a -->
                <?php 
                echo number_format($material_on_order_quantity = $entityManager->getRepository('\Roloffice\Entity\OrderMaterial')->getQuantity($material_on_order->getId(), $material_on_order->getMaterial()->getMinObracMera(), $material_on_order->getPieces() ), 2, ",", ".");
                ?>
              </td>
              <td class="px-1 text-center">
                <input class="input-box-65" type="text" name="price" value="<?php echo number_format($material_on_order->getPrice(), 4, ",", ".") ?>" >
              </td>
              <td class="px-1 text-center">
                <input class="input-box-45" type="text" name="discounts" value="<?php echo number_format($material_on_order->getDiscount(), 2, ",", ".") ?>" >
              </td>
              <td class="px-1 input-box-65">
                <?php
                $tax_base = $entityManager->getRepository('\Roloffice\Entity\OrderMaterial')->getTaxBase($material_on_order->getPrice(), $material_on_order->getDiscount(), $material_on_order_quantity);
                echo number_format($tax_base * $kurs, 2, ",", ".") 
                ?>
              </td>
              <td class="px-1 text-center"><?php echo $material_on_order->getTax() ?></td>
              <td class="px-1 input-box-45">
                <?php $tax_amount = $entityManager->getRepository('\Roloffice\Entity\OrderMaterial')->getTaxAmount($tax_base, $material_on_order->getTax() );
                echo number_format($tax_amount * $kurs, 2, ",", ".");
                ?>
              </td>
              <td class="px-1 input-box-65">
                <?php
                $sub_total = $entityManager->getRepository('\Roloffice\Entity\OrderMaterial')->getSubTotal($tax_base, $tax_amount );
                echo number_format($sub_total * $kurs, 2, ",", ".");
                ?>
              </td>
              <td class="px-1 text-center">
                <button type="submit" class="btn btn-mini btn-outline-success px-1">
                  <i class="fas fa-save" title="Snimi izmenu"> </i>
                </button>
                <a href="<?php echo $_SERVER['PHP_SELF']. '?duplicateMaterialInOrder&order_id='.$order_id.'&orderm_material_id=' .$material_on_order->getMaterial()->getId() ?>" class="btn btn-mini btn-outline-info px-1">
                  <i class="fas fa-plus" title="Dupliciraj materijal"> </i>
                </a>
                <a onClick="javascript: return confirm('Da li ste sigurni da želite da obrišete materijal?');"  href="<?php echo $_SERVER['PHP_SELF']. '?delMaterialInOrder&order_id='.$order_id.'&orderm_material_id=' .$material_on_order->getMaterial()->getId() ?>" class="btn btn-mini btn-outline-danger px-1">
                  <i class="fas fa-trash-alt"> </i>
                </a>
              </td>
            </tr>
          </form>
          <?php
          $total_tax_base = $total_tax_base + $tax_base;
          $total_tax_amount = $total_tax_amount + $tax_amount;
          $total = $total_tax_base + $total_tax_amount;
        endforeach;
        ?>
        <tr class="table-secondary">
          <td colspan="3" rowspan="4"></td>
          <td colspan="3">ukupno poreska osnovica</td>
          <td><?php echo number_format($total_tax_base * $kurs, 2, ",", ".") ?></td>
          <td colspan="5"></td>
        </tr>
        <tr class="table-secondary">
          <td colspan="5">ukupno iznos PDV-a</td>
          <td><?php echo number_format($total_tax_amount * $kurs, 2, ",", ".") ?></td>
          <td colspan="3"></td>
        </tr>
        <tr class="table-secondary">
          <td colspan="5"><strong>UKUPNO ZA UPLATU</strong></td>
          <td><strong>RSD</strong></td>
          <td><strong><?php echo number_format($total * $kurs, 2, ",", ".") ?></strong></td>
          <td colspan="2"></td>
        </tr>
        <tr class="table-secondary">
          <td colspan="5"></td>
          <td>(&#8364; </td>
          <td><?php echo number_format(($total), 2, ",", ".") ?>)</td>
          <td colspan="2"></td>
        </tr>

        </tbody>
      </table>

      <form action="<?php echo $_SERVER['PHP_SELF']. '?editOrder&order_id='.$order_id; ?>" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
        <table class="table">  
          <tbody>
            <tr class="table-secondary">
              <td width="110">Projekat:</td>
              <td>
              <select class="form-control" name="project_id">
                  
                  <option value="<?php echo ( NULL != $project_data->getId() ? $project_data->getId() : '' ) ?>" selected>
                    <?php echo ( NULL != $project_data->getId() ? $project_data->getOrdinalNumInYear().' '.$project_data->getClient()->getName().' - '.$project_data->getTItle() : '___' ) ?>
                  </option>
                  <!-- List of active project. -->
                  <?php
                  // TODO Dragan: make method that get all active Project 
                  // $project_list = $project->projectTracking(1);
                  $project_list = $entityManager->getRepository('Roloffice\Entity\Project')->getAllActiveProjects();
                  foreach( $project_list as $project_item):
                    $project_id = $project_item->getId();
                    $project_pr_id = $project_item->getOrdinalNumInYear();
                    $project_client = $project_item->getClient()->getName();
                    $project_title = $project_item->getTitle();
                    ?>
                    <option value="<?php echo $project_id?>">
                      <?php echo $project_pr_id . ' ' . $project_client . ' - ' . $project_title; ?>
                    </option>
                    <?php
                  endforeach;
                  ?>
                </select>
              </td>
            </tr>
            <tr class="table-secondary">
              <td>Naslov:</td>
              <td>
                <input class="form-control" type="text" name="title" value="<?php echo $order_data->getTitle() ?>" >
              </td>
            </tr>
            <tr class="table-secondary">
              <td>Status:</td>
              <td>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status" id="draft" value="0" <?php if ($order_data->getStatus() == 0) echo 'checked'; ?> >
                  <label class="form-check-label" for="draft"> nacrt</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status" id="ordered" value="1" <?php if ($order_data->getStatus() == 1) echo 'checked'; ?> >
                  <label class="form-check-label" for="ordered"> poručeno</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status" id="arrived" value="2" <?php if ($order_data->getStatus() == 2) echo 'checked'; ?> >
                  <label class="form-check-label" for="arrived"> stiglo</label>
                </div>
              </td>
            </tr>

            <tr class="table-secondary">
              <td>Arhivirano: </td>
              <td>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="is_archived" name="is_archived" value="1" <?php echo ($order_data->getIsArchived() == 0 ? '' : 'checked') ?> >
                  <label class="form-check-label" for="is_archived">jeste</label>
                </div>
              </td>
            </tr>

            <tr class="table-secondary">
              <td>Napomena:</td>
              <td colspan="2">
                <textarea class="form-control" rows="3" name="note"><?php echo $order_data->getNote() ?></textarea>
              </td>
            </tr>  
            <tr class="table-secondary">
              <td></td>
              <td colspan="2">
                <button type="submit" class="btn btn-sm btn-success my-1">
                  <i class="fas fa-save"></i> Snimi
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
    </div>

  </div>
  <!-- End Card Body -->

</div>
