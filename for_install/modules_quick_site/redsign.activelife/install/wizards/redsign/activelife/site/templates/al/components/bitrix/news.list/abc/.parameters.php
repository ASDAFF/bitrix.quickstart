<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!Bitrix\Main\Loader::includeModule('iblock')) {
	return;
}

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));
$arIBlock=array();
$rsIBlock = CIBlock::GetList(array('sort' => 'asc'), array('ACTIVE' => 'Y'));
while ($arr=$rsIBlock->Fetch()) {
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
}

$IBLOCK_ID = $arCurrentValues['IBLOCK_ID'];
$arProperty = array();
if (0 < intval($IBLOCK_ID)) {
	$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'));
	while ($arr = $rsProp->Fetch()) {
		$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$arTemplateParameters = array(
	'DISPLAY_DATE' => Array(
		'NAME' => getMessage('RS_SLINE.NEWS_DATE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'DISPLAY_NAME' => Array(
		'NAME' => getMessage('RS_SLINE.NEWS_NAME'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'DISPLAY_PICTURE' => Array(
		'NAME' => getMessage('RS_SLINE.NEWS_PICTURE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'DISPLAY_PREVIEW_TEXT' => Array(
		'NAME' => getMessage('RS_SLINE.NEWS_TEXT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'GROUP_BY_ABC' => array(
		'NAME' => getMessage('RS_SLINE.GROUP_BY_ABC'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_PARENT' => array(
		'NAME' => getMessage('RS_SLINE.SHOW_PARENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SECTION_PAGE_MORE_URL' => array(
		'NAME' => getMessage('RS_SLINE.SECTION_PAGE_MORE_URL'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'SECTION_PAGE_MORE_TEXT' => array(
		'NAME' => getMessage('RS_SLINE.SECTION_PAGE_MORE_TEXT'),
		'TYPE' => 'STRING',
		'DEFAULT' => getMessage('RS_SLINE.SECTION_PAGE_MORE_DEFAULT'),
	),
	'LINKED_PROP' => array(
		'NAME' => getMessage('RS_SLINE.LINKED_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arProperty),
		'DEFAULT' => '-',
	),
	'CATALOG_IBLOCK_ID' => array(
		'NAME' => getMessage('RS_SLINE.CATALOG_IBLOCK_ID'),
		'TYPE' => 'LIST',
		'ADDITIONAL_VALUES' => 'Y',
		'VALUES' => $arIBlock,
		'REFRESH' => 'Y',
	),
);

$CATALOG_IBLOCK_ID = intval($arCurrentValues['CATALOG_IBLOCK_ID']);
if (0 < $CATALOG_IBLOCK_ID) {
	$arCatalogProperty = array();
	$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $CATALOG_IBLOCK_ID, 'ACTIVE' => 'Y'));
	while ($arr = $rsProp->Fetch()) {
		$arCatalogProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
	$arTemplateParameters['CATALOG_URL'] = array(
		'NAME' => getMessage('RS_SLINE.CATALOG_URL'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'catalog/',
	);
	$arTemplateParameters['CATALOG_FILTER_NAME'] = array(
		'NAME' => getMessage('RS_SLINE.CATALOG_FILTER_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'arrFilter',
	);
	$arTemplateParameters['CATALOG_LINKED_PROP'] = array(
		'NAME' => getMessage('RS_SLINE.CATALOG_LINKED_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty),
		'DEFAULT' => '-',
	);
}