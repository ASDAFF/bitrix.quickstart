<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("mlife.asz");

$POST_RIGHT = $APPLICATION->GetGroupRight("mlife.asz");
if($POST_RIGHT == "D") {
$FilterSiteId = false;
$arSites = \Mlife\Asz\Functions::GetGroupRightSiteId();
	if(count($arSites)>0) $FilterSiteId = $arSites;
	if($FilterSiteId) $POST_RIGHT = "W";
}

$aMenu = Array(
	array(
		"parent_menu" => "global_menu_mlifeasz",
		"section" => "mlife_asz_sett",
		"sort" => 100,
		"module_id" => "mlife.asz",
		"text" => GetMessage("MLIFE_ASZ_MENU_L1"),
		"title" => GetMessage("MLIFE_ASZ_MENU_L1"),
		"items_id" => "mlife_asz_sett",
		"items" => array(
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L2"),
				"url" => "mlife_asz_curency.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_curency_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L2"),
				"sort" => 100,
			),
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L3"),
				"url" => "mlife_asz_price.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_price_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L3"),
				"sort" => 102,
			),
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L4"),
				"url" => "mlife_asz_orderstatus.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_orderstatus_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L4"),
				"sort" => 104,
			),
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L5"),
				"url" => "mlife_asz_country.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_country_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L5"),
				"sort" => 106,
			),
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L6"),
				"url" => "mlife_asz_state.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_state_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L6"),
				"sort" => 108,
			),
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L7"),
				"url" => "mlife_asz_paysystem.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_paysystem_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L7"),
				"sort" => 110,
			),
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L8"),
				"url" => "mlife_asz_delivery.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_delivery_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L8"),
				"sort" => 112,
			),
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L9"),
				"url" => "mlife_asz_orderprops.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_orderprops_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L9"),
				"sort" => 114,
			),
		),
	),
	array(
		"parent_menu" => "global_menu_mlifeasz",
		"section" => "mlife_asz_zakaz",
		"sort" => 100,
		"module_id" => "mlife.asz",
		"text" => GetMessage("MLIFE_ASZ_MENU_L10"),
		"title" => GetMessage("MLIFE_ASZ_MENU_L10"),
		"items_id" => "mlife_asz_zakaz",
		"items" => array(
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L11"),
				"url" => "mlife_asz_orderlist.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_order_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L11"),
				"sort" => 100,
			),
		),
	),
	array(
		"parent_menu" => "global_menu_mlifeasz",
		"section" => "mlife_asz_discount",
		"sort" => 110,
		"module_id" => "mlife.asz",
		"text" => GetMessage("MLIFE_ASZ_MENU_L15"),
		"title" => GetMessage("MLIFE_ASZ_MENU_L15"),
		"items_id" => "mlife_asz_discount",
		"items" => array(
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L16"),
				"url" => "mlife_asz_discount.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_discount_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L16"),
				"sort" => 110,
			),
		),
	),
	array(
		"parent_menu" => "global_menu_mlifeasz",
		"section" => "mlife_asz_metafilter",
		"sort" => 120,
		"module_id" => "mlife.asz",
		"text" => GetMessage("MLIFE_ASZ_MENU_L35"),
		"title" => GetMessage("MLIFE_ASZ_MENU_L35"),
		"items_id" => "mlife_asz_metafilter",
		"items" => array(
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L36"),
				"url" => "mlife_asz_metafilter.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_metafilter_edit.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L36"),
				"sort" => 120,
			),
		),
	),
	/*
	array(
		"parent_menu" => "global_menu_mlifeasz",
		"section" => "mlife_asz_zakaz",
		"sort" => 100,
		"module_id" => "mlife.asz",
		"text" => "Остатки",
		"title" => "Остатки",
		"items_id" => "mlife_asz_quant",
		"items" => array(
			array(
				"text" => "Список остатков",
				"url" => "mlife_asz_quant.php?lang=".LANGUAGE_ID,
				"more_url" => Array("mlife_asz_quant_edit.php?lang=".LANGUAGE_ID),
				"title" => "Список остатков",
				"sort" => 100,
			),
		),
	),
	*/
);
$aMenu[] = array(
		"parent_menu" => "global_menu_mlifeasz",
		"section" => "mlife_asz_data",
		"sort" => 100,
		"module_id" => "mlife.asz",
		"text" => GetMessage("MLIFE_ASZ_MENU_L12"),
		"title" => GetMessage("MLIFE_ASZ_MENU_L12"),
		"items_id" => "mlife_asz_data",
		"items" => array(
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L13"),
				"url" => "mlife_asz_data_import.php?lang=".LANGUAGE_ID,
				//"more_url" => Array("mlife_asz_data_import.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L13"),
				"sort" => 100,
			),
			array(
				"text" => GetMessage("MLIFE_ASZ_MENU_L14"),
				"url" => "mlife_asz_data_export.php?lang=".LANGUAGE_ID,
				//"more_url" => Array("mlife_asz_data_import.php?lang=".LANGUAGE_ID),
				"title" => GetMessage("MLIFE_ASZ_MENU_L14"),
				"sort" => 100,
			),
		),
	);
if($POST_RIGHT != "D") 
	return $aMenu;
?>
