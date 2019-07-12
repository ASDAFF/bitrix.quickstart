<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('redsign.devfunc')
	|| !CModule::IncludeModule('redsign.flyaway')) {
	return;
}

$arResult['TEMPLATE_DEFAULT'] = array(
	'TEMPLATE' => 'showcase',
	'CSS' => 'products_showcase',
);

if ($arParams['RSFLYAWAY_TEMPLATE'] == 'showcase_mob') {
	$arResult['TEMPLATE_DEFAULT'] = array(
		'TEMPLATE' => 'showcase',
		'CSS' => 'products_showcase products_showcase-mob',
	);
} elseif ($arParams['RSFLYAWAY_TEMPLATE'] == 'list') {
	$arResult['TEMPLATE_DEFAULT'] = array(
		'TEMPLATE' => 'list',
		'CSS' => 'products_list',
	);
} elseif ($arParams['RSFLYAWAY_TEMPLATE'] == 'list_little') {
	$arResult['TEMPLATE_DEFAULT'] = array(
		'TEMPLATE' => 'list_little',
		'CSS' => 'products_table',
	);
}

$max_width_size = 300;
$max_height_size = 300;
$params = array(
	'PROP_MORE_PHOTO' => $arParams['RSFLYAWAY_PROP_MORE_PHOTO'],
	'MAX_WIDTH' => $max_width_size,
	'MAX_HEIGHT' => $max_height_size,
);

// get other data
RSDevFunc::GetDataForProductItem($arResult['ITEMS'],$params);
// /get other data

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$max_width_size,'MAX_HEIGHT'=>$max_height_size));
// /get no photo

// get flyaway data
$params = array(
	'RSFLYAWAY_PROP_PRICE' => $arParams['RSFLYAWAY_PROP_PRICE'],
	'RSFLYAWAY_PROP_DISCOUNT' => $arParams['RSFLYAWAY_PROP_DISCOUNT'],
	'RSFLYAWAY_PROP_CURRENCY' => $arParams['RSFLYAWAY_PROP_CURRENCY'],
	'RSFLYAWAY_PROP_PRICE_DECIMALS' => $arParams['RSFLYAWAY_PROP_PRICE_DECIMALS'],
);

RsFlyaway::addData($arResult['ITEMS'], $params);
// /get flyaway data

// ADD AJAX URL
$arResult['AJAXPAGE_URL'] = $APPLICATION->GetCurPageParam('',array('ajaxpages', 'ajaxpagesid', 'get', 'AJAX_CALL', 'PAGEN_'.($arResult['NAV_RESULT']->NavNum)));
