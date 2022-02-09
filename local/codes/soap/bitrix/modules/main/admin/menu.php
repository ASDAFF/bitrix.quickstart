<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

if(!method_exists($USER, "CanDoOperation"))
	return false;

IncludeModuleLangFile(__FILE__);
global $DBType, $adminMenu, $adminPage;

$aMenu = array();
if($USER->CanDoOperation('view_all_users') || $USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('edit_own_profile') || $USER->CanDoOperation('view_groups') || $USER->CanDoOperation('view_other_settings'))
{
	$aMenu[] = array(
		"parent_menu" => "global_menu_settings",
		"sort" => 50,
		"text" => GetMessage("MAIN_MENU_FAVORITE_HEADER"),
		"title" => GetMessage("MAIN_MENU_FAVORITE_ALT"),
		"url" => "favorite_list.php?lang=".LANGUAGE_ID,
		"more_url" => array("favorite_edit.php"),
		"icon" => "fav_menu_icon_yellow",
		"page_icon" => "fav_page_icon",
	);
}

if($USER->CanDoOperation('view_all_users') || $USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('edit_subordinate_users') || $USER->CanDoOperation('edit_all_users') || $USER->CanDoOperation('view_groups') || $USER->CanDoOperation('view_tasks'))
{
	$array_user_items = array();
	if ($USER->CanDoOperation('view_all_users') || $USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('edit_subordinate_users') || $USER->CanDoOperation('edit_all_users'))
		$array_user_items[] = array(
				"text" => GetMessage("MAIN_MENU_USER_LIST"),
				"url" => "user_admin.php?lang=".LANGUAGE_ID,
				"more_url" => array("user_edit.php"),
				"title" => GetMessage("MAIN_MENU_USERS_ALT"),
			);

	if ($USER->CanDoOperation('view_groups'))
		$array_user_items[] = array(
			"text" => GetMessage("MAIN_MENU_GROUPS"),
			"url" => "group_admin.php?lang=".LANGUAGE_ID,
			"more_url" => array("group_edit.php"),
			"title" => GetMessage("MAIN_MENU_GROUPS_ALT"),
		);

	if ($USER->CanDoOperation('view_tasks'))
		$array_user_items[] = array(
			"text" => GetMessage("MAIN_MENU_TASKS"),
			"url" => "task_admin.php?lang=".LANGUAGE_ID,
			"more_url" => array("task_edit.php"),
			"title" => GetMessage("MAIN_MENU_TASKS_ALT"),
		);

	if ($USER->CanDoOperation('edit_all_users'))
		$array_user_items[] = array(
			"text" => GetMessage("MAIN_MENU_USER_IMPORT"),
			"url" => "user_import.php?lang=".LANGUAGE_ID,
			"title" => GetMessage("MAIN_MENU_USER_IMPORT_ALT"),
		);

	$aMenu[] = array(
		"parent_menu" => "global_menu_settings",
		"section" => "GENERAL",
		"sort" => 100,
		"text" => GetMessage("MAIN_MENU_MANAG"),
		"title" => GetMessage("MAIN_MENU_USERS_ALT"),
		"icon" => "user_menu_icon",
		"page_icon" => "user_page_icon",
		"items_id" => "menu_users",
		"items" => $array_user_items,
	);
}

if($USER->CanDoOperation('edit_own_profile')  && !($USER->CanDoOperation('view_all_users') || $USER->CanDoOperation('view_subordinate_users')))
{
	$aMenu[] = array(
		"parent_menu" => "global_menu_settings",
		"section" => "GENERAL",
		"sort" => 100,
		"text" => GetMessage("MAIN_MENU_PROFILE"),
		"title" => GetMessage("MAIN_MENU_PROFILE_ALT"),
		"icon" => "user_menu_icon",
		"page_icon" => "user_page_icon",
		"url" => "user_edit.php?lang=".LANGUAGE_ID."&amp;ID=".$USER->GetID(),
		"more_url" => array("user_edit.php"),
	);

	$aMenu[] = array(
		"parent_menu" => "global_menu_settings",
		"sort" => 200,
		"text" => GetMessage("MAIN_MENU_INTERFACE"),
		"title" => GetMessage("MAIN_MENU_INTERFACE_TITLE"),
		"icon" => "sys_menu_icon",
		"page_icon" => "sys_page_icon",
		"url" => "user_settings.php?lang=".LANGUAGE_ID,
	);
}

