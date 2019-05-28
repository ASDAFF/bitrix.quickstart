<?
define("SALE_DEBUG", false); // Debug

global $DBType;

include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/lang/", "/include.php"));

$GLOBALS["SALE_FIELD_TYPES"] = array(
		"CHECKBOX" => GetMessage("SALE_TYPE_CHECKBOX"),
		"TEXT" => GetMessage("SALE_TYPE_TEXT"),
		"SELECT" => GetMessage("SALE_TYPE_SELECT"),
		"MULTISELECT" => GetMessage("SALE_TYPE_MULTISELECT"),
		"TEXTAREA" => GetMessage("SALE_TYPE_TEXTAREA"),
		"LOCATION" => GetMessage("SALE_TYPE_LOCATION"),
		"RADIO" => GetMessage("SALE_TYPE_RADIO"),
		"FILE" => GetMessage("SALE_TYPE_FILE")
	);

if (!CModule::IncludeModule("currency"))
{
//	trigger_error("Currency is not installed");
	return false;
}

// Number of processed recurring records at one time
Define("SALE_PROC_REC_NUM", 3);
// Number of recurring payment attempts
Define("SALE_PROC_REC_ATTEMPTS", 3);
// Time between recurring payment attempts (in seconds)
Define("SALE_PROC_REC_TIME", 43200);

Define("SALE_PROC_REC_FREQUENCY", 7200);
// Owner ID base name used by CSale<etnity_name>ReportHelper clases for managing the reports.
Define("SALE_REPORT_OWNER_ID", 'sale');
//cache orders flag for real-time exhange with 1C
Define("CACHED_b_sale_order", 3600*24);

global $SALE_TIME_PERIOD_TYPES;
$SALE_TIME_PERIOD_TYPES = array(
		"H" => GetMessage("I_PERIOD_HOUR"),
		"D" => GetMessage("I_PERIOD_DAY"),
		"W" => GetMessage("I_PERIOD_WEEK"),
		"M" => GetMessage("I_PERIOD_MONTH"),
		"Q" => GetMessage("I_PERIOD_QUART"),
		"S" => GetMessage("I_PERIOD_SEMIYEAR"),
		"Y" => GetMessage("I_PERIOD_YEAR")
	);

define("SALE_VALUE_PRECISION", 2);
define("SALE_WEIGHT_PRECISION", 3);

define('BX_SALE_MENU_CATALOG_CLEAR', 'Y');

