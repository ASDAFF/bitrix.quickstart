<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arIBlock=array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arTemplateParameters = array(
	'BRAND_PAGE' => array(
		'NAME' => GetMessage('BRAND_PAGE'),
		'TYPE' => 'STRING',
	),
	'ADD_STYLES_FOR_MAIN' => array(
		'NAME' => GetMessage('ADD_STYLES_FOR_MAIN'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	),
	'BRAND_CODE' => array(
		'NAME' => GetMessage('BRAND_CODE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['HL'],
	),
	'SECTIONS_CODE' => array(
		'NAME' => GetMessage('SECTIONS_CODE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['ALL'],
	),
	'SHOW_BOTTOM_SECTIONS' => array(
		'NAME' => GetMessage('SHOW_BOTTOM_SECTIONS'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
	),
	'COUNT_ITEMS' => array(
		'NAME' => GetMessage('COUNT_ITEMS'),
		'TYPE' => 'STRING',
		'DEFAULT' => '0',
	),
	'CATALOG_FILTER_NAME' => array(
		'NAME' => GetMessage('CATALOG_FILTER_NAME'),
		'TYPE' => 'STRING',
	),
	'CATALOG_IBLOCK_ID' => array(
		'NAME' => GetMessage('CATALOG_IBLOCK_ID'),
		'TYPE' => 'LIST',
		'ADDITIONAL_VALUES' => 'Y',
		'VALUES' => $arIBlock,
		'REFRESH' => 'Y',
	),
);

if(IntVal($arCurrentValues["CATALOG_IBLOCK_ID"])>0)
{
	$listProp2 = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['CATALOG_IBLOCK_ID']);
	$arTemplateParameters['CATALOG_BRAND_CODE'] = array(
		'NAME' => GetMessage('CATALOG_BRAND_CODE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp2['HL'],
	);
}