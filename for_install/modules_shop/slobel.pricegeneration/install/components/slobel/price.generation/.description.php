<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
		"NAME" => GetMessage("NAME"),
		"DESCRIPTION" => GetMessage("DESC"),
		"ICON" => "images/price.gif",
		"CACHE_PATH" => "Y",
		"SORT" => 10,
		"PATH" => array(
				"ID" => "slobel",
				"NAME" => "SLOBEL Studio",
				"CHILD" => array(
						"ID" => "slobel-catalog",
						"NAME" => GetMessage("NAME_CHILD"),
						"SORT" => 10,
				)
		),
);
?>