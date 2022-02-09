<?
IncludeModuleLangFile(__FILE__);
$aMenu = Array();
if ($APPLICATION->GetGroupRight("sale")!="D")
{
	CModule::IncludeModule("catalog");
	global $DBType, $adminMenu;
	$bViewAll = $USER->CanDoOperation('catalog_read');
	$aMenu = array();
	/* Orders Begin*/
	$aMenu[] = array(
			"parent_menu" => "global_menu_store",
			"sort" => 100,
			"text" => GetMessage("SALE_ORDERS"),
			"title" => GetMessage("SALE_ORDERS_DESCR"),
			"icon" => "sale_menu_icon_orders",
			"page_icon" => "sale_page_icon_orders",
			"url" => "sale_order.php?lang=".LANGUAGE_ID,
			"more_url" => array(
				"sale_order_detail.php",
				"sale_order_edit.php",
				"sale_order_print.php",
				"sale_order_new.php"
			),
		);
	/* Orders End*/

	/* Catalog Begin*/
	// included in catalog/general/admin.php
	/* Catalog End*/

	/* CRM Begin*/
	if ($APPLICATION->GetGroupRight("sale") == "W")
	{
		$aMenu[] =
			array(
				"parent_menu" => "global_menu_store",
				"sort" => 300,
				"text" => GetMessage("SM_CRM"),
				"title" => GetMessage("SALE_CRM_DESCR"),
				"icon" => "sale_menu_icon_crm",
				"page_icon" => "sale_page_icon_crm",
				"url" => "sale_crm.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"sale_crm.php",
				),
			);
	}
	/* CRM End*/

	/* Buyers Begin*/
	$arMenu = array(
			"parent_menu" => "global_menu_store",
			"sort" => 400,
			"text" => GetMessage("SALE_BUYERS"),
			"title" => GetMessage("SALE_BUYERS"),
			"icon" => "sale_menu_icon_buyers",
			"page_icon" => "sale_page_icon_buyers",
			"items_id" => "menu_sale_buyers",
			"items" => Array(),
		);
	if(CBXFeatures::IsFeatureEnabled('SaleAccounts'))
	{
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_BUYERS_DESCR"),
				"title" => GetMessage("SALE_BUYERS_DESCR"),
				"url" => "sale_buyers.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"sale_buyers_profile.php",
					"sale_buyers_profile_edit.php",
					"sale_buyers_account.php",
					"sale_buyers_user.php",
				),
			);
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_BASKET"),
				"title" => GetMessage("SALE_BASKET"),
				"url" => "sale_basket.php?lang=".LANGUAGE_ID,
			);
	}
	$arMenu["items"][] = array(
		"text" => GetMessage("SM_ACCOUNTS"),
		"title" => GetMessage("SM_ACCOUNTS_ALT"),
		"url" => "sale_account_admin.php?lang=".LANGUAGE_ID,
		"more_url" => array("sale_account_edit.php"),
	);
	$arMenu["items"][] = array(
		"text" => GetMessage("SM_TRANSACT"),
		"title" => GetMessage("SM_TRANSACT"),
		"url" => "sale_transact_admin.php?lang=".LANGUAGE_ID,
		"more_url" => array("sale_transact_edit.php"),
	);

	if(CBXFeatures::IsFeatureEnabled('SaleRecurring'))
	{
		$arMenu["items"][] = array(
			"text" => GetMessage("SM_RENEW"),
			"title" => GetMessage("SM_RENEW_ALT"),
			"url" => "sale_recurring_admin.php?lang=".LANGUAGE_ID,
			"more_url" => array("sale_recurring_edit.php"),
		);
	}
	if (CBXFeatures::IsFeatureEnabled('SaleCCards'))
	{
		$arMenu["items"][] = array(
			"text" => GetMessage("SM_CCARDS"),
			"title" => GetMessage("SM_CCARDS"),
			"url" => "sale_ccards_admin.php?lang=".LANGUAGE_ID,
			"more_url" => array("sale_ccards_edit.php"),
		);
	}

	$aMenu[] = $arMenu;
	/* Buyers End*/

	/* Discounts Begin*/
	if ($APPLICATION->GetGroupRight("sale") == "W" || $USER->CanDoOperation('catalog_discount') || $bViewAll)
	{
		$arMenu =
			array(
				"parent_menu" => "global_menu_store",
				"sort" => 500,
				"text" => GetMessage("CM_DISCOUNTS"),
				"title" => GetMessage("CM_DISCOUNTS"),
				"icon" => "sale_menu_icon_catalog",
				"page_icon" => "sale_page_icon_catalog",
				"items_id" => "menu_sale_discounts",
				"items" => Array(),
			);


		if ($bViewAll || $USER->CanDoOperation('catalog_discount'))
		{
			$arMenu["items"][] = array(
				"text" => GetMessage("CM_DISCOUNTS3"),
				"url" => "/bitrix/admin/cat_discount_admin.php?lang=".LANGUAGE_ID,
				"more_url" => array("/bitrix/admin/cat_discount_edit.php"),
				"title" => GetMessage("CM_DISCOUNTS_ALT2"),
				"readonly" => !$USER->CanDoOperation('catalog_discount'),
			);
		}
		$arMenu["items"][] = array(
				"text" => GetMessage("SM_DISCOUNT"),
				"title" => GetMessage("SALE_DISCOUNT_DESCR"),
				"url" => "sale_discount.php?lang=".LANGUAGE_ID,
				"more_url" => array("sale_discount_edit.php"),
			);
		if ($bViewAll || $USER->CanDoOperation('catalog_discount'))
		{
			if(CBXFeatures::IsFeatureEnabled('CatDiscountSave'))
			{
				$arMenu["items"][] = array(
					"text" => GetMessage("CAT_DISCOUNT_SAVE"),
					"url" => "/bitrix/admin/cat_discsave_admin.php?lang=".LANGUAGE_ID,
					"more_url" => array("/bitrix/admin/cat_discsave_edit.php"),
					"title" => GetMessage("CAT_DISCOUNT_SAVE_DESCR"),
					"readonly" => !$USER->CanDoOperation('catalog_discount')
				);
			}

			$arMenu["items"][] = array(
				"text" => GetMessage("CM_COUPONS"),
				"url" => "/bitrix/admin/cat_discount_coupon.php?lang=".LANGUAGE_ID,
				"more_url" => array("/bitrix/admin/cat_discount_coupon_edit.php"),
				"title" => GetMessage("CM_COUPONS_ALT"),
				"readonly" => !$USER->CanDoOperation('catalog_discount'),
			);
		}
		$aMenu[] = $arMenu;
	}
	/* Discounts End*/

	/* Reports Begin*/
	if(CBXFeatures::IsFeatureEnabled('SaleReports'))
	{
		$arMenu = array(
			"parent_menu" => "global_menu_store",
			"sort" => 600,
			"text" => GetMessage("SALE_REPORTS"),
			"title" => GetMessage("SALE_REPORTS_DESCR"),
			"icon" => "sale_menu_icon_statistic",
			"page_icon" => "sale_page_icon_statistic",
			"items_id" => "menu_sale_stat",
			"items" => array(),
		);

		if(IsModuleInstalled('report'))
		{
			$arSaleReports = array();
			if(method_exists($adminMenu, "IsSectionActive"))
			{
				if($adminMenu->IsSectionActive("menu_sale_report") && CModule::IncludeModule("report"))
				{
					CModule::IncludeModule("sale");
					CBaseSaleReportHelper::initOwners();
					$dbRepList = Bitrix\Report\Report::getList(array(
							'select' => array('ID', 'TITLE', 'DESCRIPTION'),
							'filter' => array('=CREATED_BY' => $USER->GetID(), '=OWNER_ID' => CBaseSaleReportHelper::getOwners())
						));
					while($arReport = $dbRepList->GetNext())
					{
						$arSaleReports[] = Array(
								"text" => $arReport["TITLE"],
								"title" => $arReport["DESCRIPTION"],
								"url" => "sale_report_view.php?lang=".LANGUAGE_ID."&ID=".$arReport["ID"],
							);
					}
				}
			}

			$arMenu["items"][] = array(
				"text" => GetMessage("SALE_REPORTS_DESCR"),
				"title" => GetMessage("SALE_REPORTS_DESCR"),
				"url" => "sale_report.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"sale_report_construct.php",
					"sale_report_view.php"
				),
				"dynamic" => true,
				"module_id" => "sale",
				"items_id" => "menu_sale_report",
				"items" => $arSaleReports,
			);
		}

		$arMenu["items"][] = array(
			"text" => GetMessage("SM1_STAT"),
			"title" => GetMessage("SM1_STAT_ALT"),
			"url" => "sale_stat.php?lang=".LANGUAGE_ID."&set_default=Y",
			"more_url" => array(),
		);
		$arMenu["items"][] = array(
			"text" => GetMessage("SM1_STAT_PRODUCTS"),
			"title" => GetMessage("SM1_STAT_PRODUCTS_ALT"),
			"url" => "sale_stat_products.php?lang=".LANGUAGE_ID."&set_default=Y",
			"more_url" => array(),
		);
		$arMenu["items"][] = array(
			"text" => GetMessage("SM1_STAT_GRAPH"),
			"title" => GetMessage("SM1_STAT_GRAPH_DESCR"),
			"items_id" => "menu_sale_stat_graph",
			"items" => array(
				array(
					"text" => GetMessage("SM1_STAT_GRAPH_QUANTITY"),
					"title" => GetMessage("SM1_STAT_GRAPH_QUANTITY_DESCR"),
					"url" => "sale_stat_graph_index.php?lang=".LANGUAGE_ID."&set_default=Y",
				),
				array(
					"text" => GetMessage("SM1_STAT_GRAPH_MONEY"),
					"title" => GetMessage("SM1_STAT_GRAPH_MONEY_DESCR"),
					"url" => "sale_stat_graph_money.php?lang=".LANGUAGE_ID."&set_default=Y",
				),
			),
		);
		$aMenu[] = $arMenu;
	}
	/* Reports End*/

	/* Settings Begin*/
	if ($APPLICATION->GetGroupRight("sale") == "W")
	{
		$arMenu = array(
				"parent_menu" => "global_menu_store",
				"sort" => 700,
				"text" => GetMessage("SM_SETTINGS"),
				"title"=> GetMessage("SM_SETTINGS"),
				"icon" => "sale_menu_icon",
				"page_icon" => "sale_page_icon",
				"items_id" => "menu_sale_settings",
				"items" => array(),
			);
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_DELIVERY"),
				"title" => GetMessage("SALE_DELIVERY_DESCR"),
				"items_id" => "menu_sale_delivery",
				"items" => array(
					array(
						"text" => GetMessage("SALE_DELIVERY_OLD"),
						"title" => GetMessage("SALE_DELIVERY_OLD_DESCR"),
						"url" => "sale_delivery.php?lang=".LANGUAGE_ID,
						"page_icon" => "sale_page_icon",
						"more_url" => array("sale_delivery_edit.php"),
					),
					array(
						"text" => GetMessage("SALE_DELIVERY_HANDLERS"),
						"title" => GetMessage("SALE_DELIVERY_HANDLERS_DESCR"),
						"url" => "sale_delivery_handlers.php?lang=".LANGUAGE_ID,
						"page_icon" => "sale_page_icon",
						"more_url" => array("sale_delivery_handler_edit.php"),
					),
				),
			);
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_PAY_SYS"),
				"title" => GetMessage("SALE_PAY_SYS_DESCR"),
				"url" => "sale_pay_system.php?lang=".LANGUAGE_ID,
				"more_url" => array("sale_pay_system_edit.php"),
			);
		$arSubItems = array(
				array(
					"text" => GetMessage("sale_menu_taxes"),
					"title" => GetMessage("sale_menu_taxes_title"),
					"url" => "sale_tax.php?lang=".LANGUAGE_ID,
					"more_url" => array("sale_tax_edit.php"),
				),
				array(
					"text" => GetMessage("SALE_TAX_RATE"),
					"title" => GetMessage("SALE_TAX_RATE_DESCR"),
					"url" => "sale_tax_rate.php?lang=".LANGUAGE_ID,
					"more_url" => array("sale_tax_rate_edit.php"),
				),
				array(
					"text" => GetMessage("SALE_TAX_EX"),
					"title" => GetMessage("SALE_TAX_EX_DESCR"),
					"url" => "sale_tax_exempt.php?lang=".LANGUAGE_ID,
					"more_url" => array("sale_tax_exempt_edit.php"),
				)
			);
		if ($bViewAll || $USER->CanDoOperation('catalog_vat'))
		{
			$arSubItems[] = array(
				"text" => GetMessage("VAT"),
				"url" => "/bitrix/admin/cat_vat_admin.php?lang=".LANGUAGE_ID,
				"more_url" => array("/bitrix/admin/cat_vat_edit.php"),
				"title" => GetMessage("VAT_ALT"),
				"readonly" => !$USER->CanDoOperation('catalog_vat'),
			);
		}
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_TAX"),
				"title" => GetMessage("SALE_TAX_DESCR"),
				"items_id" => "menu_sale_taxes",
				"items"=> $arSubItems,
			);
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_PERSON_TYPE"),
				"title" => GetMessage("SALE_PERSON_TYPE_DESCR"),
				"url" => "sale_person_type.php?lang=".LANGUAGE_ID,
				"more_url" => array("sale_person_type_edit.php"),
			);
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_STATUS"),
				"title" => GetMessage("SALE_STATUS_DESCR"),
				"url" => "sale_status.php?lang=".LANGUAGE_ID,
				"more_url" => array("sale_status_edit.php"),
			);
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_ORDER_PROPS"),
				"title" => GetMessage("SALE_ORDER_PROPS_DESCR"),
				"items_id" => "menu_sale_properties",
				"items"=>array(
					array(
						"text" => GetMessage("sale_menu_properties"),
						"title" => GetMessage("sale_menu_properties_title"),
						"url" => "sale_order_props.php?lang=".LANGUAGE_ID,
						"more_url" => array("sale_order_props_edit.php"),
					),
					array(
						"text" => GetMessage("SALE_ORDER_PROPS_GR"),
						"title" => GetMessage("SALE_ORDER_PROPS_GR_DESCR"),
						"url" => "sale_order_props_group.php?lang=".LANGUAGE_ID,
						"more_url" => array("sale_order_props_group_edit.php"),
					),
				),
			);
		$arMenu["items"][] = array(
				"text" => GetMessage("SALE_LOCATION"),
				"title" => GetMessage("SALE_LOCATION_DESCR"),
				"items_id" => "menu_sale_locations",
				"items"=>array(
					array(
						"text" => GetMessage("sale_menu_locations"),
						"title" => GetMessage("sale_menu_locations_title"),
						"url" => "sale_location_admin.php?lang=".LANGUAGE_ID,
						"more_url" => array("sale_location_edit.php"),
					),
					array(
						"text" => GetMessage("SALE_LOCATION_GROUPS"),
						"title" => GetMessage("SALE_LOCATION_GROUPS_DESCR"),
						"url" => "sale_location_group_admin.php?lang=".LANGUAGE_ID,
						"more_url" => array("sale_location_group_edit.php"),
					),
					array(
						"text" => GetMessage("SALE_LOCATION_IMPORT"),
						"title" => GetMessage("SALE_LOCATION_IMPORT_DESCR"),
						"url" => "sale_location_import.php?lang=".LANGUAGE_ID,
					),
				),
			);
		if(CBXFeatures::IsFeatureEnabled('CatMultiStore'))
		{
			if ($bViewAll || $USER->CanDoOperation('catalog_store'))
			{
				$arMenu["items"][] = array(
					"text" => GetMessage("CM_STORE"),
					"url" => "/bitrix/admin/cat_store_list.php?lang=".LANGUAGE_ID,
					"more_url" => array("/bitrix/admin/cat_store_edit.php"),
					"title" => GetMessage("CM_STORE"),
					"readonly" => !$USER->CanDoOperation('catalog_store')
				);
			}
		}

		$arMenu["items"][] = array(
				"text" => GetMessage("MAIN_MENU_1C_INTEGRATION"),
				"title" => GetMessage("MAIN_MENU_1C_INTEGRATION_TITLE"),
				"url" => "1c_admin.php?lang=".LANGUAGE_ID,
				"more_url" => array("1c_admin.php"),
			);
		$arMenu["items"][] = array(
				"text" => GetMessage("MAIN_MENU_REPORT_EDIT"),
				"title" => GetMessage("MAIN_MENU_REPORT_EDIT_TITLE"),
				"url" => "sale_report_edit.php?lang=".LANGUAGE_ID,
				"more_url" => array("sale_report_edit.php"),
			);

		if(CBXFeatures::IsFeatureEnabled('CatMultiPrice'))
		{
			if ($bViewAll || $USER->CanDoOperation('catalog_group'))
			{
				$arMenu["items"][] = array(
					"text" => GetMessage("GROUP"),
					"url" => "/bitrix/admin/cat_group_admin.php?lang=".LANGUAGE_ID,
					"more_url" => array("/bitrix/admin/cat_group_edit.php"),
					"title" => GetMessage("GROUP_ALT"),
					"readonly" => !$USER->CanDoOperation('catalog_group'),
				);
			}

			if ($bViewAll || $USER->CanDoOperation('catalog_price'))
			{

				$arMenu["items"][] = array(
					"text" => GetMessage("EXTRA"),
					"url" => "/bitrix/admin/cat_extra.php?lang=".LANGUAGE_ID,
					"more_url" => array("/bitrix/admin/cat_extra_edit.php"),
					"title" => GetMessage("EXTRA_ALT"),
					"readonly" => !$USER->CanDoOperation('catalog_price'),
				);
			}
		}

		$expItems = array();
		$impItems = array();
		$page = $APPLICATION->GetCurPage();

		if(($bViewAll || $USER->CanDoOperation('catalog_export_edit') || $USER->CanDoOperation('catalog_export_exec')) && method_exists($adminMenu, "IsSectionActive"))
		{
			if($adminMenu->IsSectionActive("mnu_catalog_exp") || $page == "/bitrix/admin/cat_export_setup.php" || $page == "/bitrix/admin/cat_exec_exp.php")
			{
				include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/".$DBType."/catalog_export.php");
				$ce_db_res = CCatalogExport::GetList(array("NAME"=>"ASC", "ID"=>"ASC"), array("IN_MENU"=>"Y"));
				while ($ce_ar_res = $ce_db_res->Fetch())
				{
					if ($ce_ar_res['DEFAULT_PROFILE'] == 'Y')
					{
						$expItems[] = Array(
							"text" 	=> htmlspecialcharsbx((strlen($ce_ar_res["NAME"])>0 ? $ce_ar_res["NAME"] : $ce_ar_res["FILE_NAME"])),
							"url" 	=>	"/bitrix/admin/cat_exec_exp.php?ACT_FILE=".$ce_ar_res["FILE_NAME"]."&ACTION=EXPORT&PROFILE_ID=".$ce_ar_res["ID"]."&lang=".LANGUAGE_ID."&".bitrix_sessid_get(),
							"title"=>GetMessage("CAM_EXPORT_DESCR_EXPORT")." &quot;".htmlspecialcharsbx($ce_ar_res["NAME"])."&quot;",
							"readonly" => !$USER->CanDoOperation('catalog_export_exec'),
						);
					}
					else
					{
						$expItems[] = Array(
							"text" 	=> htmlspecialcharsbx((strlen($ce_ar_res["NAME"])>0 ? $ce_ar_res["NAME"] : $ce_ar_res["FILE_NAME"])),
							"url" 	=>	"/bitrix/admin/cat_export_setup.php?ACT_FILE=".$ce_ar_res["FILE_NAME"]."&ACTION=EXPORT_EDIT&PROFILE_ID=".$ce_ar_res["ID"]."&lang=".LANGUAGE_ID."&".bitrix_sessid_get(),
							"title"=>GetMessage("CAM_EXPORT_DESCR_EDIT")." &quot;".htmlspecialcharsbx($ce_ar_res["NAME"])."&quot;",
							"readonly" => !$USER->CanDoOperation('catalog_export_edit'),
						);
					}
				}
			}
		}

		if(($bViewAll || $USER->CanDoOperation('catalog_import_edit') || $USER->CanDoOperation('catalog_import_exec')) && method_exists($adminMenu, "IsSectionActive"))
		{
			if($adminMenu->IsSectionActive("mnu_catalog_imp") || $page == "/bitrix/admin/cat_import_setup.php" || $page == "/bitrix/admin/cat_exec_imp.php")
			{
				include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/".$DBType."/catalog_import.php");
				$ce_db_res = CCatalogImport::GetList(array("NAME"=>"ASC", "ID"=>"ASC"), array("IN_MENU"=>"Y"));
				while ($ce_ar_res = $ce_db_res->Fetch())
				{
					if ($ce_ar_res['DEFAULT_PROFILE'] == 'Y')
					{
						$impItems[] = Array(
							"text"	=> htmlspecialcharsbx((strlen($ce_ar_res["NAME"])>0 ? $ce_ar_res["NAME"] : $ce_ar_res["FILE_NAME"])),
							"url"	=> "/bitrix/admin/cat_exec_imp.php?ACT_FILE=".$ce_ar_res["FILE_NAME"]."&ACTION=IMPORT&PROFILE_ID=".$ce_ar_res["ID"]."&lang=".LANGUAGE_ID."&".bitrix_sessid_get(),
							"title"=>GetMessage("CAM_IMPORT_DESCR_IMPORT")." &quot;".htmlspecialcharsbx($ce_ar_res["NAME"])."&quot;",
							"readonly" => !$USER->CanDoOperation('catalog_import_exec'),
						);
					}
					else
					{
						$impItems[] = Array(
							"text"	=> htmlspecialcharsbx((strlen($ce_ar_res["NAME"])>0 ? $ce_ar_res["NAME"] : $ce_ar_res["FILE_NAME"])),
							"url"	=> "/bitrix/admin/cat_import_setup.php?ACT_FILE=".$ce_ar_res["FILE_NAME"]."&ACTION=IMPORT_EDIT&PROFILE_ID=".$ce_ar_res["ID"]."&lang=".LANGUAGE_ID."&".bitrix_sessid_get(),
							"title"=>GetMessage("CAM_IMPORT_DESCR_EDIT")." &quot;".htmlspecialcharsbx($ce_ar_res["NAME"])."&quot;",
							"readonly" => !$USER->CanDoOperation('catalog_import_edit'),
						);
					}
				}
			}
		}

		if ($bViewAll || $USER->CanDoOperation('catalog_export_edit') || $USER->CanDoOperation('catalog_export_exec'))
		{
			$arMenu["items"][] = array(
				"text" => GetMessage("SETUP_UNLOAD_DATA"),
				"url" => "/bitrix/admin/cat_export_setup.php?lang=".LANGUAGE_ID,
				"more_url" => array(
					"cat_export_setup_report.php",
				),
				"title" => GetMessage("SETUP_UNLOAD_DATA_ALT"),
				"dynamic"=>true,
				"module_id"=>"sale",
				"items_id"=>"mnu_catalog_exp",
				"readonly" => !$USER->CanDoOperation('catalog_export_edit') && !$USER->CanDoOperation('catalog_export_exec'),
				"items"=>$expItems
			);
		}

		if ($bViewAll || $USER->CanDoOperation('catalog_import_edit') || $USER->CanDoOperation('catalog_import_exec'))
		{
			$arMenu["items"][] = array(
					"text" => GetMessage("SETUP_LOAD_DATA"),
					"url" => "/bitrix/admin/cat_import_setup.php?lang=".LANGUAGE_ID,
					"title" => GetMessage("SETUP_LOAD_DATA_ALT"),
					"dynamic"=>true,
					"module_id"=>"sale",
					"items_id"=>"mnu_catalog_imp",
					"readonly" => !$USER->CanDoOperation('catalog_import_edit') && !$USER->CanDoOperation('catalog_import_exec'),
					"items"=>$impItems
				);
		}
		$aMenu[] = $arMenu;
	}
	/* Settings End*/

	/* Affiliates Begin*/
	if(CBXFeatures::IsFeatureEnabled('SaleAffiliate'))
	{
		$aMenu[] = array(
			"parent_menu" => "global_menu_store",
			"sort" => 800,
			"text" => GetMessage("SM1_AFFILIATES"),
			"title" => GetMessage("SM1_SHOP_AFFILIATES"),
			"icon" => "sale_menu_icon_buyers_affiliate",
			"page_icon" => "sale_page_icon_buyers",
			"items_id" => "menu_sale_affiliates",
			"items" => array(
				array(
					"text" => GetMessage("SM1_AFFILIATES_CALC"),
					"url" => "sale_affiliate_calc.php?lang=".LANGUAGE_ID,
					"more_url" => array(),
					"title" => GetMessage("SM1_AFFILIATES_CALC_ALT")
				),
				array(
					"text" => GetMessage("SM1_AFFILIATES"),
					"url" => "sale_affiliate.php?lang=".LANGUAGE_ID,
					"more_url" => array("sale_affiliate_edit.php"),
					"title" => GetMessage("SM1_SHOP_AFFILIATES")
				),
				array(
					"text" => GetMessage("SM1_AFFILIATES_TRAN"),
					"url" => "sale_affiliate_transact.php?lang=".LANGUAGE_ID,
					"more_url" => array(),
					"title" => GetMessage("SM1_AFFILIATES_TRAN_ALT")
				),
				array(
					"text" => GetMessage("SM1_AFFILIATES_PLAN"),
					"url" => "sale_affiliate_plan.php?lang=".LANGUAGE_ID,
					"more_url" => array("sale_affiliate_plan_edit.php"),
					"title" => GetMessage("SM1_AFFILIATES_PLAN_ALT")
				),
				array(
					"text" => GetMessage("SM1_AFFILIATES_TIER"),
					"url" => "sale_affiliate_tier.php?lang=".LANGUAGE_ID,
					"more_url" => array("sale_affiliate_tier_edit.php"),
					"title" => GetMessage("SM1_AFFILIATES_TIER_ALT")
				),
			),
		);
	}
	/* Affiliates End*/

	return $aMenu;
}
return $false;
?>