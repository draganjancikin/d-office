<!-- View Procuring Data -->
<div class="card mb-4 border-secondary">
    <div class="card-header p-2">
        <h6 class="m-0 text-dark">
            Narudžbenica: 
            <strong><?php echo str_pad($order_data['o_id'], 4, "0", STR_PAD_LEFT). ' - ' .date('m', strtotime($order_data['date'])) . ' <span class="font-weight-normal">(' . date('d-M-Y', strtotime($order_data['date'])). ')</span>'; ?> </strong>
            - za projekat: <?php  echo ( isset($project_data['id']) ? '<a href="/projects/?view&project_id='.$project_data['id'].'">'.$project_data['pr_id'].' '.$project_data['client_name'].' - '.$project_data['title'].'</a>' : '___' ) ?>
        </h6>
    </div>
    <div class="card-body p-2">
  
        <dl class="row mb-0">
            <dt class="col-sm-3 col-md-2">dobavljač:</dt>
            <dd class="col-sm-9 col-md-10"><?php echo $supplier_data['name']; ?></dd>
            <dt class="col-sm-3 col-md-2">adresa:</dt>
            <dd class="col-sm-9 col-md-10"><?php echo $supplier_data['street_name']. ' ' .$supplier_data['home_number']. ', ' .$supplier_data['city_name']. ', ' .$supplier_data['state_name']; ?></dd>

            <?php
            $contacts = $contact->getContactsById($order_data['supplier_id']);
            $contactsCount = 0;
            foreach ($contacts as $contact):
                $contactsCount ++;
                if($contactsCount < 5){
                    ?>
                    <dt class="col-sm-3 col-md-2"><?php echo $contact['type_name']; ?>:</dt>
                    <dd class="col-sm-9 col-md-10"><?php echo $contact['number'] . ($contact['note']=="" ? "" : ", " .$contact['note']); ?></dd>
                    <?php
                }
            endforeach;
            ?>
        </dl>
        <div class="table-responsive">
            <table class="table" >
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
                    $materials_on_order = $order->getMaterialsOnOrder($order_id);
                    foreach ($materials_on_order as $material_on_order):

                        $propertys = $material_on_order['propertys'];
                        $count++;
                        ?>
                        <form action="#" method="POST">
                            <tr>
                                <td class="px-1"><?php echo $count ;?></td>
                                <td class="px-1">
                                    <?php echo $material_on_order['name'] ?>
                                    <br />
                                    kol. <input class="input-box-45" type="text" name="pieces" value="<?php echo $material_on_order['pieces']; ?>" placeholder="(kol)" disabled >
                                    <?php // echo ( $article_on_pidb['property'] == "" ? "" : $article_on_pidb['property']); ?>
                                    <?php
                                    foreach ($propertys as $property):
                                        echo $property['property_name'] . ' <input class="input-box-50" type="text" name="' .$property['property_name']. '" value="' .number_format($property['property_quantity'], 2, ",", "."). '" placeholder="(cm)" disabled > ';
                                    endforeach;
                                    ?>
                                    <br /><?php echo ( $material_on_order['note'] == "" ? "" : $material_on_order['note'] ) ?>
                                </td>
                                <td class="px-1 text-center"><?php echo $material_on_order['unit_name'] ;?></td>
                                <td class="px-1 input-box-45">
                                    <!-- količina artikla, treba da se izračunava -->
                                    <?php  echo number_format($material_on_order['quantity'], 2, ",", "."); ?>
                                </td>
                                <td class="px-1 text-center">
                                    <input class="input-box-65" type="text" name="price" value="<?php echo number_format($material_on_order['price'], 4, ",", "."); ?>" disabled >
                                </td>
                                <td class="px-1 text-center">
                                    <input class="input-box-45" type="text" name="discounts" value="<?php echo number_format($material_on_order['discounts'], 2, ",", "."); ?>" disabled >
                                </td>
                                <td class="px-1 input-box-65"><?php echo number_format($material_on_order['tax_base']*$conf->getKurs(), 2, ",", ".") ;?></td>
                                <td class="px-1 text-center"><?php echo $material_on_order['tax'] ;?></td>
                                <td class="px-1 input-box-45"><?php echo number_format($material_on_order['tax_amount']*$conf->getKurs(), 2, ",", "."); ?></td>
                                <td class="px-1 input-box-65"><?php echo number_format($material_on_order['sub_total']*$conf->getKurs(), 2, ",", ".");?></td>
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
                        <td><?php echo number_format($total_tax_base*$conf->getKurs(), 2, ",", ".") ?></td>
                        <td colspan="5"></td>
                    </tr>
                        <tr class="table-secondary">
                        <td colspan="5">ukupno iznos PDV-a</td>
                        <td><?php echo number_format($total_tax_amount*$conf->getKurs(), 2, ",", ".") ?></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr class="table-secondary">
                        <td colspan="5"><strong>UKUPNO ZA UPLATU</strong></td>
                        <td><strong>RSD</strong></td>
                        <td><strong><?php echo number_format($total*$conf->getKurs(), 2, ",", ".") ?></strong></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="table-secondary">
                        <td colspan="5"></td>
                        <td>(eur </td>
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
                                <?php echo ( isset($project_data['id']) ? '<a href="/project/?view&project_id='.$project_data['id'].'">'.$project_data['pr_id'].' '.$project_data['client_name'].' - '.$project_data['title'].'</a>' : '___' ) ?>
                            </td>
                            <td></td>
                        </tr>
                        <tr class="table-secondary">
                            <td>Naslov:</td>
                            <td><?php echo $order_data['title'] ?></td>
                            <td></td>
                        </tr>
                        <tr class="table-secondary">
                            <td>Status:</td>
                            <td>
                                <?php 
                                if($order_data['status'] == 0):
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="draft" value="0" <?php if ($order_data['status'] == 0) echo 'checked'; ?> disabled>
                                        <label class="form-check-label" for="draft"> nacrt</label>
                                    </div>
                                    <?php
                                endif;
                                if($order_data['status'] == 1):
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="ordered" value="1" <?php if ($order_data['status'] == 1) echo 'checked'; ?> disabled>
                                        <label class="form-check-label" for="ordered"> poručeno</label>
                                    </div>
                                    <?php
                                endif;

                                if($order_data['status'] == 2):
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="arrived" value="2" <?php if ($order_data['status'] == 2) echo 'checked'; ?> disabled>
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
                                <input class="form-check-input" type="checkbox" id="is_archived" name="is_archived" <?php echo ($order_data['is_archived'] == 0 ? '' : 'checked') ?> disabled>
                                <label class="form-check-label" for="is_archived">jeste</label>
                                </div>
                            </td>
                            <td></td>
                        </tr>

                        <tr class="table-secondary">
                            <td>Napomena:</td>
                            <td colspan="2">
                                <textarea class="form-control" rows="3" name="note" disabled><?php echo $order_data['note']; ?></textarea>
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
