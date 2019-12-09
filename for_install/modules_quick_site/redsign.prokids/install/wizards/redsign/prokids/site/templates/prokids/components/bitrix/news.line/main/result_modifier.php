<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( !\Bitrix\Main\Loader::includeModule('iblock') )
	return;

$arIBlockIDs = array();
foreach($arResult["ITEMS"] as $key => $arItem)
{
	if( !in_array($arItem['IBLOCK_ID'],$arIBlockIDs))
	{
		$arIBlockIDs[] = $arItem['IBLOCK_ID'];
	}
}

$arResult['IBLOCKS'] = array();
$arFilter = array( 
	'SITE_ID' => SITE_ID, 
	'ACTIVE' => 'Y', 
	'ID' => $arIBlockIDs,
);
$dbRes = CIBlock::GetList(array(),$arFilter,false);
while($arFields = $dbRes->GetNext())
{
	$arResult['IBLOCKS'][$arFields['ID']] = array(
		'ID' => $arFields['ID'],
		'NAME' => $arFields['NAME'],
		'LIST_PAGE_URL' => str_replace(array('/','//'),'/',str_replace('#SITE_DIR#',SITE_DIR,$arFields['LIST_PAGE_URL'])),
	);
}