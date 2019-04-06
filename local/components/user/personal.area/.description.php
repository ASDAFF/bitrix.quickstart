<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Личный кабинет",
	"DESCRIPTION" => "Комплексный компонент личного кабинета",
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "FloopyMan", // for example "my_project"
		/*"CHILD" => array(
			"ID" => "", // for example "my_project:services"
			"NAME" => "",  // for example "Services"
		),*/
	),
	"COMPLEX" => "Y",
);

?>