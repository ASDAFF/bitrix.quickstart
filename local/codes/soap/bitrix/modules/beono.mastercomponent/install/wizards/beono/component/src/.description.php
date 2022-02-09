<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "#name#",
	"DESCRIPTION" => "#description#",
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "#path_id#", // for example "my_project"
		/*"CHILD" => array(
			"ID" => "#path_child_id#", // for example "my_project:services"
			"NAME" => "#path_child_name#",  // for example "Services"
		),*/
	),
	"COMPLEX" => "#type#",
);

?>