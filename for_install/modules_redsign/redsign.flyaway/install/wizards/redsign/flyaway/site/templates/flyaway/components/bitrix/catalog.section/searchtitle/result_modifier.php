<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

// get other data
$params = array(
	'MAX_WIDTH' => 80,
	'MAX_HEIGHT' => 80,
);
RSDevFunc::GetDataForProductItem($arResult['ITEMS'],$params);