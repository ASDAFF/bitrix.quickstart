<?
if($arResult['ITEMS']){
	$arSectionIDs = array();
	
	foreach($arResult['ITEMS'] as $i => $arItem){
		$arSectionIDs[] = $arItem['IBLOCK_SECTION_ID'];
		$arResult['ITEMS_BY_SECTIONS'][($arItem['IBLOCK_SECTION_ID'] ? $arItem['IBLOCK_SECTION_ID'] : 0)][] = &$arResult['ITEMS'][$i];
	}
	
	if($arSectionIDs){
		CModule::IncludeModule('iblock');
		$dbRes = CIBlockSection::GetList(array(), array('ID' => $arSectionIDs, 'ACTIVE' => 'Y', 'LEVEL_DEPTH' => 0), false, array('ID', 'NAME'));
		while($arSection = $dbRes->Fetch()){
			$arResult['SECTIONS'][$arSection['ID']] = $arSection['NAME'];
		}
	}
	
	$arResult['SECTIONS'][0] = GetMessage('FAQ_OTHER_SECTION');
}
?>