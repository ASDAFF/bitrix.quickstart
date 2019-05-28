<?
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002-2006 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

// include functions
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/admin_tool.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

ClearVars("l_");

$LOCAL_SITE_LIST_CACHE = array();
$LOCAL_PERSON_TYPE_CACHE = array();
$LOCAL_PAYED_USER_CACHE = array();
$LOCAL_PAY_SYSTEM_CACHE = array();
$LOCAL_DELIVERY_CACHE = array();
$LOCAL_STATUS_CACHE = array();

IncludeModuleLangFile(__FILE__);

$arUserGroups = $USER->GetUserGroupArray();
$intUserID = intval($USER->GetID());

$arAccessibleSites = array();
$dbAccessibleSites = CSaleGroupAccessToSite::GetList(
		array(),
		array("GROUP_ID" => $arUserGroups),
		false,
		false,
		array("SITE_ID")
	);
while ($arAccessibleSite = $dbAccessibleSites->Fetch())
{
	if (!in_array($arAccessibleSite["SITE_ID"], $arAccessibleSites))
		$arAccessibleSites[] = $arAccessibleSite["SITE_ID"];
}

$bExport = false;
if($_REQUEST["mode"] == "excel")
	$bExport = true;

$sTableID = "tbl_sale_order";

$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter_universal",
	"filter_id_from",
	"filter_id_to",
	"filter_account_number",
	"filter_tracking_number",
	"filter_date_from",
	"filter_date_to",
	"filter_date_update_from",
	"filter_date_update_to",
	"filter_lang",
	"filter_currency",
	"filter_price_from",
	"filter_price_to",
	"filter_status",
	"filter_date_status_from",
	"filter_date_status_to",
	"filter_payed_from",
	"filter_payed_to",
	"filter_payed",
	"filter_allow_delivery",
	"filter_ps_status",
	"filter_pay_system",
	"filter_canceled",
	"filter_deducted",
	"filter_marked",
	"filter_buyer",
	"filter_product_id",
	"filter_product_xml_id",
	"filter_affiliate_id",
	"filter_date_delivery_from",
	"filter_date_delivery_to",
	"filter_discount_coupon",
	"filter_person_type",
	"filter_user_id",
	"filter_user_login",
	"filter_user_email",
	"filter_group_id",
	"filter_sum_paid",
	"filter_delivery_request_sent",
);

$arOrderProps = array();
$arOrderPropsCode = array();
$dbProps = CSaleOrderProps::GetList(
	array("PERSON_TYPE_ID" => "ASC", "SORT" => "ASC"),
	array(),
	false,
	false,
	array("ID", "NAME", "PERSON_TYPE_NAME", "PERSON_TYPE_ID", "SORT", "IS_FILTERED", "TYPE", "CODE")
);
while ($arProps = $dbProps->GetNext())
{
	if(strlen($arProps["CODE"]) > 0)
	{
		if(empty($arOrderPropsCode[$arProps["CODE"]]))
			$arOrderPropsCode[$arProps["CODE"]] = $arProps;
	}
	else
	{
		$arOrderProps[IntVal($arProps["ID"])] = $arProps;
	}
}

foreach ($arOrderProps as $key => $value)
{
	if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT" && $value["TYPE"] != "FILE")
	{
		$arFilterFields[] = "filter_prop_".$key;
	}
}
foreach ($arOrderPropsCode as $key => $value)
{
	if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT" && $value["TYPE"] != "FILE")
	{
		$arFilterFields[] = "filter_prop_".$key;
	}
}

$lAdmin->InitFilter($arFilterFields);

$filter_lang = Trim($filter_lang);
if (strlen($filter_lang) > 0)
{
	if (!in_array($filter_lang, $arAccessibleSites) && $saleModulePermissions < "W")
		$filter_lang = "";
}

