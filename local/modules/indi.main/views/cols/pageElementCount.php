<div class="form-group">
	<label>Количество элементов</label>
	<select class="js-change-element-count" data-name="<?=$result['PAGE_ELEMENT_NAME']?>">
		<?
		foreach($result['PAGE_ELEMENT_COUNT_OPTION'] as $val) {
			?>
			<option value="<?=$val?>"<?=$result['PAGE_ELEMENT_COUNT'] == $val ? ' selected' : ''?>><?=$val?></option>
			<?
		}
		?>
	</select>
</div>
