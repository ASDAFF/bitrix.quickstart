<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"PROJECT_ID" =>array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PROJECT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "227062",
		),
	),
);

$arComponentParameters["PARAMETERS"]["PROJECT_HOST"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PROJECT_HOST"),
                        "TYPE" => "String",
			"DEFAULT" => 'bitrix.reformal.ru',
);

$arComponentParameters["PARAMETERS"]["TYPE_OF_INTEGRATION"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TYPE_OF_INTEGRATION"),
                        "TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
                                'integral' => GetMessage("TYPE_INTEGRAL"),
				'widget' => GetMessage("TYPE_WIDGET"),
			),
                        "COLS" => "20",
			"DEFAULT" => 'widget',
			"REFRESH" => "Y",
);

if($arCurrentValues["TYPE_OF_INTEGRATION"] != 'integral') 
{
    $arComponentParameters["PARAMETERS"]["TYPE"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TYPE"),
                        "TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
				'link' => GetMessage("TYPE_LINK"),
                                'customizable' => GetMessage("TYPE_CUSTOMIZABLE"),
				'standart' => GetMessage("TYPE_STANDART"),
			),
			"DEFAULT" => 'standart',
                        "REFRESH" => "Y",

);
    

switch($arCurrentValues["TYPE"]) 
{
    case link:
        
        $arComponentParameters["PARAMETERS"]["HEADER_TEXT"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("HEADER_TEXT"),
                        "TYPE" => "String",
			"DEFAULT" => GetMessage("YENISITE_REFORMAL_OTZYVY_I_PREDLOJENIA"),
        );
        
        $arComponentParameters["PARAMETERS"]["FROM_NEW_WINDOW"] = array(
                        "PARENT" => "BASE",
			"NAME" => GetMessage("FROM_NEW_WINDOW"),
                        "TYPE" => "CHECKBOX",
			"DEFAULT" => 'N',
        );
        
    break;

    case customizable:
        
        $arComponentParameters["PARAMETERS"]["TAB_ORIENTATION"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TAB_ORIENTATION"),
                        "TYPE" => "LIST",
                        "MULTIPLE" => "N",
			"VALUES" => array(
				'left' => GetMessage("LEFT"),
				'right' => GetMessage("RIGHT"),
				'top-left' => GetMessage("TOP_LEFT"),
                                'top-right' => GetMessage("TOP_RIGHT"),
				'bottom-left' => GetMessage("BOTTOM_LEFT"),
				'bottom-right' => GetMessage("BOTTOM_RIGHT"),
			),
			"DEFAULT" => 'left',
        );
        
        $arComponentParameters["PARAMETERS"]["TAB_INDENT"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TAB_INDENT"),
                        "TYPE" => "String",
			"DEFAULT" => '50',
        );
        
        $arComponentParameters["PARAMETERS"]["UNITS"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("UNITS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
				'px' => GetMessage("UNITS_PX"),
				'%' => GetMessage("UNITS_PERSENT"),
			),
			"DEFAULT" => 'px',

        );
        
        $arComponentParameters["PARAMETERS"]["TAB_IMAGE_URL"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TAB_IMAGE_URL"),
                        "TYPE" => "String",
			"DEFAULT" => 'image.png',
        );
        
        $arComponentParameters["PARAMETERS"]["FROM_NEW_WINDOW"] = array(
                        "PARENT" => "BASE",
			"NAME" => GetMessage("FROM_NEW_WINDOW"),
                        "TYPE" => "CHECKBOX",
			"DEFAULT" => 'N',
        );
        
    break;

    case standart:
        
        $arComponentParameters["PARAMETERS"]["HEADER_TEXT"] = array(
                        "PARENT" => "BASE",
			"NAME" => GetMessage("HEADER_TEXT"),
                        "TYPE" => "STRING",
			"DEFAULT" => GetMessage("YENISITE_REFORMAL_OTZYVY_I_PREDLOJENIA"),
        );
        
        $arComponentParameters["PARAMETERS"]["TAB_ORIENTATION"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TAB_ORIENTATION"),
                        "TYPE" => "LIST",
                        "MULTIPLE" => "N",
			"VALUES" => array(
				'left' => GetMessage("LEFT"),
				'right' => GetMessage("RIGHT"),
				'top-left' => GetMessage("TOP_LEFT"),
                                'top-right' => GetMessage("TOP_RIGHT"),
				'bottom-left' => GetMessage("BOTTOM_LEFT"),
				'bottom-right' => GetMessage("BOTTOM_RIGHT"),
			),
			"DEFAULT" => 'left',

        );
        
        $arComponentParameters["PARAMETERS"]["TAB_INDENT"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TAB_INDENT"),
                        "TYPE" => "String",
			"DEFAULT" => '50',
        );
        
        $arComponentParameters["PARAMETERS"]["UNITS"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("UNITS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
				'px' => GetMessage("UNITS_PX"),
				'%' => GetMessage("UNITS_PERSENT"),
			),
			"DEFAULT" => 'px',
        );
                
        $arComponentParameters["PARAMETERS"]["TAB_BG_COLOR"] = array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("TAB_BG_COLOR"),
                        "TYPE" => "COLORPICKER",
                        "DEFAULT" => '#F05A00'
        );
           
        $arComponentParameters["PARAMETERS"]["TAB_BORDER_COLOR"] = array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("TAB_BORDER_COLOR"),
                        "TYPE" => "COLORPICKER",
                        "DEFAULT" => '#FFFFFF'
        );

        $arComponentParameters["PARAMETERS"]["TAB_BORDER_WIDTH"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TAB_BORDER_WIDTH"),
                        "TYPE" => "LIST",
                        "MULTIPLE" => "N",
			"VALUES" => array(
				'0' => GetMessage("BORDER_WIDTH_0"),
				'1' => GetMessage("BORDER_WIDTH_1"),
				'2' => GetMessage("BORDER_WIDTH_2"),
			),
			"DEFAULT" => '2',
        );
        
        $arComponentParameters["PARAMETERS"]["ADD_LOGO"] = array(
                        "PARENT" => "BASE",
			"NAME" => GetMessage("ADD_LOGO"),
                        "TYPE" => "CHECKBOX",
			"DEFAULT" => 'Y',
        );
        
        $arComponentParameters["PARAMETERS"]["FROM_NEW_WINDOW"] = array(
                        "PARENT" => "BASE",
			"NAME" => GetMessage("FROM_NEW_WINDOW"),
                        "TYPE" => "CHECKBOX",
			"DEFAULT" => 'N',
        );
   
    break;
}
}
    
?>
