<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(

		"POSITION" => Array(
			"NAME"=>GetMessage("SZD_SHAREPLUSO_PARAMETERS_POSITION"),
			"TYPE" => "LIST",
			"DEFAULT"=>'in_place',
			"VALUES" => Array(
                "in_place" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_POSITION_IN_PLACE"), 
                "top_left" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_POSITION_TOP_LEFT"), 
                "top_right" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_POSITION_TOP_RIGHT"), 
                "bottom_left" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_POSITION_BOTTOM_LEFT"), 
                "bottom_right" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_POSITION_BOTTOM_RIGHT"), 
                ),
			"PARENT" => "BASE",
			"COLS" => 45
		),
    
		"THEME" => Array(
			"NAME"=>GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME"),
			"TYPE" => "LIST",
			"DEFAULT"=>'01',
			"VALUES" => Array(
                "01" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME_01"), 
                "02" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME_02"), 
                "03" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME_03"), 
                "04" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME_04"), 
                "05" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME_05"), 
                "06" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME_06"), 
                "07" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME_07"), 
                "08" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_THEME_08"), 
                ),
			"PARENT" => "BASE",
			"COLS" => 45
		),

		"SIZE" => Array(
			"NAME"=>GetMessage("SZD_SHAREPLUSO_PARAMETERS_SIZE"),
			"TYPE" => "LIST",
			"DEFAULT"=>'big',
			"VALUES" => Array(
                "big" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_SIZE_BIG"), 
                "medium" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_SIZE_MEDIUM"), 
                "small" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_SIZE_SMALL"), 
                ),
			"PARENT" => "BASE",
			"COLS" => 45
		),

		"FORM" => Array(
			"NAME"=>GetMessage("SZD_SHAREPLUSO_PARAMETERS_FORM"),
			"TYPE" => "LIST",
			"DEFAULT"=>'square',
			"VALUES" => Array(
                "square" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_FORM_SQUARE"), 
                "round" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_FORM_ROUND"), 
                ),
			"PARENT" => "BASE",
			"COLS" => 45
		),

		"LINE" => Array(
			"NAME"=>GetMessage("SZD_SHAREPLUSO_PARAMETERS_LINE"),
			"TYPE" => "LIST",
			"DEFAULT"=>'line',
			"VALUES" => Array(
                "line" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_LINE_1"), 
                "multiline" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_LINE_2"), 
                ),
			"PARENT" => "BASE",
			"COLS" => 45
		),

		"PLACEMENT" => Array(
			"NAME"=>GetMessage("SZD_SHAREPLUSO_PARAMETERS_PLACEMENT"),
			"TYPE" => "LIST",
			"DEFAULT"=>'horizontal',
			"VALUES" => Array(
                "horizontal" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_PLACEMENT_HORIZONTAL"), 
                "vertical" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_PLACEMENT_VERTICAL"), 
                ),
			"PARENT" => "BASE",
			"COLS" => 45
		),

		"COUNTER" => Array(
			"NAME"=>GetMessage("SZD_SHAREPLUSO_PARAMETERS_COUNTER"),
			"TYPE" => "LIST",
			"DEFAULT"=>'counter',
			"VALUES" => Array(
                "counter" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_COUNTER_YES"), 
                "nocounter" => GetMessage("SZD_SHAREPLUSO_PARAMETERS_COUNTER_NO"), 
                ),
			"PARENT" => "BASE",
			"COLS" => 45
		),

		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
	),
);
?>