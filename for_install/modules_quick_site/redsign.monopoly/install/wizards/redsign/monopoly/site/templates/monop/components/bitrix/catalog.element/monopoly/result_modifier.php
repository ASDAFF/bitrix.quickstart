<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;
if(!CModule::IncludeModule('redsign.monopoly'))
	return;

if(!empty($arResult)) {
	$params = array(
		'PROP_MORE_PHOTO' => $arParams['RSMONOPOLY_PROP_MORE_PHOTO'],
		'MAX_WIDTH' => 120,
		'MAX_HEIGHT' => 120,
		'PAGE' => 'detail',
	);
	$arItems = array(0 => &$arResult);
	RSDevFunc::GetDataForProductItem($arItems,$params);

	// get monopoly data
	$params = array(
		'RSMONOPOLY_PROP_PRICE' => $arParams['RSMONOPOLY_PROP_PRICE'],
		'RSMONOPOLY_PROP_DISCOUNT' => $arParams['RSMONOPOLY_PROP_DISCOUNT'],
		'RSMONOPOLY_PROP_CURRENCY' => $arParams['RSMONOPOLY_PROP_CURRENCY'],
		'RSMONOPOLY_PROP_PRICE_DECIMALS' => $arParams['RSMONOPOLY_PROP_PRICE_DECIMALS'],
	);
	RSMonopoly::addData($arItems,$params);
	// /get monopoly data
}

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$max_width_size,'MAX_HEIGHT'=>$max_height_size));
// /get no photo