<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME"),
	"DESCRIPTION" => GetMessage("DESCRIPTION"),
	"ICON" => '/images/icon.gif',
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "service",
	    "CHILD" => array(
	    	"ID" => "vkwidgets",
	        "NAME" => GetMessage("PATH_NAME")
	    )
	),
);
?>
