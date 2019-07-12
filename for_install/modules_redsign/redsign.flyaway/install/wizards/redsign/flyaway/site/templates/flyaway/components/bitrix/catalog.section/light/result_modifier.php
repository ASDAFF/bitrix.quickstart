<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('redsign.devfunc'))
	return;
if (!CModule::IncludeModule('redsign.flyaway'))
	return;

$arResult['TEMPLATE_DEFAULT'] = array(
	'TEMPLATE' => 'showcase',
	'CSS' => 'products_showcase',
);

if ($arParams['RSFLYAWAY_TEMPLATE'] == 'list') {
	$arResult['TEMPLATE_DEFAULT'] = array(
		'TEMPLATE' => 'list',
		'CSS' => 'products_list',
	);
} elseif ($arParams['RSFLYAWAY_TEMPLATE'] == 'list_little') {
	$arResult['TEMPLATE_DEFAULT'] = array(
		'TEMPLATE' => 'list',
		'CSS' => 'products_list products_little',
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

if ('Y' == $arParams['SHOW_SECTION_URL']) {
	if (!empty($arResult['ITEMS'])) {
		$arResult['SECTIONS'] = array();
		foreach ($arResult['ITEMS'] as $arItem) {
			$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']] = $arItem['IBLOCK_SECTION_ID'];
		}
		
		if (!empty($arResult['SECTIONS'])) {
			$dbSections = CIBlockSection::GetList(array(), array('ID' => $arResult['SECTIONS']));
			while ($arSection = $dbSections->GetNext()) {
				$arResult['SECTIONS'][$arSection['ID']] = $arSection;
			}
		}
	}
}

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
