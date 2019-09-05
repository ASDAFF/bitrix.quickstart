<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

foreach ($arResult['VALUE'] as $res) {
	?>
	<span class="system-field-edit-item">
		<input
			class="form-control"
			type="text"
			name="<?=$arParams['arUserField']['FIELD_NAME']?>"
			id="<?=$arParams['domID']?>"
			value="<?=$res?>"
			<?=$arParams['arUserField']['EDIT_IN_LIST'] == 'Y' ? '' : 'disabled=""'?>
		/>
	</span>
	<?
}

if ($arParams['arUserField']['MULTIPLE'] == 'Y' && $arParams['SHOW_BUTTON'] != 'N') {
	?>
	<button class="btn btn-default" onClick="addElement('<?=$arParams['arUserField']['FIELD_NAME']?>', this)"><?=GetMessage('USER_TYPE_PROP_ADD')?></button>
	<?
}
?>