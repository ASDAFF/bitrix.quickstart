<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$index = 0;
foreach ($arResult['VALUE'] as $res) {
	if ($index == 0
		&& $arParams['arUserField']['ENTITY_VALUE_ID'] < 1
		&& $arParams['arUserField']['SETTINGS']['DEFAULT_VALUE']['TYPE'] != 'NONE'
	) {
		if ($arParams['arUserField']['SETTINGS']['DEFAULT_VALUE']['TYPE'] == 'NOW') {
			$res = ConvertTimeStamp(time() + CTimeZone::GetOffset(), 'FULL');
		} else {
			$res = CDatabase::FormatDate($arParams['arUserField']['SETTINGS']['DEFAULT_VALUE']['VALUE'], 'YYYY-MM-DD HH:MI:SS', CLang::GetDateFormat('SHORT'));
		}
	}
	
	$name = $arParams['arUserField']['FIELD_NAME'];
	if ($arParams['arUserField']['MULTIPLE'] == 'Y') {
		$name .= '[' . $index . ']';
	}
	?>
	<span class="system-field-edit-item">
		<input
			class="form-control widget datetimepicker"
			type="text"
			name="<?=$name?>"
			id="<?=$arParams['domID']?>"
			value="<?=$res?>"
			<?=$arParams['arUserField']['EDIT_IN_LIST'] == 'Y' ? '' : 'disabled=""'?>
		/>
	</span>
	<?
	$index++;
}

if ($arParams['arUserField']['MULTIPLE'] == 'Y' && $arParams['SHOW_BUTTON'] != 'N') {
	?>
	<button class="btn btn-default" onClick="addElement('<?=$arParams['arUserField']['FIELD_NAME']?>', this)"><?=GetMessage('USER_TYPE_PROP_ADD')?></button>
	<?
}
?>