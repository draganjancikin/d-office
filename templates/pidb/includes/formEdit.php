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
                if($contactsCount < 5){
                    ?>
                    <dt class="col-sm-3 col-md-2"><?php echo $contact['name']; ?>:</dt>
                    <dd class="col-sm-9 col-md-10"><?php echo $contact['number'] . ($contact['note']=="" ? "" : ", " .$contact['note']); ?></dd>
                    <?php
                }
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
                    $articles_on_pidb = $pidb->getArticlesOnPidb($pidb_id);
                    foreach ($articles_on_pidb as $article_on_pidb):
                        $propertys = $article_on_pidb['propertys'];
                        $count++;
                        ?>
                        <form action="<?php echo $_SERVER['PHP_SELF']. '?editArticleInPidb&pidb_id='.$pidb_id.'&pidb_article_id=' .$article_on_pidb['id']; ?>" class="form-horizontal" role="form" method="post">
                            <input type="hidden" name="article_id" value="<?php echo $article_on_pidb['article_id']; ?>" />
                            <input type="hidden" name="pidb_tip_id" value="<?php echo $pidb_data['tip_id']; ?>" />
                            <tr>
                                <td class="px-1 text-center"><?php echo $count ;?></td>
                                <td class="px-1">
                                    <?php echo $article_on_pidb['name'] ?>
                                    <br />
                                    kom <input class="input-box-pieces" type="text" name="pieces" value="<?php echo $article_on_pidb['pieces']; ?>" placeholder="(kom)" />
                                    <?php
                                    foreach ($propertys as $property):
                                        echo $property['property_name'] . ' <input class="input-box-55" type="text" name="' .$property['property_name']. '" value="' .number_format($property['property_quantity'], 2, ",", ""). '" title="(cm)" /> ';
                                    endforeach;
                                    ?>
                                    <br />
                                    <input class="in-article-note" type="text" name="note" value="<?php echo $article_on_pidb['note']; ?>" />
                                </td>
                                <td class="px-1 text-center"><?php echo $article_on_pidb['unit_name'] ;?></td>
                                <td class="px-1 input-box-45">
                                    <!-- količina artikla, treba da se izračunava -->
                                    <?php  echo number_format($article_on_pidb['quantity'], 2, ",", "."); ?>
                                </td>
                                <td class="px-1 text-center">
                                    <input class="input-box-price" type="text" name="price" value="<?php echo number_format($article_on_pidb['price'], 4, ",", ""); ?>" title="Nemojte koristiti tačku ili zarez za odvajanje hiljada, već samo za decimale!" />
                                </td>
                                <td class="px-1 text-center">
                                    <input class="input-box-discounts" type="text" name="discounts" value="<?php echo number_format($article_on_pidb['discounts'], 2, ",", "."); ?>" />
                                </td>
                                <td class="px-1 input-box-65"><?php echo number_format($article_on_pidb['tax_base']*$pidb->getKurs(), 2, ",", ".") ;?></td>
                                <td class="px-1 text-center"><?php echo $article_on_pidb['tax'] ;?></td>
                                <td class="px-1 input-box-45"><?php echo number_format($article_on_pidb['tax_amount']*$pidb->getKurs(), 2, ",", "."); ?></td>
                                <td class="px-1 input-box-65"><?php echo number_format($article_on_pidb['sub_total']*$pidb->getKurs(), 2, ",", ".");?></td>
                                <td class="px-1 text-center">
                                    <button type="submit" class="btn btn-mini btn-outline-success px-1">
                                        <i class="fas fa-save" title="Snimi izmenu"> </i>
                                    </button>

                                    <a href="<?php echo $_SERVER['PHP_SELF']. '?editArticle&pidb_article_id=' . $article_on_pidb['id'] . '&pidb_id='.$pidb_id.'&pidb_tip_id=' . $pidb_data['tip_id'] ?>" class="btn btn-mini btn-outline-info px-1">
                                        <i class="fas fa-edit" title="Promeni artikal"> </i>
                                    </a>

                                    <a href="<?php echo $_SERVER['PHP_SELF']. '?duplicateArticleInPidb&pidb_id='.$pidb_id.'&pidb_tip_id='.$pidb_data['tip_id'].'&pidb_article_id=' .$article_on_pidb['id']; ?>" class="btn btn-mini btn-outline-info px-1">
                                        <i class="fas fa-plus" title="Dupliciraj artikal"> </i>
                                    </a>

                                    <a onClick="javascript: return confirm('Da li ste sigurni da želite da obrišete artikal?')"  href="<?php echo $_SERVER['PHP_SELF']. '?delArticleInPidb&pidb_id='.$pidb_id.'&pidb_tip_id='.$pidb_data['tip_id'].'&pidb_article_id=' .$article_on_pidb['id']; ?>" class="btn btn-mini btn-outline-danger px-1">
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
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="3" rowspan="6">
                            <td colspan="3">ukupno poreska osnovica</td>
                            <td class="text-right"><?php echo number_format($total_tax_base*$pidb->getKurs(), 2, ",", ".") ?></td>
                        </td>
                        <td colspan="5"></td>
                    </tr>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="5">ukupno iznos PDV-a</td>
                        <td class="text-right"><?php echo number_format($total_tax_amount*$pidb->getKurs(), 2, ",", ".") ?></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="5">UKUPNO</td>
                        <td class="text-right"></td>
                        <td class="text-right""><?php echo number_format($total*$pidb->getKurs(), 2, ",", ".") ?></td>
                        <td colspan="2" class="text-right">(&#8364; <?php echo number_format($total, 4, ",", ".") ?>)</td>
                    </tr>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="5">Avans</td>
                        <td class="text-right"></td>
                        <td class="text-right""><?php echo  number_format(($avans = $pidb->getAvansIncome($pidb_id))*$article->getKurs(), 2, ",", ".") ?></td>
                        <td colspan="2" class="text-right">(&#8364; <?php echo number_format($avans, 4, ",", ".") ?>)</td>
                    </tr>
                    <?php
                    $income = $pidb->getIncome($pidb_id);
                    if ($pidb_data['tip_id'] == 2) :
                        ?>
                        <tr class="table-<?php echo $style; ?>">
                            <td colspan="5">Uplaćeno</td>
                            <td class="text-right"></td>
                            <td class="text-right""><?php echo  number_format(($income)*$article->getKurs(), 2, ",", ".") ?></td>
                            <td colspan="2" class="text-right">(&#8364; <?php echo number_format($income, 4, ",", ".") ?>)</td>
                        </tr>
                        <?php
                    endif;
                    ?>
                    <tr class="table-<?php echo $style; ?>">
                        <td colspan="5"><strong>OSTALO ZA UPLATU</strong></td>
                        <td class="text-right"></td>
                        <td class="text-right""><strong><?php echo number_format(($total-$avans)*$article->getKurs(), 2, ",", ".") ?></strong></td>
                        <td colspan="2" class="text-right">(&#8364; <?php echo number_format($total-$avans, 4, ",", ".") ?>)</td>
                    </tr>
                </tbody>
            </table>

            <form  action="<?php echo $_SERVER['PHP_SELF']. '?editPidb&pidb_id='.$pidb_id; ?>" method="post">

                <input type="hidden" name="pidb_id" value="<?php echo $pidb_id; ?>" />
                <table class="table">
                    <tr class="table-<?php echo $style; ?>">
                        <td width="110">Naslov:</td>
                        <td colspan="2">
                            <input class="form-control" type="text" name="title" value="<?php echo $pidb_data['title'] ?>" >
                        </td>
                    </tr>
                    <tr class="table-<?php echo $style; ?>">
                        <td>Klijent: </td>
                        <td colspan="2">
                            <select class="form-control" name="client_id">
                                <option value="<?php echo $client_data['id'] ?>" selected>
                                    <?php echo $client_data['name'] ?>
                                </option>
                                <?php
                                $client_list = $client->getClients();
                                foreach( $client_list as $client_item):
                                    $client_id = $client_item['id'];
                                    $client_name = $client_item['name'];
                                    ?>
                                    <option value="<?php echo $client_id?>">
                                        <?php echo $client_name ?>
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
                                <input class="form-check-input" type="radio" name="archived" id="archived1" value="0" <?php if ($pidb_data['archived'] == 0) echo 'checked="checked" '; ?> >
                                <label class="form-check-label" for="archived1"> nije</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="archived" id="archived2" value="1" <?php if ($pidb_data['archived'] == 1) echo 'checked="checked" '; ?> >
                                <label class="form-check-label" for="archived2"> jeste</label>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                    <tr class="table-<?php echo $style; ?>">
                        <td>Napomena:</td>
                        <td colspan="2">
                            <textarea class="form-control" rows="3" name="note"><?php echo $pidb_data['note']; ?></textarea>
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
