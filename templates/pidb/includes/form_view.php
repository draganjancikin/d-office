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
                    . ($client['country'] ?? "")
                ?>
            </dd>
            <?php
            $contactsCount = 0;
            foreach ($client['contacts'] as $client_contact):
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
                    $preferences = $entityManager->find('Roloffice\Entity\Preferences', 1);
                    $kurs = $preferences->getKurs();

                    $total_tax_base = 0;
                    $total_tax_amount = 0;
                    $total = 0;

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
                                    // AccountingDocument Article Properties.
                                    $ad_a_properties = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $ad_article->getId()), array());
                                    foreach ($ad_a_properties as $ad_a_property):
                                        echo $ad_a_property->getProperty()->getName() . ' <input class="input-box-55" type="text" name="' .$ad_a_property->getProperty()->getName(). '" value="' . number_format($ad_a_property->getQuantity(), 2, ",", "."). '" title="(cm)" disabled > ';
                                    endforeach;
                                    ?>
                                    <br />
                                    <?php echo ( $ad_article->getNote() == "" ? "" : $ad_article->getNote() ) ?>
                                </td>
                                <td class="px-1 text-center"><?php echo $ad_article->getArticle()->getUnit()->getName() ?></td>
                                <td class="px-1 input-box-45">
                                    <!-- količina artikla, treba da se izračunava kao proizvod property-a -->
                                    <?php
                                        echo number_format($ad_a_quantity = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')->getQuantity($ad_article->getId(), $ad_article->getArticle()->getMinCalcMeasure(), $ad_article->getPieces() ), 2, ",", ".");
                                    ?>
                                </td>
                                <td class="px-1 text-center">
                                    <input class="input-box-price" type="text" name="price" value="<?php echo number_format($ad_article->getPrice(), 4, ",", "."); ?>" disabled >
                                </td>
                                <td class="px-1 text-center">
                                    <input class="input-box-discounts" type="text" name="discounts" value="<?php echo number_format($ad_article->getDiscount(), 2, ",", "."); ?>" disabled >
                                </td>
                                <td class="px-1 input-box-65">
                                      <?php
                                      $tax_base = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')->getTaxBase($ad_article->getPrice(), $ad_article->getDiscount(), $ad_a_quantity);
                                      echo number_format($tax_base * $kurs, 2, ",", ".")
                                      ?>
                                </td>
                                <td class="px-1 text-center"><?php echo $ad_article->getTax() ?></td>
                                <td class="px-1 input-box-45">
                                    <?php
                                    $tax_amount = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')->getTaxAmount($tax_base, $ad_article->getTax() );
                                    echo number_format($tax_amount * $kurs, 2, ",", ".");
                                    ?>
                                </td>
                                <td class="px-1 input-box-65">
                                    <?php
                                    $sub_total = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')->getSubTotal($tax_base, $tax_amount );
                                    echo number_format($sub_total * $kurs, 2, ",", ".");
                                    ?>
                                </td>
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
                        $total_tax_base = $total_tax_base + $tax_base;
                        $total_tax_amount = $total_tax_amount + $tax_amount;
                    endforeach;
                    $total = $total_tax_base + $total_tax_amount;
                    $avans = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getAvans($pidb_id);
                    $income = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getIncome($pidb_id);
                    ?>
                </tbody>
                <tfoot>
                    <tr class="table-<?php echo $style; ?>">
                        <td rowspan="6" colspan="3"></td>
                        <td colspan="3">ukupno poreska osnovica</td>
                        <td class="text-right"><?php echo number_format($total_tax_base * $kurs, 2, ",", ".") ?></td>
                        <td colspan="4"></td>
                    </tr>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="5">ukupno iznos PDV-a</td>
                        <td class="text-right"><?php echo number_format($total_tax_amount * $kurs, 2, ",", ".") ?></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="6">UKUPNO</td>
                        <td class="text-right"><?php echo number_format($total * $kurs, 2, ",", ".") ?></td>
                        <td class="text-right">(&#8364; <?php echo number_format($total, 4, ",", ".") ?>)</td>
                    </tr>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="6">Avans</td>
                        <td class="text-right"><?php echo  number_format(($avans * $kurs), 2, ",", ".") ?></td>
                        <td class="text-right">(&#8364; <?php echo number_format($avans, 4, ",", ".") ?>)</td>
                    </tr>
                    <?php if ($pidb_data->getType()->getId() == 2): ?>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="6">Uplaćeno</td>
                        <td class="text-right""><?php echo  number_format(($income * $kurs), 2, ",", ".") ?></td>
                        <td class="text-right">(&#8364; <?php echo number_format($income, 4, ",", ".") ?>)</td>
                      </tr>
                    <?php endif; ?>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="6"><strong>OSTALO ZA UPLATU<strong></td>
                        <td class="text-right"><strong><?php echo number_format(($total-$avans-$income) * $kurs, 2, ",", ".") ?></strong></td>
                        <td class="text-right">(&#8364; <?php echo number_format($total-$avans-$income, 4, ",", ".") ?>)</td>
                    </tr>
                </tfoot>
            </table>

            <form action="" method="post">
                <table class="table">
                    <tbody>
                        <tr class="table-<?php echo $style; ?>">
                              <td width="110">Naslov: </td>
                              <td><?php echo $pidb_data->getTitle() ?></td>
                              <td></td>
                        </tr>
                        <tr class="table-<?php echo $style; ?>">
                              <td>Klijent: </td>
                              <td><?php echo $client['name'] ?></td>
                              <td></td>
                        </tr>
                        <tr class="table-<?php echo $style; ?>">
                              <td>Arhivirano: </td>
                              <td><?php echo ($pidb_data->getIsArchived() == 0 ? "nije" : "jeste" ) ?></td>
                              <td></td>
                        </tr>
                        <tr class="table-<?php echo $style; ?>">
                              <td>Napomena:</td>
                              <td colspan="2">
                                <textarea class="form-control" rows="3" name="note" disabled><?php echo $pidb_data->getNote() ?></textarea>
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
