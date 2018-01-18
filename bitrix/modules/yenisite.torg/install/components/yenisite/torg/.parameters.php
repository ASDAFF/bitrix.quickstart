<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"ClientId" =>array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ClientId"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
	),
);

$arComponentParameters["PARAMETERS"]["type_url"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("type_url"),
                        "TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
                                'type_url_1' => GetMessage("type_url_1"),
				'type_url_2' => GetMessage("type_url_2"),

                        ),
			"REFRESH" => "Y",
);


if($arCurrentValues["type_url"] === "type_url_2") { 
    
    $arComponentParameters["PARAMETERS"]["param"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("param"),
                        "TYPE" => "String",
			"DEFAULT" => '',
);
}

$arComponentParameters["PARAMETERS"]["onpage"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("onpage"),
                        "TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
                                '5' => GetMessage("onpage_5"),
				'10' => GetMessage("onpage_10"),
                                '15' => GetMessage("onpage_15"),
				'20' => GetMessage("onpage_20"),
                        ),
			"REFRESH" => "Y",
);

$arComponentParameters["PARAMETERS"]["pager"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("pager"),
                        "TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
                                '1' => GetMessage("pager_1"),
				'2' => GetMessage("pager_2"),

                        ),
			"REFRESH" => "Y",
);

$arComponentParameters["PARAMETERS"]["font_color"] = array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("font_color"),
                        "TYPE" => "COLORPICKER",
                        "DEFAULT" => "#000000",
);

$arComponentParameters["PARAMETERS"]["background_color"] = array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("background_color"),
                        "TYPE" => "COLORPICKER",
                        "DEFAULT" => "#FFFFFF",
);

