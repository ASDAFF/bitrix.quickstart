<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
	if (!CModule::IncludeModule("iblock")){return;}

	$arComponentParameters = array(
	   "GROUPS" => array(
	      
		  "OPTION" => array(
	         "NAME" => GetMessage("LW_WIDGET_GROUP_PRM_OPTION"),
	         "SORT"	=> "240"
	      ),
	      
	   ),
		"PARAMETERS" => array(
			
			"MAP_NAME" => Array(
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_MAP_NAME"), 
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("LW_WIDGET_PRM_OPTION_MAP_NAME_DEFAULT"), 
				"PARENT" => "OPTION",
				"SORT"=>"5"
			),
			
			"MAP_ID" => Array(
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_MAP_ID"), 
				"TYPE" => "STRING",
				"DEFAULT" => time(), 
				"PARENT" => "OPTION",
				"SORT"=>"5"
			),
			
			"WIDTH" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_WIDTH"),
				"TYPE" => "STRING",
				"DEFAULT" => "100%",
				"REFRESH" => "N",
				"SORT"=>"10"
			),
			
			"HEIGHT" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_HEIGHT"),
				"TYPE" => "STRING",
				"DEFAULT" => "400px",
				"REFRESH" => "N",
				"SORT"=>"20"
			),

			"CENTER_MAP" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_CENTER_MAP"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"REFRESH" => "N",
				"SORT"=>"30"
			),
			
			"ZOOM_MAP" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_ZOOM_MAP"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"REFRESH" => "N",
				"SORT"=>"40"
			),
			
			"DOUBLE_CLICK_ZOOM" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_DOUBLE_CLICK_ZOOM"),
				"TYPE" => "LIST",
				"ADDITIONAL_VALUES" => "N",
				"VALUES" => array('Y'=>GetMessage("LW_WIDGET_PRM_TEXT_YES"), 'N'=>GetMessage("LW_WIDGET_PRM_TEXT_NO")),
				"REFRESH" => "Y",
				"SORT"=>"50"
			),
			
			"GEOCLICKER" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_GEOCLICKER"),
				"TYPE" => "LIST",
				"ADDITIONAL_VALUES" => "N",
				"VALUES" => array('Y'=>GetMessage("LW_WIDGET_PRM_TEXT_YES"), 'N'=>GetMessage("LW_WIDGET_PRM_TEXT_NO")),
				"REFRESH" => "Y",
				"SORT"=>"60"
			),
			
			"COORDINATES_POINTS" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_COORDINATES_POINTS"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"REFRESH" => "N",
				"SORT"=>"70"
			),
			
			"ICON_POINTS" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_ICON_POINTS").'[PNG, JPEG, JPG]',
				"TYPE" => "FILE",
				"FD_TARGET" => "F",
   				"FD_EXT" => 'jpg,jpeg,png',
   				"FD_UPLOAD" => true,
   				"FD_USE_MEDIALIB" => true,
   				"FD_MEDIALIB_TYPES" => array('video', 'sound', 'image'),
				"REFRESH" => "N",
				"SORT"=>"80"
			),
			
			"POST_POINTS" => array(
				"PARENT" => "OPTION",
				"NAME" => GetMessage("LW_WIDGET_PRM_OPTION_POST_POINTS"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"REFRESH" => "N",
				"SORT"=>"90"
			),

			
		)
	);

?>