<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;

if(!CModule::IncludeModule('redsign.activelife'))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$dbIblock = CIBlock::GetList(array("SORT"=>"ASC"), array( "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $dbIblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arTemplateParameters = array(
	'TIME_INTERVAL' => array(
		'NAME' => GetMessage('RSAL_SUPERBANNER_TIME_INTERVAL'),
		'TYPE' => 'STRING',
		'DEFAULT' => '5000',
	),
	'CHANGE_DELAY' => array(
		'NAME' => GetMessage('RSAL_SUPERBANNER_CHANGE_DELAY'),
		'TYPE' => 'STRING',
		'DEFAULT' => '1500',
	),
    'ADDITIONAL_BANNERS_IBLOCK_TYPE' => array(
		'NAME' => GetMessage('ADDITIONAL_BANNERS_IBLOCK_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $arTypesEx,
        "REFRESH" => "Y"
	),
    'ADDITIONAL_BANNERS_IBLOCK' => array(
		'NAME' => GetMessage('ADDITIONAL_BANNERS_IBLOCK'),
		'TYPE' => 'LIST',
		'VALUES' => $arIBlocks,
	),
);