$arFilter = Array();
if (IntVal($filter_id_from)>0) $arFilter[">=ID"] = IntVal($filter_id_from);
if (IntVal($filter_id_to)>0) $arFilter["<=ID"] = IntVal($filter_id_to);
if (strlen($filter_date_from)>0) $arFilter["DATE_FROM"] = Trim($filter_date_from);
if (strlen($filter_date_to)>0)
{
	if ($arDate = ParseDateTime($filter_date_to, CSite::GetDateFormat("FULL", SITE_ID)))
	{
		if (StrLen($filter_date_to) < 11)
		{
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$filter_date_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["DATE_TO"] = $filter_date_to;
	}
	else
	{
		$filter_date_to = "";
	}
}

if (strlen($filter_date_update_from)>0)
{
	$arFilter["DATE_UPDATE_FROM"] = Trim($filter_date_update_from);
}
elseif($set_filter!="Y" && $del_filter != "Y")
{
	$filter_date_update_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_date_update_from = GetTime(time()-86400*COption::GetOptionString("sale", "order_list_date", 30));
	$arFilter["DATE_UPDATE_FROM"] = $filter_date_update_from;
}

if (strlen($filter_date_update_to)>0)
{
	if ($arDate = ParseDateTime($filter_date_update_to, CSite::GetDateFormat("FULL", SITE_ID)))
	{
		if (StrLen($filter_date_update_to) < 11)
		{
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$filter_date_update_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["DATE_UPDATE_TO"] = $filter_date_update_to;
	}
	else
	{
		$filter_date_update_to = "";
	}
}

if (strlen($filter_lang)>0 && $filter_lang!="NOT_REF") $arFilter["LID"] = Trim($filter_lang);
if (strlen($filter_currency)>0) $arFilter["CURRENCY"] = Trim($filter_currency);

if (isset($filter_status) && !is_array($filter_status) && strlen($filter_status) > 0)
	$filter_status = array($filter_status);
if (isset($filter_status) && is_array($filter_status) && count($filter_status) > 0)
{
	$countFilter = count($filter_status);
	for ($i = 0; $i < $countFilter; $i++)
	{
		$filter_status[$i] = Trim($filter_status[$i]);
		if (strlen($filter_status[$i]) > 0)
			$arFilter["STATUS_ID"][] = $filter_status[$i];
	}
}
if (strlen($filter_date_status_from)>0) $arFilter["DATE_STATUS_FROM"] = Trim($filter_date_status_from);
if (strlen($filter_date_status_to)>0)
{
	if ($arDate = ParseDateTime($filter_date_status_to, CSite::GetDateFormat("FULL", SITE_ID)))
	{
		if (StrLen($filter_date_status_to) < 11)
		{
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$filter_date_status_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["DATE_STATUS_TO"] = $filter_date_status_to;
	}
	else
	{
		$filter_date_status_to = "";
	}
}

if (strlen($filter_payed_from)>0) $arFilter["DATE_PAYED_FROM"] = Trim($filter_payed_from);
if (strlen($filter_payed_to)>0)
{
	if ($arDate = ParseDateTime($filter_payed_to, CSite::GetDateFormat("FULL", SITE_ID)))
	{
		if (StrLen($filter_payed_to) < 11)
		{
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$filter_payed_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["DATE_PAYED_TO"] = $filter_payed_to;
	}
	else
	{
		$filter_payed_to = "";
	}
}

if (strlen($filter_payed)>0) $arFilter["PAYED"] = Trim($filter_payed);
if (strlen($filter_allow_delivery)>0) $arFilter["ALLOW_DELIVERY"] = Trim($filter_allow_delivery);
if (strlen($filter_ps_status)>0) $arFilter["PS_STATUS"] = Trim($filter_ps_status);
if (strlen($filter_canceled)>0) $arFilter["CANCELED"] = Trim($filter_canceled);
if (strlen($filter_deducted)>0) $arFilter["DEDUCTED"] = Trim($filter_deducted);
if (strlen($filter_marked)>0) $arFilter["MARKED"] = Trim($filter_marked);
if (strlen($filter_buyer)>0) $arFilter["%BUYER"] = Trim($filter_buyer);
if (strlen($filter_user_login)>0) $arFilter["USER_LOGIN"] = Trim($filter_user_login);
if (strlen($filter_user_email)>0) $arFilter["USER_EMAIL"] = Trim($filter_user_email);
if (IntVal($filter_user_id)>0) $arFilter["USER_ID"] = IntVal($filter_user_id);
if (is_array($filter_group_id) && count($filter_group_id) > 0)
{
	foreach($filter_group_id as $v)
	{
		if(IntVal($v) > 0)
			$arFilter["USER_GROUP_ID"][] = $v;
	}
}

if (IntVal($filter_product_id)>0) $arFilter["BASKET_PRODUCT_ID"] = IntVal($filter_product_id);
if (strlen($filter_product_xml_id)>0) $arFilter["BASKET_PRODUCT_XML_ID"] = Trim($filter_product_xml_id);
if (IntVal($filter_affiliate_id)>0) $arFilter["AFFILIATE_ID"] = IntVal($filter_affiliate_id);
if (strlen($filter_date_delivery_from)>0) $arFilter[">=DATE_ALLOW_DELIVERY"] = Trim($filter_date_delivery_from);
if (strlen($filter_date_delivery_to)>0)
{
	if ($arDate = ParseDateTime($filter_date_delivery_to, CSite::GetDateFormat("FULL", SITE_ID)))
	{
		if (StrLen($filter_date_delivery_to) < 11)
		{
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$filter_date_delivery_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["<=DATE_ALLOW_DELIVERY"] = $filter_date_delivery_to;
	}
	else
	{
		$filter_date_delivery_to = "";
	}
}
if (strlen($filter_discount_name)>0) $arFilter["BASKET_DISCOUNT_NAME"] = Trim($filter_discount_name);
if (strlen($filter_discount_value)>0) $arFilter["BASKET_DISCOUNT_VALUE"] = Trim($filter_discount_value);
if (strlen($filter_discount_coupon)>0) $arFilter["BASKET_DISCOUNT_COUPON"] = Trim($filter_discount_coupon);
if (floatval($filter_price_from)>0) $arFilter[">=PRICE"] = floatval($filter_price_from);
if (floatval($filter_price_to)>0) $arFilter["<PRICE"] = floatval($filter_price_to);
if (isset($filter_universal) && strlen($filter_universal) > 0)
	$arFilter["%NAME_SEARCH"] = trim($filter_universal);
if (strlen($filter_account_number)>0) $arFilter["ACCOUNT_NUMBER"] = Trim($filter_account_number);
if (strlen($filter_tracking_number)>0) $arFilter["TRACKING_NUMBER"] = Trim($filter_tracking_number);

if(strlen($filter_sum_paid) > 0)
{
	if($filter_sum_paid == "Y")
		$arFilter[">SUM_PAID"] = 0;
	else
		$arFilter["<=SUM_PAID"] = 0;
}

if (isset($filter_person_type) && is_array($filter_person_type) && count($filter_person_type) > 0)
{
	$countFilterPerson = count($filter_person_type);
	for ($i = 0; $i < $countFilterPerson; $i++)
	{
		if (IntVal($filter_person_type[$i]) > 0)
			$arFilter["PERSON_TYPE_ID"][] = $filter_person_type[$i];
	}
}

if (isset($filter_pay_system) && is_array($filter_pay_system) && count($filter_pay_system) > 0)
{
	$countFilterPay = count($filter_pay_system);
	for ($i = 0; $i < $countFilterPay; $i++)
	{
		if (IntVal($filter_pay_system[$i]) > 0)
			$arFilter["PAY_SYSTEM_ID"][] = $filter_pay_system[$i];
	}
}

if (isset($filter_delivery) && is_array($filter_delivery) && count($filter_delivery) > 0)
{
	$countFilterDelivery = count($filter_delivery);
	for ($i = 0; $i < $countFilterDelivery; $i++)
	{
		if (strlen($filter_delivery[$i]) > 0)
			$arFilter["DELIVERY_ID"][] = trim($filter_delivery[$i]);
	}
}

foreach ($arOrderProps as $key => $value)
{
	if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT")
	{
		$tmp = Trim(${"filter_prop_".$key});
		if (StrLen($tmp) > 0)
		{
			if($value["TYPE"]=="TEXT" || $value["TYPE"]=="TEXTAREA")
			{
				if(preg_match("/^\d+$/", $tmp))
					$arFilter["PROPERTY_VALUE_".$key] = $tmp;
				else
					$arFilter["%PROPERTY_VALUE_".$key] = $tmp;
			}
			else
				$arFilter["PROPERTY_VALUE_".$key] = $tmp;
		}
	}
}

foreach ($arOrderPropsCode as $key => $value)
{
	if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT")
	{
		$tmp = Trim(${"filter_prop_".$key});
		if (StrLen($tmp) > 0)
		{
			if($value["TYPE"]=="TEXT" || $value["TYPE"]=="TEXTAREA")
			{
				if(preg_match("/^\d+$/", $tmp))
					$arFilter["PROPERTY_VAL_BY_CODE_".$key] = $tmp;
				else
					$arFilter["%PROPERTY_VAL_BY_CODE_".$key] = $tmp;
			}
			else
				$arFilter["PROPERTY_VAL_BY_CODE_".$key] = $tmp;
		}
	}
}

if (strlen($filter_delivery_request_sent)>0) $arFilter["DELIVERY_REQUEST_SENT"] = Trim($filter_delivery_request_sent);

if ($saleModulePermissions < "W")
{
	if (count($arAccessibleSites) <= 0)
		$arAccessibleSites = array("**");

	if (strlen($filter_lang) <= 0)
		$arFilter["LID"] = $arAccessibleSites;
}

if ($saleModulePermissions == "W")
	$arFilterTmp = $arFilter;
else
	$arFilterTmp = array_merge(
		$arFilter,
		array(
			"STATUS_PERMS_GROUP_ID" => $arUserGroups,
			">=STATUS_PERMS_PERM_VIEW" => "Y"
		)
	);

if ($lAdmin->EditAction() && $saleModulePermissions >= "U")
{
	foreach ($FIELDS as $ID => $arFields)
	{
		$ID = IntVal($ID);

		if (!$lAdmin->IsUpdated($ID))
			continue;

		//$DB->StartTransaction();
		$dbOrderTmp = CSaleOrder::GetList(
			array(),
			array("ID" => $ID),
			false,
			//false,
			array("nTopCount"=>1),
			array("ID", "CANCELED", "ALLOW_DELIVERY", "DEDUCTED", "STATUS_ID")
		);
		if ($arOrderTmp = $dbOrderTmp->Fetch())
		{
			if (array_key_exists("CANCELED", $arFields)
				&& ($arFields["CANCELED"] == "Y" || $arFields["CANCELED"] == "N")
				&& $arFields["CANCELED"] != $arOrderTmp["CANCELED"])
			{
				if (CSaleOrder::CanUserCancelOrder($ID, $arUserGroups, $intUserID))
				{
					if (!CSaleOrder::CancelOrder($ID, $arFields["CANCELED"], ""))
					{
						if ($ex = $APPLICATION->GetException())
							$lAdmin->AddUpdateError($ex->GetString(), $ID);
						else
							$lAdmin->AddUpdateError(GetMessage("SOA_ERROR_CANCEL"), $ID);
					}
				}
				else
				{
					$lAdmin->AddUpdateError(GetMessage("SOA_PERMS_CANCEL"), $ID);
				}
			}

			if (array_key_exists("ALLOW_DELIVERY", $arFields)
				&& ($arFields["ALLOW_DELIVERY"] == "Y" || $arFields["ALLOW_DELIVERY"] == "N")
				&& $arFields["ALLOW_DELIVERY"] != $arOrderTmp["ALLOW_DELIVERY"])
			{
				if (CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_DELIVERY", $arUserGroups))
				{
					if (!CSaleOrder::DeliverOrder($ID, $arFields["ALLOW_DELIVERY"]))
					{
						if ($ex = $APPLICATION->GetException())
							$lAdmin->AddUpdateError($ex->GetString(), $ID);
						else
							$lAdmin->AddUpdateError(GetMessage("SOA_ERROR_DELIV"), $ID);
					}
				}
				else
				{
					$lAdmin->AddUpdateError(GetMessage("SOA_PERMS_DELIV"), $ID);
				}
			}

			if (array_key_exists("STATUS_ID", $arFields)
				&& StrLen($arFields["STATUS_ID"]) > 0
				&& $arFields["STATUS_ID"] != $arOrderTmp["STATUS_ID"])
			{
				if (CSaleOrder::CanUserChangeOrderStatus($ID, $arFields["STATUS_ID"], $arUserGroups))
				{
					if (!CSaleOrder::StatusOrder($ID, $arFields["STATUS_ID"]))
					{
						if ($ex = $APPLICATION->GetException())
							$lAdmin->AddUpdateError($ex->GetString(), $ID);
						else
							$lAdmin->AddUpdateError(GetMessage("SOA_ERROR_STATUS"), $ID);
					}
				}
				else
				{
					$lAdmin->AddUpdateError(GetMessage("SOA_PERMS_STATUS"), $ID);
				}
			}
		}
		else
		{
			$lAdmin->AddUpdateError(GetMessage("SOA_NO_ORDER"), $ID);
		}

		//$DB->Commit();
	}
}

$bShowBasketProps = (COption::GetOptionString("sale", "show_basket_props_in_order_list", "Y") == "Y") ? true : false;

foreach(GetModuleEvents("sale", "OnOrderListFilter", true) as $arEvent)
	$arFilterTmp = ExecuteModuleEventEx($arEvent, Array($arFilterTmp));

$arID = Array();
if (($arID = $lAdmin->GroupAction()) && $saleModulePermissions >= "U")
{
	$arAffectedOrders = array();

	if ($_REQUEST['action_target'] == 'selected')
	{
		$arGroupByTmp = (($saleModulePermissions == "W") ? False : array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "MAX" => "STATUS_PERMS_PERM_VIEW"));

		$arID = Array();
		$dbOrderList = CSaleOrder::GetList(
				array($by => $order),
				$arFilterTmp,
				$arGroupByTmp,
				//array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),
				false,
				array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "PAYED", "ALLOW_DELIVERY", "CANCELED", "DEDUCTED")
			);
		while ($arOrderList = $dbOrderList->Fetch())
		{
			$arID[] = $arOrderList['ID'];
			if ($_REQUEST['action'] == "update_ps_status")
			{
				$arAffectedOrders[$arOrderList["ID"]] = array(
						"PAY_SYSTEM_ID" => $arOrderList["PAY_SYSTEM_ID"],
						"PERSON_TYPE_ID" => $arOrderList["PERSON_TYPE_ID"]
					);
			}
			else
			{
				$arAffectedOrders[$arOrderList["ID"]] = $arOrderList;
			}
		}
	}
	else
	{
		$arGroupByTmp = (($saleModulePermissions == "W") ? False : array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "MAX" => "STATUS_PERMS_PERM_VIEW"));

		$dbOrderList = CSaleOrder::GetList(
				array($by => $order),
				array("ID" => $arID),
				$arGroupByTmp,
				array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),
				array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "PAYED", "ALLOW_DELIVERY", "CANCELED", "DEDUCTED", "STATUS_ID")
			);
		while ($arOrderList = $dbOrderList->Fetch())
		{
			if ($_REQUEST['action'] == "update_ps_status")
			{
				$arAffectedOrders[$arOrderList["ID"]] = array(
						"PAY_SYSTEM_ID" => $arOrderList["PAY_SYSTEM_ID"],
						"PERSON_TYPE_ID" => $arOrderList["PERSON_TYPE_ID"]
					);
			}
			else
			{
				$arAffectedOrders[$arOrderList["ID"]] = $arOrderList;
			}
		}
	}

	foreach ($arID as $ID)
	{
		if (strlen($ID) <= 0)
			continue;

		if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock) && $_REQUEST['action'] != "unlock")
		{
			$lAdmin->AddGroupError(str_replace("#DATE#", "$dateLock", str_replace("#ID#", "$lockedBY", GetMessage("SOE_ORDER_LOCKED"))), $ID);
		}
		else
		{
			switch ($_REQUEST['action'])
			{
				case "delete":
					$arItems = CSaleOrder::GetByID($ID);
					if (count($arItems) > 1)
					{
						@set_time_limit(0);

						if (CSaleOrder::CanUserDeleteOrder($ID, $arUserGroups, $intUserID))
						{
							$DB->StartTransaction();

							if (!CSaleOrder::Delete($ID))
							{
								$DB->Rollback();

								if ($ex = $APPLICATION->GetException())
									$lAdmin->AddGroupError($ex->GetString(), $ID);
								else
									$lAdmin->AddGroupError(GetMessage("SALE_DELETE_ERROR"), $ID);
							}
							else
								$DB->Commit();
						}
						else
						{
							$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SO_NO_PERMS2DEL")), $ID);
						}
					}

					break;
				case "update_ps_status":

					$psResultFile = "";

					$dbPSAction = CSalePaySystemAction::GetList(array(), array("PAY_SYSTEM_ID" => $arAffectedOrders[$ID]["PAY_SYSTEM_ID"], "PERSON_TYPE_ID" => $arAffectedOrders[$ID]["PERSON_TYPE_ID"]));
					if($arPSAction = $dbPSAction->Fetch())
					{
						$psActionPath = $_SERVER["DOCUMENT_ROOT"].$arPSAction["ACTION_FILE"];
						$psActionPath = str_replace("\\", "/", $psActionPath);
						while (substr($psActionPath, strlen($psActionPath) - 1, 1) == "/")
							$psActionPath = substr($psActionPath, 0, strlen($psActionPath) - 1);

						if (file_exists($psActionPath) && is_dir($psActionPath))
						{
							if (file_exists($psActionPath."/result.php") && is_file($psActionPath."/result.php"))
								$psResultFile = $psActionPath."/result.php";
						}
						elseif (strlen($arPSAction["RESULT_FILE"]) > 0)
						{
							if (file_exists($_SERVER["DOCUMENT_ROOT"].$arPSAction["RESULT_FILE"])
								&& is_file($_SERVER["DOCUMENT_ROOT"].$arPSAction["RESULT_FILE"]))
								$psResultFile = $_SERVER["DOCUMENT_ROOT"].$arPSAction["RESULT_FILE"];
						}

						if (strlen($psResultFile) > 0)
						{
							$ORDER_ID = $ID;
							CSalePaySystemAction::InitParamArrays(array(), $ID);

							if (include($psResultFile))
							{
								$ORDER_ID = IntVal($ORDER_ID);
								$arOrder = CSaleOrder::GetByID($ORDER_ID);
								if ($arOrder)
								{
									if ($arOrder["PS_STATUS"] == "Y" && $arOrder["PAYED"] == "N")
									{
										if ($arOrder["CURRENCY"] == $arOrder["PS_CURRENCY"]
											&& DoubleVal($arOrder["PRICE"]) == DoubleVal($arOrder["PS_SUM"]))
										{
											if (!CSaleOrder::PayOrder($arOrder["ID"], "Y", True, True))
											{
												if ($ex = $APPLICATION->GetException())
													$lAdmin->AddGroupError($ex->GetString(), $ID);
												else
													$lAdmin->AddGroupError(str_replace("#ID#", $ORDER_ID, GetMessage("SO_CANT_PAY_ORDER")), $ID);
											}
										}
									}
								}
								else
								{
									$lAdmin->AddGroupError(str_replace("#ID#", $ORDER_ID, GetMessage("SO_NO_ORDER")), $ID);
								}
							}
						}
					}

					break;

				case "unlock":
					CSaleOrder::UnLock($ID);
				break;
				case "allow_delivery":
					if($arAffectedOrders[$ID]["ALLOW_DELIVERY"] != "Y")
					{
						if (CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_DELIVERY", $arUserGroups))
						{
							if (!CSaleOrder::DeliverOrder($ID, "Y"))
							{
								if ($ex = $APPLICATION->GetException())
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_DELIV_GROUP")).": ".$ex->GetString(), $ID);
								else
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_DELIV_GROUP")), $ID);
							}
						}
						else
						{
							$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_DELIV_GROUP")), $ID);
						}
					}
					else
					{
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_DELIV_GROUP_DELIV")), $ID);
					}
				break;
				case "allow_delivery_n":
					if($arAffectedOrders[$ID]["ALLOW_DELIVERY"] == "Y")
					{
						if (CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_DELIVERY", $arUserGroups))
						{
							if (!CSaleOrder::DeliverOrder($ID, "N"))
							{
								if ($ex = $APPLICATION->GetException())
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_DELIV_GROUP")).": ".$ex->GetString(), $ID);
								else
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_DELIV_GROUP")), $ID);
							}
						}
						else
						{
							$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_DELIV_GROUP")), $ID);
						}
					}
					else
					{
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_DELIV_GROUP_DELIV_N")), $ID);
					}
				break;
				case "pay":
					if($arAffectedOrders[$ID]["PAYED"] != "Y")
					{
						if (CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_PAYMENT", $arUserGroups))
						{
							if (!CSaleOrder::PayOrder($ID, "Y"))
							{
								if ($ex = $APPLICATION->GetException())
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_PAY_GROUP")).": ".$ex->GetString(), $ID);
								else
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_PAY_GROUP")), $ID);
							}
						}
						else
						{
							$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_PAY_GROUP")), $ID);
						}
					}
					else
					{
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_PAY_GROUP_PAY")), $ID);
					}
				break;
				case "pay_n":
					if($arAffectedOrders[$ID]["PAYED"] == "Y")
					{
						if (CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_PAYMENT", $arUserGroups))
						{
							if (!CSaleOrder::PayOrder($ID, "N"))
							{
								if ($ex = $APPLICATION->GetException())
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_PAY_GROUP")).": ".$ex->GetString(), $ID);
								else
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_PAY_GROUP")), $ID);
							}
						}
						else
						{
							$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_PAY_GROUP")), $ID);
						}
					}
					else
					{
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_PAY_GROUP_PAY_N")), $ID);
					}
				break;
				case "cancel":
					if($arAffectedOrders[$ID]["CANCELED"] != "Y")
					{
						if (CSaleOrder::CanUserCancelOrder($ID, $arUserGroups, $intUserID))
						{
							if (!CSaleOrder::CancelOrder($ID, "Y"))
							{
								if ($ex = $APPLICATION->GetException())
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_CANCEL_GROUP")).": ".$ex->GetString(), $ID);
								else
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_CANCEL_GROUP")), $ID);
							}
						}
						else
						{
							$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_CANCEL_GROUP")), $ID);
						}
					}
					else
					{
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_CANCEL_GROUP_CANCEL")), $ID);
					}
				break;
				case "cancel_n":
					if($arAffectedOrders[$ID]["CANCELED"] == "Y")
					{
						if (CSaleOrder::CanUserCancelOrder($ID, $arUserGroups, $intUserID))
						{
							if (!CSaleOrder::CancelOrder($ID, "N"))
							{
								if ($ex = $APPLICATION->GetException())
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_CANCEL_GROUP")).": ".$ex->GetString(), $ID);
								else
									$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_ERROR_CANCEL_GROUP")), $ID);
							}
						}
						else
						{
							$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_CANCEL_GROUP")), $ID);
						}
					}
					else
					{
						$lAdmin->AddGroupError(str_replace("#ID#", $ID, GetMessage("SOA_PERMS_CANCEL_GROUP_CANCEL_N")), $ID);
					}
				break;
				default:
					if(substr($_REQUEST['action'], 0, strlen("status_")) == "status_")
					{
						$statusID = substr($_REQUEST['action'], strlen("status_"));
						if(strlen($statusID) > 0)
						{
							$arStatus = CSaleStatus::GetByID($statusID);
							if(CSaleOrder::CanUserChangeOrderStatus($ID, $statusID, $arUserGroups))
							{
								if($arAffectedOrders[$ID]["STATUS_ID"] != $statusID)
								{
									if (!CSaleOrder::StatusOrder($ID, $statusID))
									{
										if ($ex = $APPLICATION->GetException())
											$lAdmin->AddGroupError(GetMessage("SOA_ERROR_STATUS", Array("#ID#" => $ID, "#STATUS#" => $arStatus["NAME"])).": ".$ex->GetString(), $ID);
										else
											$lAdmin->AddGroupError(GetMessage("SOA_ERROR_STATUS", Array("#ID#" => $ID, "#STATUS#" => $arStatus["NAME"])), $ID);
									}
								}
								else
								{
									$lAdmin->AddGroupError(GetMessage("SOA_ERROR_STATUS_ALREADY", Array("#ID#" => $ID, "#STATUS#" => $arStatus["NAME"])), $ID);
								}
							}
							else
							{
								$lAdmin->AddGroupError(GetMessage("SOA_PERMS_STATUS_GROUP", Array("#ID#" => $ID, "#STATUS#" => $arStatus["NAME"])), $ID);
							}
						}
					}
					elseif(substr($_REQUEST['action'], 0, strlen("delivery_action_")) == "delivery_action_")
					{
						$actionId = substr($_REQUEST['action'], strlen("delivery_action_"));
						if(strlen($actionId) > 0)
						{
							$arResult = CSaleDeliveryHelper::execHandlerAction($ID, $actionId);

							if($arResult["RESULT"] == "OK")
							{
								$lAdmin->AddActionSuccessMessage(GetMessage("SALE_F_DELIVERY_REQUEST_ORDERN")." ".$ID.". ".GetMessage("SALE_F_DELIVERY_REQUEST_SUCCESS").".", $ID);
							}
							else
							{
								$msg = GetMessage("SALE_F_DELIVERY_REQUEST_ORDERN")." ".$ID.". ".GetMessage("SALE_F_DELIVERY_REQUEST_ERROR").".";

								if(isset($arResult["TEXT"]))
									$msg .= " ( ".$arResult["TEXT"]." ) ";

								$lAdmin->AddGroupError($msg, $ID);
							}
						}
					}

				break;
			}
		}
	}
}

$arColumn2Field = array(
		"ID" => array("ID"),
		"ACCOUNT_NUMBER" => array("ACCOUNT_NUMBER"),
		"LID" => array("LID"),
		"PERSON_TYPE" => array("PERSON_TYPE_ID"),
		"PAYED" => array("PAYED", "DATE_PAYED", "EMP_PAYED_ID"),
		"PAY_VOUCHER_NUM" => array("PAY_VOUCHER_NUM"),
		"PAY_VOUCHER_DATE" => array("PAY_VOUCHER_DATE"),
		"DELIVERY_DOC_NUM" => array("DELIVERY_DOC_NUM"),
		"DELIVERY_DOC_DATE" => array("DELIVERY_DOC_DATE"),
		"PAYED" => array("PAYED", "DATE_PAYED", "EMP_PAYED_ID"),
		"CANCELED" => array("CANCELED", "DATE_CANCELED", "EMP_CANCELED_ID"),
		"DEDUCTED" => array("DEDUCTED", "DATE_DEDUCTED", "EMP_DEDUCTED_ID"),
		"MARKED" => array("MARKED", "DATE_MARKED", "EMP_MARKED_ID", "REASON_MARKED"),
		"STATUS_ID" => array("STATUS_ID", "DATE_STATUS", "EMP_STATUS_ID"),
		"STATUS" => array("STATUS_ID", "DATE_STATUS", "EMP_STATUS_ID"),
		"PRICE_DELIVERY" => array("PRICE_DELIVERY", "CURRENCY"),
		"ALLOW_DELIVERY" => array("ALLOW_DELIVERY", "DATE_ALLOW_DELIVERY", "EMP_ALLOW_DELIVERY_ID"),
		"PRICE" => array("PRICE", "CURRENCY"),
		"SUM_PAID" => array("SUM_PAID", "CURRENCY"),
		"USER" => array("USER_ID"),
		"PAY_SYSTEM" => array("PAY_SYSTEM_ID"),
		"DELIVERY" => array("DELIVERY_ID"),
		"DATE_INSERT" => array("DATE_INSERT"),
		"DATE_UPDATE" => array("DATE_UPDATE"),
		"PS_STATUS" => array("PS_STATUS", "PS_RESPONSE_DATE"),
		"PS_STATUS_DESCRIPTION" => array("PS_STATUS_DESCRIPTION"),
		"PS_SUM" => array("PS_SUM", "PS_CURRENCY"),
		"TAX_VALUE" => array("TAX_VALUE", "CURRENCY"),
		"LOCK_STATUS" => array("LOCK_STATUS", "LOCK_USER_NAME"),
		"BASKET" => array(),
		"COMMENTS" => array("COMMENTS"),
		"REASON_CANCELED" => array("REASON_CANCELED"),
		"REASON_UNDO_DEDUCTED" => array("REASON_UNDO_DEDUCTED"),
		"REASON_MARKED" => array("REASON_MARKED"),
		"USER_DESCRIPTION" => array("USER_DESCRIPTION"),
		"USER_EMAIL" => array("USER_EMAIL"),
		"TRACKING_NUMBER" => array("TRACKING_NUMBER"),
		"DELIVERY_DATE_REQUEST" => array("DELIVERY_DATE_REQUEST"),
		"EXTERNAL_ORDER" => array("EXTERNAL_ORDER"),
	);

$arHeaders = array(
	array("id"=>"DATE_INSERT","content"=>GetMessage("SI_DATE_INSERT"), "sort"=>"DATE_INSERT", "default"=>true),
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"USER","content"=>GetMessage("SI_BUYER"), "sort"=>"USER_ID", "default"=>true),
	array("id"=>"STATUS_ID","content"=>GetMessage("SI_STATUS"), "sort"=>"STATUS_ID", "default"=>true, "title" => GetMessage("SO_S_DATE_STATUS")),
	array("id"=>"PAYED","content"=>GetMessage("SI_PAID"), "sort"=>"PAYED", "default"=>true, "title" => GetMessage("SO_S_DATE_PAYED")),
	array("id"=>"ALLOW_DELIVERY","content"=>GetMessage("SI_ALLOW_DELIVERY"), "sort"=>"ALLOW_DELIVERY", "default"=>true),
	array("id"=>"CANCELED","content"=>GetMessage("SI_CANCELED"), "sort"=>"CANCELED", "default"=>true),
	array("id"=>"DEDUCTED","content"=>GetMessage("SI_DEDUCTED"), "sort"=>"DEDUCTED", "default"=>true),
	array("id"=>"MARKED","content"=>GetMessage("SI_MARKED"), "sort"=>"MARKED", "default"=>true),
	array("id"=>"PRICE","content"=>GetMessage("SI_SUM"), "sort"=>"PRICE", "default"=>true),
	array("id"=>"BASKET","content"=>GetMessage("SI_ITEMS"), "sort"=>"", "default"=>true),
	array("id"=>"DATE_UPDATE","content"=>GetMessage("SI_DATE_UPDATE"), "sort"=>"DATE_UPDATE", "default"=>false),
	array("id"=>"LID","content"=>GetMessage("SI_SITE"), "sort"=>"LID"),
	array("id"=>"PERSON_TYPE","content"=>GetMessage("SI_PAYER_TYPE"), "sort"=>"PERSON_TYPE_ID"),
	array("id"=>"PAY_VOUCHER_NUM","content"=>GetMessage("SI_NO_PP"), "sort"=>"PAY_VOUCHER_NUM"),
	array("id"=>"PAY_VOUCHER_DATE","content"=>GetMessage("SI_DATE_PP"), "sort"=>"PAY_VOUCHER_DATE"),
	array("id"=>"STATUS","content"=>GetMessage("SI_STATUS_OLD"), "sort"=>"STATUS_ID", "default"=>false),
	array("id"=>"PRICE_DELIVERY","content"=>GetMessage("SI_DELIVERY"), "sort"=>"PRICE_DELIVERY"),
	array("id"=>"DELIVERY_DOC_NUM","content"=>GetMessage("SI_DELIVERY_DOC_NUM"), "sort"=>"DELIVERY_DOC_NUM"),
	array("id"=>"DELIVERY_DOC_DATE","content"=>GetMessage("SI_DELIVERY_DOC_DATE"), "sort"=>"DELIVERY_DOC_DATE"),
	array("id"=>"SUM_PAID","content"=>GetMessage("SI_SUM_PAID"), "sort"=>"SUM_PAID"),
	array("id"=>"USER_EMAIL","content"=>GetMessage("SALE_F_USER_EMAIL"), "sort"=>"USER_EMAIL", "default"=>false),
	array("id"=>"PAY_SYSTEM","content"=>GetMessage("SI_PAY_SYS"), "sort"=>"PAY_SYSTEM_ID", "default"=>false),
	array("id"=>"DELIVERY","content"=>GetMessage("SI_DELIVERY_SYS"), "sort"=>"DELIVERY_ID"),
	array("id"=>"PS_STATUS","content"=>GetMessage("SI_PAYMENT_PS"), "sort"=>"PS_STATUS", "default"=>false),
	array("id"=>"PS_SUM","content"=>GetMessage("SI_PS_SUM"), "sort"=>"PS_SUM"),
	array("id"=>"TAX_VALUE","content"=>GetMessage("SI_TAX"), "sort"=>"TAX_VALUE"),
	array("id"=>"BASKET_NAME","content"=>GetMessage("SOA_BASKET_NAME"), "sort"=>""),
	array("id"=>"BASKET_PRODUCT_ID","content"=>GetMessage("SOA_BASKET_PRODUCT_ID"), "sort"=>""),
	array("id"=>"BASKET_PRICE","content"=>GetMessage("SOA_BASKET_PRICE"), "sort"=>""),
	array("id"=>"BASKET_QUANTITY","content"=>GetMessage("SOA_BASKET_QUANTITY"), "sort"=>""),
	array("id"=>"BASKET_WEIGHT","content"=>GetMessage("SOA_BASKET_WEIGHT"), "sort"=>""),
	array("id"=>"BASKET_NOTES","content"=>GetMessage("SOA_BASKET_NOTES"), "sort"=>""),
	array("id"=>"BASKET_DISCOUNT_PRICE","content"=>GetMessage("SOA_BASKET_DISCOUNT_PRICE"), "sort"=>""),
	array("id"=>"BASKET_CATALOG_XML_ID","content"=>GetMessage("SOA_BASKET_CATALOG_XML_ID"), "sort"=>""),
	array("id"=>"BASKET_PRODUCT_XML_ID","content"=>GetMessage("SOA_BASKET_PRODUCT_XML_ID"), "sort"=>""),
	array("id"=>"BASKET_DISCOUNT_NAME","content"=>GetMessage("SOA_BASKET_DISCOUNT_NAME"), "sort"=>""),
	array("id"=>"BASKET_DISCOUNT_VALUE","content"=>GetMessage("SOA_BASKET_DISCOUNT_VALUE"), "sort"=>""),
	array("id"=>"BASKET_DISCOUNT_COUPON","content"=>GetMessage("SOA_BASKET_DISCOUNT_COUPON"), "sort"=>""),
	array("id"=>"BASKET_VAT_RATE","content"=>GetMessage("SOA_BASKET_VAT_RATE"), "sort"=>""),
	array("id"=>"DATE_ALLOW_DELIVERY","content"=>GetMessage("SALE_F_DATE_ALLOW_DELIVERY"), "sort"=>"DATE_ALLOW_DELIVERY"),
	array("id"=>"ACCOUNT_NUMBER","content"=>GetMessage("SOA_ACCOUNT_NUMBER"), "sort"=>""),
	array("id"=>"TRACKING_NUMBER","content"=>GetMessage("SOA_TRACKING_NUMBER"), "sort"=>""),
	array("id"=>"DELIVERY_DATE_REQUEST","content"=>GetMessage("SOA_DELIVERY_DATE_REQUEST"), "sort"=>""),
	array("id"=>"EXTERNAL_ORDER","content"=>GetMessage("SOA_EXTERNAL_ORDER"), "sort"=>"", "default"=> false),
);

if($DBType == "mysql")
{
	$arHeaders[] = array("id"=>"COMMENTS","content"=>GetMessage("SI_COMMENTS"), "sort"=>"COMMENTS", "default"=>false);
	$arHeaders[] = array("id"=>"PS_STATUS_DESCRIPTION","content"=>GetMessage("SOA_PS_STATUS_DESCR"), "sort"=>"", "default"=>false);
	$arHeaders[] = array("id"=>"USER_DESCRIPTION","content"=>GetMessage("SI_USER_DESCRIPTION"), "sort"=>"", "default"=>false);
	$arHeaders[] = array("id"=>"REASON_CANCELED","content"=>GetMessage("SI_REASON_CANCELED"), "sort"=>"", "default"=>false);
	$arHeaders[] = array("id"=>"REASON_UNDO_DEDUCTED","content"=>GetMessage("SI_REASON_UNDO_DEDUCTED"), "sort"=>"", "default"=>false);
	$arHeaders[] = array("id"=>"REASON_MARKED","content"=>GetMessage("SI_REASON_MARKED"), "sort"=>"", "default"=>false);
}
foreach ($arOrderProps as $key => $value)
{
	$arHeaders[] = array("id" => "PROP_".$key, "content" => $value["NAME"]." (".$value["PERSON_TYPE_NAME"].")", "sort" => "", "default" => false);
	$arColumn2Field["PROP_".$key] = array();
}
foreach ($arOrderPropsCode as $key => $value)
{
	$arHeaders[] = array("id" => "PROP_".$key, "content" => $value["NAME"], "sort" => "", "default" => false);
	$arColumn2Field["PROP_".$key] = array();
}

$lAdmin->AddHeaders($arHeaders);

$arSelectFields = array();
$arSelectFields[] = "ID";
$arSelectFields[] = "LID";
$arSelectFields[] = "LOCK_STATUS";
$arSelectFields[] = "LOCK_USER_NAME";

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
$bNeedProps = False;
foreach ($arVisibleColumns as $visibleColumn)
{
	if (!$bNeedProps && SubStr($visibleColumn, 0, StrLen("PROP_")) == "PROP_")
		$bNeedProps = True;

	if (array_key_exists($visibleColumn, $arColumn2Field))
	{
		if (is_array($arColumn2Field[$visibleColumn]) && count($arColumn2Field[$visibleColumn]) > 0)
		{
			$countArColumn = count($arColumn2Field[$visibleColumn]);
			for ($i = 0; $i < $countArColumn; $i++)
			{
				if (!in_array($arColumn2Field[$visibleColumn][$i], $arSelectFields))
					$arSelectFields[] = $arColumn2Field[$visibleColumn][$i];
			}
		}
	}
}

$dbSite = CSite::GetList(($b = "sort"), ($o = "asc"), array());
while ($arSite = $dbSite->Fetch())
{
	$serverName[$arSite["LID"]] = $arSite["SERVER_NAME"];
	if (strlen($serverName[$arSite["LID"]]) <= 0)
	{
		if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME) > 0)
			$serverName[$arSite["LID"]] = SITE_SERVER_NAME;
		else
			$serverName[$arSite["LID"]] = COption::GetOptionString("main", "server_name", "");
	}

	$WEIGHT_UNIT[$arSite["LID"]] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", $arSite["LID"]));
	$WEIGHT_KOEF[$arSite["LID"]] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, $arSite["LID"]));
}

