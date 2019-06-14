<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$arComponentParameters = array(

"GROUPS" => array( 
	"RSS_LINK" => array(
		"NAME" => GetMessage("SETTINGS_NEWS"), 
	), 

),
		
	"PARAMETERS" => array(
		"NEWS_BASE" => array(
			"PARENT" => "RSS_LINK", 
			"NAME" => GetMessage("LINK_NEWS"),  
			"ADDITIONAL_VALUES" => "Y"
						 
		),
		
		"NEWS_COUNT" => array(
			"PARENT" => "RSS_LINK", 
			"NAME" => GetMessage("NUMBER_NEWS"), 
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "3"
		),	
		
		"CACHE_TIME" => array("DEFAULT" => "86400"),
	),
);
?>