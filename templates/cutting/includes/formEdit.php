<!-- View Cutting Data -->
<div class="card mb-4">
  <div class="card-header p-2">
    <h6 class="m-0 font-weight-bold text-dark">
      Krojna lista: KL <?php echo str_pad($cutting_data['c_id'], 4, "0", STR_PAD_LEFT).' - '.date('m', strtotime($cutting_data['date']))  . ' <span class="font-weight-normal">(' . date('d-M-Y', strtotime($cutting_data['date'])) . ')</span>';?>
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
            <th>red.<br />broj</td>
            <th class="px-1">vrsta polja</th>
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
          $articles_on_cuttig = $cutting->getArticlesOnCutting($cutting_id);
          $fence_models = $cutting->getFenceModels();
          foreach ($articles_on_cuttig as $article_on_cuttig):
            $count ++;
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']. '?editCuttingArticle&cutting_id='. $cutting_id;?>" method="post">

              <input type="hidden" name="cutting_fence_article_id" value="<?php echo $article_on_cuttig['cutting_fence_article_id'] ?>" />
              <input type="hidden" name="cutting_fence_id" value="<?php echo $article_on_cuttig['cutting_fence_id'] ?>" />
        
              <tr>
                <td class="px-1"><?php echo $count; ?>.</td>
                <td class="px-1">
                  <select name="cutting_fence_model_id" required >
                    <option value="<?php echo $article_on_cuttig['cutting_fence_model_id'] ?>"><?php echo $article_on_cuttig['cutting_fence_model_name'] ?></option>
                    <?php
                    foreach ($fence_models as $fence_model):
                      echo '<option value="'.$fence_model['id'].'">'.$fence_model['name'].'</option>'; 
                    endforeach;
                    ?>
                  </select>
                </td>
                <td class="px-1"><input class="input-box-65" type="text" name="width" value="<?php echo $article_on_cuttig['cutting_fence_model_width'] ?>" ></td>
                <td class="px-1"><input class="input-box-65" type="text" name="height" value="<?php echo $article_on_cuttig['cutting_fence_article_height'] ?>" ></td>
                <td class="px-1"><input class="input-box-65" type="text" name="mid_height" value="<?php echo $article_on_cuttig['cutting_fence_article_mid_height'] ?>" ></td>
                <td class="px-1"><input class="input-box-45" type="text" name="space" value="<?php echo $article_on_cuttig['cutting_fence_article_space'] ?>" ></td>
                <td class="px-1"><input class="input-box-45"type="text" name="field_number" value="<?php echo $article_on_cuttig['cutting_fence_article_field_number'] ?>" ></td>
                
                <td class="px-1">
                  <button type="submit" class="btn btn-outline-secondary btn-mini">
                  <i class="fas fa-save"></i>
                  </button>  
                
                  <a href="<?php echo $_SERVER['PHP_SELF']. '?delCuttingArticle&cutting_id=' . $cutting_id .'&cutting_fence_article_id=' .$article_on_cuttig['cutting_fence_article_id']. '&cutting_fence_id=' .$article_on_cuttig['cutting_fence_id']; ?>">
                    <button class="btn btn-danger btn-mini" type="button">
                      <i class="fas fa-trash"></i>
                    </button>
                  </a>
                </td>
                  
              </tr>
            </form>
            <?php
            $total_picket_lenght = $total_picket_lenght + $article_on_cuttig['temp_picket_lenght'];
            $total_kap = $total_kap + $article_on_cuttig['temp_kap'];
          endforeach;
          ?>
          <tr>
            <td colspan="3">Ukupno letvica (m): </td>
            <td><?php echo number_format($total_picket_lenght/1000,2,".","") ?></td>
            <td colspan="3">
              <a href="<?php echo $_SERVER['PHP_SELF']. '?exportCuttingToPidb&cutting_id=' .$cutting_id. '&total_picket_lenght=' .$total_picket_lenght. '&total_kap=' .$total_kap; ?>">
                <button type="submit" class="btn btn-outline-secondary btn-sm">Otvori novi predracun</button>
              </a>
            </td>
            <td></td>
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