if(!$USER->CanDoOperation('view_other_settings') && $USER->CanDoOperation('lpa_template_edit'))
{
	$aMenu[] = array(
		"parent_menu" => "global_menu_settings",
		"section" => "MAIN",
		"sort" => 1700,
		"text" => GetMessage("MAIN_MENU_SETTINGS"),
		"title" => GetMessage("MAIN_MENU_SETTINGS_TITLE"),
		"icon" => "sys_menu_icon",
		"page_icon" => "sys_page_icon",
		"items_id" => "menu_system",
		"items" => array(
				array(
					"text" => GetMessage("MAIN_MENU_SITES_LIST"),
					"url" => "site_admin.php?lang=".LANGUAGE_ID,
					"more_url" => array("site_edit.php"),
					"title" => GetMessage("MAIN_MENU_SITES_ALT"),
				),
				array(
					"text" => GetMessage("MAIN_MENU_TEMPLATE"),
					"title" => GetMessage("MAIN_MENU_TEMPL_TITLE"),
					"url" => "template_admin.php?lang=".LANGUAGE_ID,
					"more_url" => array(
						"template_edit.php",
						"template_load.php"
					),
				),
			),
		);
}

if($USER->CanDoOperation('view_other_settings'))
{
	$aModuleItems = array();
	if(method_exists($adminMenu, "IsSectionActive"))
	{
		if($adminMenu->IsSectionActive("menu_module_settings") || ($APPLICATION->GetCurPage() == "/bitrix/admin/settings.php" && $_REQUEST["mid_menu"]<>"") || BX_SEARCH_ADMIN === true)
		{
			$adminPage->Init();
			foreach($adminPage->aModules as $module)
			{
				if($module <> "main")
				{
					if($APPLICATION->GetGroupRight($module) < "R")
						continue;

					$ifile = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module."/install/index.php";
					$ofile = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module."/options.php";
					if(!file_exists($ifile) || !file_exists($ofile))
						continue;

					$info = CModule::CreateModuleObject($module);
					$name = $info->MODULE_NAME;
					$sort = $info->MODULE_SORT;
				}
				else
				{
					if(!$USER->CanDoOperation('view_other_settings'))
						continue;
					$name = GetMessage("MAIN_MENU_MAIN_MODULE");
					$sort = -1;
				}

				$aModule = array(
					"text" => $name,
					"url" => "settings.php?lang=".LANGUAGE_ID."&amp;mid=".$module."&amp;mid_menu=1",
					"more_url"=>array("settings.php?lang=".LANGUAGE_ID."&mid=".$module."&mid_menu=1"),
					"title" => GetMessage("MAIN_MENU_MODULE_SETT")." &quot;".$name."&quot;",
					"sort" => $sort,
				);

				if(BX_SEARCH_ADMIN===true)
				{
					$lfile = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module."/lang/".LANGUAGE_ID."/options.php";
					if(file_exists($lfile))
					{
						$aModule["keywords"] = implode(' ', __IncludeLang($lfile, true));
						//$aModule["category"] = "global_menu_settings";
					}
				}

				$aModuleItems[] = $aModule;
			}
			usort($aModuleItems, create_function('$a, $b', 'if($a["sort"] == $b["sort"]) return strcasecmp($a["text"], $b["text"]); return ($a["sort"] < $b["sort"])? -1 : 1;'));
		}
	}

	$aMenu[] = array(
		"parent_menu" => "global_menu_settings",
		"section" => "MAIN",
		"sort" => 1700,
		"text" => GetMessage("MAIN_MENU_SETTINGS"),
		"title" => GetMessage("MAIN_MENU_SETTINGS_TITLE"),
		"icon" => "sys_menu_icon",
		"page_icon" => "sys_page_icon",
		"items_id" => "menu_system",
		"items" => array(
			array(
				"text" => GetMessage("MAIN_MENU_SITES"),
				"title" => GetMessage("MAIN_MENU_SITES_TITLE"),
				"items_id" => "menu_site",
				"items" => array(
					array(
						"text" => GetMessage("MAIN_MENU_SITES_LIST"),
						"url" => "site_admin.php?lang=".LANGUAGE_ID,
						"more_url" => array("site_edit.php"),
						"title" => GetMessage("MAIN_MENU_SITES_ALT"),
					),
					array(
						"text" => GetMessage("MAIN_MENU_TEMPLATE"),
						"title" => GetMessage("MAIN_MENU_TEMPL_TITLE"),
						"url" => "template_admin.php?lang=".LANGUAGE_ID,
						"more_url" => array(
							"template_edit.php",
							"template_load.php"
						),
					),
				),
			),
			array(
				"text" => GetMessage("MAIN_MENU_LANG"),
				"url" => "lang_admin.php?lang=".LANGUAGE_ID,
				"more_url" => array("lang_edit.php"),
				"title" => GetMessage("MAIN_MENU_LANGS_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_EVENT"),
				"title" => GetMessage("MAIN_MENU_EVENT_TITLE"),
				"items_id" => "menu_templates",
				"items" => array(
					array(
						"text" => GetMessage("MAIN_MENU_TEMPLATES"),
						"url" => "message_admin.php?lang=".LANGUAGE_ID,
						"more_url" => array("message_edit.php"),
						"title" => GetMessage("MAIN_MENU_TEMPLATES_ALT"),
					),
					array(
						"text" => GetMessage("MAIN_MENU_EVENT_TYPES"),
						"title" => GetMessage("MAIN_MENU_EVENT_TYPES_TITLE"),
						"url" => "type_admin.php?lang=".LANGUAGE_ID,
						"more_url" => array(
							"type_edit.php"
						),
					),
				),
			),
			array(
				"text" => GetMessage("MAIN_MENU_MODULES"),
				"url" => "module_admin.php?lang=".LANGUAGE_ID,
				"more_url" => array("module_edit.php"),
				"title" => GetMessage("MAIN_MENU_MODULES_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_CACHE"),
				"url" => "cache.php?lang=".LANGUAGE_ID,
				"more_url" => array(),
				"title" => GetMessage("MAIN_MENU_CACHE_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_URLREWRITE"),
				"url" => "urlrewrite_list.php?lang=".LANGUAGE_ID,
				"more_url" => array("urlrewrite_edit.php", "urlrewrite_reindex.php"),
				"title" => GetMessage("MAIN_MENU_URLREWRITE_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_SHORT_URLS"),
				"url" => "short_uri_admin.php?lang=".LANGUAGE_ID,
				"more_url" => array("short_uri_edit.php"),
				"title" => GetMessage("MAIN_MENU_SHORT_URLS_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_WIZARDS"),
				"url" => "wizard_list.php?lang=".LANGUAGE_ID,
				"more_url" => array("wizard_load.php", "wizard_export.php"),
				"title" => GetMessage("MAIN_MENU_WIZARDS_TITLE"),
			),

			array(
				"text" => GetMessage("MAIN_MENU_MODULE_SETTINGS"),
				"url" => "settings.php?lang=".LANGUAGE_ID,
				"title" => GetMessage("MAIN_MENU_SETTINGS_ALT"),
				"dynamic"=>true,
				"module_id"=>"main",
				"items_id"=>"menu_module_settings",
				"items"=>$aModuleItems,
			),
			array(
				"text" => GetMessage("MAIN_MENU_INTERFACE"),
				"url" => "user_settings.php?lang=".LANGUAGE_ID,
				"title" => GetMessage("MAIN_MENU_INTERFACE_TITLE"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_HOTKEYS"),
				"url" => "hot_keys_list.php?lang=".LANGUAGE_ID."",
				"more_url" => array("hot_keys_edit.php","hot_keys_test.php","hot_keys_list.php"),
				"title" => GetMessage("MAIN_MENU_HOTKEYS_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_USER_FIELD"),
				"url" => "userfield_admin.php?lang=".LANGUAGE_ID,
				"title" => GetMessage("MAIN_MENU_USER_FIELD_TITLE"),
				"more_url" => array("userfield_admin.php", "userfield_edit.php"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_CAPTCHA"),
				"url" => "captcha.php?lang=".LANGUAGE_ID,
				"title" => GetMessage("MAIN_MENU_CAPTCHA_TITLE"),
				"more_url" => array("captcha.php"),
			),
		),
	);

	$aMenu[] = array(
		"parent_menu" => "global_menu_settings",
		"section" => "TOOLS",
		"sort" => 1800,
		"text" => GetMessage("MAIN_MENU_TOOLS"),
		"title" => GetMessage("MAIN_MENU_TOOLS_TITLE"),
		"icon" => "util_menu_icon",
		"page_icon" => "util_page_icon",
		"items_id" => "menu_util",
		"items" => array(
			array(
				"text" => GetMessage("MAIN_MENU_SITE_CHECKER"),
				"url" => "site_checker.php?lang=".LANGUAGE_ID,
				"more_url" => array(),
				"title" => GetMessage("MAIN_MENU_SITE_CHECKER_ALT"),
			),
			(!IsModuleInstalled("security")?
				array(
					"text" => GetMessage("MAIN_MENU_FILE_CHECKER"),
					"url" => "file_checker.php?lang=".LANGUAGE_ID,
					"more_url" => array(),
					"title" => GetMessage("MAIN_MENU_FILE_CHECKER_ALT"),
				)
				:null
			),
			array(
				"text" => GetMessage("MAIN_MENU_PHPINFO"),
				"url" => "phpinfo.php?test_var1=AAA&amp;test_var2=BBB",
				"more_url" => array("phpinfo.php"),
				"title" => GetMessage("MAIN_MENU_PHPINFO_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_SQL"),
				"url" => "sql.php?lang=".LANGUAGE_ID."&amp;del_query=Y",
				"more_url" => array("sql.php"),
				"title" => GetMessage("MAIN_MENU_SQL_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_PHP"),
				"url" => "php_command_line.php?lang=".LANGUAGE_ID."",
				"more_url" => array("php_command_line.php"),
				"title" => GetMessage("MAIN_MENU_PHP_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_AGENT"),
				"url" => "agent_list.php?lang=".LANGUAGE_ID,
				"more_url" => array("agent_list.php", "agent_edit.php"),
				"title" => GetMessage("MAIN_MENU_AGENT_ALT"),
			),
			array(
				"text" => GetMessage("MAIN_MENU_DUMP"),
				"title" => GetMessage("MAIN_MENU_DUMP_ALT"),
				"items_id" => "backup",
				"items" => array(
					array(
						"text" => GetMessage("MAIN_MENU_DUMP_NEW"),
						"url" => "dump.php?lang=".LANGUAGE_ID,
						"more_url" => array("dump.php"),
						"title" => GetMessage("MAIN_MENU_DUMP_ALT"),
					),
					array(
						"text" => GetMessage("MAIN_MENU_DUMP_LIST"),
						"url" => "dump_list.php?lang=".LANGUAGE_ID,
						"more_url" => array("dump_list.php"),
						"title" => GetMessage("MAIN_MENU_DUMP_ALT"),
					),
					array(
						"text" => GetMessage("MAIN_MENU_DUMP_AUTO"),
						"url" => "dump_auto.php?lang=".LANGUAGE_ID,
						"more_url" => array("dump_auto.php"),
						"title" => GetMessage("MAIN_MENU_DUMP_ALT"),
					),
				),
			),
			(strtoupper($DBType) == "MYSQL"?
				Array(
					"text" => GetMessage("MAIN_MENU_OPTIMIZE_DB"),
					"url" => "repair_db.php?optimize_tables=Y&lang=".LANGUAGE_ID,
					"more_url" => array(),
					"title" => GetMessage("MAIN_MENU_OPTIMIZE_DB_ALT"),
				)
				:null
			),
			(strtoupper($DBType) == "MYSQL"?
				Array(
					"text" => GetMessage("MAIN_MENU_REPAIR_DB"),
					"url" => "repair_db.php?lang=".LANGUAGE_ID,
					"more_url" => array(),
					"title" => GetMessage("MAIN_MENU_REPAIR_DB_ALT"),
				)
				:null
			),
			($USER->CanDoOperation('view_event_log')?
				Array(
					"text" => GetMessage("MAIN_MENU_EVENT_LOG"),
					"url" => "event_log.php?lang=".LANGUAGE_ID,
					"more_url" => array(),
					"title" => GetMessage("MAIN_MENU_EVENT_LOG_ALT"),
				)
				:null
			),
			($USER->CanDoOperation('view_other_settings')?
				Array(
					"text" => GetMessage("MAIN_MENU_CHECKLIST"),
					"url" => "checklist.php?lang=".LANGUAGE_ID,
					"more_url" => array("checklist_report.php"),
					"title" => GetMessage("MAIN_MENU_CHECKLIST"),
				)
				:null
			),
		),
	);
}
if($USER->CanDoOperation('install_updates'))
{
	$arMarket = array();
	if(method_exists($adminMenu, "IsSectionActive"))
	{
		if($adminMenu->IsSectionActive("menu_marketplace"))
		{
			$CACHE = 60*60*24;
			$obCache = new CPHPCache;
			$cache_id = "main_menu_marketplace_".LANGUAGE_ID;
			if($obCache->InitCache($CACHE, $cache_id, "/"))
			{
				$vars = $obCache->GetVars();
				$arMarket = $vars["arMarket"];
			}
			else
			{
				$ht = new CHTTP();
				if($res = $ht->Get("http://marketplace.1c-bitrix.ru/data_export.php"))
				{
					if(in_array($ht->status, Array("200")))
					{
						$res = $GLOBALS["APPLICATION"]->ConvertCharset($res, "windows-1251", SITE_CHARSET);
						require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");

						$objXML = new CDataXML();
						$objXML->LoadString($res);
						$arResult = $objXML->GetArray();

						if(!empty($arResult) && is_array($arResult))
						{
							if(!empty($arResult["categories"]["#"]["category"]))
							{
								foreach($arResult["categories"]["#"]["category"] as $category)
								{
									$arCategory = Array();
									$arUrls = Array();
									if(!empty($category["#"]["items"][0]["#"]["item"]))
									{
										foreach($category["#"]["items"][0]["#"]["item"] as $catIn)
										{
											$url = "update_system_market.php?category=".$catIn["#"]["id"][0]["#"];
											$arCategory[] = Array(
													"text" => $catIn["#"]["name"][0]["#"]." (".$catIn["#"]["count"][0]["#"].")",
													"title" => GetMessage("MAIN_MENU_MP_CATEGORY")." ".$catIn["#"]["name"][0]["#"],
													"url" => $url."&lang=".LANGUAGE_ID,
												);
											$arUrls[] = $url;
										}
									}
									if(IntVal($category["#"]["id"][0]["#"]) > 0)
									{
										$url = "update_system_market.php?category=".$category["#"]["id"][0]["#"];
										$arUrls[] = $url;
									}
									else
										$url = $arUrls[0];
									$arMarket[] = array(
										"text" => $category["#"]["name"][0]["#"].(IntVal($category["#"]["count"][0]["#"]) > 0 ? " (".$category["#"]["count"][0]["#"].")" : ""),
										"url" => $url."&lang=".LANGUAGE_ID,
										"more_url" => $arUrls,
										"title" => GetMessage("MAIN_MENU_MP_CATEGORY")." ".$category["#"]["name"][0]["#"],
										"items_id" => "menu_update_section_".count($arMarket),
										"items" => $arCategory,
										);
								}
							}
						}
					}
				}
				if($obCache->StartDataCache())
				{
					$arCacheData = array("arMarket" => $arMarket);
					$obCache->EndDataCache($arCacheData);
				}
			}
		}
	}
	$arMarketMenu = array(
		"sort" => 100,
		"parent_menu" => "global_menu_marketplace",
		"icon" => "update_marketplace",
		"page_icon" => "update_marketplace_page_icon",
		"text" => GetMessage("MAIN_MENU_UPDATES_MARKET_CATALOG"),
		"url" => "update_system_market.php?lang=".LANGUAGE_ID,
		"more_url" => array("update_system_market_detail.php"),
		"title" => GetMessage("MAIN_MENU_UPDATES_MARKET_CATALOG_ALT"),
		);
	if(in_array(LANGUAGE_ID, Array("ru", "ua")))
	{
		$arMarketMenu["dynamic"] = true;
		$arMarketMenu["module_id"] = "main";
		$arMarketMenu["items_id"] ="menu_marketplace";
		$arMarketMenu["items"] = $arMarket;
	}

	$aMenu[] = $arMarketMenu;
	$aMenu[] = array(
		"sort" => 200,
		"parent_menu" => "global_menu_marketplace",
		"icon" => "update_menu_icon_partner",
		"page_icon" => "update_page_icon_partner",
		"text" => GetMessage("MAIN_MENU_UPDATES_PARTNER_NEW"),
		"url" => "update_system_partner.php?lang=".LANGUAGE_ID,
		"more_url" => array("update_system_partner.php"),
		"title" => GetMessage("MAIN_MENU_UPDATES_PARTNER_NEW_ALT"),
	);
	$aMenu[] = array(
		"sort" => 300,
		"parent_menu" => "global_menu_marketplace",
		"icon" => "update_marketplace_modules",
		"page_icon" => "update_marketplace_modules_page_icon",
		"text" => GetMessage("MAIN_MENU_UPDATES_PARTNER_MODULES"),
		"url" => "partner_modules.php?lang=".LANGUAGE_ID,
		"more_url" => array("partner_modules.php"),
		"title" => GetMessage("MAIN_MENU_UPDATES_PARTNER_MODULES_ALT"),
	);
	$aMenu[] = array(
		"sort" => 400,
		"parent_menu" => "global_menu_marketplace",
		"icon" => "update_menu_icon",
		"page_icon" => "update_page_icon",
		"text" => GetMessage("MAIN_MENU_UPDATES_NEW"),
		"url" => "update_system.php?lang=".LANGUAGE_ID,
		"more_url" => array("sysupdate_log.php", "sysupdate.php", "update_system.php"),
		"title" => GetMessage("MAIN_MENU_UPDATES_NEW_ALT"),
	);
}
if($USER->CanDoOperation('edit_ratings'))
{
	$aMenu[] = array(
		"parent_menu" => "global_menu_services",
		"section" => "rating",
		"sort" => 300,
		"text" => GetMessage("MAIN_MENU_RATING"),
		"title" => GetMessage("MAIN_MENU_RATING_ALT"),
		"icon" => "rating_menu_icon",
		"page_icon" => "rating_page_icon",
		"items_id" => "menu_rating",
		"items" => array(
			array(
				"page_icon" => "rating_page_icon",
				"text" => GetMessage("MAIN_MENU_RATING_LIST"),
				"title" => GetMessage("MAIN_MENU_RATING_LIST_ALT"),
				"url" => "rating_list.php?lang=".LANGUAGE_ID,
				"more_url" => array("rating_edit.php"),
			),
			array(
				"page_icon" => "rating_rule_page_icon",
				"text" => GetMessage("MAIN_MENU_RATING_RULE_LIST"),
				"title" => GetMessage("MAIN_MENU_RATING_RULE_LIST_ALT"),
				"url" => "rating_rule_list.php?lang=".LANGUAGE_ID,
				"more_url" => array("rating_rule_edit.php"),
			),
			array(
				"page_icon" => "rating_settings_page_icon",
				"text" => GetMessage("MAIN_MENU_RATING_SETTINGS"),
				"title" => GetMessage("MAIN_MENU_RATING_SETTINGS_ALT"),
				"url" => "rating_settings.php?lang=".LANGUAGE_ID,
			),
		),
	);
}

//print_r($aMenu);
return $aMenu;
?>
