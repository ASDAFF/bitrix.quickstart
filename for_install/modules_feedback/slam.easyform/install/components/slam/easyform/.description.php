<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME"        => Loc::getMessage("CL_NAME"),
	"DESCRIPTION" => Loc::getMessage("CL_DESCRIPTION"),
	"ICON"        => "/images/cp.png",
	"PATH"        => array(
		"ID"   => "slam",
		"NAME" => Loc::getMessage("CL_NODE_NAME"),
	),
);