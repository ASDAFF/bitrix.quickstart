<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"GROUPS" => array(
			"SCRIPT" => array(
            "NAME" => GetMessage("MLIFE_VKG_SCRIPT_TITLE"),
            "SORT" => "311",
            ),
			"VIASUAl" => array(
            "NAME" => GetMessage("MLIFE_VKG_VIASUAl_TITLE"),
            "SORT" => "310",
            ),
			"IMPORT" => array(
            "NAME" => GetMessage("MLIFE_VKG_IMPORT_TITLE"),
            "SORT" => "309",
            ),
	),
);

$arComponentParameters["PARAMETERS"]["IST"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_IST"),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'G' => GetMessage("MLIFE_VKG_PARAM_ID_IST_VALUE_G"),
				'U' => GetMessage("MLIFE_VKG_PARAM_ID_IST_VALUE_U"),
			),
			'DEFAULT' => 'U',
			"PARENT" => "IMPORT",
		);
$arComponentParameters["PARAMETERS"]["ID_IST"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_ID_IST"),
			'TYPE' => 'TEXT',
			'DEFAULT' => 0,
			"PARENT" => "IMPORT",
		);
$arComponentParameters["PARAMETERS"]["ID_GAL"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_ID_GAL"),
			'TYPE' => 'TEXT',
			'DEFAULT' => 0,
			"PARENT" => "IMPORT",
		);
$arComponentParameters["PARAMETERS"]["PHOTO_SIZES"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_PHOTO_SIZES"),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'1' => GetMessage("MLIFE_VKG_PARAM_YES"),
				'0' => GetMessage("MLIFE_VKG_PARAM_NO"),
			),
			'DEFAULT' => '1',
			"PARENT" => "IMPORT",
			"REFRESH" => "N",
		);
$arComponentParameters["PARAMETERS"]["KOL_PHOTO"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_KOL_PHOTO"),
			'TYPE' => 'TEXT',
			'DEFAULT' => 0,
			"PARENT" => "VIASUAl",
		);
$arComponentParameters["PARAMETERS"]["READMORE"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_READMORE"),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'0' => GetMessage("MLIFE_VKG_PARAM_NO"),
				'1' => GetMessage("MLIFE_VKG_PARAM_YES"),
			),
			'DEFAULT' => 0,
			"PARENT" => "VIASUAl",
		);
$arComponentParameters["PARAMETERS"]["JQUERY"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_JQUERY"),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'0' => GetMessage("MLIFE_VKG_PARAM_NO"),
				'1' => GetMessage("MLIFE_VKG_PARAM_YES"),
			),
			'DEFAULT' => 0,
			"PARENT" => "SCRIPT",
		);
$arComponentParameters["PARAMETERS"]["FANCY"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_FANCY"),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'1' => GetMessage("MLIFE_VKG_PARAM_YES"),
				'0' => GetMessage("MLIFE_VKG_PARAM_NO"),
			),
			'DEFAULT' => '1',
			"PARENT" => "SCRIPT",
		);
$arComponentParameters["PARAMETERS"]["FANCY_TRUMB"] = array(
			'NAME' => GetMessage("FANCY_TRUMB"),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'1' => GetMessage("MLIFE_VKG_PARAM_YES"),
				'0' => GetMessage("MLIFE_VKG_PARAM_NO"),
			),
			'DEFAULT' => '1',
			"PARENT" => "SCRIPT",
		);
$arComponentParameters["PARAMETERS"]["WIEV_TRUMB"] = array(
			'NAME' => GetMessage("MLIFE_VKG_PARAM_FANSY_WIEV_TRUMB"),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'1' => GetMessage("MLIFE_VKG_PARAM_YES"),
				'0' => GetMessage("MLIFE_VKG_PARAM_NO"),
			),
			'DEFAULT' => '1',
			"PARENT" => "VIASUAl",
			"REFRESH" => "Y",
		);
if($arCurrentValues["WIEV_TRUMB"]==1){
$arComponentParameters["PARAMETERS"]["FANCY_TRUMB_WIDTH"] = array(
			'NAME' => GetMessage("MLIFE_VKG_FANCY_TRUMB_WIDTH"),
			'TYPE' => 'TEXT',
			'DEFAULT' => 60,
			"PARENT" => "VIASUAl",
		);
$arComponentParameters["PARAMETERS"]["FANCY_TRUMB_HEIGHT"] = array(
			'NAME' => GetMessage("MLIFE_VKG_FANCY_TRUMB_HEIGHT"),
			'TYPE' => 'TEXT',
			'DEFAULT' => 100,
			"PARENT" => "VIASUAl",
		);
}		
$arComponentParameters["PARAMETERS"]["CACHE_TIME"] = array(
			'DEFAULT'=>3600,
		);



?>