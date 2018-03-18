<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arComponentDescription = array(
	"NAME"			 => GetMessage("INFOSPICE_FAVORITES_IZBRANNOE"),
	"DESCRIPTION"	 => GetMessage("INFOSPICE_FAVORITES_VYVODIT_MENU_IZBRANN"),
	"ICON"			 => "/images/news_list.gif",
	"SORT"			 => 20,
	"CACHE_PATH"	 => "Y",
	"PATH"			 => array(
		"ID"	 => "infospice",
		"NAME"	 => GetMessage("INFOSPICE_FAVORITES_KOMPONENTY") . " infospice",
	),
	"COMPLEX"		 => "N",
);
?>