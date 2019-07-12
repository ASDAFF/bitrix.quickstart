<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if(CModule::IncludeModuleEx('bitrix.fashion')==3){
	echo GetMessage("TEST_END");
	return;
}

$arInfo = CSiteFashionStore::SectionResModifier($arResult["ITEMS"]);

$arResult["BASE_PRICES"] = $arInfo['BASE_PRICES'];
$arResult["ITEMS"] = $arInfo['ITEMS'];
?>