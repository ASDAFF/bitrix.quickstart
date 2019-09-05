<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

foreach ($arResult['VALUE'] as $res) {
	?>
	<span class="system-field-edit-item">
		<?
		if($arParams["arUserField"]["SETTINGS"]["ROWS"] < 2) {
			?>
			<input
				class="form-control"
				type="text"
				name="<?=$arParams['arUserField']['FIELD_NAME']?>"
				id="<?=$arParams['domID']?>"
				value="<?=$res?>"
				<?=intval($arParams["arUserField"]["SETTINGS"]["SIZE"]) > 0 ? 'size="' . $arParams["arUserField"]["SETTINGS"]["SIZE"] . '"' : ''?>
				<?=intval($arParams["arUserField"]["SETTINGS"]["MAX_LENGTH"]) > 0 ? 'maxlength="' . $arParams["arUserField"]["SETTINGS"]["MAX_LENGTH"] . '"' : ''?>
				<?=$arParams['arUserField']['EDIT_IN_LIST'] == 'Y' ? '' : 'disabled=""'?>
			/>
			<?
		} else {
			?>
			<textarea
				class="form-control"
				name="<?=$arParams["arUserField"]["FIELD_NAME"]?>"
				id="<?=$arParams['domID']?>"
				<?=intval($arParams["arUserField"]["SETTINGS"]["MAX_LENGTH"]) > 0 ? 'maxlength="' . $arParams["arUserField"]["SETTINGS"]["MAX_LENGTH"] . '"' : ''?>
				<?=intval($arParams["arUserField"]["SETTINGS"]["SIZE"]) > 0 ? 'cols="' . $arParams["arUserField"]["SETTINGS"]["SIZE"] . '"' : ''?>
				<?=intval($arParams["arUserField"]["SETTINGS"]["ROWS"]) > 0 ? 'rows="' . $arParams["arUserField"]["SETTINGS"]["ROWS"] . '"' : ''?>
				<?=$arParams['arUserField']['EDIT_IN_LIST'] == 'Y' ? '' : 'disabled=""'?>
			><?=$res?></textarea><?
		}
		?>
	</span>
	<?
}

if ($arParams['arUserField']['MULTIPLE'] == 'Y' && $arParams['SHOW_BUTTON'] != 'N') {
	?>
	<button class="btn btn-default" onClick="addElement('<?=$arParams['arUserField']['FIELD_NAME']?>', this)"><?=GetMessage('USER_TYPE_PROP_ADD')?></button>
	<?
}
?>