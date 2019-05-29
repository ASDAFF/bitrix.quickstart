<?php

if( CModule::IncludeModule('iblock') )
{
	global $APPLICATION;
	
	//$APPLICATION->RestartBuffer();
	//echo '<pre>'.print_r( $arParams, true ).'</pre>';
    //exit;

	$MAP2GIS_ID = str_replace( '.', '', uniqid( 'map2g_', true ) );

    // validate params (bitrix fast-component-add-fix) - when no params, even no defaults!
    if( !isset($arParams) ) $arParams = array(); // !!!
    if( !isset($arParams['MAP_WIDTH']) ) $arParams['MAP_WIDTH'] = 500;
    if( !isset($arParams['MAP_HEIGHT']) ) $arParams['MAP_HEIGHT'] = 400;
    if( !isset($arParams['MAP_CONTROL_ZOOM']) ) $arParams['MAP_CONTROL_ZOOM'] = 'N';
    if( !isset($arParams['MAP_CONTROL_DBLCLICK_ZOOM']) ) $arParams['MAP_CONTROL_DBLCLICK_ZOOM'] = 'Y';
    if( !isset($arParams['MAP_CONTROL_FULLSCREEN_BUTTON']) ) $arParams['MAP_CONTROL_FULLSCREEN_BUTTON'] = 'Y';
    if( !isset($arParams['MAP_CONTROL_GEOCLICKER']) ) $arParams['MAP_CONTROL_GEOCLICKER'] = 'N';
    if( !isset($arParams['MAP_CONTROL_RIGHTBUTTON_MAGNIFIER']) ) $arParams['MAP_CONTROL_RIGHTBUTTON_MAGNIFIER'] = 'N';
    if( !isset($arParams['MAP_ZOOM']) ) $arParams['MAP_ZOOM'] = 10;
    if( !isset($arParams['CENTER_POINT_LAT']) ) $arParams['CENTER_POINT_LAT'] = 55.7383;
    if( !isset($arParams['CENTER_POINT_LON']) ) $arParams['CENTER_POINT_LON'] = 37.5946;
    if( !isset($arParams['~MAP_DATA']) ) $arParams['~MAP_DATA'] = serialize( null ); // default
    // validate params end

	$arResult = array(
        // map unique id-prefix
		'MAP2GIS_ID'                        => $MAP2GIS_ID,
        // map settings
        'MAP_WIDTH'                         => $arParams['MAP_WIDTH'],
        'MAP_HEIGHT'                        => $arParams['MAP_HEIGHT'],
        'MAP_CONTROL_ZOOM'                  => $arParams['MAP_CONTROL_ZOOM'],
        'MAP_CONTROL_DBLCLICK_ZOOM'         => $arParams['MAP_CONTROL_DBLCLICK_ZOOM'],
        'MAP_CONTROL_FULLSCREEN_BUTTON'     => $arParams['MAP_CONTROL_FULLSCREEN_BUTTON'],
        'MAP_CONTROL_GEOCLICKER'            => $arParams['MAP_CONTROL_GEOCLICKER'],
        'MAP_CONTROL_RIGHTBUTTON_MAGNIFIER' => $arParams['MAP_CONTROL_RIGHTBUTTON_MAGNIFIER'],
        // starting position
		'MAP_ZOOM'                          => $arParams['MAP_ZOOM'],
		'CENTER_POINT_LAT'                  => 54.739074926162, // set later below
		'CENTER_POINT_LON'                  => 55.98310804367,  // set later below
		// map markers/points
		'POINTS'                            => array(),         // set later below
        'POINTS_COUNT'                      => 0                // set later below
	);

	// fill points
    $MAP_DATA = @unserialize( $arParams['~MAP_DATA'] ); // hola-hola
    if( is_array($MAP_DATA) )
    {
        $arResult['CENTER_POINT_LAT'] = $MAP_DATA['LAT'];
        $arResult['CENTER_POINT_LON'] = $MAP_DATA['LON'];
        $arResult['MAP_ZOOM']         = $MAP_DATA['SCALE'];
        $arResult['POINTS']           = $MAP_DATA['PLACEMARKS']; // array = array
        $arResult['POINTS_COUNT']     = count( $arResult['POINTS'] );
    }
    
    //$APPLICATION->RestartBuffer();
	//echo '<pre>'.print_r( $arResult, true ).'</pre>';
	//exit;
	
	$this->IncludeComponentTemplate();
}
else
{
	ShowError( GetMessage('IBLOCK_MODULE_NOT_INSTALLED') );
}

/* arREsult = Array(
    [MAP2GIS_ID] => map2g_5090e2186c1fd074353525
    [MAP_WIDTH] => 500
    [MAP_HEIGHT] => 400
    [MAP_CONTROL_ZOOM] => Y
    [MAP_CONTROL_DBLCLICK_ZOOM] => N
    [MAP_CONTROL_FULLSCREEN_BUTTON] => Y
    [MAP_CONTROL_GEOCLICKER] => N
    [MAP_CONTROL_RIGHTBUTTON_MAGNIFIER] => Y
    [MAP_ZOOM] => 13
    [CENTER_POINT_LAT] => 55.748252737423
    [CENTER_POINT_LON] => 37.617259301757
    [POINTS] => Array
        (
            [0] => Array
                (
                    [TEXT] => hghgf
                    [LAT] => 55.763128794572
                    [LON] => 37.635798730469
                ) ... */

?>