if($saleModulePermissions == "W")
{
	if (array_key_exists("BASKET_DISCOUNT_NAME", $arFilter) || array_key_exists("BASKET_DISCOUNT_VALUE", $arFilter) || array_key_exists("BASKET_DISCOUNT_COUPON", $arFilter))
		$arGroupByTmp = $arSelectedFields;
	else
		$arGroupByTmp = false;
}
else
{
	foreach($arSelectFields as $k => $v)
	{
		if(in_array($v, Array("COMMENTS")) && $saleModulePermissions < "U")
			unset($arSelectFields[$k]);
	}
	$arGroupByTmp = array_merge($arSelectFields, array("MAX" => "STATUS_PERMS_PERM_VIEW"));
}

if (!isset($_REQUEST["by"]) && $by == "ID")
	$order = "DESC";

if ($by == "STATUS_ID")
	$arFilterOrder["DATE_STATUS"] = $order;
elseif ($by == "PAYED")
	$arFilterOrder["DATE_PAYED"] = $order;
elseif ($by == "CANCELED")
	$arFilterOrder["DATE_CANCELED"] = $order;
elseif ($by == "DEDUCTED")
	$arFilterOrder["DATE_DEDUCTED"] = $order;
else
	$arFilterOrder[$by] = $order;

$sScript = "";

$dbOrderList = CSaleOrder::GetList(
	$arFilterOrder,
	$arFilterTmp,
	$arGroupByTmp,
	array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),
	$arSelectFields
);

$dbOrderList = new CAdminResult($dbOrderList, $sTableID);
$dbOrderList->NavStart();

$lAdmin->NavText($dbOrderList->GetNavPrint(GetMessage("SALE_PRLIST")));

