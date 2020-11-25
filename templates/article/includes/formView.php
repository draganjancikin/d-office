<!-- View Article Data -->
<div class="card mb-4">
    <div class="card-header p-2">
        <h6 class="m-0 text-dark">Pregled artikla: <strong><?php echo $article_data['name'] ?></strong></h6>
    </div>
    <div class="card-body p-2">
        <form>
            <fieldset disabled>

                <div class="form-group row">
                    <label for="disabledSelectGroup" class="col-sm-3 col-lg-2 col-form-label text-right">Grupa proizvoda:</label>
                    <div class="col-sm-3">
                        <select id="disabledSelectGroup" name="group_id" class="form-control">
                            <?php
                            if($article_group = $article->getArticleGroupById($article_data['group_id'])) :
                                ?>
                                <option value="<?php echo $article_group['id'] ?>"><?php echo $article_group['name'] ?></option>
                                <?php
                            endif;
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
                    <div class="col-sm-8">
                        <input class="form-control" id="disabledInputName" type="text" name="name" value="<?php echo $article_data['name'] ?>" maxlength="96">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledSelectUnit" class="col-sm-3 col-lg-2 col-form-label text-right">Jedinica mere:</label>
                    <div class="col-sm-3">
                        <select id="disabledSelectUnit" name="unit_id" class="form-control">
                        <option value="<?php echo $article_data['unit_id'] ?>"><?php echo $article_data['unit_name'] ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="disabledInputWeight" class="col-sm-3 col-lg-2 col-form-label text-right">Težina:</label>
                    <div class="col-sm-2">
                        <input class="form-control" id="disabledInputWeight" type="text" name="weight" value="<?php echo $article_data['weight'] ?>" >
                    </div>
                    <div class="col-sm-2">g</div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-lg-2 col-form-label text-right" for="disabledInputMinMera">Min obrač. mera: </label>
                    <div class="col-sm-2">
                        <input class="form-control" id="disabledInputMinMera" type="text" name="min_obrac_mera" value="<?php echo $article_data['min_obrac_mera'] ?>" >
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-lg-2 col-form-label text-right" for="disabledInputPrice">Cena: </label>
                    <div class="col-sm-2">
                        <input class="form-control" id="disabledInputPrice" type="text" name="price" value="<?php echo $article_data['price'] ?>">
                    </div>
                    <div class="col-sm-2">eur</div>
                </div>

                <div class="form-group row">
                    <label for="inputNote" class="col-sm-3 col-lg-2 col-form-label text-right">Beleška:</label>
                    <div class="col-md-8">
                        <textarea id="inputNote" class="form-control" rows="2" name="note"><?php echo $article_data['note'] ?></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-3 offset-sm-3 offset-lg-2">
                        <button type="submit" class="btn btn-sm btn-secondary">
                            <i class="fas fa-save"></i> Snimi
                        </button>
                    </div>
                </div>

            </fieldset>
        </form>
    </div>
    <!-- End Card Body -->

    <div class="card-header p-2">
        <h6 class="m-0 text-dark">Pregled osobina artikla</h6>
    </div>
    <div class="card-body p-2">

        <?php
        $propertys = $article->getPropertyByArticleId($article_id);
        foreach ($propertys as $property):
            ?>
            <form method="post">
                <fieldset disabled>

                    <div class="form-group row">
                        <div class="col-sm-4">
                            <select class="form-control" name="material_id">
                                <option value="<?php echo $property['id'] ?>"><?php echo $property['name'] ?></option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <a href="#" class="btn btn-sm btn-secondary disabled">
                                <i class="fas fa-trash-alt"> </i>
                            </a>
                        </div>
                    </div>

                </fieldset>
            </form>
            <?php
        endforeach;
        ?>

    </div>
    <!-- End Card Body -->

</div>