$GLOBALS["AVAILABLE_ORDER_FIELDS"] = array(
		"ID" => array("COLUMN_NAME" => "ID", "NAME" => GetMessage("SI_ORDER_ID"), "SELECT" => "ID,DATE_INSERT", "CUSTOM" => "Y", "SORT" => "ID"),
		"LID" => array("COLUMN_NAME" => GetMessage("SI_SITE"), "NAME" => GetMessage("SI_SITE"), "SELECT" => "LID", "CUSTOM" => "N", "SORT" => "LID"),
		"PERSON_TYPE" => array("COLUMN_NAME" => GetMessage("SI_PAYER_TYPE"), "NAME" => GetMessage("SI_PAYER_TYPE"), "SELECT" => "PERSON_TYPE_ID", "CUSTOM" => "Y", "SORT" => "PERSON_TYPE_ID"),
		"PAYED" => array("COLUMN_NAME" => GetMessage("SI_PAID"), "NAME" => GetMessage("SI_PAID_ORDER"), "SELECT" => "PAYED,DATE_PAYED,EMP_PAYED_ID", "CUSTOM" => "Y", "SORT" => "PAYED"),
		"PAY_VOUCHER_NUM" => array("COLUMN_NAME" => GetMessage("SI_NO_PP"), "NAME" => GetMessage("SI_NO_PP_DOC"), "SELECT" => "PAY_VOUCHER_NUM", "CUSTOM" => "N", "SORT" => "PAY_VOUCHER_NUM"),
		"PAY_VOUCHER_DATE" => array("COLUMN_NAME" => GetMessage("SI_DATE_PP"), "NAME" => GetMessage("SI_DATE_PP_DOC"), "SELECT" => "PAY_VOUCHER_DATE", "CUSTOM" => "N", "SORT" => "PAY_VOUCHER_DATE"),
		"DELIVERY_DOC_NUM" => array("COLUMN_NAME" => GetMessage("SI_DATE_PP_DELIVERY_DOC_NUM"), "NAME" => GetMessage("SI_DATE_PP_DOC_DELIVERY_DOC_NUM"), "SELECT" => "DELIVERY_DOC_NUM", "CUSTOM" => "N", "SORT" => "DELIVERY_DOC_NUM"),
		"DELIVERY_DOC_DATE" => array("COLUMN_NAME" => GetMessage("SI_DATE_PP_DELIVERY_DOC_DATE"), "NAME" => GetMessage("SI_DATE_PP_DOC_DELIVERY_DOC_DATE"), "SELECT" => "DELIVERY_DOC_DATE", "CUSTOM" => "N", "SORT" => "DELIVERY_DOC_DATE"),
		"PAYED" => array("COLUMN_NAME" => GetMessage("SI_PAID"), "NAME" => GetMessage("SI_PAID_ORDER"), "SELECT" => "PAYED,DATE_PAYED,EMP_PAYED_ID", "CUSTOM" => "Y", "SORT" => "PAYED"),
		"CANCELED" => array("COLUMN_NAME" => GetMessage("SI_CANCELED"), "NAME" => GetMessage("SI_CANCELED_ORD"), "SELECT" => "CANCELED,DATE_CANCELED,EMP_CANCELED_ID", "CUSTOM" => "Y", "SORT" => "CANCELED"),
		"STATUS" => array("COLUMN_NAME" => GetMessage("SI_STATUS"), "NAME" => GetMessage("SI_STATUS_ORD"), "SELECT" => "STATUS_ID,DATE_STATUS,EMP_STATUS_ID", "CUSTOM" => "Y", "SORT" => "STATUS_ID"),
		"PRICE_DELIVERY" => array("COLUMN_NAME" => GetMessage("SI_DELIVERY"), "NAME" => GetMessage("SI_DELIVERY"), "SELECT" => "PRICE_DELIVERY,CURRENCY", "CUSTOM" => "Y", "SORT" => "PRICE_DELIVERY"),
		"ALLOW_DELIVERY" => array("COLUMN_NAME" => GetMessage("SI_ALLOW_DELIVERY"), "NAME" => GetMessage("SI_ALLOW_DELIVERY1"), "SELECT" => "ALLOW_DELIVERY,DATE_ALLOW_DELIVERY,EMP_ALLOW_DELIVERY_ID", "CUSTOM" => "Y", "SORT" => "ALLOW_DELIVERY"),
		"PRICE" => array("COLUMN_NAME" => GetMessage("SI_SUM"), "NAME" => GetMessage("SI_SUM_ORD"), "SELECT" => "PRICE,CURRENCY", "CUSTOM" => "Y", "SORT" => "PRICE"),
		"SUM_PAID" => array("COLUMN_NAME" => GetMessage("SI_SUM_PAID"), "NAME" => GetMessage("SI_SUM_PAID1"), "SELECT" => "SUM_PAID,CURRENCY", "CUSTOM" => "Y", "SORT" => "SUM_PAID"),
		"USER" => array("COLUMN_NAME" => GetMessage("SI_BUYER"), "NAME" => GetMessage("SI_BUYER"), "SELECT" => "USER_ID", "CUSTOM" => "Y", "SORT" => "USER_ID"),
		"PAY_SYSTEM" => array("COLUMN_NAME" => GetMessage("SI_PAY_SYS"), "NAME" => GetMessage("SI_PAY_SYS"), "SELECT" => "PAY_SYSTEM_ID", "CUSTOM" => "Y", "SORT" => "PAY_SYSTEM_ID"),
		"DELIVERY" => array("COLUMN_NAME" => GetMessage("SI_DELIVERY_SYS"), "NAME" => GetMessage("SI_DELIVERY_SYS"), "SELECT" => "DELIVERY_ID", "CUSTOM" => "Y", "SORT" => "DELIVERY_ID"),
		"DATE_UPDATE" => array("COLUMN_NAME" => GetMessage("SI_DATE_UPDATE"), "NAME" => GetMessage("SI_DATE_UPDATE"), "SELECT" => "DATE_UPDATE", "CUSTOM" => "N", "SORT" => "DATE_UPDATE"),
		"PS_STATUS" => array("COLUMN_NAME" => GetMessage("SI_PAYMENT_PS"), "NAME" => GetMessage("SI_PS_STATUS"), "SELECT" => "PS_STATUS,PS_RESPONSE_DATE", "CUSTOM" => "N", "SORT" => "PS_STATUS"),
		"PS_SUM" => array("COLUMN_NAME" => GetMessage("SI_PS_SUM"), "NAME" => GetMessage("SI_PS_SUM1"), "SELECT" => "PS_SUM,PS_CURRENCY", "CUSTOM" => "Y", "SORT" => "PS_SUM"),
		"TAX_VALUE" => array("COLUMN_NAME" => GetMessage("SI_TAX"), "NAME" => GetMessage("SI_TAX_SUM"), "SELECT" => "TAX_VALUE,CURRENCY", "CUSTOM" => "Y", "SORT" => "TAX_VALUE"),
		"BASKET" => array("COLUMN_NAME" => GetMessage("SI_ITEMS"), "NAME" => GetMessage("SI_ITEMS_ORD"), "SELECT" => "", "CUSTOM" => "Y", "SORT" => "")
	);

