<!-- View Article Group Data -->
<div class="card mb-4">
	<div class="card-header p-2">
		<h6 class="m-0 text-dark">Pregled grupe artikala: <strong><?php echo $article_group_data->getName() ?></strong></h6>
	</div>
	<div class="card-body p-2">
		<form>
			<fieldset disabled>

				<div class="form-group row">
					<label for="disabledInputName" class="col-sm-3 col-lg-2 col-form-label text-right">Naziv:</label>
					<div class="col-sm-8">
						<input class="form-control" id="disabledInputName" type="text" name="name"
									 value="<?php echo $article_group_data->getName() ?>" maxlength="96">
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
</div>
