<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

try
{
	$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
	$menu = GetMenuTypes($site);

	$arComponentParameters = array(
		"GROUPS" => array(),
		"PARAMETERS" => array(
			"ROOT_MENU_TYPE" => array(
				"NAME" => Loc::getMessage('MENU_DESCRIPTION_ROOT_MENU_TYPE'),
				"TYPE" => "LIST",
				"DEFAULT" => 'left',
				"VALUES" => $menu,
				"PARENT" => "BASE"
			),
			"INNER_MENU_TYPE" => array(
				"NAME" => Loc::getMessage('MENU_DESCRIPTION_INNER_MENU_TYPE'),
				"TYPE" => "LIST",
				"DEFAULT" => 'top',
				"VALUES" => $menu,
				"PARENT" => "BASE"
			),
			"SHOW_ALL" => array(
				"NAME" => Loc::getMessage('MENU_DESCRIPTION_SHOW_ALL'),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
				"PARENT" => "BASE"
			),
			"DIR" => array(
				"NAME" => Loc::getMessage('MENU_DESCRIPTION_DIR'),
				"TYPE" => "STRING",
				"PARENT" => "BASE",
				"DEFAULT" => "/"
			),
			"CACHE_TIME" => Array("DEFAULT" => 3600),
		)
	);
}
catch (Main\LoaderException $e)
{
	ShowError($e -> getMessage());
}
?>
