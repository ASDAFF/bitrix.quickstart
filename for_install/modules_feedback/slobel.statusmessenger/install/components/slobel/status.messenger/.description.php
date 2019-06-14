<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
		"NAME" => GetMessage("NAME"),
		"DESCRIPTION" => GetMessage("DESC"),
		"ICON" => "images/messenger.gif",
		"CACHE_PATH" => "Y",
		"SORT" => 20,
		"PATH" => array(
				"ID" => "slobel",
				"NAME" => "SLOBEL Studio",
				"CHILD" => array(
						"ID" => "slobel-utilities",
						"NAME" => GetMessage("NAME_CHILD"),
						"SORT" => 90,
				)
		),
);
?>