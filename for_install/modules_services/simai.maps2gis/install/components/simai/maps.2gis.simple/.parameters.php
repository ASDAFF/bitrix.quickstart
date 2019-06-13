<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

// needed to setup map points
//global $APPLICATION;
//$APPLICATION->AddHeadScript( 'http://maps.api.2gis.ru/1.0' );
//$APPLICATION->AddHeadString( '<script src="http://maps.api.2gis.ru/1.0" type="text/javascript" charset="utf-8"></script>' );

$arComponentParameters = array(
	"GROUPS" => array(
	),
    // BASE SETTINGS
	"PARAMETERS" => array(
		'MAP_DATA' => array(
			'PARENT'   => 'BASE',
            'NAME'     => GetMessage('2GIS_MAP_DATA'),
            'TYPE'     => 'CUSTOM',
            'MULTIPLE' => 'N',
			'JS_FILE'  => '/bitrix/components/simai/maps.2gis.simple/settings/settings.js',
			'JS_EVENT' => 'On2GisMapSettingsEdit',
			'JS_DATA'  => LANGUAGE_ID.'||'.GetMessage('2GIS_PARAM_DATA_SET'),
			'DEFAULT'  => serialize( array(
				'LAT'        => 55.7383,
				'LON'        => 37.5946,
				'SCALE'      => 10,
                'PLACEMARKS' => array()
			) ),
			'PARENT'   => 'BASE',
		),
		'MAP_WIDTH' => array(
			'PARENT' => 'BASE',
            'NAME' => GetMessage('2GIS_PARAM_MAP_WIDTH'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            "DEFAULT" => '500'
		),
		'MAP_HEIGHT' => array(
			'PARENT' => 'BASE',
            'NAME' => GetMessage('2GIS_PARAM_MAP_HEIGHT'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            "DEFAULT" => '400'
		),
		'MAP_ZOOM' => array(
			'PARENT' => 'BASE',
            'NAME' => GetMessage('2GIS_PARAM_MAP_ZOOM'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            "DEFAULT" => '15'
		),
        // additional settings
        'MAP_CONTROL_ZOOM' => array(
            'NAME' => GetMessage('2GIS_PARAM_ENABLE_ZOOM_CONTROL'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'ADDITIONAL_SETTINGS'
		),
		'MAP_CONTROL_DBLCLICK_ZOOM' => array(
            'NAME' => GetMessage('2GIS_PARAM_ENABLE_DBLCLICK_ZOOM'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'PARENT' => 'ADDITIONAL_SETTINGS'
		),
		'MAP_CONTROL_FULLSCREEN_BUTTON' => array(
            'NAME' => GetMessage('2GIS_PARAM_ENABLE_FULLSCREEN_BUTTON'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'PARENT' => 'ADDITIONAL_SETTINGS'
		),
		'MAP_CONTROL_GEOCLICKER' => array(
            'NAME' => GetMessage('2GIS_PARAM_ENABLE_GEOCLICKER'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'ADDITIONAL_SETTINGS'
		),
		'MAP_CONTROL_RIGHTBUTTON_MAGNIFIER' => array(
            'NAME' => GetMessage('2GIS_PARAM_ENABLE_RIGHTBUTTON_MAGNIFIER'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'PARENT' => 'ADDITIONAL_SETTINGS'
		)
    ),
);


//CIBlockParameters::AddPagerSettings( $arComponentParameters, GetMessage("2GIS_DESC_PAGER_NEWS"), true, true );

?>
