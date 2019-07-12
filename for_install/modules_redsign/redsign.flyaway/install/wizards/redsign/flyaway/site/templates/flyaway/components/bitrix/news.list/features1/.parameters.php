<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('iblock')
	|| !CModule::IncludeModule('redsign.flyaway') 
	|| !CModule::IncludeModule('redsign.devfunc')) {
	return;
}

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
	'RS_LINK' => array(
		'NAME' => GetMessage('RS.LINK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
	'RS_BLANK' => array(
		'NAME' => GetMessage('RS.BLANK'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['SNL'],
	),
);

RSFLYAWAY_AddComponentParameters($arTemplateParameters, array('blockName','owlSupport'));
if ($arCurrentValues['RS_USE_OWL'] == 'Y') {
	RSFLYAWAY_AddComponentParameters($arTemplateParameters, array('owlSettings'));
} else {
	RSFLYAWAY_AddComponentParameters($arTemplateParameters, array('bootstrapCols'));
}
