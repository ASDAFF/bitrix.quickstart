<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if ($arParams['arUserField']['SETTINGS']['DISPLAY'] == 'CHECKBOX') {
	?>
	<input type="hidden" name="<?=$arParams['arUserField']['FIELD_NAME']?>" value=""/>
	<?
	foreach ($arParams['arUserField']['USER_TYPE']['FIELDS'] as $key => $val) {
		$selected = in_array($key, $arResult['VALUE']);
		?>
		<span class="system-field-edit-item">
			<?
			if($arParams['arUserField']['MULTIPLE'] == 'Y') {
				?>
				<label class="checkbox-inline">
					<input
						type="checkbox"
						name="<?=$arParams['arUserField']['FIELD_NAME']?>"
						value="<?=$key?>"
						<?=$selected ? 'checked=""' : ''?>
					/> <?=$val?>
				</label>
				<?
			} else {
				?>
				<label class="radio-inline">
					<input
						type="radio"
						name="<?=$arParams['arUserField']['FIELD_NAME']?>"
						value="<?=$key?>"
						<?=$selected ? 'checked=""' : ''?>
					/> <?=$val?>
				</label>
				<?
			}
			?>
		</span>
		<?
	}
} else {
	?>
	<span class="system-field-edit-item">
		<select
			class="form-control"
			name="<?=$arParams['arUserField']['FIELD_NAME']?>"
			<?=$arParams['arUserField']['SETTINGS']['LIST_HEIGHT'] > 1 ? 'size="' . $arParams['arUserField']['SETTINGS']['LIST_HEIGHT'] . '"' : ''?>
			<?=$arParams['arUserField']['MULTIPLE'] == 'Y' ? 'multiple=""' : ''?>
		>
			<?
			foreach ($arParams['arUserField']['USER_TYPE']['FIELDS'] as $key => $val) {
				$selected = in_array($key, $arResult['VALUE']);
				?>
				<option value="<?=$key?>"<?=$selected ? ' selected=""' : ''?>><?=$val?></option>
				<?
			}
			?>
		</select>
	</span>
	<?
}