CModule::AddAutoloadClasses(
	"sale",
	array(
		"CSaleDelivery" => $DBType."/delivery.php",
		"CSaleDeliveryHandler" => $DBType."/delivery_handler.php",
		"CSaleDeliveryHelper" => "general/delivery_helper.php",
		"CSaleDelivery2PaySystem" => "general/delivery_2_pay_system.php",
		"CSaleLocation" => $DBType."/location.php",
		"CSaleLocationGroup" => $DBType."/location_group.php",

		"CSaleBasket" => $DBType."/basket.php",
		"CSaleBasketHelper" => "general/basket_helper.php",
		"CSaleUser" => $DBType."/basket.php",

		"CSaleOrder" => $DBType."/order.php",
		"CSaleOrderProps" => $DBType."/order_props.php",
		"CSaleOrderPropsGroup" => $DBType."/order_props_group.php",
		"CSaleOrderPropsValue" => $DBType."/order_props_values.php",
		"CSaleOrderPropsVariant" => $DBType."/order_props_variant.php",
		"CSaleOrderUserProps" => $DBType."/order_user_props.php",
		"CSaleOrderUserPropsValue" => $DBType."/order_user_props_value.php",
		"CSaleOrderTax" => $DBType."/order_tax.php",
		"CSaleOrderHelper" => "general/order_helper.php",

		"CSalePaySystem" => $DBType."/pay_system.php",
		"CSalePaySystemAction" => $DBType."/pay_system_action.php",
		"CSalePaySystemsHelper" => "general/pay_system_helper.php",
		"CSalePaySystemTarif" => "general/pay_system_tarif.php",

		"CSaleTax" => $DBType."/tax.php",
		"CSaleTaxRate" => $DBType."/tax_rate.php",

		"CSalePersonType" => $DBType."/person_type.php",
		"CSaleDiscount" => $DBType."/discount.php",
		"CSaleUserAccount" => $DBType."/user.php",
		"CSaleUserTransact" => $DBType."/user_transact.php",
		"CSaleUserCards" => $DBType."/user_cards.php",
		"CSaleRecurring" => $DBType."/recurring.php",
		"CSaleStatus" => $DBType."/status.php",

		"CSaleLang" => $DBType."/settings.php",
		"CSaleGroupAccessToSite" => $DBType."/settings.php",
		"CSaleGroupAccessToFlag" => $DBType."/settings.php",

		"CSaleAuxiliary" => $DBType."/auxiliary.php",

		"CSaleAffiliate" => $DBType."/affiliate.php",
		"CSaleAffiliatePlan" => $DBType."/affiliate_plan.php",
		"CSaleAffiliatePlanSection" => $DBType."/affiliate_plan_section.php",
		"CSaleAffiliateTier" => $DBType."/affiliate_tier.php",
		"CSaleAffiliateTransact" => $DBType."/affiliate_transact.php",
		"CSaleExport" => $DBType."/export.php",
		"CSaleOrderLoader" => "general/order_loader.php",

		"CSaleMeasure" => "general/measurement.php",
		"CSaleProduct" => $DBType."/product.php",

		"CSaleViewedProduct" => $DBType."/product.php",

		"CSaleHelper" => "general/helper.php",
		"CSaleMobileOrderUtils" => "general/mobile_order.php",
		"CSaleMobileOrderPull" => "general/mobile_order.php",
		"CSaleMobileOrderPush" => "general/mobile_order.php",
		"CSaleMobileOrderFilter" => "general/mobile_order.php",

		"CBaseSaleReportHelper" => "general/sale_report_helper.php",
		"CSaleReportSaleOrderHelper" => "general/sale_report_helper.php",
		"CSaleReportUserHelper" => "general/sale_report_helper.php",
		"CSaleReportSaleFuserHelper" => "general/sale_report_helper.php",

		"IBXSaleProductProvider" => "general/product_provider.php",
		"CSaleStoreBarcode" => $DBType."/store_barcode.php",

		"CSaleOrderChange" => $DBType."/order_change.php",
		"CSaleOrderChangeFormat" => "general/order_change.php",

		"CSaleOrderChange" => $DBType."/order_change.php",
		"CSaleOrderChangeFormat" => "general/order_change.php",

		"Bitrix\\Sale\\OrderTable" => "lib/order.php",
		"Bitrix\\Sale\\BasketTable" => "lib/basket.php",
		"Bitrix\\Sale\\FuserTable" => "lib/fuser.php",
		"Bitrix\\Sale\\StatusLangTable" => "lib/statuslang.php",
		"Bitrix\\Sale\\PaySystemTable" => "lib/paysystem.php",
		"Bitrix\\Sale\\DeliveryTable" => "lib/delivery.php",
		"Bitrix\\Sale\\DeliveryHandlerTable" => "lib/deliveryhandler.php",
		"Bitrix\\Sale\\PersonTypeTable" => "lib/persontype.php",
		"\\Bitrix\\Sale\\OrderTable" => "lib/order.php",
		"\\Bitrix\\Sale\\BasketTable" => "lib/basket.php",
		"\\Bitrix\\Sale\\FuserTable" => "lib/fuser.php",
		"\\Bitrix\\Sale\\StatusLangTable" => "lib/statuslang.php",
		"\\Bitrix\\Sale\\PaySystemTable" => "lib/paysystem.php",
		"\\Bitrix\\Sale\\DeliveryTable" => "lib/delivery.php",
		"\\Bitrix\\Sale\\DeliveryHandlerTable" => "lib/deliveryhandler.php",
		"\\Bitrix\\Sale\\PersonTypeTable" => "lib/persontype.php",
		"CSaleReportSaleGoodsHelper" => "general/sale_report_helper.php",
		"CSaleReportSaleProductHelper" => "general/sale_report_helper.php",
		"Bitrix\\Sale\\ProductTable" => "lib/product.php",
		"Bitrix\\Sale\\GoodsSectionTable" => "lib/goodssection.php",
		"Bitrix\\Sale\\SectionTable" => "lib/section.php",
		"Bitrix\\Sale\\StoreProductTable" => "lib/storeproduct.php",
		"\\Bitrix\\Sale\\ProductTable" => "lib/product.php",
		"\\Bitrix\\Sale\\GoodsSectionTable" => "lib/goodssection.php",
		"\\Bitrix\\Sale\\SectionTable" => "lib/section.php",
		"\\Bitrix\\Sale\\StoreProductTable" => "lib/storeproduct.php",
		"\\Bitrix\\Sale\\SalesZone" => "lib/saleszone.php",
		"Bitrix\\Sale\\Delivery\\OrderDeliveryTable" => "lib/delivery/orderdelivery.php",

		"Bitrix\\Sale\\Product2ProductTable" => "lib/product2product.php",
		"Bitrix\\Sale\\OrderProcessingTable" => "lib/orderprocessing.php",

		"CSaleBasketFilter" => "general/sale_cond.php",
		"CSaleCondCtrlGroup" => "general/sale_cond.php",
		"CSaleCondCtrlBasketGroup" => "general/sale_cond.php",
		"CSaleCondCtrlBasketFields" => "general/sale_cond.php",
		"CSaleCondCtrlBasketProps" => "general/sale_cond.php",
		"CSaleCondCtrlOrderFields" => "general/sale_cond.php",
		"CSaleCondCtrlCommon" => "general/sale_cond.php",
		"CSaleCondTree" => "general/sale_cond.php",
		"CSaleDiscountActionApply" => "general/sale_act.php",
		"CSaleActionCtrlGroup" => "general/sale_act.php",
		"CSaleActionCtrlDelivery" => "general/sale_act.php",
		"CSaleActionCtrlBasketGroup" => "general/sale_act.php",
		"CSaleActionCtrlSubGroup" => "general/sale_act.php",
		"CSaleActionCondCtrlBasketFields" => "general/sale_act.php",
		"CSaleActionTree" => "general/sale_act.php",
		"CSaleDiscountConvert" => "general/discount_convert.php",

		"CSalePdf" => "general/pdf.php",
		"CSaleYMHandler" => "general/ym_handler.php",
		"Bitrix\\Sale\\TradingPlatformTable" => "lib/tradingplatform.php",
		"CSaleYMLocation" => "general/ym_location.php"
	)
);

