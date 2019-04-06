<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
	if (!CModule::IncludeModule("iblock")){return;}
	
	$arViews = Array(
		"VIEW_SCHEME" => GetMessage("VIEW_SCHEME"),
		"VIEW_SATELLITE" => GetMessage("VIEW_SATELLITE"), 
		"VIEW_HYBRID" => GetMessage("VIEW_HYBRID")
	);
	
	$arControls = Array(
		"CONTROLS_NONE" => GetMessage("CONTROLS_NONE"),
		"CONTROLS_VIEW_SELECTOR" => GetMessage("CONTROLS_VIEW_SELECTOR"),
		"CONTROLS_ZOOM_CONTROL" => GetMessage("CONTROLS_ZOOM_CONTROL"), 
		/*"CONTROLS_SMALL_ZOOM_CONTROL" => GetMessage("CONTROLS_SMALL_ZOOM_CONTROL"),*/
		/*"CONTROLS_SCALE_LINE" => GetMessage("CONTROLS_SCALE_LINE"),*/
		/*"CONTROLS_MINI_MAP" => GetMessage("CONTROLS_MINI_MAP"),*/
		"CONTROLS_SEARCH" => GetMessage("CONTROLS_SEARCH"),
		"CONTROLS_TRAFFIC" => GetMessage("CONTROLS_TRAFFIC")
	);
	
	$arComponentParameters = array(
	"GROUPS" => array(
		
		"MAP" => array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_MAP"),
			"SORT"	=> "100"
		),
		"ADDITIONAL" => array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_ADDITIONAL"),
			"SORT"	=> "200"
		),
	
	),
	"PARAMETERS" => array(
	
		"MAP_ID" => Array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_MAP_ID"), 
			"TYPE" => "STRING",
			"DEFAULT" => time(), 
			"PARENT" => "MAP",
		),
		
		"MAP_WIDTH" => array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "100%",
			"REFRESH" => "N",
			"PARENT" => "MAP",
		),
		
		"MAP_HEIGHT" => array(			
			"NAME" => GetMessage("LW_WIDGET_YMAP_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "400px",
			"REFRESH" => "N",
			"PARENT" => "MAP",
		),
		
		"MAP_CENTER" => Array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_MAP_CENTER"), 
			"TYPE" => "STRING",
			"DEFAULT" => "56.8378,60.6034", 
			"PARENT" => "MAP"
		),
		
		"MAP_ZOOM" => Array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_MAP_ZOOM"), 
			"TYPE" => "STRING",
			"DEFAULT" => "12", 
			"PARENT" => "MAP"
		),
		
		"MAP_VIEW" => Array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_MAP_VIEW"),
			"TYPE"=>"LIST", 
			"MULTIPLE"=>"N", 
			"VALUES" => $arViews,
			"DEFAULT"=> "VIEW_SCHEME", 
			"COLS"=>25, 
			"PARENT" => "ADDITIONAL",
		),
		
		"MAP_CONTROLS" => Array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_MAP_CONTROLS"),
			"TYPE"=>"LIST", 
			"MULTIPLE"=>"Y", 
			"VALUES" => $arControls,
			"DEFAULT"=> "CONTROLS_NONE", 
			"COLS"=>25, 
			"PARENT" => "ADDITIONAL",
		),
		
		"MAP_POINTS" => Array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_POINTS"), 
			"TYPE" => "STRING",
			"DEFAULT" => "56.8433,60.6443; 56.8125,60.5778", 
			"PARENT" => "ADDITIONAL"
		),
		
		"MAP_POINTS_TEXT" => Array(
			"NAME" => GetMessage("LW_WIDGET_YMAP_POINTS_TEXT"), 
			"TYPE" => "STRING",
			"DEFAULT" => "Завод; Офис", 
			"PARENT" => "ADDITIONAL"
		),
		
	)
	);
?>