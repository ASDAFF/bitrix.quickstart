<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!Bitrix\Main\Loader::includeModule('iblock')){
	return;
}

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$arIBlock=array();
$rsIBlock = CIBlock::GetList(array('sort' => 'asc'), array('ACTIVE' => 'Y'));
while($arr=$rsIBlock->Fetch()){
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
}

$IBLOCK_ID = $arCurrentValues['IBLOCK_ID'];
$arProperty = array();
if(0 < intval($IBLOCK_ID)){
	$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'));
	while($arr = $rsProp->Fetch()){
		$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}

$arTemplateParameters = array(
	'BRAND_PROP' => array(
		'NAME' => GetMessage('RS_SLINE.BRAND_PROP'),
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
if ($CATALOG_IBLOCK_ID > 0) {
    
	$arCatalogProperty = array();
	$rsProp = CIBlockProperty::GetList(Array('sort' => 'asc', 'name' => 'asc'), Array('IBLOCK_ID' => $CATALOG_IBLOCK_ID, 'ACTIVE' => 'Y'));

	while ($arr = $rsProp->Fetch()) {
		$arCatalogProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}

	$arTemplateParameters['CATALOG_FILTER_NAME'] = array(
		'NAME' => getMessage('RS_SLINE.CATALOG_FILTER_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'arrFilter',
	);

	$arTemplateParameters['CATALOG_BRAND_PROP'] = array(
		'NAME' => getMessage('RS_SLINE.CATALOG_BRAND_PROP'),
		'TYPE' => 'LIST',
		'VALUES' => array_merge($defaultListValues, $arCatalogProperty),
        'DEFAULT' => '-',
	);

	$arTemplateParameters["SEF_CATALOG"] = array(
	    'PARENT' => 'URL_TEMPLATES',
		'NAME' => getMessage('RS_SLINE.SEF_CATALOG'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	);
/*
	$arTemplateParameters['BRAND_URL'] = array(
	    'PARENT' => 'URL_TEMPLATES',
	    'NAME' => getMessage('RS_SLINE.BRAND_URL'),
	    'TYPE' => 'STRING',
	    'DEFAULT' => '/catalog/filter/#SMART_FILTER_PATH#/apply/',
	);
*/
}