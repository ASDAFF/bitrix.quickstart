<?php
$cp = $this->__component;
if (CModule::IncludeModule('iblock'))
{
	$rsElement = CIBlockElement::GetList(array('NAME'=>'ASC','SORT'=>'ASC'), array('IBLOCK_CODE' => array('AUTHORS', 'GANRES')), false, false, array('ID','NAME','IBLOCK_CODE', 'IBLOCK_ID'));
	while( $arElement = $rsElement->GetNext() )
	{
		if((is_array($arResult['PROPERTIES']['GANRES']['VALUE']) && in_array($arElement['ID'], $arResult['PROPERTIES']['GANRES']['VALUE'])) ||
			(is_array($arResult['PROPERTIES']['AUTHORS']['VALUE']) && in_array($arElement['ID'], $arResult['PROPERTIES']['AUTHORS']['VALUE'])) )
		$arResult['LINKED'][ToUpper($arElement['IBLOCK_CODE'])][$arElement['ID']] = $arElement['NAME'];
	}
	$cp->SetResultCacheKeys(array('LINKED'));
}
if($arResult["PROPERTIES"]['READER']['VALUE']){
	$rsUser = CUser::GetByID($arResult["PROPERTIES"]['READER']['VALUE']);
	$arUser = $rsUser->Fetch();
	$arResult['READER'] = array($arUser["NAME"], $arUser["SECOND_NAME"], $arUser["LAST_NAME"]);
	
	$cp->SetResultCacheKeys(array('READER'));
}
?>