function GetBasketListSimple($bSkipFUserInit = False)
{
	$fUserID = CSaleBasket::GetBasketUserID($bSkipFUserInit);
	if ($fUserID > 0)
		return CSaleBasket::GetList(
			array("NAME" => "ASC"),
			array("FUSER_ID" => $fUserID, "LID" => SITE_ID, "ORDER_ID" => "NULL")
		);
	else
		return False;
}

function GetBasketList($bSkipFUserInit = False)
{
	$fUserID = CSaleBasket::GetBasketUserID($bSkipFUserInit);
	$arRes = array();
	if ($fUserID > 0)
	{
		$db_res = CSaleBasket::GetList(
			array("NAME" => "ASC"),
			array("FUSER_ID" => $fUserID, "LID" => SITE_ID, "ORDER_ID" => "NULL")
		);
		while ($res = $db_res->GetNext())
		{
			if (strlen($res["CALLBACK_FUNC"]) > 0 || strlen($res["PRODUCT_PROVIDER_CLASS"]) > 0)
			{
				CSaleBasket::UpdatePrice($res["ID"], $res["CALLBACK_FUNC"], $res["MODULE"], $res["PRODUCT_ID"], $res["QUANTITY"], $res["PRODUCT_PROVIDER_CLASS"]);
				$res = CSaleBasket::GetByID($res["ID"]);
			}
			$arRes[] = $res;
		}
	}
	return $arRes;
}

