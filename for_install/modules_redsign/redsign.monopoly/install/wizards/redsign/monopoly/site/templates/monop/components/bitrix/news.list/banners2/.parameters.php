<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('redsign.mshop'))
	return;
if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arIBlockTypes = array(); 
$dbIBlockType = CIBlockType::GetList(
   array("sort" => "asc"),
   array("ACTIVE" => "Y")
);
while ($arIBlockType = $dbIBlockType->Fetch()) {
    $arIBlockLangName = CIBlockType::GetByIDLang($arIBlockType["ID"], LANGUAGE_ID);
    if($arIBlockLangName) {
        $arIBlockTypes[$arIBlockType["ID"]] = "[".$arIBlockType["ID"]."] ".$arIBlockTypeLang["NAME"];
    }
}
    
$arIBlock = array();
$iblockFilter = (
	!empty($arCurrentValues['IBLOCK_TYPE'])
	? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
	: array('ACTIVE' => 'Y')
);
$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $dbIBlock->Fetch())
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];

$bannerTemplates = array(
    'wide' => GetMessage('RSMONOPOLY_BANNER_TEMPLATE_WIDE'),
    'center' => GetMessage('RSMONOPOLY_BANNER_TEMPLATE_CENTER'),
    'extended' => GetMessage('RSMONOPOLY_BANNER_TEMPLATE_EXTENDED')
);

$arTemplateParameters = array(
	'RSMONOPOLY_BANNER_TYPE' => array(
		'NAME' => GetMessage('RSMONOPOLY_BANNER_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_LINK' => array(
		'NAME' => GetMessage('RSMONOPOLY_LINK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_BLANK' => array(
		'NAME' => GetMessage('RSMONOPOLY_BLANK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_TEXT_1' => array(
		'NAME' => GetMessage('RSMONOPOLY_TEXT_1'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_TEXT_2' => array(
		'NAME' => GetMessage('RSMONOPOLY_TEXT_2'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_PRICE' => array(
		'NAME' => GetMessage('RSMONOPOLY_PRICE'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RSMONOPOLY_BANNER_VIDEO_MP4' => array(
		'NAME' => GetMessage('RSMONOPOLY_BANNER_VIDEO_MP4'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
	'RSMONOPOLY_BANNER_VIDEO_WEBM' => array(
		'NAME' => GetMessage('RSMONOPOLY_BANNER_VIDEO_WEBM'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
    'RSMONOPOLY_BANNER_TEMPLATE' => array(
        'NAME' => GetMessage('RSMONOPOLY_BANNER_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $bannerTemplates
    )
);

RSMONOPOLY_AddComponentParameters($arTemplateParameters,array('owlSettings'));