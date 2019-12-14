<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;
if( empty($arParams['RSMONOPOLY_PROP_CITY']) || empty($arParams['RSMONOPOLY_PROP_TYPE']) || empty($arParams['RSMONOPOLY_PROP_COORDINATES']) )
	return;

$arResult['CITIES'] = array();
$arResult['CITIES']['VALUES'] = array();
$propertyEnums = CIBlockPropertyEnum::GetList(array(),array("IBLOCK_ID"=>$arParams['IBLOCK_ID'], "CODE"=>$arParams['RSMONOPOLY_PROP_CITY']));
while($arFields = $propertyEnums->GetNext()) {
	$arResult['CITIES']['VALUES'][] = array(
		'ID' => $arFields['ID'],
		'VALUE' => $arFields['VALUE'],
		'XML_ID' => $arFields['XML_ID'],
	);
}

$arResult['FILTER'] = array();
$arResult['FILTER']['VALUES'] = array();
$propertyEnums = CIBlockPropertyEnum::GetList(array(),array("IBLOCK_ID"=>$arParams['IBLOCK_ID'], "CODE"=>$arParams['RSMONOPOLY_PROP_TYPE']));
while($arFields = $propertyEnums->GetNext()) {
	$arResult['FILTER']['VALUES'][] = array(
		'ID' => $arFields['ID'],
		'VALUE' => $arFields['VALUE'],
		'XML_ID' => $arFields['XML_ID'],
	);
}