function SaleFormatCurrency($fSum, $strCurrency, $OnlyValue = False)
{
	return CurrencyFormat($fSum, $strCurrency);
}

function AutoPayOrder($ORDER_ID)
{
	$ORDER_ID = IntVal($ORDER_ID);

	$arOrder = CSaleOrder::GetByID($ORDER_ID);
	if (!$arOrder)
		return false;
	if ($arOrder["PS_STATUS"] != "Y")
		return false;
	if ($arOrder["PAYED"] != "N")
		return false;

	if ($arOrder["CURRENCY"] == $arOrder["PS_CURRENCY"]
		&& DoubleVal($arOrder["PRICE"]) == DoubleVal($arOrder["PS_SUM"]))
	{
		if (CSaleOrder::PayOrder($order["ID"], "Y", True, False))
			return true;
	}

	return false;
}

function CurrencyModuleUnInstallSale()
{
	$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SALE_INCLUDE_CURRENCY"), "SALE_DEPENDES_CURRENCY");
	return false;

}

if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/include.php"))
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/include.php");

function PayUserAccountDeliveryOrderCallback($productID, $userID, $bPaid, $orderID, $quantity = 1)
{
	global $DB;

	$productID = IntVal($productID);
	$userID = IntVal($userID);
	$bPaid = ($bPaid ? True : False);
	$orderID = IntVal($orderID);

	if ($userID <= 0)
		return False;

	if ($orderID <= 0)
		return False;

	if (!($arOrder = CSaleOrder::GetByID($orderID)))
		return False;

	$baseLangCurrency = CSaleLang::GetLangCurrency($arOrder["LID"]);
	$arAmount = unserialize(COption::GetOptionString("sale", "pay_amount", 'a:4:{i:1;a:2:{s:6:"AMOUNT";s:2:"10";s:8:"CURRENCY";s:3:"EUR";}i:2;a:2:{s:6:"AMOUNT";s:2:"20";s:8:"CURRENCY";s:3:"EUR";}i:3;a:2:{s:6:"AMOUNT";s:2:"30";s:8:"CURRENCY";s:3:"EUR";}i:4;a:2:{s:6:"AMOUNT";s:2:"40";s:8:"CURRENCY";s:3:"EUR";}}'));
	if (!array_key_exists($productID, $arAmount))
		return False;

	$currentPrice = $arAmount[$productID]["AMOUNT"] * $quantity;
	$currentCurrency = $arAmount[$productID]["CURRENCY"];
	if ($arAmount[$productID]["CURRENCY"] != $baseLangCurrency)
	{
		$currentPrice = CCurrencyRates::ConvertCurrency($arAmount[$productID]["AMOUNT"], $arAmount[$productID]["CURRENCY"], $baseLangCurrency) * $quantity;
		$currentCurrency = $baseLangCurrency;
	}

	if (!CSaleUserAccount::UpdateAccount($userID, ($bPaid ? $currentPrice : -$currentPrice), $currentCurrency, "MANUAL", $orderID, "Payment to user account"))
		return False;

	return True;
}

