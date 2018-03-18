<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	'PARAMETERS' => array(
    	'LINK' => array(
            'NAME' => GetMessage("PARAMETERS_LINK_NAME"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'PARENT' => 'BASE',
            'DEFAULT' => 'public20003922',
       	),
	 	"TYPE" => array(
			"NAME" => GetMessage("PARAMETERS_TYPE_NAME"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => array('0'=>GetMessage("PARAMETERS_VALUES_0"), '1'=>GetMessage("PARAMETERS_VALUES_1"), '2'=>GetMessage("PARAMETERS_VALUES_2")),
			"REFRESH" => "Y",
			"PARENT" => "BASE",
		),
    	'WIDTH' => array(
            'NAME' => GetMessage("PARAMETERS_WIDTH_NAME"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'PARENT' => 'VISUAL',
            'DEFAULT' => '220',
       	),
    	'HEIGHT' => array(
            'NAME' => GetMessage("PARAMETERS_HEIGHT_NAME"),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'PARENT' => 'VISUAL',
            'DEFAULT' => '400',
       	),
    	'COLOR_BACKGROUND' => array(
            'NAME' => GetMessage("PARAMETERS_COLOR_BACKGROUND_NAME"),
            'TYPE' => 'COLORPICKER',
            'MULTIPLE' => 'N',
            'PARENT' => 'VISUAL',
            'DEFAULT' => '#FFFFFF',
       	),
    	'COLOR_TEXT' => array(
            'NAME' => GetMessage("PARAMETERS_COLOR_TEXT_NAME"),
            'TYPE' => 'COLORPICKER',
            'MULTIPLE' => 'N',
            'PARENT' => 'VISUAL',
            'DEFAULT' => '#2B587A',
       	),
    	'COLOR_BUTTON' => array(
            'NAME' => GetMessage("PARAMETERS_COLOR_BUTTON_NAME"),
            'TYPE' => 'COLORPICKER',
            'MULTIPLE' => 'N',
            'PARENT' => 'VISUAL',
            'DEFAULT' => '#5B7FA6',
       	),
      "CACHE_TIME" => array(),
	),
);
if ($arCurrentValues['TYPE'] == 2) 
{ 
    $arComponentParameters['PARAMETERS']['WIDE'] = array(
      'NAME' => GetMessage("PARAMETERS_WIDE_NAME"),
      'TYPE' => 'CHECKBOX',
      'MULTIPLE' => 'N',
      'PARENT' => 'VISUAL',
  	);
} 
?>