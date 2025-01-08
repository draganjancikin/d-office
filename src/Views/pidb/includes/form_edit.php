<?php

?>
<div class="card border-<?php echo $style; ?> mb-4">
  <div class="card-header bg-<?php echo $style; ?> p-2">
    <h6 class="m-0 font-weight-bold text-white">
      <?php echo $vrsta;?>
      <?php echo str_pad($pidb_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . ' - '
                  . $pidb_data->getDate()->format('m')
                  . ' <span class="font-weight-normal">(' . $pidb_data->getDate()->format('d-M-Y') . ')</span>'; ?>
      <span class="font-weight-normal">
        <?php echo ": " . $pidb_data->getTitle() ?>
      </span>
    </h6>
  </div>
  <div class="card-body p-2">
    <dl class="row mb-0">
      <dt class="col-sm-3 col-md-2">klijent:</dt>
      <dd class="col-sm-9 col-md-10">
        <a href="/clients/?view&client_id=<?php echo $client['id'] ?>" title="Pregled svih podataka o: <?php echo $client['name'] ?>">
          <?php echo $client['name'] ?>
        </a>
      </dd>

      <dt class="col-sm-3 col-md-2">adresa:</dt>
      <dd class="col-sm-9 col-md-10">
        <?php echo ($client['street'] ?? "") . ' ' . $client['home_number']
          . ($client['street'] && $client['city'] ? ", " : "")
          . ($client['city'] ?? "")
          . ($client['city'] && $client['country'] ? ", " : "")
          . ($client['country'] ?? "") ?>
      </dd>
      <?php
      $contactsCount = 0;
      foreach ($client['contacts'] as $client_contact):
        $client_contact_data = $entityManager->getRepository('\App\Entity\Contact')
                                              ->findOneBy(array('id' =>$client_contact->getId()));
        $client_contact_type = $client_contact_data->getType();
        $contactsCount ++;
        if ($contactsCount < 5):
          ?>
          <dt class="col-sm-3 col-md-2"><?php echo $client_contact_type->getName() ?>:</dt>
          <dd class="col-sm-9 col-md-10">
            <?php echo $client_contact_data->getBody()
                . ($client_contact_data->getNote()=="" ? "" : ", " .$client_contact_data->getNote()) ?>
          </dd>
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
          $preferences = $entityManager->find('App\Entity\Preferences', 1);
          $kurs = $preferences->getKurs();

          $total_tax_base = 0;
          $total_tax_amount = 0;
          $total = 0;

          $ad_articles = $entityManager->getRepository('\App\Entity\AccountingDocument')->getArticles($pidb_id);
          foreach ($ad_articles as $ad_article):
            $count++;
            ?>
            <form action="<?php echo '/pidb/' . $pidb_id . '/article/' . $ad_article->getId() ?>/edit"
                  class="form-horizontal" role="form"
                  method="post">
              <input type="hidden" name="article_id" value="<?php echo $ad_article->getId() ?>" />
              <input type="hidden" name="pidb_tip_id" value="<?php echo $pidb_data->getType()->getId() ?>" />
              <tr>
                <td class="px-1 text-center"><?php echo $count ;?></td>
                <td class="px-1">
                  <?php echo $ad_article->getArticle()->getName() ?>
                  <br />
                  kom <input class="input-box-pieces" type="text" name="pieces" value="<?php echo $ad_article->getPieces() ?>" placeholder="(kom)" />
                  <?php
                  // AccountingDocument Article Properties.
                  $ad_a_properties = $entityManager->getRepository('\App\Entity\AccountingDocumentArticleProperty')
                                                      ->findBy(array('accounting_document_article' => $ad_article->getId()), array());
                  foreach ($ad_a_properties as $ad_a_property):
                    echo $ad_a_property->getProperty()->getName()
                      . ' <input class="input-box-55" type="text" name="'. $ad_a_property->getProperty()->getName()
                      . '" value="' . number_format($ad_a_property->getQuantity(), 2, ",", "")
                      . '" title="(cm)" /> ';
                  endforeach;
                  ?>
                  <br />
                  <input class="in-article-note" type="text" name="note" value="<?php echo $ad_article->getNote() ?>" />
                </td>
                <td class="px-1 text-center"><?php echo $ad_article->getArticle()->getUnit()->getName() ?></td>
                <td class="px-1 input-box-45">
                  <!-- količina artikla, treba da se izračunava -->
                  <?php
                      echo number_format($ad_a_quantity = $entityManager->getRepository('\App\Entity\AccountingDocumentArticle')->getQuantity($ad_article->getId(), $ad_article->getArticle()->getMinCalcMeasure(), $ad_article->getPieces() ), 2, ",", ".");
                  ?>
                </td>
                <td class="px-1 text-center">
                  <input class="input-box-price"
                         type="text"
                         name="price"
                         value="<?php echo number_format($ad_article->getPrice(), 4, ",", ""); ?>"
                         title="Nemojte koristiti tačku ili zarez za odvajanje hiljada, već samo za decimale!" />
                </td>
                <td class="px-1 text-center">
                  <input class="input-box-discounts"
                         type="text"
                         name="discounts"
                         value="<?php echo number_format($ad_article->getDiscount(), 2, ",", "."); ?>" />
                </td>
                <td class="px-1 input-box-65">
                  <?php
                  //echo number_format($article_on_pidb['tax_base']*$pidb->getKurs(), 2, ",", ".") ;
                  $tax_base = $entityManager->getRepository('\App\Entity\AccountingDocumentArticle')->getTaxBase($ad_article->getPrice(), $ad_article->getDiscount(), $ad_a_quantity);
                  echo number_format($tax_base * $kurs, 2, ",", ".")
                  ?>
                </td>
                <td class="px-1 text-center"><?php echo $ad_article->getTax() ;?></td>
                <td class="px-1 input-box-45">
                  <?php
                  $tax_amount = $entityManager->getRepository('\App\Entity\AccountingDocumentArticle')->getTaxAmount($tax_base, $ad_article->getTax() );
                  echo number_format($tax_amount * $kurs, 2, ",", ".");
                  ?>
                </td>
                <td class="px-1 input-box-65">
                  <?php
                  $sub_total = $entityManager->getRepository('\App\Entity\AccountingDocumentArticle')->getSubTotal($tax_base, $tax_amount );
                  echo number_format($sub_total * $kurs, 2, ",", ".");
                  ?>
                </td>
                <td class="px-1 text-center">
                  <button type="submit" class="btn btn-mini btn-outline-success px-1">
                    <i class="fas fa-save" title="Snimi izmenu"> </i>
                  </button>

                  <a href="<?php echo '/pidb/' . $pidb_id . '/article/' . $ad_article->getId() . '/change' ?>"
                  class="btn btn-mini
                  btn-outline-info px-1">
                    <i class="fas fa-edit" title="Promeni artikal"> </i>
                  </a>

                  <a href="<?php echo '/pidb/' . $pidb_id . '/article/' . $ad_article->getId() . '/duplicate' ?>"
                     class="btn btn-mini btn-outline-info px-1">
                    <i class="fas fa-plus" title="Dupliciraj artikal"> </i>
                  </a>

                  <a onClick="javascript: return confirm('Da li ste sigurni da želite da uklonite artikal iz ' +
                   'dokumenta?')"  href="<?php echo '/pidb/' . $pidb_id . '/article/' .$ad_article->getId() ?>/delete"
                     class="btn btn-mini btn-outline-danger px-1">
                    <i class="fas fa-trash" title="Obriši artikal"> </i>
                  </a>
                </td>
              </tr>
            </form>
            <?php
            $total_tax_base = $total_tax_base + $tax_base;
            $total_tax_amount = $total_tax_amount + $tax_amount;
          endforeach;
          $total = $total_tax_base + $total_tax_amount;
          $avans = $entityManager->getRepository('\App\Entity\AccountingDocument')->getAvans($pidb_id);
          $income = $entityManager->getRepository('\App\Entity\AccountingDocument')->getIncome($pidb_id);
          ?>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="3" rowspan="6">
              <td colspan="3">ukupno poreska osnovica</td>
              <td class="text-right"><?php echo number_format($total_tax_base * $kurs, 2, ",", ".") ?></td>
            </td>
            <td colspan="5"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5">ukupno iznos PDV-a</td>
            <td class="text-right"><?php echo number_format($total_tax_amount * $kurs, 2, ",", ".") ?></td>
            <td colspan="3"></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5">UKUPNO</td>
            <td class="text-right"></td>
            <td class="text-right""><?php echo number_format($total * $kurs, 2, ",", ".") ?></td>
            <td colspan="2" class="text-right">(&#8364; <?php echo number_format($total, 4, ",", ".") ?>)</td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5">Avans</td>
            <td class="text-right"></td>
            <td class="text-right""><?php echo  number_format(($avans * $kurs), 2, ",", ".") ?></td>
            <td colspan="2" class="text-right">(&#8364; <?php echo number_format($avans, 4, ",", ".") ?>)</td>
          </tr>
          <?php if ($pidb_data->getType()->getId() == 2): ?>
            <tr class="table-<?php echo $style; ?>">
              <td colspan="5">Uplaćeno</td>
              <td class="text-right"></td>
              <td class="text-right""><?php echo  number_format(($income * $kurs), 2, ",", ".") ?></td>
              <td colspan="2" class="text-right">(&#8364; <?php echo number_format($income, 4, ",", ".") ?>)</td>
            </tr>
            <?php endif; ?>
          <tr class="table-<?php echo $style; ?>">
            <td colspan="5"><strong>OSTALO ZA UPLATU</strong></td>
            <td class="text-right"></td>
            <td class="text-right""><strong><?php echo number_format(($total-$avans-$income) * $kurs, 2, ",", ".") ?></strong></td>
            <td colspan="2" class="text-right">(&#8364; <?php echo number_format($total-$avans-$income, 4, ",", ".") ?>)</td>
          </tr>
        </tbody>
      </table>


      <form action="/pidb/<?php echo $pidb_id ?>/edit " method="post">

        <input type="hidden" name="pidb_id" value="<?php echo $pidb_id; ?>" />
        <table class="table">
          <tr class="table-<?php echo $style; ?>">
            <td width="110">Naslov:</td>
            <td colspan="2">
              <input class="form-control" type="text" name="title" value="<?php echo $pidb_data->getTitle() ?>" >
            </td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td>Klijent: </td>
            <td colspan="2">
              <select class="form-control" name="client_id">
                <option value="<?php echo $client['id'] ?>" selected>
                  <?php echo $client['name'] ?>
                </option>
                <?php
                $clients_list = $entityManager->getRepository('\App\Entity\Client')->findBy(array(), array('name' => "ASC"));
                foreach ($clients_list as $client_item):
                  ?>
                  <option value="<?php echo $client_item->getId() ?>">
                    <?php echo $client_item->getName() ?>
                  </option>
                  <?php
                endforeach;
                ?>
              </select>
            </td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td>Arhivirano:</td>
            <td>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="archived" id="archived1" value="0" <?php if ($pidb_data->getIsArchived() == 0) echo 'checked="checked" '; ?> >
                <label class="form-check-label" for="archived1"> nije</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="archived" id="archived2" value="1" <?php if ($pidb_data->getIsArchived() == 1) echo 'checked="checked" '; ?> >
                <label class="form-check-label" for="archived2"> jeste</label>
              </div>
            </td>
            <td></td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td>Napomena:</td>
            <td colspan="2">
              <textarea class="form-control" rows="3" name="note"><?php echo $pidb_data->getNote() ?></textarea>
            </td>
          </tr>
          <tr class="table-<?php echo $style; ?>">
            <td></td>
            <td colspan="2">
              <button type="submit" class="btn btn-sm btn-success my-1">
                <i class="fas fa-save"></i> Snimi
              </button>
            </td>
          </tr>
        </table>

      </form>

    </div>

  </div>
  <!-- End Card Body -->
</div>
<!-- End Card -->