/*
* Formats user name. Used everywhere in 'sale' module
*
*/
function GetFormatedUserName($USER_ID, $bEnableId = true)
{
	$result = "";
	$USER_ID = IntVal($USER_ID);

	if($USER_ID > 0)
	{
		if (!isset($LOCAL_PAYED_USER_CACHE[$USER_ID]) || !is_array($LOCAL_PAYED_USER_CACHE[$USER_ID]))
		{
			$dbUser = CUser::GetByID($USER_ID);
			if ($arUser = $dbUser->Fetch())
			{
				$LOCAL_PAYED_USER_CACHE[$USER_ID] = CUser::FormatName(
						CSite::GetNameFormat(false),
						array(
							"NAME" => $arUser["NAME"],
							"LAST_NAME" => $arUser["LAST_NAME"],
							"SECOND_NAME" => $arUser["SECOND_NAME"],
							"LOGIN" => $arUser["LOGIN"]
						),
						true, true
					);
			}
		}

		if ($bEnableId)
			$result .= '[<a href="/bitrix/admin/user_edit.php?ID='.$USER_ID.'&lang='.LANGUAGE_ID.'">'.$USER_ID.'</a>] ';

		if (CBXFeatures::IsFeatureEnabled('SaleAccounts'))
			$result .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID='.$USER_ID.'&lang='.LANGUAGE_ID.'">';
		else
			$result .= '<a href="/bitrix/admin/user_edit.php?ID='.$USER_ID.'&lang='.LANGUAGE_ID.'">';

		$result .= $LOCAL_PAYED_USER_CACHE[$USER_ID];
		$result .= '</a>';
	}

	return $result;
}

/*
 * Updates basket item arrays with information about measures from catalog
 * Basically adds MEASURE_TEXT field with the measure name to each basket item array
 *
 * @param array $arBasketItems - array of basket items' arrays
 * @return mixed
 */
