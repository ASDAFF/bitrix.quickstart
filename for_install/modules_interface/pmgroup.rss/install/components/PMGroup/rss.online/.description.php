<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
$arComponentDescription = array(
	"NAME"			=> GetMessage("NEWS_SHOW_COMPONENT_NAME"),
	"DESCRIPTION"	=> GetMessage("NEWS_SHOW_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/rss.gif",
	"VERSION" => "1.0.0", 
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => GetMessage("TITLE_PM"),
		"CHILD" => array(
         "ID" => "inform",
         "NAME" => GetMessage("INFORMERS")),		
		),
	);
?>