while ($arOrder = $dbOrderList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arOrder, "sale_order_detail.php?ID=".$f_ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_"));

	$row->AddField("DATE_ALLOW_DELIVERY", $f_DATE_ALLOW_DELIVERY);

	$lamp = "/bitrix/images/sale/".$arOrder['LOCK_STATUS'].".gif";
	if ($arOrder['LOCK_STATUS']=="green")
		$lamp_alt = GetMessage("SMOL_GREEN_ALT");
	elseif($arOrder['LOCK_STATUS']=="yellow")
		$lamp_alt = GetMessage("SMOL_YELLOW_ALT");
	else
		$lamp_alt = str_replace("#LOCK_USER_NAME#", trim($arOrder['LOCK_USER_NAME']), GetMessage("SMOL_RED_ALT"));

	$idTmp = "<table><tr><td valign=\"top\">";
	if (!$bExport)
		$idTmp .= "<img src='".$lamp."' hspace='4' alt='".htmlspecialcharsbx($lamp_alt)."' title='".htmlspecialcharsbx($lamp_alt)."' />";
	$idTmp .= "</td>
		<td><b><a href='/bitrix/admin/sale_order_detail.php?ID=".$f_ID.GetFilterParams("filter_")."&lang=".LANGUAGE_ID."' title='".GetMessage("SALE_DETAIL_DESCR")."'>".GetMessage("SO_ORDER_ID_PREF").$arOrder["ID"]."</a></b></td>";
	$idTmp .= "</tr></table>";

	$row->AddField("ID", $idTmp);

	$fieldValue = "";
	if (in_array("LID", $arVisibleColumns))
	{
		if (!isset($LOCAL_SITE_LIST_CACHE[$arOrder["LID"]])
			|| empty($LOCAL_SITE_LIST_CACHE[$arOrder["LID"]]))
		{
			$dbSite = CSite::GetByID($arOrder["LID"]);
			if ($arSite = $dbSite->Fetch())
				$LOCAL_SITE_LIST_CACHE[$arOrder["LID"]] = htmlspecialcharsEx($arSite["NAME"]);
		}
		$fieldValue = "[".$arOrder["LID"]."] ".$LOCAL_SITE_LIST_CACHE[$arOrder["LID"]];
	}
	$row->AddField("LID", $fieldValue);

	$fieldValue = "";
	if (in_array("PERSON_TYPE", $arVisibleColumns))
	{
		if (!isset($LOCAL_PERSON_TYPE_CACHE[$arOrder["PERSON_TYPE_ID"]])
			|| empty($LOCAL_PERSON_TYPE_CACHE[$arOrder["PERSON_TYPE_ID"]]))
		{
			if ($arPersonType = CSalePersonType::GetByID($arOrder["PERSON_TYPE_ID"]))
				$LOCAL_PERSON_TYPE_CACHE[$arOrder["PERSON_TYPE_ID"]] = htmlspecialcharsEx($arPersonType["NAME"]);
		}
		$fieldValue = "[";
		if ($saleModulePermissions >= "W")
			$fieldValue .= '<a href="/bitrix/admin/sale_person_type.php?lang='.LANGUAGE_ID.'">';
		$fieldValue .= $arOrder["PERSON_TYPE_ID"];
		if ($saleModulePermissions >= "W")
			$fieldValue .= "</a>";
		$fieldValue .= "] ".$LOCAL_PERSON_TYPE_CACHE[$arOrder["PERSON_TYPE_ID"]];
	}
	$row->AddField("PERSON_TYPE", $fieldValue);

	$fieldValue = "";
	if (in_array("PAYED", $arVisibleColumns))
	{
		$fieldValue .= "<span id=\"payed_".$arOrder["ID"]."\">".(($arOrder["PAYED"] == "Y") ? GetMessage("SO_YES") : GetMessage("SO_NO"))."</span>";
		$fieldValueTmp = $arOrder["DATE_PAYED"];
		if (strlen($arOrder["DATE_PAYED"]) > 0)
		{
			if (IntVal($arOrder["EMP_PAYED_ID"]) > 0)
			{
				if (!isset($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_PAYED_ID"]])
					|| empty($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_PAYED_ID"]]))
				{
					$dbUser = CUser::GetByID($arOrder["EMP_PAYED_ID"]);
					if ($arUser = $dbUser->Fetch())
						$LOCAL_PAYED_USER_CACHE[$arOrder["EMP_PAYED_ID"]] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]." (".$arUser["LOGIN"].")");
				}
				$fieldValueTmp .= '<br />[<a href="/bitrix/admin/user_edit.php?ID='.$arOrder["EMP_PAYED_ID"].'&lang='.LANGUAGE_ID.'">'.$arOrder["EMP_PAYED_ID"].'</a>] ';
				$fieldValueTmp .= $LOCAL_PAYED_USER_CACHE[$arOrder["EMP_PAYED_ID"]];
			}
			if (!$bExport)
			{
				$sScript .= "
						new top.BX.CHint({
							parent: top.BX('payed_".$arOrder["ID"]."'),
							show_timeout: 10,
							hide_timeout: 100,
							dx: 2,
							preventHide: true,
							min_width: 250,
							hint: '".CUtil::JSEscape($fieldValueTmp)."'
						});
				";
			}
		}
	}
	$row->AddField("PAYED", $fieldValue);
	$row->AddField("PAY_VOUCHER_NUM", $f_PAY_VOUCHER_NUM);
	$row->AddField("PAY_VOUCHER_DATE", $f_PAY_VOUCHER_DATE);
	$row->AddField("DELIVERY_DOC_NUM", $f_DELIVERY_DOC_NUM);
	$row->AddField("DELIVERY_DOC_DATE", $f_DELIVERY_DOC_DATE);

	if ($row->bEditMode != true
		|| $row->bEditMode == true && !CSaleOrder::CanUserCancelOrder($f_ID, $arUserGroups, $intUserID))
	{
		$fieldValue = "";
		if (in_array("CANCELED", $arVisibleColumns))
		{
			$fieldValue .= "<span id=\"cancel_".$arOrder["ID"]."\">".(($arOrder["CANCELED"] == "Y") ? GetMessage("SO_YES") : GetMessage("SO_NO"))."</span>";
			$fieldValueTmp = $arOrder["DATE_CANCELED"];
			if (IntVal($arOrder["DATE_CANCELED"]) > 0)
			{
				if (IntVal($arOrder["EMP_CANCELED_ID"]) > 0)
				{
					if (!isset($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_CANCELED_ID"]])
						|| empty($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_CANCELED_ID"]]))
					{
						$dbUser = CUser::GetByID($arOrder["EMP_CANCELED_ID"]);
						if ($arUser = $dbUser->Fetch())
							$LOCAL_PAYED_USER_CACHE[$arOrder["EMP_CANCELED_ID"]] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]." (".$arUser["LOGIN"].")");
					}
					$fieldValueTmp .= '<br />[<a href="/bitrix/admin/user_edit.php?ID='.$arOrder["EMP_CANCELED_ID"].'&lang='.LANGUAGE_ID.'">'.$arOrder["EMP_CANCELED_ID"].'</a>] ';
					$fieldValueTmp .= $LOCAL_PAYED_USER_CACHE[$arOrder["EMP_CANCELED_ID"]];
				}

				if (!$bExport)
				{
					$sScript .= "
						new top.BX.CHint({
							parent: top.BX('cancel_".$arOrder["ID"]."'),
							show_timeout: 10,
							hide_timeout: 100,
							dx: 2,
							preventHide: true,
							min_width: 250,
							hint: '".CUtil::JSEscape($fieldValueTmp)."'
						});
					";
				}
			}
		}
		$row->AddField("CANCELED", $fieldValue, $fieldValue);
	}
	else
	{
		$row->AddCheckField("CANCELED");
	}

	if (in_array("STATUS", $arVisibleColumns))
	{
		if ($row->bEditMode == true)
		{
			$arStatusList = False;
			$arFilter = array("LID" => LANG);
			$arGroupByTmpSt = false;
			if ($saleModulePermissions < "W")
			{
				$arFilter["GROUP_ID"] = $arUserGroups;
				$arFilter["PERM_STATUS_FROM"] = "Y";
				$arFilter["ID"] = $arOrder["STATUS_ID"];
				$arGroupByTmpSt = array("ID", "NAME", "MAX" => "PERM_STATUS_FROM");
			}
			$dbStatusList = CSaleStatus::GetList(
					array(),
					$arFilter,
					$arGroupByTmpSt,
					false,
					array("ID", "NAME")
				);
			$arStatusList = $dbStatusList->GetNext();
		}

		if ($row->bEditMode != true
			|| $row->bEditMode == true && !$arStatusList)
		{
			$fieldValue = "";
			if (in_array("STATUS", $arVisibleColumns))
			{
				if (!isset($LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]])
					|| empty($LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]]))
				{
					if ($arStatus = CSaleStatus::GetByID($arOrder["STATUS_ID"]))
						$LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]] = htmlspecialcharsEx($arStatus["NAME"]);
				}

				$fieldValue .= "[";
				if ($saleModulePermissions >= "W")
					$fieldValue .= '<a href="/bitrix/admin/sale_status_edit.php?ID='.$arOrder["STATUS_ID"].'&lang='.LANGUAGE_ID.'">';
				$fieldValue .= $arOrder["STATUS_ID"];
				if ($saleModulePermissions >= "W")
					$fieldValue .= "</a>";

				$fieldValue .= "] ".$LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]]."<br />";

				$fieldValue .= $arOrder["DATE_STATUS"];

				if (IntVal($arOrder["EMP_STATUS_ID"]) > 0)
				{
					if (!isset($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_STATUS_ID"]])
						|| empty($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_STATUS_ID"]]))
					{
						$dbUser = CUser::GetByID($arOrder["EMP_STATUS_ID"]);
						if ($arUser = $dbUser->Fetch())
							$LOCAL_PAYED_USER_CACHE[$arOrder["EMP_STATUS_ID"]] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]." (".$arUser["LOGIN"].")");
					}
					$fieldValue .= '<br />[<a href="/bitrix/admin/user_edit.php?ID='.$arOrder["EMP_STATUS_ID"].'&lang='.LANGUAGE_ID.'">'.$arOrder["EMP_STATUS_ID"].'</a>] ';
					$fieldValue .= $LOCAL_PAYED_USER_CACHE[$arOrder["EMP_STATUS_ID"]];
				}
			}
			$row->AddField("STATUS", $fieldValue, $fieldValue);
		}
		else
		{
			if ($row->VarsFromForm() && $_REQUEST["FIELDS"])
				$val = $_REQUEST["FIELDS"][$f_ID]["STATUS_ID"];
			else
				$val = $f_STATUS_ID;

			$fieldValue = "<select name=\"FIELDS[".$f_ID."][STATUS_ID]\">";
			$arFilter = array("LID" => LANG);
			$arGroupByTmpS = false;

			if ($saleModulePermissions < "W")
			{
				$arFilter["GROUP_ID"] = $arUserGroups;
				$arFilter["PERM_STATUS"] = "Y";
			}
			$dbStatusListTmp = CSaleStatus::GetList(
					array("SORT" => "ASC"),
					$arFilter,
					$arGroupByTmpS,
					false,
					array("ID", "NAME", "SORT")
				);
			while($arStatusListTmp = $dbStatusListTmp->GetNext())
			{
				$fieldValue .= "<option value=\"".$arStatusListTmp["ID"]."\"".(($arStatusListTmp["ID"] == $val) ? " selected" : "").">[".$arStatusListTmp["ID"]."] ".$arStatusListTmp["NAME"]."</option>";
			}
			$fieldValue .= "</select>";

			$row->AddField("STATUS", $fieldValue, $fieldValue);
		}
	}

	if (in_array("STATUS_ID", $arVisibleColumns))
	{
		$arStatusList = false;
		if ($row->bEditMode)
		{
			$arStatusList = False;
			$arFilter = array("LID" => LANG);
			$arGroupByTmpSt = false;
			if ($saleModulePermissions < "W")
			{
				$arFilter["GROUP_ID"] = $arUserGroups;
				$arFilter["PERM_STATUS_FROM"] = "Y";
				$arFilter["ID"] = $arOrder["STATUS_ID"];
				$arGroupByTmpSt = array("ID", "NAME", "MAX" => "PERM_STATUS_FROM");
			}
			$dbStatusList = CSaleStatus::GetList(
					array(),
					$arFilter,
					$arGroupByTmpSt,
					false,
					array("ID", "NAME")
				);
			$arStatusList = $dbStatusList->GetNext();
		}

		if ($row->bEditMode !== true
			|| $row->bEditMode && !$arStatusList)
		{
			$fieldValue = "";
			$fieldValueTmp = "";
			if (in_array("STATUS_ID", $arVisibleColumns))
			{
				if (!isset($LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]])
					|| empty($LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]]))
				{
					if ($arStatus = CSaleStatus::GetByID($arOrder["STATUS_ID"]))
						$LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]] = htmlspecialcharsEx($arStatus["NAME"]);
				}

				$fieldValueTmp .= "[";
				if ($saleModulePermissions >= "W")
					$fieldValueTmp .= '<a href="/bitrix/admin/sale_status_edit.php?ID='.$arOrder["STATUS_ID"].'&lang='.LANGUAGE_ID.'">';
				$fieldValueTmp .= $arOrder["STATUS_ID"];
				if ($saleModulePermissions >= "W")
					$fieldValueTmp .= "</a>";

				$fieldValueTmp .= "] ".$LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]];

				$fieldValue .= '<span id="status_order_'.$arOrder["ID"].'">'.$LOCAL_STATUS_CACHE[$arOrder["STATUS_ID"]].'</span>';

				$fieldValueTmp .= "<br />".$arOrder["DATE_STATUS"];

				if (IntVal($arOrder["EMP_STATUS_ID"]) > 0)
				{
					if (!isset($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_STATUS_ID"]])
						|| empty($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_STATUS_ID"]]))
					{
						$dbUser = CUser::GetByID($arOrder["EMP_STATUS_ID"]);
						if ($arUser = $dbUser->Fetch())
							$LOCAL_PAYED_USER_CACHE[$arOrder["EMP_STATUS_ID"]] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]." (".$arUser["LOGIN"].")");
					}
					$fieldValueTmp .= '<br />[<a href="/bitrix/admin/user_edit.php?ID='.$arOrder["EMP_STATUS_ID"].'&lang='.LANGUAGE_ID.'">'.$arOrder["EMP_STATUS_ID"].'</a>] ';
					$fieldValueTmp .= $LOCAL_PAYED_USER_CACHE[$arOrder["EMP_STATUS_ID"]];
				}

				if (!$bExport)
				{
					$sScript .= "
						new top.BX.CHint({
							parent: top.BX('status_order_".$arOrder["ID"]."'),
							show_timeout: 10,
							hide_timeout: 100,
							dx: 2,
							preventHide: true,
							min_width: 250,
							hint: '".CUtil::JSEscape($fieldValueTmp)."'
						});
					";
				}
			}
			$row->AddField("STATUS_ID", $fieldValue);
		}
		else
		{
			if ($row->VarsFromForm() && $_REQUEST["FIELDS"])
				$val = $_REQUEST["FIELDS"][$f_ID]["STATUS_ID"];
			else
				$val = $f_STATUS_ID;

			$arFilter = array("LID" => LANG);
			$arGroupByTmpS = false;

			if ($saleModulePermissions < "W")
			{
				$arFilter["GROUP_ID"] = $arUserGroups;
				$arFilter["PERM_STATUS"] = "Y";
			}
			$arStatusList = Array();
			$dbStatusListTmp = CSaleStatus::GetList(
					array("SORT" => "ASC"),
					$arFilter,
					$arGroupByTmpS,
					false,
					array("ID", "NAME", "SORT")
				);
			while($arStatusListTmp = $dbStatusListTmp->Fetch())
			{
				$arStatusList[$arStatusListTmp["ID"]] = "[".$arStatusListTmp["ID"]."] ".$arStatusListTmp["NAME"];

			}
			$row->AddSelectField("STATUS_ID", $arStatusList);
		}
	}

	$row->AddField("PRICE_DELIVERY", htmlspecialcharsex(SaleFormatCurrency($arOrder["PRICE_DELIVERY"], $arOrder["CURRENCY"])));

	$fieldValue = "";
	if (in_array("ALLOW_DELIVERY", $arVisibleColumns))
	{
		if ($row->bEditMode != true
			|| $row->bEditMode == true && !CSaleOrder::CanUserChangeOrderFlag($f_ID, "PERM_DELIVERY", $arUserGroups))
		{
			$fieldValue .= "<span id=\"allow_deliv_".$arOrder["ID"]."\">".(($arOrder["ALLOW_DELIVERY"] == "Y") ? GetMessage("SO_YES") : GetMessage("SO_NO"))."</span>";

			$fieldValueTmp = $arOrder["DATE_ALLOW_DELIVERY"];
			if (strlen($arOrder["DATE_ALLOW_DELIVERY"]) > 0)
			{
				if (IntVal($arOrder["EMP_ALLOW_DELIVERY_ID"]) > 0)
				{
					if (!isset($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_ALLOW_DELIVERY_ID"]])
						|| empty($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_ALLOW_DELIVERY_ID"]]))
					{
						$dbUser = CUser::GetByID($arOrder["EMP_ALLOW_DELIVERY_ID"]);
						if ($arUser = $dbUser->Fetch())
							$LOCAL_PAYED_USER_CACHE[$arOrder["EMP_ALLOW_DELIVERY_ID"]] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]." (".$arUser["LOGIN"].")");
					}
					$fieldValueTmp .= '<br />[<a href="/bitrix/admin/user_edit.php?ID='.$arOrder["EMP_ALLOW_DELIVERY_ID"].'&lang='.LANGUAGE_ID.'">'.$arOrder["EMP_ALLOW_DELIVERY_ID"].'</a>] ';
					$fieldValueTmp .= $LOCAL_PAYED_USER_CACHE[$arOrder["EMP_ALLOW_DELIVERY_ID"]];
				}

				if (!$bExport)
				{
					$sScript .= "
						new top.BX.CHint({
							parent: top.BX('allow_deliv_".$arOrder["ID"]."'),
							show_timeout: 10,
							hide_timeout: 100,
							dx: 2,
							preventHide: true,
							min_width: 250,
							hint: '".CUtil::JSEscape($fieldValueTmp)."'
						});
					";
				}
			}

			$row->AddField("ALLOW_DELIVERY", $fieldValue);
		}
		else
		{
			$row->AddCheckField("ALLOW_DELIVERY");
		}
	}
	else
	{
		$row->AddField("ALLOW_DELIVERY", $fieldValue, $fieldValue);
	}

	$fieldValue = "";
	if (in_array("DEDUCTED", $arVisibleColumns))
	{
		$fieldValue .= "<span id=\"DEDUCTED_".$arOrder["ID"]."\">".(($arOrder["DEDUCTED"] == "Y") ? GetMessage("SO_YES") : GetMessage("SO_NO"))."</span>";
		$fieldValueTmp = $arOrder["DATE_DEDUCTED"];
		if (strlen($arOrder["DATE_DEDUCTED"]) > 0)
		{
			if (IntVal($arOrder["EMP_DEDUCTED_ID"]) > 0)
			{
				if (!isset($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_DEDUCTED_ID"]])
					|| empty($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_DEDUCTED_ID"]]))
				{
					$dbUser = CUser::GetByID($arOrder["EMP_DEDUCTED_ID"]);
					if ($arUser = $dbUser->Fetch())
						$LOCAL_PAYED_USER_CACHE[$arOrder["EMP_DEDUCTED_ID"]] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]." (".$arUser["LOGIN"].")");
				}
				$fieldValueTmp .= '<br />[<a href="/bitrix/admin/user_edit.php?ID='.$arOrder["EMP_DEDUCTED_ID"].'&lang='.LANGUAGE_ID.'">'.$arOrder["EMP_DEDUCTED_ID"].'</a>] ';
				$fieldValueTmp .= $LOCAL_PAYED_USER_CACHE[$arOrder["EMP_DEDUCTED_ID"]];
			}
			if (!$bExport)
			{
				$sScript .= "
						new top.BX.CHint({
							parent: top.BX('DEDUCTED_".$arOrder["ID"]."'),
							show_timeout: 10,
							hide_timeout: 100,
							dx: 2,
							preventHide: true,
							min_width: 250,
							hint: '".CUtil::JSEscape($fieldValueTmp)."'
						});
				";
			}
		}
	}
	$row->AddField("DEDUCTED", $fieldValue);

	$fieldValue = "";
	if (in_array("MARKED", $arVisibleColumns))
	{
		$fieldValue .= "<span id=\"MARKED_".$arOrder["ID"]."\" style=\"".(($arOrder["MARKED"] == "Y") ? "color: #ff2400;" : "")."\" >".(($arOrder["MARKED"] == "Y") ? GetMessage("SO_YES") : GetMessage("SO_NO"))."</span>";
		$fieldValueTmp = $arOrder["DATE_MARKED"];
		if (strlen($arOrder["DATE_MARKED"]) > 0)
		{
			if (IntVal($arOrder["EMP_MARKED_ID"]) > 0)
			{
				if (!isset($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_MARKED_ID"]])
					|| empty($LOCAL_PAYED_USER_CACHE[$arOrder["EMP_MARKED_ID"]]))
				{
					$dbUser = CUser::GetByID($arOrder["EMP_MARKED_ID"]);
					if ($arUser = $dbUser->Fetch())
						$LOCAL_PAYED_USER_CACHE[$arOrder["EMP_MARKED_ID"]] = htmlspecialcharsEx($arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]." (".$arUser["LOGIN"].")");
				}
				$fieldValueTmp .= '<br />[<a href="/bitrix/admin/user_edit.php?ID='.$arOrder["EMP_MARKED_ID"].'&lang='.LANGUAGE_ID.'">'.$arOrder["EMP_MARKED_ID"].'</a>] ';
				$fieldValueTmp .= $LOCAL_PAYED_USER_CACHE[$arOrder["EMP_MARKED_ID"]];
			}

			if ($arOrder["MARKED"] == "Y" && isset($arOrder["REASON_MARKED"]) && strlen($arOrder["REASON_MARKED"]) > 0)
			{
				$fieldValueTmp .= "<br/>".$arOrder["REASON_MARKED"];
			}

			if (!$bExport)
			{
				$sScript .= "
						new top.BX.CHint({
							parent: top.BX('MARKED_".$arOrder["ID"]."'),
							show_timeout: 10,
							hide_timeout: 100,
							dx: 2,
							preventHide: true,
							min_width: 250,
							hint: '".CUtil::JSEscape($fieldValueTmp)."'
						});
				";
			}
		}
	}
	$row->AddField("MARKED", $fieldValue);

	$fieldValue = "";
	if (in_array("REASON_MARKED", $arVisibleColumns))
	{
		$fieldValue = "<span id=\"REASON_MARKED_".$arOrder["ID"]."\" style=\"".(($arOrder["MARKED"] == "Y") ? "color: #ff2400;" : "")."\" >".(($arOrder["MARKED"] == "Y") ? $arOrder["REASON_MARKED"] : "")."</span>";
	}
	$row->AddField("REASON_MARKED", $fieldValue);

	$row->AddField("PRICE", htmlspecialcharsex(SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"])));
	$row->AddField("SUM_PAID", htmlspecialcharsex(SaleFormatCurrency($arOrder["SUM_PAID"], $arOrder["CURRENCY"])));

	$fieldValue = "";
	if (in_array("USER", $arVisibleColumns))
		$fieldValue = GetFormatedUserName($arOrder["USER_ID"], true);
	$row->AddField("USER", $fieldValue);

	$fieldValue = "";
	if (in_array("PAY_SYSTEM", $arVisibleColumns))
	{
		if (IntVal($arOrder["PAY_SYSTEM_ID"]) > 0)
		{
			if (!isset($LOCAL_PAY_SYSTEM_CACHE[$arOrder["PAY_SYSTEM_ID"]])
				|| empty($LOCAL_PAY_SYSTEM_CACHE[$arOrder["PAY_SYSTEM_ID"]]))
			{
				if ($arPaySys = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]))
					$LOCAL_PAY_SYSTEM_CACHE[$arOrder["PAY_SYSTEM_ID"]] = htmlspecialcharsEx($arPaySys["NAME"]);
			}

			$fieldValue .= "[";
			if ($saleModulePermissions >= "W")
				$fieldValue .= '<a href="/bitrix/admin/sale_pay_system_edit.php?ID='.$arOrder["PAY_SYSTEM_ID"].'&lang='.LANGUAGE_ID.'">';
			$fieldValue .= $arOrder["PAY_SYSTEM_ID"];
			if ($saleModulePermissions >= "W")
				$fieldValue .= "</a>";

			$fieldValue .= "] ".$LOCAL_PAY_SYSTEM_CACHE[$arOrder["PAY_SYSTEM_ID"]];
		}
	}
	$row->AddField("PAY_SYSTEM", $fieldValue);

	$fieldValue = "";
	if (in_array("DELIVERY", $arVisibleColumns))
	{
		if (strpos($arOrder["DELIVERY_ID"], ":") !== false)
		{
			if (!isset($obDelivery))
			{
				$obDelivery = new CSaleDeliveryHandler();
				$obDelivery->GetList(array("SITE_ID" => "ASC"), array("SITE_ID" => "ALL", "ACTIVE" => "ALL"));
			}

			$arId = explode(":", $arOrder["DELIVERY_ID"]);

			$rsDelivery = CSaleDeliveryHandler::GetBySID($arId[0]);
			$arDelivery = $rsDelivery->Fetch();

			$fieldValue .= "[";
			if ($saleModulePermissions >= "W")
				$fieldValue .= '<a href="/bitrix/admin/sale_delivery_handler_edit.php?SID='.$arId[0].'&lang='.LANGUAGE_ID.'">';
			$fieldValue .= $arOrder["DELIVERY_ID"];
			if ($saleModulePermissions >= "W")
				$fieldValue .= "</a>";

			$fieldValue .= "] ".htmlspecialcharsEx($arDelivery["NAME"]);
			$fieldValue .= " (".htmlspecialcharsEx($arDelivery["PROFILES"][$arId[1]]["TITLE"]).")";
		}
		elseif (IntVal($arOrder["DELIVERY_ID"]) > 0)
		{
			if (!isset($LOCAL_DELIVERY_CACHE[$arOrder["DELIVERY_ID"]])
				|| empty($LOCAL_DELIVERY_CACHE[$arOrder["DELIVERY_ID"]]))
			{
				if ($arDelivery = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]))
					$LOCAL_DELIVERY_CACHE[$arOrder["DELIVERY_ID"]] = htmlspecialcharsEx($arDelivery["NAME"]);
			}

			$fieldValue .= "[";
			if ($saleModulePermissions >= "W")
				$fieldValue .= '<a href="/bitrix/admin/sale_delivery_edit.php?ID='.$arOrder["DELIVERY_ID"].'&lang='.LANGUAGE_ID.'">';
			$fieldValue .= $arOrder["DELIVERY_ID"];
			if ($saleModulePermissions >= "W")
				$fieldValue .= "</a>";

			$fieldValue .= "] ".$LOCAL_DELIVERY_CACHE[$arOrder["DELIVERY_ID"]];
		}
	}
	$row->AddField("DELIVERY", $fieldValue);

	$row->AddField("DATE_UPDATE", $arOrder["DATE_UPDATE"]);

	$fieldValue = "";
	if ($arOrder["PS_STATUS"] == "Y")
		$fieldValue = GetMessage("SO_SUCCESS")."<br />".$arOrder["PS_RESPONSE_DATE"];
	elseif ($arOrder["PS_STATUS"] == "N")
		$fieldValue = GetMessage("SO_UNSUCCESS")."<br />".$arOrder["PS_RESPONSE_DATE"];
	else
		$fieldValue = GetMessage("SO_NONE");
	$row->AddField("PS_STATUS", $fieldValue);

	$row->AddField("PS_SUM", htmlspecialcharsex(SaleFormatCurrency($arOrder["PS_SUM"], $arOrder["PS_CURRENCY"])));
	$row->AddField("TAX_VALUE", htmlspecialcharsex(SaleFormatCurrency($arOrder["TAX_VALUE"], $arOrder["CURRENCY"])));

	$fieldValue = "";
	$fieldName = "";
	$fieldQuantity = "";
	$fieldProductID = "";
	$fieldPrice = "";
	$fieldWeight = "";
	$fieldNotes = "";
	$fieldDiscountPrice = "";
	$fieldCatalogXML = "";
	$fieldProductXML = "";
	$fieldDiscountName  = "";
	$fieldDiscountValue  = "";
	$fieldDiscountCoupon = "";
	$fieldVatRate  = "";

	$bNeedBasket = false;
	foreach($arVisibleColumns as $val)
	{
		if(strpos($val, "BASKET") !== false)
			$bNeedBasket = true;
	}

	if ($bNeedBasket)
	{
		$bNeedLine = False;
		$arBasketItems = array();
		$arElementId = array();

		$dbItemsList = CSaleBasket::GetList(
				array("SET_PARENT_ID" => "DESC", "TYPE" => "DESC", "NAME" => "ASC"),
				array("ORDER_ID" => $arOrder["ID"])
			);
		while ($arItem = $dbItemsList->GetNext())
		{
			$arBasketItems[] = $arItem;
			$arElementId[] = $arItem["PRODUCT_ID"];
		}

		$arBasketItems = getMeasures($arBasketItems);

		foreach ($arBasketItems as $arItem)
		{
			$measure = (isset($arItem["MEASURE_TEXT"])) ? $arItem["MEASURE_TEXT"] : GetMessage("SO_SHT");

			if ($bNeedLine && !CSaleBasketHelper::isSetItem($arItem))
			{
				$fieldName .= "<hr size=\"1\" width=\"90%\">";
				$fieldQuantity .= "<hr size=\"1\" width=\"90%\">";
				$fieldProductID .= "<hr size=\"1\" width=\"90%\">";
				$fieldPrice .= "<hr size=\"1\" width=\"90%\">";
				$fieldWeight .= "<hr size=\"1\" width=\"90%\">";
				$fieldNotes .= "<hr size=\"1\" width=\"90%\">";
				$fieldDiscountPrice .= "<hr size=\"1\" width=\"90%\">";
				$fieldCatalogXML .= "<hr size=\"1\" width=\"90%\">";
				$fieldProductXML .= "<hr size=\"1\" width=\"90%\">";
				$fieldDiscountName  .= "<hr size=\"1\" width=\"90%\">";
				$fieldDiscountValue  .= "<hr size=\"1\" width=\"90%\">";
				$fieldDiscountCoupon .= "<hr size=\"1\" width=\"90%\">";
				$fieldVatRate  .= "<hr size=\"1\" width=\"90%\">";
			}
			$bNeedLine = True;

			$hidden = "";
			$setItemClass = "";
			$linkClass = "";
			if (CSaleBasketHelper::isSetItem($arItem))
			{
				$hidden = "style=\"display:none\"";
				$setItemClass = "class=\"set_item_".$arItem["SET_PARENT_ID"]."\"";
				$linkClass = "set-item-link-name";
			}

			$fieldValue .= "<div ".$hidden. " ".$setItemClass.">";
			$fieldValue .= "[".$arItem["PRODUCT_ID"]."] ";

			if(strpos($arItem["DETAIL_PAGE_URL"], "http") === false)
				$url = "http://".$serverName[$arOrder["LID"]].htmlspecialcharsBack($arItem["DETAIL_PAGE_URL"]);
			else
				$url = htmlspecialcharsBack($arItem["DETAIL_PAGE_URL"]);

			if (strlen($arItem["DETAIL_PAGE_URL"]) > 0)
				$fieldValue .= "<a href=\"".$url."\" class=\"".$linkClass."\">";
			$fieldValue .= $arItem["NAME"];
			if (strlen($arItem["DETAIL_PAGE_URL"]) > 0)
				$fieldValue .= "</a>";

			$fieldValue .= " <nobr>(".$arItem["QUANTITY"]." ".$measure.")</nobr>";

			if ($bShowBasketProps)
			{
				$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arItem["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
				while($arProp = $dbProp -> GetNext())
				{
					if (strlen($arProp["VALUE"]) > 0)
						$fieldValue .= "<div><small>".$arProp["NAME"].": ".$arProp["VALUE"]."</small></div>";
				}
			}

			if (CSaleBasketHelper::isSetParent($arItem)):
				$fieldValue .= "<div class=\"set-link-block\">";
				$fieldValue	.= "<a class=\"dashed-link show-set-link\" href=\"javascript:void(0);\" id=\"set_toggle_link_".$arItem["SET_PARENT_ID"]."\" onclick=\"fToggleSetItems(".$arItem["SET_PARENT_ID"].")\">".GetMessage("SOA_SHOW_SET")."</a>";
				$fieldValue .= "</div>";
			endif;

			if ($bNeedLine)
				$fieldValue .= "<hr size=\"1\" width=\"90%\">";

			$fieldValue .= "</div>";

			if(strlen($arItem["NAME"]) > 0)
			{
				$fieldName .= "<nobr>";
				if (strlen($arItem["DETAIL_PAGE_URL"]) > 0)
					$fieldName .= "<a href=\"".$url."\">";
				$fieldName .= $arItem["NAME"];
				if (strlen($arItem["DETAIL_PAGE_URL"]) > 0)
					$fieldName .= "</a>";
				$fieldName .= "</nobr>";
			}
			else
				$fieldName .= "<br />";
			if(strlen($arItem["QUANTITY"]) > 0)
				$fieldQuantity .= $arItem["QUANTITY"]." ".$measure;
			else
				$fieldQuantity .= "<br />";
			if(strlen($arItem["PRODUCT_ID"]) > 0)
				$fieldProductID .= $arItem["PRODUCT_ID"];
			else
				$fieldProductID .= "<br />";
			if(strlen($arItem["PRICE"]) > 0)
				$fieldPrice .= "<nobr>".htmlspecialcharsex(SaleFormatCurrency($arItem["PRICE"], $arItem["CURRENCY"]))."</nobr>";
			else
				$fieldPrice .= "<br />";
			if(strlen($arItem["WEIGHT"]) > 0)
			{
				if(DoubleVal($WEIGHT_KOEF[$arOrder["LID"]]) > 0)
					$fieldWeight .= roundEx(DoubleVal($arItem["WEIGHT"]/$WEIGHT_KOEF[$arOrder["LID"]]), SALE_WEIGHT_PRECISION)." ".$WEIGHT_UNIT[$arOrder["LID"]];
				else
					$fieldWeight .= roundEx(DoubleVal($arItem["WEIGHT"]), SALE_WEIGHT_PRECISION)." ".$WEIGHT_UNIT[$arOrder["LID"]];
			}
			else
				$fieldWeight .= "<br />";
			if(strlen($arItem["NOTES"]) > 0)
				$fieldNotes .= $arItem["NOTES"];
			else
				$fieldNotes .= "<br />";
			if(strlen($arItem["DISCOUNT_PRICE"]) > 0)
				$fieldDiscountPrice .= "<nobr>".htmlspecialcharsex(SaleFormatCurrency($arItem["DISCOUNT_PRICE"], $arItem["CURRENCY"]))."</nobr>";
			else
				$fieldDiscountPrice .= "<br />";
			if(strlen($arItem["CATALOG_XML_ID"]) > 0)
				$fieldCatalogXML .= $arItem["CATALOG_XML_ID"];
			else
				$fieldCatalogXML .= "<br />";
			if(strlen($arItem["PRODUCT_XML_ID"]) > 0)
				$fieldProductXML .= $arItem["PRODUCT_XML_ID"];
			else
				$fieldProductXML .= "<br />";
			if(strlen($arItem["DISCOUNT_NAME"]) > 0)
				$fieldDiscountName .= $arItem["DISCOUNT_NAME"];
			else
				$fieldDiscountName .= "<br />";
			if(strlen($arItem["DISCOUNT_VALUE"]) > 0)
			{
				$fieldDiscountValue .= roundEx($arItem["DISCOUNT_VALUE"], 2);
				if(strpos($arItem["DISCOUNT_VALUE"], "%") !== false)
					$fieldDiscountValue .= "%";
			}
			else
				$fieldDiscountValue .= "<br />";
			if(strlen($arItem["DISCOUNT_COUPON"]) > 0)
				$fieldDiscountCoupon .= $arItem["DISCOUNT_COUPON"];
			else
				$fieldDiscountCoupon .= "<br />";
			if(strlen($arItem["VAT_RATE"]) > 0)
				$fieldVatRate .= $arItem["VAT_RATE"];
			else
				$fieldVatRate .= "<br />";
		}
		unset($arItem);
	}
	$row->AddField("BASKET", $fieldValue);
	$row->AddField("BASKET_NAME", $fieldName);
	$row->AddField("BASKET_QUANTITY", $fieldQuantity);
	$row->AddField("BASKET_PRODUCT_ID", $fieldProductID);
	$row->AddField("BASKET_PRICE", $fieldPrice);
	$row->AddField("BASKET_WEIGHT", $fieldWeight);
	$row->AddField("BASKET_NOTES", $fieldNotes);
	$row->AddField("BASKET_DISCOUNT_PRICE", $fieldDiscountPrice);
	$row->AddField("BASKET_CATALOG_XML_ID", $fieldCatalogXML);
	$row->AddField("BASKET_PRODUCT_XML_ID", $fieldProductXML);
	$row->AddField("BASKET_DISCOUNT_NAME", $fieldDiscountName);
	$row->AddField("BASKET_DISCOUNT_VALUE", $fieldDiscountValue);
	$row->AddField("BASKET_DISCOUNT_COUPON", $fieldDiscountCoupon);
	$row->AddField("BASKET_VAT_RATE", $fieldVatRate);

	if ($bNeedProps)
	{
		$dbProps = CSaleOrderPropsValue::GetOrderProps($arOrder["ID"]);
		while ($arProps = $dbProps->GetNext())
		{
			if (array_key_exists($arProps["ORDER_PROPS_ID"], $arOrderProps) || array_key_exists($arProps["CODE"], $arOrderPropsCode))
			{
				if($arProps["TYPE"] == "MULTISELECT" || $arProps["TYPE"] == "SELECT" || $arProps["TYPE"] == "RADIO")
				{
					if($arProps["TYPE"] == "MULTISELECT")
					{
						$valMulti = "";
						$curVal = explode(",", $arProps["VALUE"]);
						$bNeedLine = false;
						foreach ($curVal as $val)
						{
							if ($bNeedLine)
								$valMulti .= "<hr size=\"1\" width=\"90%\">";
							$arPropVariant = CSaleOrderPropsVariant::GetByValue($arProps["ORDER_PROPS_ID"], $val);
							$valMulti .= "[".htmlspecialcharsEx($val)."] ".htmlspecialcharsEx($arPropVariant["NAME"])."<br />";
							$bNeedLine = true;
						}
						if(strlen($arProps["CODE"]) > 0)
							$row->AddField("PROP_".$arProps["CODE"], $valMulti);
						else
							$row->AddField("PROP_".$arProps["ORDER_PROPS_ID"], $valMulti);
					}
					else
					{
						$arPropVariant = CSaleOrderPropsVariant::GetByValue($arProps["ORDER_PROPS_ID"], $arProps["VALUE"]);
						if(strlen($arProps["CODE"]) > 0)
							$row->AddField("PROP_".$arProps["CODE"], "[".htmlspecialcharsEx($arProps["VALUE"])."] ".htmlspecialcharsEx($arPropVariant["NAME"]));

						else
							$row->AddField("PROP_".$arProps["ORDER_PROPS_ID"], "[".htmlspecialcharsEx($arProps["VALUE"])."] ".htmlspecialcharsEx($arPropVariant["NAME"]));
					}
				}
				elseif($arProps["TYPE"] == "CHECKBOX")
				{
					if($arProps["VALUE"] == "Y")
					{
						if(strlen($arProps["CODE"]) > 0)
							$row->AddField("PROP_".$arProps["CODE"], GetMessage("SALE_YES"));
						else
							$row->AddField("PROP_".$arProps["ORDER_PROPS_ID"], GetMessage("SALE_YES"));
					}
				}
				elseif($arProps["TYPE"] == "LOCATION")
				{
					$arVal = CSaleLocation::GetByID($arProps["VALUE"], LANG);
					if(strlen($arProps["CODE"]) > 0)
						$row->AddField("PROP_".$arProps["CODE"], htmlspecialcharsEx($arVal["COUNTRY_NAME"].((strlen($arVal["COUNTRY_NAME"])<=0 || strlen($arVal["CITY_NAME"])<=0) ? "" : " - ").$arVal["CITY_NAME"]));
					else
						$row->AddField("PROP_".$arProps["ORDER_PROPS_ID"], htmlspecialcharsEx($arVal["COUNTRY_NAME"].((strlen($arVal["COUNTRY_NAME"])<=0 || strlen($arVal["CITY_NAME"])<=0) ? "" : " - ").$arVal["CITY_NAME"]));
				}
				elseif($arProps["TYPE"] == "FILE")
				{
					$fileValue = "";
					if (strpos($arProps["VALUE"], ",") !== false)
					{
						$arValues = explode(",", $arProps["VALUE"]);
						foreach ($arValues as $fileId)
						{
							$fileValue .= showImageOrDownloadLink(trim($fileId), $arOrder["ID"]);
							$fileValue .= "<br/>";
						}
					}
					else
					{
						$fileValue = showImageOrDownloadLink($arProps["VALUE"], $arOrder["ID"]);
					}

					if(strlen($arProps["CODE"]) > 0)
						$row->AddField("PROP_".$arProps["CODE"], $fileValue);
					else
						$row->AddField("PROP_".$arProps["ORDER_PROPS_ID"], $fileValue);
				}
				else
				{
					if(strlen($arProps["CODE"]) > 0)
						$row->AddField("PROP_".$arProps["CODE"], $arProps["VALUE"]);
					else
						$row->AddField("PROP_".$arProps["ORDER_PROPS_ID"], $arProps["VALUE"]);
				}
			}
		}
	}
	else
	{
		foreach ($arOrderProps as $key => $value)
			$row->AddField("PROP_".$key, "");
		foreach ($arOrderPropsCode as $key => $value)
			$row->AddField("PROP_".$key, "");
	}

	$row->AddField("DELIVERY_DATE_REQUEST", (is_null($arOrder["DELIVERY_DATE_REQUEST"]) ? GetMessage("SO_NO") : GetMessage("SO_YES")));
	$row->AddField("EXTERNAL_ORDER", ($arOrder["EXTERNAL_ORDER"] !="Y" ? GetMessage("SO_NO") : GetMessage("SO_YES")));

	$arActions = array();

	if (($arOrder['LOCK_STATUS'] == "red" && $saleModulePermissions >= "W") || $arOrder['LOCK_STATUS'] == "yellow")
	{
		$arActions[] = array(
			"ICON" => "unlock",
			"TEXT" => GetMessage("IBEL_A_UNLOCK"),
			"TITLE" => GetMessage("IBLOCK_UNLOCK_ALT"),
			"ACTION" => $lAdmin->ActionDoGroup($arOrder["ID"], "unlock", '')
		);
		$arActions[] = array("SEPARATOR" => true);
	}

	$arActions[] = array("ICON"=>"view", "TEXT"=>GetMessage("SALE_DETAIL_DESCR"), "ACTION"=>$lAdmin->ActionRedirect("sale_order_detail.php?ID=".$f_ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_")), "DEFAULT"=>true);
	$arActions[] = array("ICON"=>"print", "TEXT"=>GetMessage("SALE_PRINT_DESCR"), "ACTION"=>$lAdmin->ActionRedirect("sale_order_print.php?ID=".$f_ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_")));
	if ($arOrder['LOCK_STATUS'] != "red")
	{
		if (CSaleOrder::CanUserUpdateOrder($f_ID, $arUserGroups))
			$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("SALE_OEDIT_DESCR"), "ACTION"=>$lAdmin->ActionRedirect("sale_order_new.php?ID=".$f_ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_")));
		if ($saleModulePermissions == "W"
			|| $f_PAYED != "Y" && CSaleOrder::CanUserDeleteOrder($f_ID, $arUserGroups, $intUserID))
		{
			$arActions[] = array("SEPARATOR" => true);
			$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("SALE_DELETE_DESCR"), "ACTION"=>"if(confirm('".GetMessage('SALE_CONFIRM_DEL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
		}
	}

	$row->AddActions($arActions);
}

$arFooterArray = array(
	array(
		"title" => GetMessage('SOAN_FILTERED1').":",
		"value" => $dbOrderList->SelectedRowsCount()
	),
);

if ($saleModulePermissions == "W")
{
	$dbOrderList = CSaleOrder::GetList(
		array("CURRENCY" => "ASC"),
		$arFilterTmp,
		array("CURRENCY", "SUM" => "PRICE"),
		false, //array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),
		array("CURRENCY", "SUM" => "PRICE")
	);
	while ($arOrderList = $dbOrderList->Fetch())
	{
		$arFooterArray[] = array(
			"title" => GetMessage("SOAN_ITOG")." ".$arOrderList["CURRENCY"].":",
			"value" => htmlspecialcharsex(SaleFormatCurrency($arOrderList["PRICE"], $arOrderList["CURRENCY"]))
		);
	}
}
elseif (($saleModulePermissions < "W") && (COption::GetOptionString("sale", "show_order_sum", "N")=="Y"))
{
	$arOrdersSum = array();
	$dbOrderList = CSaleOrder::GetList(
		array($by => $order),
		$arFilterTmp,
		$arGroupByTmp,
		false, //array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),
		$arSelectFields
	);
	while ($arOrder = $dbOrderList->Fetch())
	{
		if (!array_key_exists($arOrder["CURRENCY"], $arOrdersSum))
			$arOrdersSum[$arOrder["CURRENCY"]] = 0;
		$arOrdersSum[$arOrder["CURRENCY"]] += $arOrder["PRICE"];
	}

	foreach ($arOrdersSum as $key => $value)
	{
		$arFooterArray[] = array(
			"title" => GetMessage("SOAN_ITOG")." ".$key.":",
			"value" => $value
		);
	}
}
$order_sum = "";
foreach($arFooterArray as $val)
{
	$order_sum .= $val["title"]." ".$val["value"]."<br />";
}

$lAdmin->BeginEpilogContent();
echo "<script>", $sScript, "\nif (document.getElementById('order_sum')) {setTimeout(function(){document.getElementById('order_sum').innerHTML = '".CUtil::JSEscape($order_sum)."';}, 10);}\n","</script>";
?>
<script>
function exportData(val)
{
	var oForm = document.form_<?= $sTableID ?>;
	var expType = oForm.action_target.checked;

	var par = "mode=excel";
	if (!expType)
	{
		var num = oForm.elements.length;
		for (var i = 0; i < num; i++)
		{
			if (oForm.elements[i].tagName.toUpperCase() == "INPUT"
				&& oForm.elements[i].type.toUpperCase() == "CHECKBOX"
				&& oForm.elements[i].name.toUpperCase() == "ID[]"
				&& oForm.elements[i].checked == true)
			{
				par += "&OID[]=" + oForm.elements[i].value;
			}
		}
	}

	if (expType)
	{
		par += "<?= CUtil::JSEscape(GetFilterParams("filter_", false)); ?>";
	}

	if (par.length > 0)
	{
		window.open("sale_order_export.php?EXPORT_FORMAT="+val+"&"+par, "vvvvv");
	}
}
</script>
<?
$lAdmin->EndEpilogContent();

$arGroupActionsTmp = array(
	"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
	"update_ps_status" => GetMessage("SOAN_UPDATE_PS_STATUS"),
	"allow_delivery" => GetMessage("SOAN_LIST_DELIVERY"),
	"allow_delivery_n" => GetMessage("SOAN_LIST_DELIVERY_N"),
	"pay" => GetMessage("SOAN_LIST_PAY"),
	"pay_n" => GetMessage("SOAN_LIST_PAY_N"),
	"cancel" => GetMessage("SOAN_LIST_CANCEL"),
	"cancel_n" => GetMessage("SOAN_LIST_CANCEL_N"),
	);

	$arFilter = array("LID" => LANG);
	$arGroupByTmpSt = false;
	if ($saleModulePermissions < "W")
	{
		$arFilter["GROUP_ID"] = $arUserGroups;
		$arFilter["PERM_STATUS_FROM"] = "Y";
		$arGroupByTmpSt = array("ID", "NAME", "MAX" => "PERM_STATUS_FROM", "SORT");
	}

	$dbStatusList = CSaleStatus::GetList(
			array("SORT" => "ASC"),
			$arFilter,
			$arGroupByTmpSt,
			false,
			array("ID", "NAME", "SORT")
		);

	while($arStatusList = $dbStatusList->Fetch())
		$arGroupActionsTmp["status_".$arStatusList["ID"]] = GetMessage("SOAN_LIST_STATUS_CHANGE")." \"".$arStatusList["NAME"]."\"";

	$arGroupActionsTmp["export_csv"] = array(
			"action" => "exportData('csv')",
			"value" => "export_csv",
			"name" => str_replace("#EXP#", "CSV", GetMessage("SOAN_EXPORT_2"))
		);
	$arGroupActionsTmp["export_commerceml"] = array(
			"action" => "exportData('commerceml')",
			"value" => "export_commerceml",
			"name" => str_replace("#EXP#", "CommerceML", GetMessage("SOAN_EXPORT_2"))
		);
	$arGroupActionsTmp["export_commerceml2"] = array(
			"action" => "exportData('commerceml2')",
			"value" => "export_commerceml2",
			"name" => str_replace("#EXP#", "CommerceML 2.0", GetMessage("SOAN_EXPORT_2"))
		);

$strPath2Export = BX_PERSONAL_ROOT."/php_interface/include/sale_export/";
if (file_exists($_SERVER["DOCUMENT_ROOT"].$strPath2Export))
{
	if ($handle = opendir($_SERVER["DOCUMENT_ROOT"].$strPath2Export))
	{
		while (($file = readdir($handle)) !== false)
		{
			if ($file == "." || $file == "..")
				continue;
			if (is_file($_SERVER["DOCUMENT_ROOT"].$strPath2Export.$file) && substr($file, strlen($file)-4)==".php")
			{
				$export_name = substr($file, 0, strlen($file) - 4);
				$arGroupActionsTmp["export_".$export_name] = array(
					"action" => "exportData('".$export_name."')",
					"value" => "export_".$export_name,
					"name" => str_replace("#EXP#", $export_name, GetMessage("SOAN_EXPORT_2"))
				);
			}
		}
	}
	closedir($handle);
}

$deliveryActions = CSaleDeliveryHandler::getActionsNames();
$arGroupActionsTmp["delivery_action_REQUEST_SELF"] = $deliveryActions["REQUEST_SELF"];
//$arGroupActionsTmp["delivery_action_REQUEST_TAKE"] = $deliveryActions["REQUEST_TAKE"];

$lAdmin->AddGroupActionTable($arGroupActionsTmp);

$aContext = array();
$arStatusList = array();
if ($saleModulePermissions == "U")
{
	$dbStatusList = CSaleStatus::GetList(
		array(),
		array("ID" => "N", "PERM_UPDATE" => 'Y', 'GROUP_ID' => $USER->GetUserGroupArray()),
		false,
		array('nTopCount' => 1),
		array('ID', 'PERM_UPDATE')
	);
	$arStatusList = $dbStatusList->Fetch();
}
if (empty($arStatusList))
	$arStatusList = array();
if ($saleModulePermissions == "W" || ($saleModulePermissions == "U" && !empty($arStatusList) && $arStatusList["PERM_UPDATE"] == "Y"))
{
	$siteLID = "";
	$arSiteMenu = array();
	$arSitesShop = array();
	$arSitesTmp = array();
	$rsSites = CSite::GetList($b="id", $o="asc", Array("ACTIVE" => "Y"));
	while ($arSite = $rsSites->GetNext())
	{
		$site = COption::GetOptionString("sale", "SHOP_SITE_".$arSite["ID"], "");
		if ($arSite["ID"] == $site)
		{
			$arSitesShop[] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
		}
		$arSitesTmp[] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
	}

	$rsCount = count($arSitesShop);
	if ($rsCount <= 0)
	{
		$arSitesShop = $arSitesTmp;
		$rsCount = count($arSitesShop);
	}

	if ($rsCount == 1)
	{
		$siteLID = "&LID=".$arSitesShop[0]["ID"];
	}
	else
	{
		foreach ($arSitesShop as &$val)
		{
			$arSiteMenu[] = array(
				"TEXT" => $val["NAME"]." (".$val["ID"].")",
				"ACTION" => "window.location = 'sale_order_new.php?lang=".LANGUAGE_ID."&LID=".$val["ID"]."';"
			);
		}
		if (isset($val))
			unset($val);
	}
	$aContext = array(
		array(
			"TEXT" => GetMessage("SALE_A_NEWORDER"),
			"ICON" => "btn_new",
			"LINK" => "sale_order_new.php?lang=".LANGUAGE_ID.$siteLID,
			"TITLE" => GetMessage("SALE_A_NEWORDER_TITLE"),
			"MENU" => $arSiteMenu
		),
	);
}
$lAdmin->AddAdminContextMenu($aContext);


$lAdmin->CheckListMode();


/*********************************************************************/
/********************  PAGE  *****************************************/
/*********************************************************************/

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$APPLICATION->SetTitle(GetMessage("SALE_SECTION_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>

<script type="text/javascript">
function fToggleSetItems(setParentId)
{
	var elements = document.getElementsByClassName('set_item_' + setParentId);
	var hide = false;

	for (var i = 0; i < elements.length; ++i)
	{
		if (elements[i].style.display == 'none' || elements[i].style.display == '')
		{
			elements[i].style.display = 'table-row';
			hide = true;
		}
		else
			elements[i].style.display = 'none';
	}

	if (hide)
		BX("set_toggle_link_" + setParentId).innerHTML = '<?=GetMessage("SOA_HIDE_SET")?>';
	else
		BX("set_toggle_link_" + setParentId).innerHTML = '<?=GetMessage("SOA_SHOW_SET")?>';
}
</script>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$arFilterFieldsTmp = array(
	"filter_universal" => GetMessage("SOA_ROW_BUYER"),
	"filter_date_insert" => GetMessage("SALE_F_DATE"),
	"filter_date_update" => GetMessage("SALE_F_DATE_UPDATE"),
	"filter_id_from" => GetMessage("SALE_F_ID"),
	"filter_account_number" => GetMessage("SALE_F_ACCOUNT_NUMBER"),
	"filter_tracking_number" => GetMessage("SALE_F_TRACKING_NUMBER"),
	"filter_currency" => GetMessage("SALE_F_LANG_CUR"),
	"filter_price" => GetMessage("SOA_F_PRICE"),
	"filter_status" => GetMessage("SALE_F_STATUS"),
	"filter_date_status_from" => GetMessage("SALE_F_DATE_STATUS"),
	"filter_payed" => GetMessage("SALE_F_PAYED"),
	"filter_payed_from" => GetMessage("SALE_F_DATE_PAYED"),
	"filter_allow_delivery" => GetMessage("SALE_F_ALLOW_DELIVERY"),
	"filter_date_allow_delivery" => GetMessage("SALE_F_DATE_ALLOW_DELIVERY"),
	"filter_ps_status" => GetMessage("SALE_F_PS_STATUS"),
	"filter_person_type" => GetMessage("SALE_F_PERSON_TYPE"),
	"filter_pay_system" => GetMessage("SALE_F_PAY_SYSTEM"),
	"filter_delivery" => GetMessage("SALE_F_DELIVERY"),
	"filter_canceled" => GetMessage("SALE_F_CANCELED"),
	"filter_deducted" => GetMessage("SALE_F_DEDUCTED"),
	"filter_marked" => GetMessage("SALE_F_MARKED"),
	//"filter_buyer" => GetMessage("SALE_F_BUYER"),
	"filter_user_id" => GetMessage("SALE_F_USER_ID"),
	"filter_user_login" => GetMessage("SALE_F_USER_LOGIN"),
	"filter_user_email" => GetMessage("SALE_F_USER_EMAIL"),
	"filter_group_id" => GetMessage("SALE_F_USER_GROUP_ID"),
	"filter_product_id" => GetMessage("SO_PRODUCT_ID"),
	"filter_product_xml_id" => GetMessage("SO_PRODUCT_XML_ID"),
	"filter_affiliate_id" => GetMessage("SO_AFFILIATE_ID"),
	"filter_coupon" => GetMessage("SO_DISCOUNT_COUPON"),
	"filter_sum_paid" => GetMessage("SO_SUM_PAID"),
);

foreach ($arOrderProps as $key => $value)
	if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT")
		$arFilterFieldsTmp[] = $value["NAME"];

foreach ($arOrderPropsCode as $key => $value)
	if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT")
		$arFilterFieldsTmp[] = $value["NAME"];

//"filter_delivery_request_sent" => GetMessage("SALE_F_DELIVERY_DATE_REQUEST")
$arFilterFieldsTmp[] = GetMessage("SALE_F_DELIVERY_REQUEST_SENT");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	$arFilterFieldsTmp
);

$oFilter->SetDefaultRows(array("filter_universal", "filter_status", "filter_canceled"));

$oFilter->AddPreset(array(
		"ID" => "find_prioritet",
		"NAME" => GetMessage("SOA_PRESET_PRIORITET"),
		"FIELDS" => array(
			"filter_status" => "N",
			"filter_price_from" => "10000",
			"filter_price_to" => ""
			),
		//"SORT_FIELD" => array("DATE_INSERT" => "DESC"),
	));

$oFilter->AddPreset(array(
		"ID" => "find_allow_delivery",
		"NAME" => GetMessage("SOA_PRESET_ALLOW_DWLIVERY"),
		"FIELDS" => array(
			"filter_allow_delivery" => "Y",
			"filter_canceled" => "N",
			"filter_date_delivery_from_FILTER_PERIOD" => "month",
			"filter_date_delivery_from_FILTER_DIRECTION" => "current",
			),
		//"SORT_FIELD" => array("DATE_ALLOW_DELIVERY" => "DESC"),
	));

$oFilter->AddPreset(array(
		"ID" => "find_allow_payed",
		"NAME" => GetMessage("SOA_PRESET_PAYED"),
		"FIELDS" => array(
			"filter_canceled" => "N",
			"filter_payed" => "Y",
			"filter_allow_delivery" => "N",
			),
		//"SORT_FIELD" => array("DATE_UPDATE" => "DESC"),
	));

$oFilter->AddPreset(array(
		"ID" => "find_order_null",
		"NAME" => GetMessage("SOA_PRESET_ORDER_NULL"),
		"FIELDS" => array(
			"filter_canceled" => "N",
			"filter_payed" => "",
			"filter_allow_delivery" => "",
			"filter_status" => array("N", "P"),
			"filter_date_update_from_FILTER_PERIOD" => "before",
			"filter_date_update_from_FILTER_DIRECTION" => "previous",
			"filter_date_update_to" => ConvertTimeStamp(AddToTimeStamp(Array("DD" => -7))),
			),
		//"SORT_FIELD" => array("DATE_UPDATE" => "DESC"),
	));

$oFilter->Begin();
?>
	<tr>
		<td><?=GetMessage('SOA_ROW_BUYER')?>:</td>
		<td>
			<input type="text" name="filter_universal" value="<?echo htmlspecialcharsbx($filter_universal)?>" size="40">
		</td>
	</tr>
	<tr>
		<td><b><?echo GetMessage("SALE_F_DATE");?>:</b></td>
		<td>
			<?echo CalendarPeriod("filter_date_from", $filter_date_from, "filter_date_to", $filter_date_to, "find_form", "Y")?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_DATE_UPDATE");?>:</td>
		<td>
			<?echo CalendarPeriod("filter_date_update_from", $filter_date_update_from, "filter_date_update_to", $filter_date_update_to, "find_form", "Y")?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_ID");?>:</td>
		<td>
			<script type="text/javascript">
				function filter_id_from_Change()
				{
					if (document.find_form.filter_id_to.value.length<=0)
					{
						document.find_form.filter_id_to.value = document.find_form.filter_id_from.value;
					}
				}
			</script>
			<?echo GetMessage("SALE_F_FROM");?>
			<input type="text" name="filter_id_from" OnChange="filter_id_from_Change()" value="<?echo (IntVal($filter_id_from)>0)?IntVal($filter_id_from):""?>" size="10">
			<?echo GetMessage("SALE_F_TO");?>
			<input type="text" name="filter_id_to" value="<?echo (IntVal($filter_id_to)>0)?IntVal($filter_id_to):""?>" size="10">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_ACCOUNT_NUMBER");?>:</td>
		<td>
			<input type="text" name="filter_account_number" value="<?echo htmlspecialcharsEx($filter_account_number)?>" size="10">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_TRACKING_NUMBER");?>:</td>
		<td>
			<input type="text" name="filter_tracking_number" value="<?echo htmlspecialcharsEx($filter_tracking_number)?>" size="10">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_LANG_CUR");?>:</td>
		<td>
			<select name="filter_lang">
				<option value=""><?= htmlspecialcharsex(GetMessage("SALE_F_ALL")) ?></option>
				<?
				$dbSitesList = CLang::GetList(($b1="sort"), ($o1="asc"));
				while ($arSitesList = $dbSitesList->Fetch())
				{
					if (!in_array($arSitesList["LID"], $arAccessibleSites)
						&& $saleModulePermissions < "W")
						continue;

					?><option value="<?= htmlspecialcharsbx($arSitesList["LID"])?>"<?if ($arSitesList["LID"] == $filter_lang) echo " selected";?>>[<?= htmlspecialcharsex($arSitesList["LID"]) ?>]&nbsp;<?= htmlspecialcharsex($arSitesList["NAME"]) ?></option><?
				}
				?>
			</select>
			/
			<?echo CCurrency::SelectBox("filter_currency", $filter_currency, GetMessage("SALE_F_ALL"), false, "", ""); ?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("SOA_F_PRICE");?>:</td>
		<td>
			<?echo GetMessage("SOA_F_PRICE_FROM");?>
			<input type="text" name="filter_price_from" value="<?=(floatval($filter_price_from)>0)?floatval($filter_price_from):""?>" size="3">

			<?echo GetMessage("SOA_F_PRICE_TO");?>
			<input type="text" name="filter_price_to" value="<?=(floatval($filter_price_to)>0)?floatval($filter_price_to):""?>" size="3">
		</td>
	</tr>
	<tr>
		<td valign="top"><?echo GetMessage("SALE_F_STATUS")?>:<br /><img src="/bitrix/images/sale/mouse.gif" width="44" height="21" border="0" alt=""></td>
		<td valign="top">
			<select name="filter_status[]" multiple size="3">
				<?
				$dbStatusList = CSaleStatus::GetList(
						array("SORT" => "ASC"),
						array("LID" => LANGUAGE_ID),
						false,
						false,
						array("ID", "NAME", "SORT")
					);
				while ($arStatusList = $dbStatusList->Fetch())
				{
					?><option value="<?= htmlspecialcharsbx($arStatusList["ID"]) ?>"<?if (is_array($filter_status) && in_array($arStatusList["ID"], $filter_status)) echo " selected"?>>[<?= htmlspecialcharsbx($arStatusList["ID"]) ?>] <?= htmlspecialcharsEx($arStatusList["NAME"]) ?></option><?
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_DATE_STATUS");?>:</td>
		<td>
			<?echo CalendarPeriod("filter_date_status_from", $filter_date_status_from, "filter_date_status_to", $filter_date_status_to, "find_form", "Y")?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_PAYED")?>:</td>
		<td>
			<select name="filter_payed">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<option value="Y"<?if ($filter_payed=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_payed=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_DATE_PAYED")?></td>
		<td>
			<?echo CalendarPeriod("filter_payed_from", $filter_payed_from, "filter_payed_to", $filter_payed_to, "find_form", "Y")?>
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("SALE_F_ALLOW_DELIVERY")?>:</td>
		<td>
			<select name="filter_allow_delivery">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<option value="Y"<?if ($filter_allow_delivery=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_allow_delivery=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_DATE_ALLOW_DELIVERY");?>:</td>
		<td>
			<?echo CalendarPeriod("filter_date_delivery_from", $filter_date_delivery_from, "filter_date_delivery_to", $filter_date_delivery_to, "find_form", "Y")?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_PS_STATUS")?>:</td>
		<td>
			<select name="filter_ps_status">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<option value="Y"<?if ($filter_ps_status=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_ps_status=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
				<option value="X"<?if ($filter_ps_status=="X") echo " selected"?>><?echo GetMessage("SALE_YES_NO")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_PERSON_TYPE");?>:</td>
		<td>
			<select name="filter_person_type[]" multiple size="3">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<?
				$l = CSalePersonType::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array());
				while ($personType = $l->Fetch()):
					?><option value="<?echo htmlspecialcharsbx($personType["ID"])?>"<?if (is_array($filter_person_type) && in_array($personType["ID"], $filter_person_type)) echo " selected"?>>[<?echo htmlspecialcharsbx($personType["ID"]) ?>] <?echo htmlspecialcharsbx($personType["NAME"])?> <?echo "(".htmlspecialcharsbx(implode(", ", $personType["LIDS"])).")";?></option><?
				endwhile;
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_PAY_SYSTEM");?>:</td>
		<td>
			<select name="filter_pay_system[]" multiple size="3">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<?
				$l = CSalePaySystem::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array());
				while ($paySystem = $l->Fetch()):
					?><option value="<?echo htmlspecialcharsbx($paySystem["ID"])?>"<?if (is_array($filter_pay_system) && in_array($paySystem["ID"], $filter_pay_system)) echo " selected"?>>[<?echo htmlspecialcharsbx($paySystem["ID"]) ?>] <?echo htmlspecialcharsbx($paySystem["NAME"])?> <?echo "(".htmlspecialcharsbx($paySystem["LID"]).")";?></option><?
				endwhile;
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_DELIVERY");?>:</td>
		<td>
			<select name="filter_delivery[]" multiple size="3">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<?
				$rsDeliveryServicesList = CSaleDeliveryHandler::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array());
				$arDeliveryServicesList = array();
				while ($arDeliveryService = $rsDeliveryServicesList->Fetch())
				{
					if (!is_array($arDeliveryService) || !is_array($arDeliveryService["PROFILES"])) continue;

					foreach ($arDeliveryService["PROFILES"] as $profile_id => $arDeliveryProfile)
					{
						$delivery_id = $arDeliveryService["SID"].":".$profile_id;
						?><option value="<?echo htmlspecialcharsbx($delivery_id)?>"<?if (is_array($filter_delivery) && in_array($delivery_id, $filter_delivery)) echo " selected"?>>[<?echo htmlspecialcharsbx($delivery_id)?>] <?echo htmlspecialcharsbx($arDeliveryService["NAME"].": ".$arDeliveryProfile["TITLE"])?></option><?
					}
				}

				/*Old Delivery*/
				$dbDelivery = CSaleDelivery::GetList(
							array("SORT"=>"ASC", "NAME"=>"ASC"),
							array(
									"ACTIVE" => "Y",
								)
					);

				while ($arDelivery = $dbDelivery->GetNext())
				{
					?><option value="<?echo $arDelivery["ID"]?>"<?if (is_array($filter_delivery) && in_array($arDelivery["ID"], $filter_delivery)) echo " selected"?>>[<?echo $arDelivery["ID"]?>] <?echo $arDelivery["NAME"]?></option><?
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_CANCELED")?>:</td>
		<td>
			<select name="filter_canceled">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<option value="Y"<?if ($filter_canceled=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_canceled=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_DEDUCTED")?>:</td>
		<td>
			<select name="filter_deducted">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<option value="Y"<?if ($filter_deducted=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_deducted=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_MARKED")?>:</td>
		<td>
			<select name="filter_marked">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<option value="Y"<?if ($filter_marked=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_marked=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
			</select>
		</td>
	</tr>
	<!--<tr>
		<td><?echo GetMessage("SALE_F_BUYER");?>:</td>
		<td>
			<input type="text" name="filter_buyer" value="<?echo htmlspecialcharsbx($filter_buyer)?>" size="40"><?=ShowFilterLogicHelp()?>
		</td>
	</tr>-->
	<tr>
		<td><?echo GetMessage("SALE_F_USER_ID");?>:</td>
		<td>
			<?echo FindUserID("filter_user_id", $filter_user_id, "", "find_form");?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_USER_LOGIN");?>:</td>
		<td>
			<input type="text" name="filter_user_login" value="<?echo htmlspecialcharsEx($filter_user_login)?>" size="40">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_USER_EMAIL");?>:</td>
		<td>
			<input type="text" name="filter_user_email" value="<?echo htmlspecialcharsEx($filter_user_email)?>" size="40">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SALE_F_USER_GROUP_ID")?>:</td>
		<td>
			<?
			$z = CGroup::GetDropDownList("AND ID!=2");
			echo SelectBoxM("filter_group_id[]", $z, $filter_group_id, "", false, 5);
			?>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("SO_PRODUCT_ID")?></td>
		<td>
			<script type="text/javascript">
			function FillProductFields(index, arParams, iblockID)
			{
				if (arParams["id"])
					document.find_form.filter_product_id.value = arParams["id"];

				if (arParams["name"])
				{
					el = document.getElementById("product_name_alt");
					if(el)
						el.innerHTML = arParams["name"];
				}
			}
			</script>
			<input name="filter_product_id" value="<?= htmlspecialcharsbx($filter_product_id) ?>" size="5" type="text">&nbsp;<input type="button" value="..." id="cat_prod_button" onClick="window.open('sale_product_search.php?func_name=FillProductFields', '', 'scrollbars=yes,resizable=yes,width=980,height=550,top='+Math.floor((screen.height - 500)/2-14)+',left='+Math.floor((screen.width - 800)/2-5));"><span id="product_name_alt" class="adm-filter-text-search"></span>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("SO_PRODUCT_XML_ID") ?>:</td>
		<td><input name="filter_product_xml_id" value="<?= htmlspecialcharsbx($filter_product_xml_id) ?>" size="40" type="text"></td>
	</tr>
	<tr>
		<td><?= GetMessage("SO_AFFILIATE_ID") ?>:</td>
		<td>
			<input type="text" name="filter_affiliate_id" value="<?= htmlspecialcharsbx($filter_affiliate_id) ?>" size="10" maxlength="10">
			<IFRAME name="hiddenframe_affiliate" id="id_hiddenframe_affiliate" src="" width="0" height="0" style="width:0px; height:0px; border: 0px"></IFRAME>
			<input type="button" class="button" name="FindAffiliate" OnClick="window.open('/bitrix/admin/sale_affiliate_search.php?func_name=SetAffiliateID', '', 'scrollbars=yes,resizable=yes,width=800,height=500,top='+Math.floor((screen.height - 500)/2-14)+',left='+Math.floor((screen.width - 400)/2-5));" value="...">
			<span id="div_affiliate_name"></span>
			<script type="text/javascript">
			function SetAffiliateID(id)
			{
				document.find_form.filter_affiliate_id.value = id;
			}

			function SetAffiliateName(val)
			{
				if (val != "NA")
					document.getElementById('div_affiliate_name').innerHTML = val;
				else
					document.getElementById('div_affiliate_name').innerHTML = '<?= GetMessage("SO1_NO_AFFILIATE") ?>';
			}

			var affiliateID = '';
			function ChangeAffiliateName()
			{
				if (affiliateID != document.find_form.filter_affiliate_id.value)
				{
					affiliateID = document.find_form.filter_affiliate_id.value;
					if (affiliateID != '' && !isNaN(parseInt(affiliateID, 10)))
					{
						document.getElementById('div_affiliate_name').innerHTML = '<i><?= GetMessage("SO1_WAIT") ?></i>';
						window.frames["hiddenframe_affiliate"].location.replace('/bitrix/admin/sale_affiliate_get.php?ID=' + affiliateID + '&func_name=SetAffiliateName');
					}
					else
						document.getElementById('div_affiliate_name').innerHTML = '';
				}
				timerID = setTimeout('ChangeAffiliateName()',2000);
			}
			ChangeAffiliateName();
			</script>
		</td>
	</tr>
	<tr>
		<td><?= GetMessage("SO_DISCOUNT_COUPON") ?>:</td>
		<td><input name="filter_discount_coupon" value="<?= htmlspecialcharsbx($filter_discount_coupon) ?>" size="40" type="text"></td>
	</tr>
	<tr>
		<td><?= GetMessage("SO_SUM_PAID") ?>:</td>
		<td>
			<select name="filter_sum_paid">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<option value="Y"<?if ($filter_sum_paid=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_sum_paid=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
			</select>
		</td>
	</tr>
	<?
	foreach ($arOrderProps as $key => $value)
	{
		if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT")
		{
			?>
			<tr>
				<td valign="top"><?= $value["NAME"] ?>:</td>
				<td valign="top">
					<?
					$curVal = ${"filter_prop_".$key};
					if ($value["TYPE"]=="CHECKBOX")
					{
						?><input type="checkbox" name="filter_prop_<?= $key ?>" value="Y"<?if ($curVal == "Y") echo " checked";?>><?
					}
					elseif ($value["TYPE"]=="TEXT" || $value["TYPE"]=="TEXTAREA")
					{
						?><input type="text" size="30" maxlength="250" value="<?= htmlspecialcharsbx($curVal) ?>" name="filter_prop_<?= $key ?>"><?=ShowFilterLogicHelp()?><?
					}
					elseif ($value["TYPE"]=="SELECT" || $value["TYPE"]=="MULTISELECT")
					{
						?>
						<select name="filter_prop_<?= $key ?>">
							<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
							<?
							$db_vars = CSaleOrderPropsVariant::GetList(($by="SORT"), ($order="ASC"), Array("ORDER_PROPS_ID" => $key));
							while ($vars = $db_vars->Fetch())
							{
								?><option value="<?echo $vars["VALUE"]?>"<?if ($vars["VALUE"]==$curVal) echo " selected"?>><?echo htmlspecialcharsbx($vars["NAME"])?></option><?
							}
							?>
						</select>
						<?
					}
					elseif ($value["TYPE"]=="LOCATION")
					{
						?>
						<select name="filter_prop_<?= $key ?>">
							<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
							<?
							$db_vars = CSaleLocation::GetList(Array("SORT"=>"ASC", "COUNTRY_NAME_LANG"=>"ASC", "CITY_NAME_LANG"=>"ASC"), array(), LANG);
							while ($vars = $db_vars->Fetch())
							{
								?><option value="<?echo $vars["ID"]?>"<?if (IntVal($vars["ID"])==IntVal($curVal)) echo " selected"?>><?echo htmlspecialcharsbx($vars["COUNTRY_NAME"]." - ".$vars["CITY_NAME"])?></option><?
							}
							?>
						</select>
						<?
					}
					elseif ($value["TYPE"]=="RADIO")
					{
						?><input type="radio" name="filter_prop_<?= $key ?>" value=""><?echo GetMessage("SALE_F_ALL")?><br /><?
						$db_vars = CSaleOrderPropsVariant::GetList(($by="SORT"), ($order="ASC"), Array("ORDER_PROPS_ID"=>$key));
						while ($vars = $db_vars->Fetch())
						{
							?><input type="radio" name="filter_prop_<?= $key ?>" value="<?echo $vars["VALUE"]?>"<?if ($vars["VALUE"]==$curVal) echo " checked"?>><?echo htmlspecialcharsbx($vars["NAME"])?><br /><?
						}
					}
					?>
				</td>
			</tr>
			<?
		}
	}

	foreach ($arOrderPropsCode as $key => $value)
	{
		if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT")
		{
			?>
			<tr>
				<td valign="top"><?= $value["NAME"] ?>:</td>
				<td valign="top">
					<?
					$curVal = ${"filter_prop_".$key};
					if ($value["TYPE"]=="CHECKBOX")
					{
						?><input type="checkbox" name="filter_prop_<?= $key ?>" value="Y"<?if ($curVal == "Y") echo " checked";?>><?
					}
					elseif ($value["TYPE"]=="TEXT" || $value["TYPE"]=="TEXTAREA")
					{
						?><input type="text" size="30" maxlength="250" value="<?= htmlspecialcharsbx($curVal) ?>" name="filter_prop_<?= $key ?>"><?=ShowFilterLogicHelp()?><?
					}
					elseif ($value["TYPE"]=="SELECT" || $value["TYPE"]=="MULTISELECT")
					{
						?>
						<select name="filter_prop_<?= $key ?>">
							<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
							<?
							$db_vars = CSaleOrderPropsVariant::GetList(($by="SORT"), ($order="ASC"), Array("ORDER_PROPS_ID" => $value["ID"]));
							while ($vars = $db_vars->Fetch())
							{
								?><option value="<?echo $vars["VALUE"]?>"<?if ($vars["VALUE"]==$curVal) echo " selected"?>><?echo htmlspecialcharsbx($vars["NAME"])?></option><?
							}
							?>
						</select>
						<?
					}
					elseif ($value["TYPE"]=="LOCATION")
					{
						?>
						<select name="filter_prop_<?= $key ?>">
							<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
							<?
							$db_vars = CSaleLocation::GetList(Array("SORT"=>"ASC", "COUNTRY_NAME_LANG"=>"ASC", "CITY_NAME_LANG"=>"ASC"), array(), LANG);
							while ($vars = $db_vars->Fetch())
							{
								?><option value="<?echo $vars["ID"]?>"<?if (IntVal($vars["ID"])==IntVal($curVal)) echo " selected"?>><?echo htmlspecialcharsbx($vars["COUNTRY_NAME"]." - ".$vars["CITY_NAME"])?></option><?
							}
							?>
						</select>
						<?
					}
					elseif ($value["TYPE"]=="RADIO")
					{
						?><input type="radio" name="filter_prop_<?= $key ?>" value=""><?echo GetMessage("SALE_F_ALL")?><br /><?
						$db_vars = CSaleOrderPropsVariant::GetList(($by="SORT"), ($order="ASC"), Array("ORDER_PROPS_ID"=>$value["ID"]));
						while ($vars = $db_vars->Fetch())
						{
							?><input type="radio" name="filter_prop_<?= $key ?>" value="<?echo $vars["VALUE"]?>"<?if ($vars["VALUE"]==$curVal) echo " checked"?>><?echo htmlspecialcharsbx($vars["NAME"])?><br /><?
						}
					}
					?>
				</td>
			</tr>
			<?
		}
	}
	?>
	<tr>
		<td><?echo GetMessage("SALE_F_DELIVERY_REQUEST_SENT");?>:</td>
		<td>
			<select name="filter_delivery_request_sent">
				<option value=""><?echo GetMessage("SALE_F_ALL")?></option>
				<option value="Y"<?if ($filter_delivery_request_sent=="Y") echo " selected"?>><?echo GetMessage("SALE_YES")?></option>
				<option value="N"<?if ($filter_delivery_request_sent=="N") echo " selected"?>><?echo GetMessage("SALE_NO")?></option>
			</select>
		</td>
	</tr>

<?
$oFilter->Buttons(
	array(
		"table_id" => $sTableID,
		"url" => $APPLICATION->GetCurPage(),
		"form" => "find_form"
	)
);
$oFilter->End();
?>
</form>

<?
$lAdmin->DisplayList();

echo BeginNote();
?>
<span id="order_sum"><? echo $order_sum;?></span>
<?
echo EndNote();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>