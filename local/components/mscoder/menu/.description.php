<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage('MENU_DESCRIPTION_NAME'),
	"DESCRIPTION" => Loc::getMessage('MENU_DESCRIPTION_DESCRIPTION'),
	"ICON" => "/images/menu.gif",
	"SORT" => "10",
	"PATH" => array(
		"ID" => "mscoder",
		"NAME" => Loc::getMessage('MENU_DESCRIPTION_GROUP'),
		"SORT" => 1,
		"CHILD" => array(
			"ID" => "other",
			"NAME" => Loc::getMessage('MENU_DESCRIPTION_DIR'),
			"SORT" => 1000
		)
	),
);

?>