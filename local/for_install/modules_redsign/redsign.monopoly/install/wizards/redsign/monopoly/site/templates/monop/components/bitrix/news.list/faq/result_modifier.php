<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;

$arResult['FILTER'] = array();
if( $arParams['RSMONOPOLY_PROP_FAQ_TYPE']!='' ) {
	$arResult['FILTER']['VALUES'] = array();
	$propertyEnums = CIBlockPropertyEnum::GetList(array(),array("IBLOCK_ID"=>$arParams['IBLOCK_ID'], "CODE"=>$arParams['RSMONOPOLY_PROP_FAQ_TYPE']));
	while($arFields = $propertyEnums->GetNext()) {
		$arResult['FILTER']['VALUES'][] = array(
			'ID' => $arFields['ID'],
			'VALUE' => $arFields['VALUE'],
			'XML_ID' => $arFields['XML_ID'],
		);
	}
}