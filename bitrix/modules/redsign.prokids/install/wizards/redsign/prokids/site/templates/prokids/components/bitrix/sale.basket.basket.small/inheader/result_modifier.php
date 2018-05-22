<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$arResult = RSDevFuncResultModifier::SaleBasketBasketSmall($arResult);
$arResult["RIGHT_WORD"] = RSDevFunc::BasketEndWord($arResult["NUM_PRODUCTS"]);