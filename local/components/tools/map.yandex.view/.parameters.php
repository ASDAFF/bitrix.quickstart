<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$MAP_KEY = '';
$strMapKeys = COPtion::GetOptionString('fileman', 'map_yandex_keys');

$strDomain = $_SERVER['HTTP_HOST'];
$wwwPos = strpos($strDomian, 'www.');
if ($wwwPos === 0)
	$strDomain = substr($strDomain, 4);

if ($strMapKeys)
{
	$arMapKeys = unserialize($strMapKeys);
	
	if (array_key_exists($strDomain, $arMapKeys))
		$MAP_KEY = $arMapKeys[$strDomain];
}

$arComponentParameters = array(
	'GROUPS' => array(
	),
	'PARAMETERS' => array(
		'KEY' => array(
			'NAME' => GetMessage('MYMS_PARAM_KEY'),
			'TYPE' => 'STRING',
			'PARENT' => 'BASE',
			'DEFAULT' => $MAP_KEY,
		),
		
		'INIT_MAP_TYPE' => array(
			'NAME' => GetMessage('MYMS_PARAM_INIT_MAP_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'MAP' => GetMessage('MYMS_PARAM_INIT_MAP_TYPE_MAP'),
				'SATELLITE' => GetMessage('MYMS_PARAM_INIT_MAP_TYPE_SATELLITE'),
				'HYBRID' => GetMessage('MYMS_PARAM_INIT_MAP_TYPE_HYBRID'),
				'PMAP' => '����� (�������� �����)',
				'PHYBRID' => '������ (�������� �����)'
			),
			'DEFAULT' => 'PMAP',
			'ADDITIONAL_VALUES' => 'N',
			'PARENT' => 'BASE'
		),
		
		'PLAINSTYLE' => array(
			'NAME' => "���� � ����� �����",
			'TYPE' => 'LIST',
			'VALUES' => array(
				"default#lightbluePoint" => "�����������",
				"default#lightblueSmallPoint" => "default#lightblueSmallPoint",          
				"default#whiteSmallPoint" => "default#whiteSmallPoint",
				"default#whitePoint" => "default#whitePoint",
				"default#greenSmallPoint" => "default#greenSmallPoint",
				"default#greenPoint" => "default#greenPoint",
				"default#redSmallPoint" => "default#redSmallPoint",
				"default#redPoint" => "default#redPoint",
				"default#yellowSmallPoint" => "default#yellowSmallPoint",
				"default#yellowPoint" => "default#yellowPoint",
				"default#darkblueSmallPoint" => "default#darkblueSmallPoint",
				"default#darkbluePoint" => "default#darkbluePoint",
				"default#nightSmallPoint" => "default#nightSmallPoint",
				"default#nightPoint" => "default#nightPoint",
				"default#greySmallPoint" => "default#greySmallPoint",
				"default#greyPoint" => "default#greyPoint",
				"default#blueSmallPoint" => "default#blueSmallPoint",
				"default#bluePoint" => "default#bluePoint",
				"default#orangeSmallPoint" => "default#orangeSmallPoint",
				"default#orangePoint" => "default#orangePoint",
				"default#darkorangeSmallPoint" => "default#darkorangeSmallPoint",
				"default#darkorangePoint" => "default#darkorangePoint",
				"default#pinkSmallPoint" => "default#pinkSmallPoint",
				"default#pinkPoint" => "default#pinkPoint",
				"default#violetSmallPoint" => "default#violetSmallPoint",
				"default#violetPoint" => "default#violetPoint",
				"plain#lightbluePoint" => "plain#lightbluePoint",
				"plain#bluePoint" => "plain#bluePoint",
				"plain#darkbluePoint" => "plain#darkbluePoint",
				"plain#nightPoint" => "plain#nightPoint",
				"plain#whitePoint" => "plain#whitePoint",
				"plain#yellowPoint" => "plain#yellowPoint",
				"plain#orangePoint" => "plain#orangePoint",
				"plain#darkorangePoint" => "plain#darkorangePoint",
				"plain#redPoint" => "plain#redPoint",
				"plain#brownPoint" => "plain#brownPoint",
				"plain#greenPoint" => "plain#greenPoint",
				"plain#darkgreenPoint" => "plain#darkgreenPoint",
				"plain#pinkPoint" => "plain#pinkPoint",
				"plain#violetPoint" => "plain#violetPoint",
				"plain#greyPoint" => "plain#greyPoint",
				"plain#blackPoint" => "plain#blackPoint"
			),
			'DEFAULT' => 'default#lightbluePoint',
			'ADDITIONAL_VALUES' => 'Y',
			'PARENT' => 'BASE'
		),

		'MAP_DATA' => array(
			'NAME' => GetMessage('MYMS_PARAM_DATA'),
			'TYPE' => 'CUSTOM',
			'JS_FILE' => '/bitrix/components/bitrix/map.yandex.view/settings/settings.js',
			'JS_EVENT' => 'OnYandexMapSettingsEdit',
			'JS_DATA' => LANGUAGE_ID.'||'.GetMessage('MYMS_PARAM_DATA_SET').'||'.GetMessage('MYMS_PARAM_DATA_NO_KEY').'||'.GetMessage('MYMS_PARAM_DATA_GET_KEY').'||'.GetMessage('MYMS_PARAM_DATA_GET_KEY_URL'),
			'DEFAULT' => serialize(array(
				'yandex_lat' => GetMessage('MYMS_PARAM_DATA_DEFAULT_LAT'),
				'yandex_lon' => GetMessage('MYMS_PARAM_DATA_DEFAULT_LON'),
				'yandex_scale' => 10
			)),
			'PARENT' => 'BASE',
		),

		'MAP_WIDTH' => array(
			'NAME' => GetMessage('MYMS_PARAM_MAP_WIDTH'),
			'TYPE' => 'STRING',
			'DEFAULT' => '600',
			'PARENT' => 'BASE',
		),
		
		'MAP_HEIGHT' => array(
			'NAME' => GetMessage('MYMS_PARAM_MAP_HEIGHT'),
			'TYPE' => 'STRING',
			'DEFAULT' => '500',
			'PARENT' => 'BASE',
		),
		
		'CONTROLS' => array(
			'NAME' => GetMessage('MYMS_PARAM_CONTROLS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => array(
				'TOOLBAR' => GetMessage('MYMS_PARAM_CONTROLS_TOOLBAR'), 
				'ZOOM' => GetMessage('MYMS_PARAM_CONTROLS_ZOOM'), 
				'SMALLZOOM' => GetMessage('MYMS_PARAM_CONTROLS_SMALLZOOM'), 
				'MINIMAP' => GetMessage('MYMS_PARAM_CONTROLS_MINIMAP'), 
				'TYPECONTROL' => GetMessage('MYMS_PARAM_CONTROLS_TYPECONTROL'), 
				'SCALELINE' => GetMessage('MYMS_PARAM_CONTROLS_SCALELINE')
			),
			
			'DEFAULT' => array('TOOLBAR', 'ZOOM', 'MINIMAP', 'TYPECONTROL', 'SCALELINE'),
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		
		'OPTIONS' => array(
			'NAME' => GetMessage('MYMS_PARAM_OPTIONS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => array(
				'ENABLE_SCROLL_ZOOM' => GetMessage('MYMS_PARAM_OPTIONS_ENABLE_SCROLL_ZOOM'),
				'ENABLE_DBLCLICK_ZOOM' => GetMessage('MYMS_PARAM_OPTIONS_ENABLE_DBLCLICK_ZOOM'),
				'ENABLE_DRAGGING' => GetMessage('MYMS_PARAM_OPTIONS_ENABLE_DRAGGING'),
				'ENABLE_HOTKEYS' => GetMessage('MYMS_PARAM_OPTIONS_ENABLE_HOTKEYS'),
				/*'ENABLE_RULER' => GetMessage('MYMS_PARAM_OPTIONS_ENABLE_RULER'),*/
			),
			
			'DEFAULT' => array('ENABLE_SCROLL_ZOOM', 'ENABLE_DBLCLICK_ZOOM', 'ENABLE_DRAGGING'),
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
		
		'MAP_ID' => array(
			'NAME' => GetMessage('MYMS_PARAM_MAP_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			'PARENT' => 'ADDITIONAL_SETTINGS',
		),
	),
);
?>