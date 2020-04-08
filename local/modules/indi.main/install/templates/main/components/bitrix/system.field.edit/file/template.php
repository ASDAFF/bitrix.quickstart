<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$nameSuffix = ($arParams["arUserField"]["MULTIPLE"] == "Y" ? "[]" : "");
foreach ($arResult['VALUE'] as $res) {
	?>
	<span class="system-field-edit-item">
		<input type="hidden" name="<?=$arParams["arUserField"]["FIELD_NAME"]?>_old_id<?=$nameSuffix?>" value="<?=$res?>"/>
		<input
			class="form-control"
			type="file"
			name="<?=$arParams['arUserField']['FIELD_NAME']?>"
			id="<?=$arParams['domID']?>"
			value=""
			<?=$arParams['arUserField']['EDIT_IN_LIST'] == 'Y' ? '' : 'disabled=""'?>
		/>
	</span>
	<?
}

if ($arParams['arUserField']['MULTIPLE'] == 'Y' && $arParams['SHOW_BUTTON'] != 'N') {
	?>
	<button class="btn btn-default" onClick="addElementFile('<?=$arParams['arUserField']['FIELD_NAME']?>', this)"><?=GetMessage('USER_TYPE_PROP_ADD')?></button>
	<?
}
?>