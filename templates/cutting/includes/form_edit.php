<!-- View Cutting Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-dark">
      Krojna lista: KL <?php echo str_pad($cutting_data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).' - '.$cutting_data->getCreatedAt()->format('m')  . ' <span class="font-weight-normal">(' . $cutting_data->getCreatedAt()->format('d-M-Y') . ')</span>';?>
    </h6>
  </div>
  <div class="card-body p-2">
    <dl class="row mb-0">

      <dt class="col-sm-3 col-md-2">klijent:</dt>
      <dd class="col-sm-9 col-md-10"><?php echo $client_data->getName() ?></dd>

      <dt class="col-sm-3 col-md-2">adresa:</dt>
      <dd class="col-sm-9 col-md-10">
        <?php echo $client_street->getName(). ' ' .$client_data->getHomeNumber(). ', ' .$client_city->getName(). ', ' .$client_country->getName() ?>
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
          $cutting_sheet_articles = $entityManager->getRepository('\Roloffice\Entity\CuttingSheet')->getArticlesOnCuttingSheet($cutting_sheet_id);
          foreach ($cutting_sheet_articles as $cutting_sheet_article):
            $count ++;
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']. '?updateCuttingSheetArticle'?>" method="post">

              <input type="hidden" name="cutting_sheet__article_id" value="<?php echo $cutting_sheet_article->getId() ?>" />
              <input type="hidden" name="cutting_sheet_id" value="<?php echo $cutting_sheet_id ?>" />
        
              <tr>
                <td class="px-1"><?php echo $count; ?>.</td>
                <td class="px-1">
                  <select name="fence_model_id" required >
                    <option value="<?php echo $cutting_sheet_article->getFenceModel()->getId() ?>"><?php echo $cutting_sheet_article->getFenceModel()->getName() ?></option>
                    <?php
                    foreach ($fence_models as $fence_model):
                      ?>
                      <option value="<?php echo $fence_model->getId() ?>"><?php echo $fence_model->getName() ?></option>
                      <?php 
                    endforeach;
                    ?>
                  </select>
                </td>
                <td class="px-1">
                    <select name="picket_width" required>
                      <option value="<?php echo $cutting_sheet_article->getPicketWidth() ?>"><?php echo $cutting_sheet_article->getPicketWidth() ?></option>
                      <option value="35">35</option>
                      <option value="60">60</option>
                      <option value="80">80</option>
                      <option value="100">100</option>
                    </select>
                  </td>
                <td class="px-1"><input class="input-box-65" type="text" name="width" value="<?php echo $cutting_sheet_article->getWidth() ?>" ></td>
                <td class="px-1"><input class="input-box-65" type="text" name="height" value="<?php echo $cutting_sheet_article->getHeight() ?>" ></td>
                <td class="px-1"><input class="input-box-65" type="text" name="mid_height" value="<?php echo $cutting_sheet_article->getMidHeight() ?>" ></td>
                <td class="px-1"><input class="input-box-45" type="text" name="space" value="<?php echo $cutting_sheet_article->getSpace() ?>" ></td>
                <td class="px-1"><input class="input-box-45"type="text" name="number_of_fields" value="<?php echo $cutting_sheet_article->getNumberOfFields() ?>" ></td>
                
                <td class="px-1">
                  <button type="submit" class="btn btn-outline-secondary btn-mini">
                  <i class="fas fa-save"></i>
                  </button>  
                  <!-- TODO -->
                  <a href="<?php echo $_SERVER['PHP_SELF']. '?removeArticleFromCuttingSheet&cutting_sheet_id=' . $cutting_sheet_id .'&cutting_sheet__article_id=' .$cutting_sheet_article->getId() ?>">
                    <button class="btn btn-danger btn-mini" type="button">
                      <i class="fas fa-trash"></i>
                    </button>
                  </a>
                </td>
                  
              </tr>
            </form>
            <?php
            $cutting_sheet__article__picket_number = $entityManager->getRepository('\Roloffice\Entity\CuttingSheetArticle')->getCuttingSheetArticlePicketNumber($cutting_sheet_article->getId()) * $cutting_sheet_article->getNumberOfFields();

            $cutting_sheet__article__picket_lenght = $entityManager->getRepository('\Roloffice\Entity\CuttingSheetArticle')->getArticlePicketLength($cutting_sheet_article->getId()) * $cutting_sheet_article->getNumberOfFields();

            $total_kap = $total_kap + $cutting_sheet__article__picket_number;
            $total_picket_lenght = $total_picket_lenght + $cutting_sheet__article__picket_lenght;
          endforeach;
          ?>
          <tr>
            <td colspan="3">Ukupno letvica (m): </td>
            <td><?php echo number_format($total_picket_lenght/1000,2,".","") ?></td>
            <td colspan="5">
              <?php
              if ($cutting_sheet_articles):
                ?>
                <a href="<?php echo $_SERVER['PHP_SELF']. '?exportCuttingSheetToAccountingDocument&cutting_id=' .$cutting_sheet_id. '&total_picket_lenght=' .$total_picket_lenght. '&total_kap=' .$total_kap. '&picket_width=' . $cutting_sheet_article->getPicketWidth() ?>">
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
  <!-- End Card Body -->
</div>
