<!-- View Procuring Data -->
<div class="card mb-4 border-secondary">
  <div class="card-header p-2">
    <h6 class="m-0 text-dark">
      Narudžbenica: 
      <strong><?php echo str_pad($order_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT). ' - ' .$order_data->getDate()->format('m')  . ' <span class="font-weight-normal">(' . $order_data->getDate()->format('d-M-Y'). ')</span>'; ?> </strong>
      - za projekat: <?php  echo ( isset($project_data['id']) ? '<a href="/projects/?view&project_id='.$project_data['id'].'">'.$project_data['pr_id'].' '.$project_data['client_name'].' - '.$project_data['title'].'</a>' : '___' ) ?>
    </h6>
  </div>
  <div class="card-body p-2">

    <dl class="row mb-0">
      <dt class="col-sm-3 col-md-2">dobavljač:</dt>
      <dd class="col-sm-9 col-md-10"><?php echo $supplier_data->getName() ?></dd>
      <dt class="col-sm-3 col-md-2">adresa:</dt>
      <dd class="col-sm-9 col-md-10"><?php echo $supplier_data->getStreet()->getName(). ' ' .$supplier_data->getHomeNumber(). ', ' .$supplier_data->getCity()->getName(). ', ' .$supplier_data->getCountry()->getName() ?></dd>

      <?php
      $supplier_contacts = $supplier_data->getContacts();
      $contactsCount = 0;
      foreach ($supplier_contacts as $supplier_contact):
        $supplier_contact_data = $entityManager->getRepository('\Roloffice\Entity\Contact')->findOneBy( array('id' =>$supplier_contact->getId()) );
        $contactsCount ++;
        if($contactsCount < 5){
          ?>
          <dt class="col-sm-3 col-md-2"><?php echo $supplier_contact_data->getType()->getName() ?>:</dt>
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
          $count = 0;
          $total_tax_base = 0;
          $total_tax_amount = 0;
          $total = 0;
          $materials_on_order = $entityManager->getRepository('\Roloffice\Entity\Order')->getMaterialsOnOrder($order_id);
          foreach ($materials_on_order as $material_on_order):
            $count++;
            ?>
            <form action="#" method="POST">
              <tr>
                <td class="px-1"><?php echo $count ;?></td>
                <td class="px-1">
                  <?php echo $material_on_order->getMaterial()->getName() ?>
                  <br />
                  kol. <input class="input-box-45" type="text" name="pieces" value="<?php echo $material_on_order->getPieces() ?>" placeholder="(kol)" disabled >
                  <?php
                  $material_on_order_properties = $entityManager->getRepository('\Roloffice\Entity\OrderMaterial')->getPropertiesOnOrderMaterial($material_on_order->getId());
                  foreach ($material_on_order_properties as $material_on_order_property):
                    echo $material_on_order_property->getProperty()->getName() . ' <input class="input-box-50" type="text" name="' .$material_on_order_property->getProperty()->getName(). '" value="' .number_format($material_on_order_property->getQuantity(), 2, ",", "."). '" placeholder="(cm)" disabled > ';
                  endforeach;
                  ?>
                  <br /><?php echo ( $material_on_order->getNote() == "" ? "" : $material_on_order->getNote() ) ?>
                </td>
                <td class="px-1 text-center"><?php echo $material_on_order->getMaterial()->getUnit()->getName()?></td>
                <td class="px-1 input-box-45">
                  <!-- količina artikla, treba da se izračunava kao proizvod property-a -->
                  <?php 
                  // TODO Dragan 
                  echo $material_on_order_quantity = $entityManager->getRepository('\Roloffice\Entity\OrderMaterial')->getQuantity($material_on_order->getId());
                  // echo number_format($material_on_order['quantity'], 2, ",", "."); 
                  ?>
                </td>
                <td class="px-1 text-center">
                  <input class="input-box-65" type="text" name="price" value="<?php echo number_format($material_on_order->getPrice(), 4, ",", "."); ?>" disabled >
                </td>
                <td class="px-1 text-center">
                  <input class="input-box-45" type="text" name="discounts" value="<?php echo number_format($material_on_order->getDiscount(), 2, ",", "."); ?>" disabled >
                </td>
                <td class="px-1 input-box-65"><?php // TODO Dragan echo number_format($material_on_order['tax_base']*$order->getKurs(), 2, ",", ".") ;?></td>
                <td class="px-1 text-center"><?php echo $material_on_order->getTax() ?></td>
                <td class="px-1 input-box-45"><?php // TODO Dragan echo number_format($material_on_order['tax_amount']*$order->getKurs(), 2, ",", "."); ?></td>
                <td class="px-1 input-box-65"><?php // TODO Dragan echo number_format($material_on_order['sub_total']*$order->getKurs(), 2, ",", ".");?></td>
                <td class="px-1 text-center">
                  <button type="submit" class="btn btn-mini btn-outline-secondary px-1 disabled" disabled>
                    <i class="fas fa-save" title="Snimi izmenu"> </i>
                  </button>
                  <a class="btn btn-mini btn-outline-secondary px-1 disable">
                    <i class="fas fa-plus" title="Dupliciraj materijal"> </i>
                  </a>
                  <a class="btn btn-mini btn-outline-secondary px-1 disable">
                    <i class="fas fa-trash" title="Obriši materijal"> </i> 
                  </a>
                </td>
              </tr>
            </form>  
            <?php
            $total_tax_base = $total_tax_base + $material_on_order['tax_base'];
            $total_tax_amount = $total_tax_amount + $material_on_order['tax_amount'];
            $total = $total_tax_base + $total_tax_amount;
          endforeach;
          ?>
          <tr class="table-secondary">
            <td colspan="3" rowspan="4"></td>
            <td colspan="3">ukupno poreska osnovica</td>
            <td><?php echo number_format($total_tax_base*$order->getKurs(), 2, ",", ".") ?></td>
            <td colspan="5"></td>
          </tr>
            <tr class="table-secondary">
            <td colspan="5">ukupno iznos PDV-a</td>
            <td><?php echo number_format($total_tax_amount*$order->getKurs(), 2, ",", ".") ?></td>
            <td colspan="3"></td>
          </tr>
          <tr class="table-secondary">
            <td colspan="5"><strong>UKUPNO ZA UPLATU</strong></td>
            <td><strong>RSD</strong></td>
            <td><strong><?php echo number_format($total*$order->getKurs(), 2, ",", ".") ?></strong></td>
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
      <form action="#" method="POST">

        <table class="table">
          <tbody>
            <tr class="table-secondary">
              <td width="110">Projekat:</td>
              <td>
                <?php echo ( isset($project_data['id']) ? '<a href="/projects/?view&project_id='.$project_data['id'].'">'.$project_data['pr_id'].' '.$project_data['client_name'].' - '.$project_data['title'].'</a>' : '___' ) ?>
              </td>
              <td></td>
            </tr>
            <tr class="table-secondary">
              <td>Naslov:</td>
              <td><?php echo $order_data->getTitle() ?></td>
              <td></td>
            </tr>
            <tr class="table-secondary">
              <td>Status:</td>
              <td>
                <?php 
                if($order_data->getStatus() == 0):
                  ?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="draft" value="0" <?php if ($order_data->getStatus() == 0) echo 'checked'; ?> disabled>
                    <label class="form-check-label" for="draft"> nacrt</label>
                  </div>
                  <?php
                endif;
                if($order_data->getStatus() == 1):
                  ?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="ordered" value="1" <?php if ($order_data->getStatus() == 1) echo 'checked'; ?> disabled>
                    <label class="form-check-label" for="ordered"> poručeno</label>
                  </div>
                  <?php
                endif;

                if($order_data->getStatus() == 2):
                  ?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="arrived" value="2" <?php if ($order_data->getStatus() == 2) echo 'checked'; ?> disabled>
                    <label class="form-check-label" for="arrived"> stiglo</label>
                  </div>
                  <?php
                endif;
                ?>
              </td>
              <td></td>
            </tr>

            <tr class="table-secondary">
                <td>Arhivirano: </td>
                <td>
                  <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="is_archived" name="is_archived" <?php echo ($order_data->getIsArchived() == 0 ? '' : 'checked') ?> disabled>
                  <label class="form-check-label" for="is_archived">jeste</label>
                  </div>
                </td>
                <td></td>
            </tr>

            <tr class="table-secondary">
              <td>Napomena:</td>
              <td colspan="2">
                <textarea class="form-control" rows="3" name="note" disabled><?php echo $order_data->getNote() ?></textarea>
              </td>
            </tr>

            <tr class="table-secondary">
              <td></td>
              <td colspan="2">
                <button type="submit" class="btn btn-sm btn-light my-1" disabled>
                  <i class="fas fa-save"></i> Snimi
                </button>
              </td>
            </tr>

          </tbody>
        </table>
      </form>
    </div>

  </div>
</div>
