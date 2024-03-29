<!-- View Cutting Data -->
<div class="card mb-4">
  
    <div class="card-header p-2">
        <h6 class="m-0 font-weight-bold text-dark">
            Krojna lista: KL <?php echo str_pad($cutting_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).' - '.$cutting_data->getCreatedAt()->format('m') . ' <span class="font-weight-normal">(' . $cutting_data->getCreatedAt()->format('d-M-Y') . ')</span>';?>
        </h6>
    </div>
    <div class="card-body p-2">

        <dl class="row mb-0">
            <dt class="col-sm-3 col-md-2">klijent:</dt>
            <dd class="col-sm-9 col-md-10"><?php echo $client['name'] ?></dd>
            <dt class="col-sm-3 col-md-2">adresa:</dt>
            <dd class="col-sm-9 col-md-10">
                <?php echo $client['street'] . ' ' . $client['home_number']. ', ' . $client['city'] . ', ' . $client['country'] ?>
            </dd>
        </dl>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>red.<br />broj</th>
                        <th class="px-1">vrsta polja</th>
                        <th class="px-1">širina<br /> letvice</th>
                        <th class="px-1">širina<br />polja</th>
                        <th class="px-1">visina<br />polja</th>
                        <th class="px-1">srednja<br />visina<br />polja</th>
                        <th class="px-1">razmak<br />letvica</th>
                        <th class="px-1">broj<br />polja</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
          <?php 
          $count = 0;
          $total_picket_lenght = 0;
          $total_kap = 0;
          $cutting_sheet_articles = $entityManager->getRepository('\Roloffice\Entity\CuttingSheet')->getArticlesOnCuttingSheet($id);
          foreach ($cutting_sheet_articles as $cutting_sheet_article):
            $count ++;
            ?>
            <form >
              <fieldset disabled>
                <input type="hidden" name="cutting_fence_article_id" value="<?php echo $cutting_sheet_article->getId() ?>" />
                <input type="hidden" name="id" value="<?php echo $id ?>" />
        
                <tr>
                  <td class="px-1"><?php echo $count; ?>.</td>
                  <td class="px-1">
                    <select name="fence_model_id" required disabled>
                      <option value="<?php echo $cutting_sheet_article->getFenceModel()->getId() ?>"><?php echo $cutting_sheet_article->getFenceModel()->getName() ?></option>
                    </select>
                  </td>
                  <td class="px-1">
                    <select name="picket_width" required disabled>
                      <option value="<?php echo $cutting_sheet_article->getPicketWidth() ?>"><?php echo $cutting_sheet_article->getPicketWidth() ?></option>
                    </select>
                  </td>
                  <td class="px-1"><input class="input-box-65" type="text" name="width" value="<?php echo $cutting_sheet_article->getWidth() ?>" disabled ></td>
                  <td class="px-1"><input class="input-box-65" type="text" name="height" value="<?php echo $cutting_sheet_article->getHeight() ?>" disabled ></td>
                  <td class="px-1"><input class="input-box-65" type="text" name="mid_height" value="<?php echo $cutting_sheet_article->getMidHeight() ?>" disabled ></td>
                  <td class="px-1"><input class="input-box-45" type="text" name="space" value="<?php echo $cutting_sheet_article->getSpace() ?>" disabled ></td>
                  <td class="px-1"><input class="input-box-45"type="text" name="field_number" value="<?php echo $cutting_sheet_article->getNumberOfFields() ?>" disabled ></td>
                
                  <td class="px-1">
                    <button type="submit" class="btn btn-mini btn-outline-secondary disabled" disabled>
                      <i class="fas fa-save"></i>
                    </button>  
                    <a>
                      <button class="btn btn-mini btn-secondary disabled" type="button" disabled>
                        <i class="fas fa-trash"></i>
                      </button>
                    </a>
                  </td>
                </tr>
              </fieldset>
            </form>
            <?php
            $cutting_sheet__article__picket_number = $entityManager->getRepository('\Roloffice\Entity\CuttingSheetArticle')->getPicketsNumber($cutting_sheet_article->getId()) * $cutting_sheet_article->getNumberOfFields();
            
            $cutting_sheet__article__picket_lenght = $entityManager->getRepository('\Roloffice\Entity\CuttingSheetArticle')->getPicketsLength($cutting_sheet_article->getId()) * $cutting_sheet_article->getNumberOfFields();
            
            $total_picket_lenght = $total_picket_lenght + $cutting_sheet__article__picket_lenght;
            $total_kap = $total_kap + $cutting_sheet__article__picket_number;
          endforeach;
          ?>
          <tr>
            <td colspan="3">Ukupno letvica (m): </td>
            <td><?php echo number_format($total_picket_lenght/1000,2,".","") ?></td>
            <td colspan="5">
              <?php
              if ($cutting_sheet_articles):
                ?>
                <a href="<?php echo $_SERVER['PHP_SELF']. '?exportToAccountingDocument&id=' .$id. '&total_picket_lenght=' .$total_picket_lenght. '&total_kap=' .$total_kap. '&picket_width=' . $cutting_sheet_article->getPicketWidth() ?>">
                  <button type="submit" class="btn btn-outline-secondary btn-sm">Otvori novi predracun</button>
                </a>
                <?php
              endif;
              ?>
            </td>
            <td></td>
          </tr>
          <tr>
            <td colspan="3">Ukupno kapa za letvice (kom): </td>
            <td><?php echo $total_kap ?></td>
          </tr>

        </tbody>
      </table>
    </div>
  
  </div>
  
</div>
