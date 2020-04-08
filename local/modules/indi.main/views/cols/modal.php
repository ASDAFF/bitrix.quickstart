<div class="modal fade" id="change-columns-modal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-label="Close" data-dismiss="modal" class="icon icon-close-bold close" type="button"></button>
				<h3 class="modal-title">Изменить столбцы</h3>
			</div>
			<div class="modal-body">
				<form class="js-change-element-col-option" data-name="<?=$result['PAGE_ELEMENT_NAME']?>">
					<div class="form-group">
						<?
						foreach($result['PAGE_ELEMENT_COL_OPTION_FULL'] as $key=>$val) {
							?>
							<div class="checkbox">
								<label><input type="checkbox" name="<?=$key?>"<?=$result["PAGE_ELEMENT_COL_OPTION"][$key] ? ' checked="checked"' : ''?> value="1"><span><?=$val['NAME']?></span></label>
							</div>
							<?
						}
						?>
					</div>
					<div class="form-footer">
						<button class="btn" type="submit">Сохранить</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<p><a class="icon-link fake" data-toggle="modal" href="#change-columns-modal"><span class="icon icon-settings"></span><span>Изменить столбцы</span></a></p>
