<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arResult['JSON_EXT'] = RSDevFuncOffersExtension::GetJSONElement(
	$arResult,
	$arParams['PROPS_ATTRIBUTES'],
	$arParams['PRICE_CODE'],array('SKU_MORE_PHOTO_CODE'=>$arParams['PROP_SKU_MORE_PHOTO'],'SIZES'=>array('WIDTH'=>210,'HEIGHT'=>140))
);