function getMeasures($arBasketItems)
{
	if (CModule::IncludeModule("catalog"))
	{
		$arElementId = array();
		foreach ($arBasketItems as $arItem)
			$arElementId[] = $arItem["PRODUCT_ID"];

		if (!empty($arElementId))
		{
			$bUseMeasures = false;
			$arBasket2Measure = array();
			$dbres = CCatalogProduct::GetList(
				array(),
				array("ID" => array_unique($arElementId)),
				false,
				false,
				array("ID", "MEASURE")
			);
			while ($arRes = $dbres->GetNext())
			{
				foreach ($arBasketItems as &$basketItem)
				{
					if ($basketItem["PRODUCT_ID"] == $arRes["ID"] && intval($arRes["MEASURE"]) > 0)
					{
						$bUseMeasures = true;
						$basketItem["MEASURE"] = $arRes["MEASURE"];
						$arBasket2Measure[$basketItem["ID"]] = $arRes["MEASURE"];
					}
				}
				unset($basketItem);
			}
		}

		$arDefaultMeasure = CCatalogMeasure::getDefaultMeasure(true, true);

		if ($bUseMeasures)
		{
			$dbMeasure = CCatalogMeasure::GetList(
				array(),
				array("ID" => array_unique(array_values($arBasket2Measure))),
				false,
				false,
				array("ID", "SYMBOL_RUS", "SYMBOL_INTL")
			);
			while ($arMeasure = $dbMeasure->Fetch())
			{
				foreach ($arBasketItems as &$basketItem)
				{
					if (array_key_exists($basketItem["ID"], $arBasket2Measure) && $arMeasure["ID"] == $arBasket2Measure[$basketItem["ID"]])
					{
						$basketItem["MEASURE_TEXT"] = (LANGUAGE_ID == "ru") ? $arMeasure["SYMBOL_RUS"] : $arMeasure["SYMBOL_INTL"];
					}else
					{
						$basketItem["MEASURE_TEXT"] = (LANGUAGE_ID == "ru") ? $arDefaultMeasure["SYMBOL_RUS"] : $arDefaultMeasure["SYMBOL_INTL"];
					}
				}
				unset($basketItem);
			}
		}else
		{
			foreach ($arBasketItems as &$basketItem)
			{
				if (!array_key_exists($basketItem["ID"], $arBasket2Measure) || !array_key_exists('MEASURE_TEXT', $basketItem) || strlen($basketItem["MEASURE_TEXT"]) == 0)
				{
					$basketItem["MEASURE_TEXT"] = (LANGUAGE_ID == "ru") ? $arDefaultMeasure["SYMBOL_RUS"] : $arDefaultMeasure["SYMBOL_INTL"];
				}
			}
			unset($basketItem);

		}

		return $arBasketItems;
	}
	else
		return false;
}

/*
 * Updates basket items' arrays with information about ratio from catalog
 * Basically adds MEASURE_RATIO field with the ratio coefficient to each basket item array
 *
 * @param array $arBasketItems - array of basket items' arrays
 * @return mixed
 */
function getRatio($arBasketItems)
{
	if (CModule::IncludeModule("catalog"))
	{
		$arElementId = array();
		foreach ($arBasketItems as $arItem)
			$arElementId[] = $arItem["PRODUCT_ID"];

		if (!empty($arElementId))
		{
			$dbRatio = CCatalogMeasureRatio::GetList(array(), array_unique($arElementId));
			while ($arRatio = $dbRatio->GetNext())
			{
				foreach ($arBasketItems as &$basketItem)
				{
					if ($arRatio["PRODUCT_ID"] == $basketItem["PRODUCT_ID"])
						$basketItem["MEASURE_RATIO"] = $arRatio["RATIO"];
				}
			}
			unset($basketItem);

			return $arBasketItems;
		}
	}
}


/*
 * Creates an array of iblock properties for the elements with certain IDs
 *
 * @param array $arElementId - array of element id
 * @param array $arSelect - properties to select
 * @return array - array of properties' values in the form of array("ELEMENT_ID" => array of props)
 */
function getProductProps($arElementId, $arSelect)
{
	if (!CModule::IncludeModule("iblock"))
		return array();

	if (empty($arElementId))
		return array();

	$arProductData = array();
	$arElementData = array();
	$res = CIBlockElement::GetList(
		array(),
		array("=ID" => array_unique($arElementId)),
		false,
		false,
		array("ID", "IBLOCK_ID")
	);
	while ($arElement = $res->GetNext())
		$arElementData[$arElement["IBLOCK_ID"]][] = $arElement["ID"]; // two getlists are used to support 1 and 2 type of iblock properties

	foreach ($arElementData as $iblockId => $arElemId) // todo: possible performance bottleneck
	{
		$res = CIBlockElement::GetList(
			array(),
			array("IBLOCK_ID" => $iblockId, "=ID" => array_unique($arElemId)),
			false,
			false,
			$arSelect
		);
		while ($arElement = $res->GetNext())
		{
			$id = $arElement["ID"];
			foreach ($arElement as $key => $value)
			{
				if (is_array($arProductData[$id])
					&& array_key_exists($key, $arProductData[$id])
					&& !is_array($arProductData[$id][$key])
					&& !in_array($value, explode(", ", $arProductData[$id][$key]))
				) // if we have multiple property value
				{
					$arProductData[$id][$key] .= ", ".$value;
				}
				elseif (empty($arProductData[$id][$key]))
				{
					$arProductData[$id][$key] = $value;
				}
			}
		}
	}

	return $arProductData;
}

?>