<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!IsModuleInstalled('iblock') || !CModule::IncludeModule('iblock')) {
	return;
}

//Handle of parameters
$arParams['SITE_ID'] = trim($arParams['SITE_ID']);
$arParams['IBLOCK_TYPE'] = trim($arParams['IBLOCK_TYPE']);
$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);
$arParams['FILTER'] = (array) $arParams['FILTER'];

//We have to save current user and create new one
//because of possible agent execution
global $USER;
$SAVED_USER = $USER;
$USER = new CUser;

$componentResult = 0;

$iblocksFilter = array(
	'ACTIVE' => 'Y'
);
if ($arParams['IBLOCK_ID']) {
	$iblocksFilter['ID'] = $arParams['IBLOCK_ID'];
}
if ($arParams['IBLOCK_TYPE']) {
	$iblocksFilter['TYPE'] = $arParams['IBLOCK_TYPE'];
}
if ($arParams['SITE_ID']) {
	$iblocksFilter['SITE_ID'] = $arParams['SITE_ID'];
}
$iblocks = CIBlock::GetList(
	array(
		'SORT' => 'ASC',
	),
	$iblocksFilter
);

$elementsOrder = array(
	$arParams['SORT_BY'] => $arParams['SORT_ORDER'],
);
$elementsFilter = array(
	'ACTIVE' => 'Y',
	'>DATE_ACTIVE_FROM' => $arParams['DATE_ACTIVE_FROM'],
	'<=DATE_ACTIVE_FROM' => $arParams['DATE_ACTIVE_TO'],
);
if ($arParams['FILTER']) {
	$elementsFilter = array_merge($elementsFilter, $arParams['FILTER']);
}

$site = CSite::GetByID($arParams['SITE_ID'])->Fetch();
$arResult['SERVER_NAME'] = $site['SERVER_NAME'];

$arResult['IBLOCKS'] = array();
while($iblock = $iblocks->Fetch())
{
	$arResult['IBLOCKS'][$iblock['ID']] = $iblock;

	$elementsFilter['IBLOCK_ID'] = $iblock['ID'];
	
	$elements = CIBlockElement::GetList(
		$elementsOrder,
		$elementsFilter,
		false,
		false,
		array(
			'ID',
			'IBLOCK_ID',
			'DETAIL_PAGE_URL',
			'PREVIEW_PICTURE',
			'DATE_ACTIVE_FROM',
			'NAME',
			'PREVIEW_TEXT',
			'PREVIEW_TEXT_TYPE',
		)
	);
	$arResult['IBLOCKS'][$iblock['ID']]['ITEMS'] = array();
	while ($element = $elements->GetNext()) {
		$element['PREVIEW_PICTURE'] = CFile::GetFileArray($element['PREVIEW_PICTURE']);
		
		if(strpos($element['DETAIL_PAGE_URL'], 'http') !== 0)
			$element['DETAIL_PAGE_URL'] = 'http://' . $site['SERVER_NAME'] . $element['DETAIL_PAGE_URL'];
		
		$arResult['IBLOCKS'][$iblock['ID']]['ITEMS'][] = $element;
		$componentResult++;
	}
}

if($componentResult)
	$this->IncludeComponentTemplate();

//Restore user
$USER = $SAVED_USER;

return $componentResult;