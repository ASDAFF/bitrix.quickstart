<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = array(
	"NAME"        => Loc::GetMessage("webes_ic_NAME"),
	"DESCRIPTION" => Loc::GetMessage("webes_ic_DESCRIPTION"),
	"ICON"        => "/images/webes.png",
	"PATH"        => array(
		"ID"   => Loc::GetMessage("webes_ic_PATH_ID"),
		"NAME" => Loc::GetMessage("webes_ic_PATH_NAME"),
	),
);
?>