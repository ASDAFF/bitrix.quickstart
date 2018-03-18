<?
######################################################
# Name: energosoft.slider                            #
# File: .description.php                             #
# (c) 2005-2012 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ENERGOSOFT_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("ENERGOSOFT_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "ENERGOSOFT",
		"NAME" => GetMessage("ENERGOSOFT"),
		"CHILD" => array(
			"ID" => "ENERGOSOFT_MULTIMEDIA",
			"NAME" => GetMessage("ENERGOSOFT_GROUP"),
			"SORT" => 30,
		),
	),
);
?>