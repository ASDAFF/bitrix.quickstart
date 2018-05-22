<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arThemes = array(
	'orange' => GetMessage('TEMPLATE_THEME_orange'),
	'green' => GetMessage('TEMPLATE_THEME_green'),
	'blue' => GetMessage('TEMPLATE_THEME_blue'),
	'purple' => GetMessage('TEMPLATE_THEME_purple'),
	'yellow' => GetMessage('TEMPLATE_THEME_yellow'),
	'lime' => GetMessage('TEMPLATE_THEME_lime'),
	'red' => GetMessage('TEMPLATE_THEME_red'),
	'brown' => GetMessage('TEMPLATE_THEME_brown'),
	'towny' => GetMessage('TEMPLATE_THEME_towny'),
	'dark_blue' => GetMessage('TEMPLATE_THEME_dark_blue'),
);

$arTemplateParameters = array(
	'TEMPLATE_THEME' => array(
		'NAME' => GetMessage('TEMPLATE_THEME'),
		'TYPE' => 'LIST',
		'VALUES' => $arThemes,
		'DEFAULT' => 'orange',
	),
	'Z_INDEX' => array(
		'NAME' => GetMessage('Z_INDEX'),
		'TYPE' => 'STRING',
		'DEFAULT' => '500',
	),
	'MAX_WIDTH' => array(
		'NAME' => GetMessage('MAX_WIDTH'),
		'TYPE' => 'STRING',
		'DEFAULT' => '1240',
	),
	'ADD_BODY_PADDING' => array(
		'NAME' => GetMessage('ADD_BODY_PADDING'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
	'USE_ONLINE_CONSUL' => array(
		'NAME' => GetMessage('USE_ONLINE_CONSUL'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	),
	'INCLUDE_JQUERY' => array(
		'NAME' => GetMessage('INCLUDE_JQUERY'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
	'INCLUDE_JQUERY_COOKIE' => array(
		'NAME' => GetMessage('INCLUDE_JQUERY_COOKIE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
	),
	'ON_UNIVERSAL_AJAX_HANDLER' => array(
		'PARENT' => 'BASE',
		'NAME' => GetMessage('ON_UNIVERSAL_AJAX_HANDLER'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	),
);

if( $arCurrentValues['USE_ONLINE_CONSUL']=='Y' )
{
	$arTemplateParameters['ONLINE_CONSUL_LINK'] = array(
		'NAME' => GetMessage('ONLINE_CONSUL_LINK'),
		'TYPE' => 'STRING',
		'DEFAULT' => '#',
	);
}

if( $arCurrentValues['USE_COMPARE']=='Y' )
{
	if(!CModule::IncludeModule('iblock'))
	{
		return;
	}

	$arIBlockType = CIBlockParameters::GetIBlockTypes();

	$rsIBlock = CIBlock::GetList(array('sort'=>'asc'),array('TYPE'=>$arCurrentValues['COMPARE_IBLOCK_TYPE'],'ACTIVE'=>'Y'));
	while($arr=$rsIBlock->Fetch())
	{
		$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
	}
	
	$arTemplateParameters['COMPARE_IBLOCK_TYPE'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => GetMessage('COMPARE_IBLOCK_TYPE'),
		'TYPE' => 'LIST',
		'ADDITIONAL_VALUES' => 'Y',
		'VALUES' => $arIBlockType,
		'REFRESH' => 'Y',
	);
	$arTemplateParameters['COMPARE_IBLOCK_ID'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => GetMessage('COMPARE_IBLOCK_ID'),
		'TYPE' => 'LIST',
		'ADDITIONAL_VALUES' => 'Y',
		'VALUES' => $arIBlock,
		'REFRESH' => 'Y',
	);
	$arTemplateParameters['COMPARE_NAME'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => GetMessage('COMPARE_NAME'),
		'TYPE' => 'STRING',
	);
	$arTemplateParameters['COMPARE_RESULT_PATH'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => GetMessage('COMPARE_RESULT_PATH'),
		'TYPE' => 'STRING',
	);
}

if( $arCurrentValues['ON_UNIVERSAL_AJAX_HANDLER']=='Y' )
{
	$arTemplateParameters['UNIVERSAL_AJAX_FINDER_COMPARE_ADD'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => GetMessage('UNIVERSAL_AJAX_FINDER_COMPARE_ADD'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'action=ADD_TO_COMPARE_LIST',
	);
	$arTemplateParameters['UNIVERSAL_AJAX_FINDER_COMPARE_REMOVE'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => GetMessage('UNIVERSAL_AJAX_FINDER_COMPARE_REMOVE'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'action=DELETE_FROM_COMPARE_LIST',
	);
	$arTemplateParameters['UNIVERSAL_AJAX_FINDER_FAVORITE'] = array(
		'PARENT' => 'FAVORITE_SETTINGS',
		'NAME' => GetMessage('UNIVERSAL_AJAX_FINDER_FAVORITE'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'action=add2favorite',
	);
	$arTemplateParameters['UNIVERSAL_AJAX_FINDER_BASKET'] = array(
		'PARENT' => 'BASKET_SETTINGS',
		'NAME' => GetMessage('UNIVERSAL_AJAX_FINDER_BASKET'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'action=ADD2BASKET',
	);
}