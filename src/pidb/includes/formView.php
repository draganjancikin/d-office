<?php
switch ($pidb_data['tip_id']) {
  case 1:
    $vrsta = "Predračun";
    $oznaka = "P_";
    $style = 'info';
    break;

  case 2:
    $vrsta = "Otpremnica";
    $oznaka = "O_";
    $style = 'secondary';
    break;

  case 4:
    $vrsta = "Povratnica";
    $oznaka = "POV_";
    $style = 'warning';
    break;
    
  default:
    $vrsta = "_";
    $oznaka = "_";
    $style = 'default';
    break;
}
?>
<div class="card border-<?php echo $style; ?> mb-4">
  <div class="card-header bg-<?php echo $style; ?> p-2">
    <h6 class="m-0 font-weight-bold text-white">
      <?php echo $vrsta;?> 
      <?php echo str_pad($pidb_data['y_id'], 4, "0", STR_PAD_LEFT). ' - ' .date('m', strtotime($pidb_data['date'])) . ' <span class="font-weight-normal">(' . date('d-M-Y', strtotime($pidb_data['date'])) . ')</span>'; ?>
      <span class="font-weight-normal">
        <?php echo ": " . $pidb_data['title']; ?>
      </span>
    </h6>
  </div>
  <div class="card-body p-2">
    <dl class="row mb-0">
      <dt class="col-sm-3 col-md-2">klijent:</dt>
      <dd class="col-sm-9 col-md-10">
        <a href="/clients/?view&client_id=<?php echo $client_data['id']; ?>" title="Pregled svih podataka o: <?php echo $client_data['name']; ?>">
          <?php echo $client_data['name']; ?>
        </a>
      </dd>

      <dt class="col-sm-3 col-md-2">adresa:</dt>
      <dd class="col-sm-9 col-md-10"><?php echo $client_data['street_name']. ' ' .$client_data['home_number']. ', ' .$client_data['city_name']. ', ' .$client_data['state_name']; ?></dd>

      <?php
      $contacts = $contact->getContactsById($pidb_data['client_id']);
      $contactsCount = 0;
      foreach ($contacts as $contact):
        $contactsCount ++;
        if($contactsCount < 5):
          ?>
          <dt class="col-sm-3 col-md-2"><?php echo $contact['type_name']; ?>:</dt>
          <dd class="col-sm-9 col-md-10"><?php echo $contact['number'] . ($contact['note']=="" ? "" : ", " .$contact['note']); ?></dd>
          <?php
        endif;
      endforeach;
      ?>
    </dl>
  
    <div class="table-responsive">

      <table class="table" >
        <thead>
          <tr class="table-<?php echo $style; ?>">
            <th class="px-1 text-center">#</th>
            <th class="px-1">naziv proizvoda</th>
            <th class="px-1 text-center">jed.<br />mere</th>
            <th class="px-1 text-center">kol.</th>
            <th class="px-1 text-center">cena</th>
            <th class="px-1 text-center">popust<br />%</th>
            <th class="px-1 text-center">poreska<br />osnovica</th>
            <th class="px-1 text-center">PDV<br />%</th>
            <th class="px-1 text-center">iznos PDV</th>
            <th class="px-1 text-center">ukupno</th>
            <th class="px-1 text-center"></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 0;
          $total_tax_base = 0;
          $total_tax_amount = 0;
          $total = 0;
          $articles_on_pidb = $pidb->getArticlesOnPidb($pidb_id);
          foreach ($articles_on_pidb as $article_on_pidb):
            $propertys = $article_on_pidb['propertys'];
            $count++;
            ?>
            <form action="#" class="form-horizontal" role="form" method="post">
              <tr>
                <td class="px-1 text-center"><?php echo $count ;?></td>
                <td class="px-1">
                  <?php echo $article_on_pidb['name'] ?>
                  <br />
                  kom <input class="input-box-pieces" type="text" name="pieces" value="<?php echo $article_on_pidb['pieces']; ?>" placeholder="(kom)" disabled >
                  <?php
                  foreach ($propertys as $property):
                    echo $property['property_name'] . ' <input class="input-box-55" type="text" name="' .$property['property_name']. '" value="' .number_format($property['property_quantity'], 2, ",", "."). '" title="(cm)" disabled > ';
                  endforeach;
                  ?>
                  <br /><?php echo ( $article_on_pidb['note'] == "" ? "" : $article_on_pidb['note'] ) ?>
                </td>
                <td class="px-1 text-center"><?php echo $article_on_pidb['unit_name'] ;?></td>
                <td class="px-1 input-box-45">
                  <!-- količina artikla, treba da se izračunava -->
                  <?php  echo number_format($article_on_pidb['quantity'], 2, ",", "."); ?>
                </td>
                <td class="px-1 text-center">
                  <input class="input-box-price" type="text" name="price" value="<?php echo number_format($article_on_pidb['price'], 4, ",", "."); ?>" disabled >
                </td>
                <td class="px-1 text-center">
                  <input class="input-box-discounts" type="text" name="discounts" value="<?php echo number_format($article_on_pidb['discounts'], 2, ",", "."); ?>" disabled >
                </td>
                <td class="px-1 input-box-65"><?php echo number_format($article_on_pidb['tax_base']*$pidb->getKurs(), 2, ",", ".") ;?></td>
                <td class="px-1 text-center"><?php echo $article_on_pidb['tax'] ;?></td>
                <td class="px-1 input-box-45"><?php echo number_format($article_on_pidb['tax_amount']*$pidb->getKurs(), 2, ",", "."); ?></td>
                <td class="px-1 input-box-65"><?php echo number_format($article_on_pidb['sub_total']*$pidb->getKurs(), 2, ",", ".");?></td>
                <td class="px-1 text-center">
                <button type="submit" class="btn btn-mini btn-outline-secondary px-1 disabled" disabled>
                  <i class="fas fa-save" title="Snimi izmenu"> </i> 
                </button>
                <a class="btn btn-mini btn-outline-secondary px-1 disable">
                  <i class="fas fa-edit" title="Izmeni artikal"> </i>
                </a>
                <a class="btn btn-mini btn-outline-secondary px-1 disable">
                  <i class="fas fa-plus" title="Dupliciraj artikal"> </i>
                </a>
                <a class="btn btn-mini btn-outline-secondary px-1 disable">
                  <i class="fas fa-trash" title="Obriši artikal"> </i>
                </a>
              </td>
              </tr>
            </form>
            <?php
            $total_tax_base = $total_tax_base + $article_on_pidb['tax_base'];
            $total_tax_amount = $total_tax_amount + $article_on_pidb['tax_amount'];
            $total = $total_tax_base + $total_tax_amount;
          endforeach;
          ?>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="3" rowspan="6"><td colspan="3">ukupno poreska osnovica</td><td class="text-right"><?php echo number_format($total_tax_base*$pidb->getKurs(), 2, ",", ".") ?></td><td colspan="5"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5">ukupno iznos PDV-a</td><td class="text-right"><?php echo number_format($total_tax_amount*$pidb->getKurs(), 2, ",", ".") ?></td><td colspan="3"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5">UKUPNO</td><td class="text-right"></td><td class="text-right""><?php echo number_format($total*$pidb->getKurs(), 2, ",", ".") ?></td><td colspan="2"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5">Avans</td><td class="text-right"></td><td class="text-right""><?php echo  number_format(($avans = $pidb->getAvans($pidb_id))*$article->getKurs(), 2, ",", ".") ?></td><td colspan="2"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5"><b>OSTALO ZA UPLATU</b></td><td class="text-right"><b>RSD</b></td><td class="text-right""><b><?php echo number_format(($total-$avans)*$article->getKurs(), 2, ",", ".") ?></b></td><td colspan="2"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5"></td><td class="text-right">(eur </td><td class="text-right"><?php echo number_format($total-$avans, 2, ",", ".") ?>)</td><td colspan="2"></td>
          </tr>
        </tbody>            
      </table>

      <form action="" method="post">
        <table class="table">
          <tbody>
            <tr class="table-<?php echo $style; ?>">
              <td width="110">Naslov: </td>
              <td><?php echo $pidb_data['title'] ?></td>
              <td></td>
            </tr>
            <tr class="table-<?php echo $style; ?>">
              <td>Klijent: </td>
              <td><?php echo $client_data['name'] ?></td>
              <td></td>
            </tr> 
            <tr class="table-<?php echo $style; ?>">
              <td>Arhivirano: </td>
              <td><?php echo ($pidb_data['archived'] == 0 ? "nije" : "jeste" ) ?></td>
              <td></td>
            </tr>
            <tr class="table-<?php echo $style; ?>">
              <td>Napomena:</td>
              <td colspan="2">
                <textarea class="form-control" rows="3" name="note" disabled><?php echo $pidb_data['note']; ?></textarea>
              </td>
            </tr>
            <tr class="table-<?php echo $style; ?>">
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
  <!-- End Card Body -->
</div>
<!-- End Card -->
