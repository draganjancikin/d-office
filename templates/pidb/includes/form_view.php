<?php
switch ($pidb_data->getType()->getId()) {
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
      <?php echo str_pad($pidb_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT). ' - ' .$pidb_data->getCreatedAt()->format('m') . ' <span class="font-weight-normal">(' . $pidb_data->getCreatedAt()->format('d-M-Y') . ')</span>'; ?>
      <span class="font-weight-normal">
        <?php echo ": " . $pidb_data->getTitle() ?>
      </span>
    </h6>
  </div>
  <div class="card-body p-2">
    <dl class="row mb-0">
      <dt class="col-sm-3 col-md-2">klijent:</dt>
      <dd class="col-sm-9 col-md-10">
        <a href="/clients/?viewClient&client_id=<?php echo $client_data->getId() ?>" title="Pregled svih podataka o: <?php echo $client_data->getName() ?>">
          <?php echo $client_data->getName() ?>
        </a>
      </dd>

      <dt class="col-sm-3 col-md-2">adresa:</dt>
      <dd class="col-sm-9 col-md-10"><?php echo $client_street->getName(). ' ' .$client_data->getHomeNumber(). ', ' .$client_city->getName(). ', ' .$client_country->getName() ?></dd>

      <?php
      $client_contacts = $client_data->getContacts();
      $contactsCount = 0;
      foreach ($client_contacts as $client_contact):
        $client_contact_data = $entityManager->getRepository('\Roloffice\Entity\Contact')->findOneBy( array('id' =>$client_contact->getId()) );
        $client_contact_type = $client_contact_data->getType();
        $contactsCount ++;
        if($contactsCount < 5):
          ?>
          <dt class="col-sm-3 col-md-2"><?php echo $client_contact_type->getName() ?>:</dt>
          <dd class="col-sm-9 col-md-10"><?php echo $client_contact_data->getBody() . ($client_contact_data->getNote()=="" ? "" : ", " .$client_contact_data->getNote()) ?></dd>
          <?php
        endif;
      endforeach;
      ?>
    </dl>

    <div class="table-responsive">

      <table class="table table-hover" >
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
            <th class="px-1 text-center" id="pidb-tools"></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 0;
          // TODO: Dragan
          $ad_articles = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getArticles($pidb_id);
          foreach ($ad_articles as $ad_article):
            $count++;
            ?>
            <form action="#" class="form-horizontal" role="form" method="post">
              <tr>
                <td class="px-1 text-center"><?php echo $count ;?></td>
                <td class="px-1">
                  <?php echo $ad_article->getArticle()->getName() ?>
                  <br />
                  kom <input class="input-box-pieces" type="text" name="pieces" value="<?php echo $ad_article->getPieces() ?>" placeholder="(kom)" disabled >
                  <?php
                  // TODO: Dragan
                  $propertys = $ad_article['propertys'];
                  
                  foreach ($propertys as $property):
                    echo $property['property_name'] . ' <input class="input-box-55" type="text" name="' .$property['property_name']. '" value="' .number_format($property['property_quantity'], 2, ",", "."). '" title="(cm)" disabled > ';
                  endforeach;
                  ?>
                  <br />
                  <?php echo ( $ad_article['note'] == "" ? "" : $ad_article['note'] ) ?>
                </td>
                <td class="px-1 text-center"><?php echo $ad_article['unit_name'] ;?></td>
                <td class="px-1 input-box-45">
                  <!-- količina artikla, treba da se izračunava -->
                  <?php  echo number_format($ad_article['quantity'], 2, ",", "."); ?>
                </td>
                <td class="px-1 text-center">
                  <input class="input-box-price" type="text" name="price" value="<?php echo number_format($ad_article['price'], 4, ",", "."); ?>" disabled >
                </td>
                <td class="px-1 text-center">
                  <input class="input-box-discounts" type="text" name="discounts" value="<?php echo number_format($ad_article['discounts'], 2, ",", "."); ?>" disabled >
                </td>
                <td class="px-1 input-box-65"><?php echo number_format($ad_article['tax_base']*$pidb->getKurs(), 2, ",", ".") ;?></td>
                <td class="px-1 text-center"><?php echo $ad_article['tax'] ;?></td>
                <td class="px-1 input-box-45"><?php echo number_format($ad_article['tax_amount']*$pidb->getKurs(), 2, ",", "."); ?></td>
                <td class="px-1 input-box-65"><?php echo number_format($ad_article['sub_total']*$pidb->getKurs(), 2, ",", ".");?></td>
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
          endforeach;
          $total_tax_base = $pidb->getTotalAmountsByPidbId($pidb_id)['tax_base'];
          $total_tax_amount = $pidb->getTotalAmountsByPidbId($pidb_id)['tax_amount'];
          $total = $pidb->getTotalAmountsByPidbId($pidb_id)['total'];
            ?>
        </tbody>
        <tfoot>
          <tr class="table-<?php echo $style; ?>">
            <td rowspan="6" colspan="3"></td>
            <td colspan="3">ukupno poreska osnovica</td>
            <td class="text-right"><?php echo number_format($total_tax_base*$pidb->getKurs(), 2, ",", ".") ?></td>
            <td colspan="4"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5">ukupno iznos PDV-a</td>
            <td class="text-right"><?php echo number_format($total_tax_amount*$pidb->getKurs(), 2, ",", ".") ?></td>
            <td colspan="2"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="6">UKUPNO</td>
            <td class="text-right"><?php echo number_format($total*$pidb->getKurs(), 2, ",", ".") ?></td>
            <td class="text-right">(&#8364; <?php echo number_format($total, 4, ",", ".") ?>)</td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="6">Avans</td>
            <td class="text-right"><?php echo  number_format(($avans = $pidb->getAvansIncome($pidb_id))*$article->getKurs(), 2, ",", ".") ?></td>
            <td class="text-right">(&#8364; <?php echo number_format($avans, 4, ",", ".") ?>)</td>
          </tr>
          <?php
          $income = $pidb->getIncome($pidb_id);
          if ($pidb_data['tip_id'] == 2) :
            ?>
            <tr class="table-<?php echo $style; ?>">
              <td colspan="6">Uplaćeno</td>
              <td class="text-right""><?php echo  number_format(($income)*$article->getKurs(), 2, ",", ".") ?></td>
              <td class="text-right">(&#8364; <?php echo number_format($income, 4, ",", ".") ?>)</td>
            </tr>
            <?php
          endif;
          ?>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="6"><strong>OSTALO ZA UPLATU<strong></td>
            <td class="text-right"><strong><?php echo number_format(($total-$avans-$income)*$article->getKurs(), 2, ",", ".") ?></strong></td>
            <td class="text-right">(&#8364; <?php echo number_format($total-$avans-$income, 4, ",", ".") ?>)</td>
          </tr>
        </tfoot>
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
              <td><?php echo $client_data->getName() ?></td>
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
