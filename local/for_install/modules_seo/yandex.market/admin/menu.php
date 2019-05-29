<?
/** @global CMain$APPLICATION */
use Bitrix\Main\Localization\Loc;

if ($USER->IsAdmin())
{
	Loc::loadMessages(__FILE__);

	return array(
		"parent_menu" => "global_menu_services",
		"section" => "yamarket",
		"sort" => 1000,
		"text" => Loc::getMessage("YANDEX_MARKET_MENU_CONTROL"),
		"title" => Loc::getMessage("YANDEX_MARKET_MENU_TITLE"),
		"icon" => "sale_menu_icon_marketplace",
		"items_id" => "menu_yamarket",
		"items" => array(
			array(
				"text" => Loc::getMessage("YANDEX_MARKET_MENU_SETTINGS"),
				"title" => Loc::getMessage("YANDEX_MARKET_MENU_SETTINGS"),
				"url" => "yamarket_setup_list.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"yamarket_setup_list.php",
					"yamarket_setup_edit.php",
					"yamarket_setup_run.php",
				)
			),
			array(
				"text" => Loc::getMessage("YANDEX_MARKET_MENU_LOG"),
				"title" => Loc::getMessage("YANDEX_MARKET_MENU_LOG"),
				"url" => "yamarket_log.php?lang=".LANGUAGE_ID,
				"more_url" => array()
			),
			array(
				"text" => Loc::getMessage("YANDEX_MARKET_MENU_HELP"),
				"title" => Loc::getMessage("YANDEX_MARKET_MENU_HELP"),
				"url" => "javascript:window.open('https://yandex.ru/support/market-cms/', '_blank');void(0);",
				"more_url" => array()
			)
		)
	);
}
else
{
	return false;
}