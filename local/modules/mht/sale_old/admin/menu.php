<?
IncludeModuleLangFile(__FILE__);
$aMenu = array();
$bViewAll = $USER->CanDoOperation('catalog_read');
$boolVat = $USER->CanDoOperation('catalog_vat');

$boolStore = $USER->CanDoOperation('catalog_store');
$boolGroup = $USER->CanDoOperation('catalog_group');
$boolPrice = $USER->CanDoOperation('catalog_price');
$boolExportEdit = $USER->CanDoOperation('catalog_export_edit');
$boolExportExec = $USER->CanDoOperation('catalog_export_exec');
$boolImportEdit = $USER->CanDoOperation('catalog_import_edit');
$boolImportExec = $USER->CanDoOperation('catalog_import_exec');

global $adminMenu;

if ($APPLICATION->GetGroupRight("sale")!="D")
{
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
	if (CBXFeatures::IsFeatureEnabled('SaleCCards') && COption::GetOptionString("sale", "use_ccards", "N") == "Y")
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
}
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

	if ($APPLICATION->GetGroupRight("sale") == "W")
	{
		$arMenu["items"][] = array(
			"text" => GetMessage("SALE_MENU_DISCOUNT"),
			"title" => GetMessage("SALE_MENU_DISCOUNT_TITLE"),
			"url" => "sale_discount.php?lang=".LANGUAGE_ID,
			"more_url" => array("sale_discount_edit.php"),
		);
	}
	$aMenu[] = $arMenu;
}
	/* Discounts End*/

if ($USER->CanDoOperation('catalog_store') || $bViewAll)
{
	$arMenu = array(
		"parent_menu" => "global_menu_store",
		"sort" => 550,
		"text" => GetMessage("SALE_STORE"),
		"title" => GetMessage("SALE_STORE_DESCR"),
		"icon" => "sale_menu_icon_store",
		"page_icon" => "sale_page_icon_store",
		"items_id" => "menu_catalog_store",
		"items" => array(),
	);
	$aMenu[] = $arMenu;

}

if ($APPLICATION->GetGroupRight("sale") != "D")
{
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
					$dbRepList = Bitrix\Report\ReportTable::getList(array(
						'select' => array('ID', 'TITLE', 'DESCRIPTION'),
						'filter' => array('=CREATED_BY' => $USER->GetID(), '=OWNER_ID' => CBaseSaleReportHelper::getOwners())
					));
					while($arReport = $dbRepList->fetch())
					{
						$arSaleReports[] = array(
							"text" => htmlspecialcharsbx($arReport["TITLE"]),
							"title" => htmlspecialcharsbx($arReport["DESCRIPTION"]),
							"url" => "sale_report_view.php?lang=".LANGUAGE_ID."&ID=".$arReport["ID"],
							"more_url" => array("sale_report_construct.php?lang=".LANGUAGE_ID."&ID=".$arReport["ID"]),
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
}

	/* Settings Begin*/
if ($APPLICATION->GetGroupRight("sale") == "W" ||
	$bViewAll || $boolVat || $boolStore || $boolGroup || $boolPrice ||
	$boolExportEdit || $boolExportExec || $boolImportEdit || $boolImportExec
)
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

	if ($APPLICATION->GetGroupRight("sale") == "W")
	{
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
	}

	$arSubItems = array();
	if ($APPLICATION->GetGroupRight("sale") == "W")
	{
		$arSubItems[] = array(
			"text" => GetMessage("sale_menu_taxes"),
			"title" => GetMessage("sale_menu_taxes_title"),
			"url" => "sale_tax.php?lang=".LANGUAGE_ID,
			"more_url" => array("sale_tax_edit.php"),
		);
		$arSubItems[] = array(
			"text" => GetMessage("SALE_TAX_RATE"),
			"title" => GetMessage("SALE_TAX_RATE_DESCR"),
			"url" => "sale_tax_rate.php?lang=".LANGUAGE_ID,
			"more_url" => array("sale_tax_rate_edit.php"),
		);
		$arSubItems[] = array(
			"text" => GetMessage("SALE_TAX_EX"),
			"title" => GetMessage("SALE_TAX_EX_DESCR"),
			"url" => "sale_tax_exempt.php?lang=".LANGUAGE_ID,
			"more_url" => array("sale_tax_exempt_edit.php"),
		);
	}
	if ($APPLICATION->GetGroupRight("sale") == "W" || $bViewAll || $boolVat)
	{
		$arMenu["items"][] = array(
			"text" => GetMessage("SALE_TAX"),
			"title" => GetMessage("SALE_TAX_DESCR"),
			"items_id" => "menu_sale_taxes",
			"items"=> $arSubItems,
		);
	}

	if ($APPLICATION->GetGroupRight("sale") == "W")
	{
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

		if ($APPLICATION->GetGroupRight("sale") == "W" && (LANGUAGE_ID == "ru" || LANGUAGE_ID == "ua"))
		{
			$arMenu["items"][] = array(
				"text" => GetMessage("SALE_YANDEX_MARKET"),
				"title" => GetMessage("SALE_YANDEX_MARKET_DESCR"),
				"url" => "sale_ymarket.php?lang=".LANGUAGE_ID,
				"more_url" => array("sale_ymarket.php"),
			);
		}

	}
	$aMenu[] = $arMenu;
}
	/* Settings End*/

if ($APPLICATION->GetGroupRight("sale") != "D")
{
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
}

if (!empty($aMenu))
	return $aMenu;
else
	return $false;
?>