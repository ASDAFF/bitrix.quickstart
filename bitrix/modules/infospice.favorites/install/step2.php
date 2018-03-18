<?if (!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<?
if (CModule::IncludeModule('iblock')) {
	global $USER;
	if (!$arBlock = CIBlock::GetList(array(), array('TYPE' => 'favorites'))->Fetch()) {
		$obIBlock = new CIBlock;
		$arFields = Array(
			"ACTIVE"		 => 'Y',
			"NAME"			 => GetMessage("INFOSPICE_FAVORITES_IZBRANNOE"),
			"CODE"			 => 'favorites',
			"IBLOCK_TYPE_ID" => 'favorites',
			"SITE_ID"		 => array('s1'),
			//"GROUP_ID"		 => array(3 => 'R'),
			"SORT"			 => "1",
		);

		$ID = $obIBlock->Add($arFields);
	} else {
		$this->IBLOCK_ID = $arBlock['ID'];
	}
}
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>"/>
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>"/>
</form>
