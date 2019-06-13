 <? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
	
		"NUMPOSTS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("NUMPOSTS_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => 10,
			"REFRESH" => "N"
		),
		
		"COLORSCHEME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("COLORSCHEME_NAME"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
				"light" => GetMessage("COLOR_LIGHT"),
				"dark" => GetMessage("COLOR_DARK")
			),
			"DEFAULT" => "light",
			"REFRESH" => "N"
		),
		
		"ORDER_BY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("NAME_ORDER_BY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array(
				"social" => GetMessage("SOCIAL_ORDER"),
				"time" => GetMessage("TIME_ORDER"),
				"reverse_time" => GetMessage("REVERSE_TIME_ORDER")
			),
			"DEFAULT" => "social"
		),
		
		"WIDTH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("NAME_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => 550
		)
	)
);
?>