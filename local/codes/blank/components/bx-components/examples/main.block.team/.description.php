<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage('MAIN_BLOCK_TEAM_DESCRIPTION_NAME'),
	"DESCRIPTION" => Loc::getMessage('MAIN_BLOCK_TEAM_DESCRIPTION_DESCRIPTION'),
	"ICON" => '/images/icon.gif',
	"SORT" => 20,
	"PATH" => array(
		"ID" => 'narkpe',
		"NAME" => Loc::getMessage('MAIN_BLOCK_TEAM_DESCRIPTION_GROUP'),
		"SORT" => 10,
		"CHILD" => array(
			"ID" => 'main',
			"NAME" => Loc::getMessage('MAIN_BLOCK_TEAM_DESCRIPTION_DIR'),
			"SORT" => 10
		)
	),
);

?>