<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('redsign.devfunc'))
	return;
if (!CModule::IncludeModule('redsign.flyaway'))
	return;

$arResult['TEMPLATE_DEFAULT'] = array(
	'TEMPLATE' => 'showcase',
	'CSS' => 'products_showcase',
);

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

// ADD AJAX URL
$arResult['AJAXPAGE_URL'] = $APPLICATION->GetCurPageParam('',array('ajaxpages', 'ajaxpagesid', 'get', 'AJAX_CALL', 'PAGEN_'.($arResult['NAV_RESULT']->NavNum)));