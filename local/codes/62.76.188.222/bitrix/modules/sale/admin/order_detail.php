<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

$crmMode = (defined("BX_PUBLIC_MODE") && BX_PUBLIC_MODE && isset($_REQUEST["CRM_MANAGER_USER_ID"]));

if ($crmMode)
{
	CUtil::DecodeUriComponent($_REQUEST);
	CUtil::DecodeUriComponent($_POST);
	
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/bitrix/themes/.default/sale.css\" />";
}

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

function CRMModeOutput($text)
{
	while(@ob_end_clean());
	echo $text;
	die();
}

/*
 * get template recomendet & busket product
 */
function fGetFormatedProduct($USER_ID, $LID, $arData, $CNT, $currency, $crmMode = false, $type)
{
	$result = "";

	if (!is_array($arData) || count($arData) <= 0)
		return $result;

	$result = "<table width=\"100%\">";
	foreach ($arData as $items)
	{
		if ($items["MODULE"] == "catalog" && CModule::IncludeModule('catalog') && CModule::IncludeModule('iblock'))
		{
			$imgCode = 0;
			$imgUrlProduct = "";

			$dbProduct = CIBlockElement::GetList(array(), array("ID" => $items["PRODUCT_ID"]), false, false, array('IBLOCK_ID', 'IBLOCK_SECTION_ID', 'DETAIL_PICTURE', 'PREVIEW_PICTURE'));
			$arProduct = $dbProduct->Fetch();
			$items["DETAIL_PICTURE"] = $arProduct["DETAIL_PICTURE"];
			$items["PREVIEW_PICTURE"] = $arProduct["PREVIEW_PICTURE"];

			if ($arProduct["IBLOCK_ID"] > 0 && $arProduct["IBLOCK_SECTION_ID"] > 0)
				$items["EDIT_PAGE_URL"] = "/bitrix/admin/iblock_element_edit.php?ID=".$items["PRODUCT_ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arProduct["IBLOCK_ID"]."&find_section_section=".$arProduct["IBLOCK_SECTION_ID"];

			if ($items["DETAIL_PICTURE"] > 0)
				$imgCode = $items["DETAIL_PICTURE"];
			elseif ($items["PREVIEW_PICTURE"] > 0)
				$imgCode = $items["PREVIEW_PICTURE"];

			$items["NAME"] = htmlspecialcharsex($items["NAME"]);
			$items["DETAIL_PAGE_URL"] = htmlspecialcharsex($items["DETAIL_PAGE_URL"]);
			$items["CURRENCY"] = htmlspecialcharsex($items["CURRENCY"]);

			if ($imgCode > 0)
			{
				$arFile = CFile::GetFileArray($imgCode);
				$arImgProduct = CFile::ResizeImageGet($arFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
				if (is_array($arImgProduct))
				{
					$imgUrl = $arImgProduct["src"];
					$imgProduct = "<a href=\"".$items["EDIT_PAGE_URL"]."\" target=\"_blank\"><img src=\"".$arImgProduct["src"]."\" alt=\"\" title=\"".$items["NAME"]."\" ></a>";
				}
			}
			else
				$imgProduct = "<div class='no_foto'>".GetMessage('SOD_NO_FOTO')."</div>";

			$result .= "<tr>
							<td class=\"tab_img\">".$imgProduct."</td>
							<td class=\"tab_text\">
								<div class=\"order_name\"><a href=\"".$items["EDIT_PAGE_URL"]."\" target=\"_blank\" title=\"".$items["NAME"]."\">".$items["NAME"]."</a></div>
								<div class=\"order_price\">
									".GetMessage('SOD_ORDER_RECOM_PRICE').": <b>".SaleFormatCurrency($items["PRICE"], $currency)."</b>
								</div>";

			$arResult = CSaleProduct::GetProductSku($USER_ID, $LID, $items["PRODUCT_ID"], $items["NAME"]);

			$arResult["POPUP_MESSAGE"] = array(
				"PRODUCT_ADD" => GetMEssage('SOD_POPUP_TO_BUSKET'),
				"PRODUCT_NOT_ADD" => GetMEssage('SOD_POPUP_TO_BUSKET_NOT'),
				"PRODUCT_PRICE_FROM" => GetMessage('SOD_POPUP_FROM')
			);

			if (!$crmMode)
			{
				if (count($arResult["SKU_ELEMENTS"]) > 0)
				{
					$result .= "<a href=\"javascript:void(0);\" class=\"get_new_order\" onClick=\"fAddToBusketMoreProductSku(".CUtil::PhpToJsObject($arResult['SKU_ELEMENTS']).", ".CUtil::PhpToJsObject($arResult['SKU_PROPERTIES']).", '', ".CUtil::PhpToJsObject($arResult["POPUP_MESSAGE"])." );\"><span></span>".GetMessage('SOD_SUBTAB_ADD_ORDER')."</a>";
				}
				else
				{
					$url = "/bitrix/admin/sale_order_new.php?lang=".LANG."&user_id=".$USER_ID."&LID=".$LID."&product[]=".$items["PRODUCT_ID"];
					$result .= "<a href=\"".$url."\" target=\"_blank\" class=\"get_new_order\"><span></span>".GetMessage('SOD_SUBTAB_ADD_ORDER')."</a>";
				}
			}

			$result .= "</td></tr>";
		}
	}

	$result .= "<tr><td colspan='2' align='right' class=\"more_product\">";
	if ($CNT > 2)
		$result .= "<a href='javascript:void(0);' onClick=\"fGetMoreProduct('".$type."');\"  class=\"get_more\">".GetMessage('SOD_SUBTAB_MORE')."<span></span></a>";
	$result .= "</td></tr>";

	$result .= "</table>";

	return $result;
}

function fShowEditor($userId)
{
	if(IntVal($userId) > 0)
	{
		if (!isset($LOCAL_PAYED_USER_CACHE[$userId])
			|| !is_array($LOCAL_PAYED_USER_CACHE[$userId]))
		{
			$dbUser = CUser::GetByID($userId);
			if ($arUser = $dbUser->Fetch())
				$LOCAL_PAYED_USER_CACHE[$userId] = htmlspecialcharsEx("(".$arUser["LOGIN"].") ".$arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"]);
		}
		echo " &nbsp;&nbsp;[<a href=\"/bitrix/admin/user_edit.php?ID=".$userId."&lang=".LANG."\">".$userId."</a>] ";
		echo $LOCAL_PAYED_USER_CACHE[$userId];
	}
}

if (isset($ORDER_AJAX) AND $ORDER_AJAX == "Y" AND check_bitrix_sessid())
{
	/*
	* get more product
	*/
	if (isset($type) && $type != "")
	{
		$arResult = array();
		$arErrors = array();
		$userId = IntVal($userId);
		$fuserId = IntVal($fUserId);
		$arOrderProduct = CUtil::JsObjectToPhp($arProduct);

		if ($type == 'busket')
		{
			$arShoppingCart = CSaleBasket::DoGetUserShoppingCart($LID, $userId, $fuserId, $arErrors, array());
			if (count($arShoppingCart) > 0)
				$arResult["ITEMS"] = fGetFormatedProduct($userId, $LID, $arShoppingCart, 1, $currency, $crmMode, $type);
			else
				$arResult["ITEMS"] = GetMessage('SOD_SUBTAB_BUSKET_NULL');
		}
		if ($type == 'recom')
		{
			$arRecomendetResult = CSaleProduct::GetRecommendetProduct($userId, $LID, $arOrderProduct, "Y");
			$arResult["ITEMS"] = fGetFormatedProduct($userId, $LID, $arRecomendetResult, 1, $currency, $crmMode, $type);
		}
		if ($type == 'viewed')
		{
			$arViewed = array();
			$dbViewsList = CSaleViewedProduct::GetList(
					array("DATE_VISIT"=>"DESC"),
					array("FUSER_ID" => $fuserId, ">PRICE" => 0, "!CURRENCY" => ""),
					false,
					array('nTopCount' => 10),
					array('ID', 'PRODUCT_ID', 'LID', 'MODULE', 'NAME', 'DETAIL_PAGE_URL', 'PRICE', 'CURRENCY', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
				);
			while ($arViews = $dbViewsList->Fetch())
				$arViewed[] = $arViews;

			$arResult["ITEMS"] =  fGetFormatedProduct($userId, $LID, $arViewed, 1, $currency, $crmMode, $type);
		}

		$arResult["TYPE"] = $type;
		$result = CUtil::PhpToJSObject($arResult);

		CRMModeOutput($result);
		exit;
	}

	/*
	 * save comment
	 */
	if (strlen($comment) > 0)
	{
		$bUserCanEditOrder = CSaleOrder::CanUserUpdateOrder($id, $GLOBALS["USER"]->GetUserGroupArray());

		if (isset($change) && $change == "Y" && $bUserCanEditOrder)
		{
			CUtil::DecodeUriComponent($comment);
			CSaleOrder::CommentsOrder($id, $comment);
		}

		CRMModeOutput('ok');
		exit;
	}
}


$ID = intval($_REQUEST["ID"]);
$errorMessage = "";

if ($ID <= 0)
{
	if ($crmMode)
		CRMModeOutput("Order is not found");
	else
		LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));
}

$customTabber = new CAdminTabEngine("OnAdminSaleOrderView", array("ID" => $ID));

$arTransactTypes = array(
	"ORDER_PAY" => GetMessage("SOD_PAYMENT"),
	"CC_CHARGE_OFF" => GetMessage("SOD_FROM_CARD"),
	"OUT_CHARGE_OFF" => GetMessage("SOD_INPUT"),
	"ORDER_UNPAY" => GetMessage("SOD_CANCEL_PAYMENT"),
	"ORDER_CANCEL_PART" => GetMessage("SOD_CANCEL_SEMIPAYMENT"),
	"MANUAL" => GetMessage("SOD_HAND"),
	"DEL_ACCOUNT" => GetMessage("SOD_DELETE"),
	"AFFILIATE" => GetMessage("SOD1_AFFILIATES_PAY"),
);

$bUserCanViewOrder = CSaleOrder::CanUserViewOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanEditOrder = CSaleOrder::CanUserUpdateOrder($ID, $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanCancelOrder = CSaleOrder::CanUserCancelOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanPayOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_PAYMENT", $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanDeliverOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "PERM_DELIVERY", $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanDeleteOrder = CSaleOrder::CanUserDeleteOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());

/****************/
if ($saleModulePermissions >= "U" && check_bitrix_sessid() && empty($dontsave))
{
	if(!$customTabber->Check())
	{
		if($ex = $APPLICATION->GetException())
			$errorMessage .= $ex->GetString();
		else
			$errorMessage .= "Error. ";
	}
	elseif ($REQUEST_METHOD == "POST" && $save_order_data == "Y")
	{
		if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		{
			$errorMessage .= str_replace(array("#DATE#", "#ID#"), array($dateLock, $lockedBY), GetMessage("SOE_ORDER_LOCKED")).". ";
		}
		else
		{
			if (isset($_REQUEST["change_status"]) && $_REQUEST["change_status"] == "Y")
			{
				$errorMessageTmp = "";

				$STATUS_ID = trim($_REQUEST["STATUS_ID"]);
				if (strlen($STATUS_ID) <= 0)
					$errorMessageTmp .= GetMessage("ERROR_NO_STATUS").". ";

				if (strlen($errorMessageTmp) <= 0)
				{
					if (!CSaleOrder::CanUserChangeOrderStatus($ID, $STATUS_ID, $GLOBALS["USER"]->GetUserGroupArray()))
						$errorMessageTmp .= GetMessage("SOD_NO_PERMS2STATUS").". ";
				}

				if (strlen($errorMessageTmp) <= 0)
				{
					if (!CSaleOrder::StatusOrder($ID, $STATUS_ID))
					{
						if ($ex = $APPLICATION->GetException())
						{
							if ($ex->GetID() != "ALREADY_FLAG")
								$errorMessageTmp .= $ex->GetString();
						}
						else
							$errorMessageTmp .= GetMessage("ERROR_CHANGE_STATUS").". ";
					}
				}

				if ($errorMessageTmp != "")
					$errorMessage .= $errorMessageTmp;
			}

			if (isset($_REQUEST["change_cancel"]) && $_REQUEST["change_cancel"] == "Y")
			{
				$errorMessageTmp = "";

				if (!$bUserCanCancelOrder)
					$errorMessageTmp .= GetMessage("SOD_NO_PERMS2CANCEL").". ";

				if (strlen($errorMessageTmp) <= 0)
				{
					$CANCELED = trim($_REQUEST["CANCELED"]);
					$REASON_CANCELED = trim($_REQUEST["REASON_CANCELED"]);
					if ($CANCELED != "Y")
						$CANCELED = "N";

					if ($CANCELED != "Y" && $CANCELED != "N")
						$errorMessageTmp .= GetMessage("SOD_WRONG_CANCEL_FLAG").". ";
				}

				if (strlen($errorMessageTmp) <= 0)
				{
					if (!CSaleOrder::CancelOrder($ID, $CANCELED, $REASON_CANCELED))
					{
						if ($ex = $APPLICATION->GetException())
						{
							if ($ex->GetID() != "ALREADY_FLAG")
								$errorMessageTmp .= $ex->GetString();
						}
						else
							$errorMessageTmp .= GetMessage("ERROR_CANCEL_ORDER").". ";
					}
				}

				if ($errorMessageTmp != "")
					$errorMessage .= $errorMessageTmp;
			}

			if (isset($_REQUEST["change_pay_form"]) && $_REQUEST["change_pay_form"] == "Y")
			{
				$errorMessageTmp = "";

				if (!$bUserCanPayOrder)
					$errorMessageTmp .= GetMessage("SOD_NO_PERMS2PAYFLAG").". ";

				if (strlen($errorMessageTmp) <= 0)
				{
					$PAYED = trim($_REQUEST["PAYED"]);
					if ($PAYED != "Y")
						$PAYED = "N";

					if ($PAYED != "Y" && $PAYED != "N")
						$errorMessageTmp .= GetMessage("SOD_WRONG_PAYFLAG").". ";
				}

				if (strlen($errorMessageTmp) <= 0)
				{
					$arAdditionalFields = array(
						"PAY_VOUCHER_NUM" => ((strlen($_REQUEST["PAY_VOUCHER_NUM"]) > 0) ? $_REQUEST["PAY_VOUCHER_NUM"] : False),
						"PAY_VOUCHER_DATE" => ((strlen($_REQUEST["PAY_VOUCHER_DATE"]) > 0) ? $_REQUEST["PAY_VOUCHER_DATE"] : False)
					);

					$bWithdraw = true;
					$bPay = true;
					if ($_REQUEST["PAY_FROM_ACCOUNT"] == "Y")
					{
						$bPay = false;
					}
					if ($PAYED == "N" && $_REQUEST["PAY_FROM_ACCOUNT_BACK"] != "Y")
						$bWithdraw = false;

					if ($change_status_popup == "N")
					{
						if (!CSaleOrder::PayOrder($ID, $PAYED, $bWithdraw, $bPay, 0, $arAdditionalFields))
						{
							if ($ex = $APPLICATION->GetException())
							{
								if ($ex->GetID() != "ALREADY_FLAG")
									$errorMessageTmp .= $ex->GetString();
							}
							else
								$errorMessageTmp .= GetMessage("ERROR_PAY_ORDER").". ";
						}
					}

					$arFields = array(
						"PAYED" => $PAYED,
						"EMP_PAYED_ID" => ( IntVal($USER->GetID())>0 ? IntVal($USER->GetID()) : false ),
					);
					$arFields = array_merge($arFields, $arAdditionalFields);

					$res = CSaleOrder::Update($ID, $arFields);
				}

				if ($errorMessageTmp != "")
					$errorMessage .= $errorMessageTmp;
			}

//delivery
			if (isset($_REQUEST["change_delivery_form"]) && $_REQUEST["change_delivery_form"] == "Y")
			{
				$errorMessageTmp = "";

				if (!$bUserCanDeliverOrder)
					$errorMessageTmp .= GetMessage("SOD_NO_PERMS2DELIV").". ";

				if (strlen($errorMessageTmp) <= 0)
				{
					$ALLOW_DELIVERY = trim($_REQUEST["ALLOW_DELIVERY"]);
					if ($ALLOW_DELIVERY != "Y")
						$ALLOW_DELIVERY = "N";
					if ($ALLOW_DELIVERY != "Y" && $ALLOW_DELIVERY != "N")
						$errorMessageTmp .= GetMessage("SOD_WRONG_DELIV_FLAG").". ";
				}

				if (strlen($errorMessageTmp) <= 0)
				{
					$arAdditionalFields = array(
						"DELIVERY_DOC_NUM" => ((strlen($_REQUEST["DELIVERY_DOC_NUM"]) > 0) ? $_REQUEST["DELIVERY_DOC_NUM"] : False),
						"DELIVERY_DOC_DATE" => ((strlen($_REQUEST["DELIVERY_DOC_DATE"]) > 0) ? $_REQUEST["DELIVERY_DOC_DATE"] : False)
					);

					if ($change_status_popup == "N")
					{
						if (!CSaleOrder::DeliverOrder($ID, $ALLOW_DELIVERY, 0, $arAdditionalFields))
						{
							if ($ex = $APPLICATION->GetException())
							{
								if ($ex->GetID() != "ALREADY_FLAG")
									$errorMessageTmp .= $ex->GetString();
							}
							else
								$errorMessageTmp .= GetMessage("ERROR_DELIVERY_ORDER").". ";
						}
					}

					$arFields = array(
						"ALLOW_DELIVERY" => $ALLOW_DELIVERY,
						"EMP_ALLOW_DELIVERY_ID" => ( IntVal($USER->GetID())>0 ? IntVal($USER->GetID()) : false )
					);
					$arFields = array_merge($arFields, $arAdditionalFields);

					$res = CSaleOrder::Update($ID, $arFields);
				}

				if ($errorMessageTmp != "")
					$errorMessage .= $errorMessageTmp;
			}
			if (isset($_REQUEST["change_comments"]) && $_REQUEST["change_comments"] == "Y")
			{
				$errorMessageTmp = "";

				if (!$bUserCanEditOrder)
					$errorMessageTmp .= GetMessage("SOD_NO_PERMS2DEL").". ";

				if (strlen($errorMessageTmp) <= 0)
				{
					if (!CSaleOrder::CommentsOrder($ID, $_REQUEST["COMMENTS"]))
					{
						if ($ex = $APPLICATION->GetException())
						{
							if ($ex->GetID() != "ALREADY_FLAG")
								$errorMessageTmp .= $ex->GetString();
						}
						else
							$errorMessageTmp .= GetMessage("ERROR_CHANGE_COMMENT").". ";
					}
				}

				if ($errorMessageTmp != "")
					$errorMessage .= $errorMessageTmp;
			}

			if (strlen($errorMessage) <= 0)
			{
				if ($crmMode)
					CRMModeOutput($ID);

				LocalRedirect("sale_order_detail.php?ID=".$ID."&save_order_result=ok&lang=".LANG.GetFilterParams("filter_", false));
			}
		}
	}
	//elseif (isset($_REQUEST["ps_update"]) && strlen($_REQUEST["ps_update"]) > 0)
	elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "ps_update")
	{
		$errorMessageTmp = "";

		$arOrder = CSaleOrder::GetByID($ID);
		if (!$arOrder)
			$errorMessageTmp .= GetMessage("ERROR_NO_ORDER")."<br>";

		if (strlen($errorMessageTmp) <= 0)
		{
			$psResultFile = "";

			$arPaySys = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"], $arOrder["PERSON_TYPE_ID"]);

			$psActionPath = $_SERVER["DOCUMENT_ROOT"].$arPaySys["PSA_ACTION_FILE"];
			$psActionPath = str_replace("\\", "/", $psActionPath);
			while (substr($psActionPath, strlen($psActionPath) - 1, 1) == "/")
				$psActionPath = substr($psActionPath, 0, strlen($psActionPath) - 1);

			if (file_exists($psActionPath) && is_dir($psActionPath))
			{
				if (file_exists($psActionPath."/result.php") && is_file($psActionPath."/result.php"))
					$psResultFile = $psActionPath."/result.php";
			}
			elseif (strlen($arPaySys["PSA_RESULT_FILE"]) > 0)
			{
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$arPaySys["PSA_RESULT_FILE"])
					&& is_file($_SERVER["DOCUMENT_ROOT"].$arPaySys["PSA_RESULT_FILE"]))
					$psResultFile = $_SERVER["DOCUMENT_ROOT"].$arPaySys["PSA_RESULT_FILE"];
			}

			if (strlen($psResultFile) <= 0)
				$errorMessageTmp .= GetMessage("SOD_NO_PS_SCRIPT").". ";
		}

		if (strlen($errorMessageTmp) <= 0)
		{
			$ORDER_ID = $ID;
			CSalePaySystemAction::InitParamArrays($arOrder, $ID, $arPaySys["PSA_PARAMS"]);
			if (!include($psResultFile))
				$errorMessageTmp .= GetMessage("ERROR_CONNECT_PAY_SYS").". ";
		}

		if (strlen($errorMessageTmp) <= 0)
		{
			$ORDER_ID = IntVal($ORDER_ID);
			$arOrder = CSaleOrder::GetByID($ORDER_ID);
			if (!$arOrder)
				$errorMessageTmp .= str_replace("#ID#", $ORDER_ID, GetMessage("SOD_NO_ORDER")).". ";
		}
		if (strlen($errorMessageTmp) <= 0)
		{
			if ($arOrder["PS_STATUS"] == "Y" && $arOrder["PAYED"] == "N")
			{
				if ($arOrder["CURRENCY"] == $arOrder["PS_CURRENCY"]
					&& doubleval($arOrder["PRICE"]) == doubleval($arOrder["PS_SUM"]))
				{
					if (!CSaleOrder::PayOrder($arOrder["ID"], "Y", True, True))
					{
						if ($ex = $APPLICATION->GetException())
							$errorMessageTmp .= $ex->GetString();
						else
							$errorMessageTmp .= str_replace("#ID#", $ORDER_ID, GetMessage("SOD_CANT_PAY")).". ";
					}
				}
			}
		}

		if ($errorMessageTmp != "")
			$errorMessage .= $errorMessageTmp;

		if (strlen($errorMessage) <= 0)
		{
			if ($crmMode)
				CRMModeOutput($ID);

			if (strlen($apply) > 0 || $_REQUEST["action"] == "ps_update")
				LocalRedirect("sale_order_detail.php?ID=".$ID."&save_order_result=ok_ps&lang=".LANG.GetFilterParams("filter_", false));

			CSaleOrder::UnLock($ID);
			LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));
		}
	}
}
elseif (!empty($dontsave))
{
	CSaleOrder::UnLock($ID);
	if ($crmMode)
		CRMModeOutput($ID);

	LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));
}
/****************/

$dbOrder = CSaleOrder::GetList(
	array("ID" => "DESC"),
	array("ID" => $ID),
	false,
	false,
	array("ID", "LID", "PERSON_TYPE_ID", "PAYED", "DATE_PAYED", "EMP_PAYED_ID", "CANCELED", "DATE_CANCELED", "EMP_CANCELED_ID", "REASON_CANCELED", "STATUS_ID", "DATE_STATUS", "PAY_VOUCHER_NUM", "PAY_VOUCHER_DATE", "EMP_STATUS_ID", "PRICE_DELIVERY", "ALLOW_DELIVERY", "DATE_ALLOW_DELIVERY", "EMP_ALLOW_DELIVERY_ID", "PRICE", "CURRENCY", "DISCOUNT_VALUE", "SUM_PAID", "USER_ID", "PAY_SYSTEM_ID", "DELIVERY_ID", "DATE_INSERT", "DATE_INSERT_FORMAT", "DATE_UPDATE", "USER_DESCRIPTION", "ADDITIONAL_INFO", "PS_STATUS", "PS_STATUS_CODE", "PS_STATUS_DESCRIPTION", "PS_STATUS_MESSAGE", "PS_SUM", "PS_CURRENCY", "PS_RESPONSE_DATE", "COMMENTS", "TAX_VALUE", "STAT_GID", "RECURRING_ID", "AFFILIATE_ID", "LOCK_STATUS", "USER_LOGIN", "USER_NAME", "USER_LAST_NAME", "USER_EMAIL", "DELIVERY_DOC_NUM", "DELIVERY_DOC_DATE")
);
if (!($arOrder = $dbOrder->Fetch()))
	LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));

$WEIGHT_UNIT = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", $arOrder["LID"]));
$WEIGHT_KOEF = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, $arOrder["LID"]));

$APPLICATION->SetTitle(GetMessage("SALE_EDIT_RECORD", array("#ID#"=>$ID)));

$aTabs = array();
$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("SODN_TAB_ORDER"), "TITLE" => GetMessage("SODN_TAB_ORDER_DESCR"), "ICON" => "sale");
//$aTabs[] = array("DIV" => "edit2", "TAB" => GetMessage("SODN_TAB_BASKET"), "TITLE" => GetMessage("SODN_TAB_BASKET_DESCR"), "ICON" => "sale");
$aTabs[] = array("DIV" => "edit3", "TAB" => GetMessage("SODN_TAB_TRANSACT"), "TITLE" => GetMessage("SODN_TAB_TRANSACT_DESCR"), "ICON" => "sale");

$tabControl = new CAdminForm("order_view", $aTabs);

$tabControl->AddTabs($customTabber);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
		array(
				"TEXT" => GetMessage("SOD_TO_LIST"),
				"LINK" => "/bitrix/admin/sale_order_detail.php?ID=".$ID."&dontsave=Y&lang=".LANGUAGE_ID.GetFilterParams("filter_"),
				"ICON"=>"btn_list",
			)
	);

$aMenu[] = array("SEPARATOR" => "Y");

if ($bUserCanEditOrder)
{
	$aMenu[] = array(
			"TEXT" => GetMessage("SOD_TO_EDIT"),
			"LINK" => "/bitrix/admin/sale_order_new.php?ID=".$ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_"),
			"ICON"=>"btn_edit",
		);
	$aMenu[] = array(
			"TEXT" => GetMessage("SOD_TO_NEW_ORDER"),
			"LINK" => "/bitrix/admin/sale_order_new.php?lang=".LANGUAGE_ID."&LID=".$arOrder["LID"],
			"ICON"=>"btn_edit",
		);
}

$aMenu[] = array(
		"TEXT" => GetMessage("SOD_TO_PRINT"),
		"LINK" => "/bitrix/admin/sale_order_print.php?ID=".$ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_"),

	);

if ($saleModulePermissions == "W" || $arOrder["PAYED"] != "Y" && $bUserCanDeleteOrder)
{
	$aMenu[] = array(
			"TEXT" => GetMessage("SODN_CONFIRM_DEL"),
			"LINK" => "javascript:if(confirm('".GetMessage("SODN_CONFIRM_DEL_MESSAGE")."')) window.location='sale_order.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get().urlencode(GetFilterParams("filter_"))."'",
			"WARNING" => "Y",
			"ICON"=>"btn_delete",
		);
}

$link = DeleteParam(array("mode"));
$link = $GLOBALS["APPLICATION"]->GetCurPage()."?mode=settings".($link <> ""? "&".$link:"");

$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?
CAdminMessage::ShowMessage($errorMessage);

if (strlen($save_order_result) > 0)
{
	$okMessage = "";

	if ($save_order_result == "ok_status")
		$okMessage = GetMessage("SOD_OK_STATUS");
	elseif ($save_order_result == "ok_cancel")
		$okMessage = GetMessage("SOD_OK_CANCEL");
	elseif ($save_order_result == "ok_pay")
		$okMessage = GetMessage("SOD_OK_PAY");
	elseif ($save_order_result == "ok_delivery")
		$okMessage = GetMessage("SOD_OK_DELIVERY");
	elseif ($save_order_result == "ok_comment")
		$okMessage = GetMessage("SOD_OK_COMMENT");
	elseif ($save_order_result == "ok_ps")
		$okMessage = GetMessage("SOD_OK_PS");
	else
		$okMessage = GetMessage("SOD_OK_OK");

	CAdminMessage::ShowNote($okMessage);
}

if (!$bUserCanViewOrder)
{
	CAdminMessage::ShowMessage(str_replace("#ID#", $ID, GetMessage("SOD_NO_PERMS2VIEW")).". ");
}
else
{
	if (!CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		CSaleOrder::Lock($ID);

	$customOrderView = COption::GetOptionString("sale", "path2custom_view_order", "");
	if (strlen($customOrderView) > 0
		&& file_exists($_SERVER["DOCUMENT_ROOT"].$customOrderView)
		&& is_file($_SERVER["DOCUMENT_ROOT"].$customOrderView))
	{
		include($_SERVER["DOCUMENT_ROOT"].$customOrderView);
	}
	else
	{
		$tabControl->BeginEpilogContent();
		?>
		<?= GetFilterHiddens("filter_"); ?>
		<?= bitrix_sessid_post(); ?>
		<input type="hidden" name="lang" value="<?= LANG ?>">
		<input type="hidden" name="ID" value="<?= $ID ?>">
		<input type="hidden" name="save_order_data" value="Y">
		<?
		$tabControl->EndEpilogContent();

		$tabControl->Begin();


		$tabControl->BeginNextFormTab();

			$tabControl->AddSection("order_id", GetMessage("P_ORDER_ID"));
				$tabControl->BeginCustomField("ORDER_DATE_CREATE", GetMessage("SOD_ORDER_DATE_CREATE"));
					?>
					<tr>
						<td width="40%"><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td width="60%"><?echo $arOrder["DATE_INSERT"]?></td>
					</tr>
					<?
				$tabControl->EndCustomField("ORDER_DATE_CREATE", '');

				$tabControl->BeginCustomField("DATE_UPDATE", GetMessage("SOD_DATE_UPDATE"));
					?>
					<tr>
						<td width="40%"><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td width="60%"><?echo $arOrder["DATE_UPDATE"]?></td>
					</tr>
					<?
				$tabControl->EndCustomField("DATE_UPDATE", '');

				$arSitesShop = array();
				$rsSites = CSite::GetList($by="id", $order="asc", Array("ACTIVE" => "Y"));
				while ($arSite = $rsSites->Fetch())
				{
					$site = COption::GetOptionString("sale", "SHOP_SITE_".$arSite["ID"], "");
					if ($arSite["ID"] == $site)
					{
						$arSitesShop[$arSite["ID"]] = array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
					}
				}

				if (count($arSitesShop) > 1)
				{
					$tabControl->BeginCustomField("ORDER_SITE", GetMessage("ORDER_SITE"), true);
					?>
					<tr>
						<td width="40%">
							<?= GetMessage("ORDER_SITE") ?>:
						</td>
						<td width="60%"><?=htmlspecialcharsbx($arSitesShop[$arOrder["LID"]]["NAME"])." (".$arOrder["LID"].")"?>
						</td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_SITE");
				}

				$tabControl->BeginCustomField("ORDER_STATUS", GetMessage("SOD_CUR_STATUS"));
					?>
					<tr>
						<td width="40%"><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td width="60%">
									<?
									$arStatusList = False;
									$arFilter = array("LID" => LANG);
									$arGroupByTmp = false;
									if ($saleModulePermissions < "W")
									{
										$arFilter["GROUP_ID"] = $GLOBALS["USER"]->GetUserGroupArray();
										$arFilter["PERM_STATUS_FROM"] = "Y";
										$arFilter["ID"] = $arOrder["STATUS_ID"];
										$arGroupByTmp = array("ID", "NAME", "MAX" => "PERM_STATUS_FROM");
									}
									$dbStatusList = CSaleStatus::GetList(
											array(),
											$arFilter,
											$arGroupByTmp,
											false,
											array("ID", "NAME", "PERM_STATUS_FROM")
										);
									$arStatusList = $dbStatusList->GetNext();

									$statusOrder = "";

									?>
									<div id="editStatusDIV">
										<select name="STATUS_ID" id="STATUS_ID" onChange="BX('change_status').value='Y';">
											<?
											if ($arStatusList)
											{
												$arFilter = array("LID" => LANG);
												$arGroupByTmp = false;
												if ($saleModulePermissions < "W")
												{
													$arFilter["GROUP_ID"] = $GLOBALS["USER"]->GetUserGroupArray();
													$arFilter["PERM_STATUS"] = "Y";
												}
												$dbStatusListTmp = CSaleStatus::GetList(
														array("SORT" => "ASC"),
														$arFilter,
														$arGroupByTmp,
														false,
														array("ID", "NAME", "SORT")
													);
												while($arStatusListTmp = $dbStatusListTmp->GetNext())
												{
													$select = "";
													if ($arStatusListTmp["ID"]==$arOrder["STATUS_ID"])
														$select = " selected";

													$statusOrder .= "<option value=\"".$arStatusListTmp["ID"]."\" ".$select.">[".$arStatusListTmp["ID"]."] ".$arStatusListTmp["NAME"]."</option>";
												}
											}

											echo $statusOrder;
										?>
										</select>
										<input type="hidden" name="change_status" id="change_status" value="N">
										<input type="hidden" name="change_status_popup" id="change_status_popup" value="N">
										<input type="submit" name="apply" value="<?=GetMessage('SALE_SAVE');?>" >
									</div>
						</td>
					</tr>

					<?if(strlen($arOrder["DATE_STATUS"]) > 0):?>
						<tr>
							<td><?=GetMessage('SOD_DATE_STATUS');?>:</td>
							<td><?=$arOrder["DATE_STATUS"]?>
								<?if (!$crmMode && IntVal($arOrder["EMP_STATUS_ID"]) > 0)
									echo fShowEditor($arOrder["EMP_STATUS_ID"]);
								?>
							</td>
						</tr>
					<?endif;?>
					<?
				$tabControl->EndCustomField("ORDER_STATUS", '');

				$tabControl->BeginCustomField("ORDER_CANCELED", GetMessage("SOD_CANCEL_Y"));
					?>
					<?if ($arOrder["CANCELED"] == "N"):?>
						<?if($bUserCanCancelOrder):?>
							<tr>
								<td width="40%">&nbsp;</td>
								<td valign="middle" >
									<a title="<?=GetMessage('SOD_CANCEL_Y')?>" onClick="fShowCancelOrder(this, '');" class="adm-btn-wrap" href="javascript:void(0);"><span class="adm-btn"><?=GetMessage('SOD_CANCEL_Y')?></span></a>
								</td>
							</tr>
						<?else:?>
							<tr>
								<td><?=GetMessage("SOD_CANCELED")?>:</td>
								<td><?=GetMessage("SALE_NO")?></td>
							</tr>
						<?endif;?>
						<?if(strlen($arOrder["DATE_CANCELED"]) > 0):?>
							<tr>
								<td><?=GetMessage('SOD_DATE_CANCELED');?>:</td>
								<td><?=$arOrder["DATE_CANCELED"]?>
									<?if (!$crmMode && IntVal($arOrder["EMP_CANCELED_ID"]) > 0)
										echo fShowEditor($arOrder["EMP_CANCELED_ID"]);
									?>
								</td>
							</tr>
						<?endif;?>

					<?else:?>
						<tr>
							<td><span class="order_cancel_left"><?=GetMessage("SOD_CANCELED")?>:</span></td>
							<td valign="top">
								<span class="order_cancel_right"><?=GetMessage("SALE_YES")?></span>
								<?if($bUserCanCancelOrder):?>&nbsp;&nbsp;<a href="javascript:void(0);" onClick="fCancelCancelOrder();" class="adm-btn-wrap"><span class="adm-btn"><?=GetMessage('SOD_CANCEL_N');?></span></a><?endif;?>
							</td>
						</tr>
						<?if(strlen($arOrder["DATE_CANCELED"]) > 0):?>
							<tr>
								<td><?=GetMessage('SOD_DATE_CANCELED');?>:</td>
								<td><?=$arOrder["DATE_CANCELED"]?>
									<?if (!$crmMode && IntVal($arOrder["EMP_CANCELED_ID"]) > 0)
										echo fShowEditor($arOrder["EMP_CANCELED_ID"]);
									?>
								</td>
							</tr>
						<?endif;?>
						<tr>
							<td><?=GetMessage('SOD_CANCEL_REASON_TITLE')?>:</td>
							<td><?=htmlspecialcharsbx($arOrder["REASON_CANCELED"])?></td>
						</tr>
					<?endif;?>

					<tr>
						<td valign="top">
							<input type="hidden" name="change_cancel" id="id_change_cancel_hidden" value="N">
							<input type="hidden" name="CANCELED" id="ORDER_CANCELED" value="<?=htmlspecialcharsbx($arOrder["CANCELED"])?>">
							<input type="hidden" name="REASON_CANCELED" id="ORDER_REASON_CANCELED" value="<?=htmlspecialcharsbx($arOrder["REASON_CANCELED"])?>">

							<div id="popup_cancel_order_form" class="sale_popup_form" style="display:none; font-size:13px;">
								<table>
									<?if ($arOrder["CANCELED"] == "Y"):?>
									<tr>
										<td><label for="FORM_CANCEL_CANCEL_ORDER"><?=GetMessage('SOD_CANCEL_N')?></label></td>
										<td>
											<input type="checkbox" name="FORM_CANCEL_CANCEL_ORDER" id="FORM_CANCEL_CANCEL_ORDER" value="N" onChange="fChangeCancelOrder();" />
										</td>
									</tr>
									<?endif;?>
									<tr>
										<td colspan="2"><?=GetMessage('SOD_CANCEL_REASON_TITLE')?><br />
											<textarea name="FORM_REASON_CANCELED" id="FORM_REASON_CANCELED" rows="3" cols="30"><?= htmlspecialcharsEx($arOrder["REASON_CANCELED"]) ?></textarea><br />
											<small><?=GetMessage('SOD_CANCEL_REASON_ADIT')?></small>
										</td>
									</tr>

								</table>
							</div>

							<script>
								function fCancelCancelOrder()
								{
									BX('id_change_cancel_hidden').value = 'Y';
									BX('ORDER_CANCELED').value = 'N';
									BX.submit(BX('order_view_form'));
								}
								function fShowCancelOrder(el, type)
								{
									if (type == '')
										BX('ORDER_CANCELED').value = 'Y';

									formCancelOrder = BX.PopupWindowManager.create("sale-popup-cancel", el, {
										offsetTop : -100,
										offsetLeft : -150,
										autoHide : true,
										closeByEsc : true,
										closeIcon : true,
										titleBar : true,
										draggable: {restrict:true},
										titleBar: {content: BX.create("span", {html: '<?=GetMessageJS('SOD_CANCEL_ORDER')?>', 'props': {'className': 'sale-popup-title-bar'}})},
										content : BX("popup_cancel_order_form")
									});
									formCancelOrder.setButtons([
										new BX.PopupWindowButton({
											text : "<?=GetMessage('SOD_POPUP_SAVE')?>",
											className : "",
											events : {
												click : function()
												{
													BX('ORDER_REASON_CANCELED').value = BX.findChild(BX('sale-popup-cancel'), {'attr': {name: 'FORM_REASON_CANCELED'}}, true, false).value;
													BX('id_change_cancel_hidden').value = 'Y';
													BX.submit(BX('order_view_form'));
												}
											}
										}),
										new BX.PopupWindowButton({
											text : "<?=GetMessage('SOD_POPUP_CANCEL')?>",
											className : "",
											events : {
												click : function()
												{
													BX('id_change_cancel_hidden').value = 'N';
													BX('ORDER_REASON_CANCELED').value = '';
													BX('ORDER_CANCELED').value = 'N';

													formCancelOrder.close();
												}
											}
										})
									]);

									formCancelOrder.show();
									BX('FORM_REASON_CANCELED').focus();
								}
							</script>
						</td>
					</tr>
					<?
				$tabControl->EndCustomField("ORDER_CANCELED", '');

			$tabControl->AddSection("order_user", GetMessage("P_ORDER_USER_ACC"));

				$tabControl->BeginCustomField("ORDER_PROPS", GetMessage("SOD_ORDER_PROPS"));
					$dbUser = CUser::GetByID($arOrder["USER_ID"]);
					$arUser = $dbUser->Fetch();
				?>
					<tr>
						<td valign="top" width="40%"><?=GetMessage('SOD_BUYER_LOGIN')?>:</td>
						<td valign="middle"><a href="/bitrix/admin/sale_buyers_profile.php?ID=<?=$arOrder["USER_ID"]?>&lang=<?=LANG?>"><?= htmlspecialcharsEx($arUser["LOGIN"]); ?></a></td>
					</tr>
					<tr>
						<td valign="top"><?echo GetMessage("P_ORDER_PERS_TYPE")?>:</td>
						<td valign="middle"><?
							echo "[";
							if ($saleModulePermissions >= "W")
								echo "<a href=\"/bitrix/admin/sale_person_type_edit.php?ID=".$arOrder["PERSON_TYPE_ID"]."&lang=".LANG."\">";
							echo $arOrder["PERSON_TYPE_ID"];
							if ($saleModulePermissions >= "W")
								echo "</a>";
							echo "] ";
							$arPersonType = CSalePersonType::GetByID($arOrder["PERSON_TYPE_ID"]);
							echo htmlspecialcharsEx($arPersonType["NAME"]);
							?>
						</td>
					</tr>
				<?
					$dbOrderProps = CSaleOrderPropsValue::GetOrderProps($ID);
					$iGroup = -1;
					while ($arOrderProps = $dbOrderProps->Fetch())
					{
						if ($iGroup != IntVal($arOrderProps["PROPS_GROUP_ID"]))
						{
							?>
							<tr>
								<td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;color:rgb(75, 98, 103);"><?=htmlspecialcharsEx($arOrderProps["GROUP_NAME"]);?></td>
							</tr>
							<?
							$iGroup = IntVal($arOrderProps["PROPS_GROUP_ID"]);
						}
						?>
						<tr>
							<td valign="top"><?echo htmlspecialcharsEx($arOrderProps["NAME"])?>:</td>
							<td valign="middle">
							<?
							if ($arOrderProps["TYPE"] == "CHECKBOX")
							{
								if ($arOrderProps["VALUE"] == "Y")
									echo GetMessage("SALE_YES");
								else
									echo GetMessage("SALE_NO");
							}
							elseif ($arOrderProps["TYPE"] == "TEXT" || $arOrderProps["TYPE"] == "TEXTAREA")
							{
								if ($arOrderProps["CODE"] == 'phone' &&
									$arOrderProps["IS_EMAIL"] == "N" &&
									$arOrderProps["IS_PAYER"] == "N" &&
									$arOrderProps["IS_PROFILE_NAME"] == "N")
								{
									echo "<a href='callto:".htmlspecialcharsEx($arOrderProps["VALUE"])."'>".htmlspecialcharsEx($arOrderProps["VALUE"])."</a>";
								}
								elseif ($arOrderProps["IS_EMAIL"] == "Y")
									echo "<a href=\"mailto:".htmlspecialcharsEx($arOrderProps["VALUE"])."\">".htmlspecialcharsEx($arOrderProps["VALUE"])."</a>";
								else
									echo htmlspecialcharsEx($arOrderProps["VALUE"]);
							}
							elseif ($arOrderProps["TYPE"] == "SELECT" || $arOrderProps["TYPE"] == "RADIO")
							{
								$arVal = CSaleOrderPropsVariant::GetByValue($arOrderProps["ORDER_PROPS_ID"], $arOrderProps["VALUE"]);
								echo htmlspecialcharsEx($arVal["NAME"]);
							}
							elseif ($arOrderProps["TYPE"] == "MULTISELECT")
							{
								$curVal = explode(",", $arOrderProps["VALUE"]);
								for ($i = 0; $i < count($curVal); $i++)
								{
									$arVal = CSaleOrderPropsVariant::GetByValue($arOrderProps["ORDER_PROPS_ID"], $curVal[$i]);
									if ($i > 0)
										echo ", ";
									echo htmlspecialcharsEx($arVal["NAME"]);
								}
							}
							elseif ($arOrderProps["TYPE"] == "LOCATION")
							{
								$arVal = CSaleLocation::GetByID($arOrderProps["VALUE"], LANG);
								echo htmlspecialcharsEx($arVal["COUNTRY_NAME"].((strlen($arVal["COUNTRY_NAME"])<=0 || strlen($arVal["CITY_NAME"])<=0) ? "" : " - ").$arVal["CITY_NAME"]);
							}
							else
							{
								echo htmlspecialcharsEx($arOrderProps["VALUE"]);
							}
							?>
							</td>
						</tr>
					<?
					}
				$tabControl->EndCustomField("ORDER_PROPS", '');

			$tabControl->AddSection("order_delivery", GetMessage("P_ORDER_DELIVERY_TITLE"));

				$tabControl->BeginCustomField("ORDER_DELIVERY", GetMessage("P_ORDER_DELIVERY"));
					?>
					<tr>
						<td width="40%"><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td>
							<?
							if (strpos($arOrder["DELIVERY_ID"], ":") !== false)
							{
								$arId = explode(":", $arOrder["DELIVERY_ID"]);

								$dbDelivery = CSaleDeliveryHandler::GetBySID($arId[0]);
								$arDelivery = $dbDelivery->Fetch();

								echo "[".$arDelivery["SID"]."] ".htmlspecialcharsEx($arDelivery["NAME"])." (".$arOrder["LID"].")";
								echo "<br />[".htmlspecialcharsEx($arId[1])."] ".htmlspecialcharsEx($arDelivery["PROFILES"][$arId[1]]["TITLE"]);
							}
							elseif (IntVal($arOrder["DELIVERY_ID"]) > 0)
							{
								$arDelivery = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
								echo "[".$arDelivery["ID"]."] ".$arDelivery["NAME"]." (".$arDelivery["LID"].")";
							}
							else
							{
								echo GetMessage("SOD_NONE");
							}
							?>
						</td>
					</tr>
				<?
				$tabControl->EndCustomField("ORDER_DELIVERY", '');

				$tabControl->BeginCustomField("ORDER_ALLOW_DELIVERY", GetMessage("P_ORDER_ALLOW_DELIVERY"));
				?>
					<?if ($arOrder["ALLOW_DELIVERY"] == "N"):?>
						<?if($bUserCanDeliverOrder):?>
							<tr>
								<td width="40%">&nbsp;</td>
								<td valign="middle" class="btn_order">
									<a title="<?=GetMessage('SOD_ALLOW_DELIVERY_DO_Y')?>" onClick="fShowAllowDelivery(this, '');" class="adm-btn adm-btn-green" href="javascript:void(0);"><?=GetMessage('SOD_ALLOW_DELIVERY_DO_Y')?></a>
								</td>
							</tr>
						<?else:?>
							<tr>
								<td><?=GetMessage("SOD_DELIVERY_IS_ALLOW")?>:</td>
								<td><?=GetMessage("SALE_NO")?></td>
							</tr>
						<?endif;?>

						<?if(strlen($arOrder["DATE_ALLOW_DELIVERY"]) > 0):?>
						<tr>
							<td><?=GetMessage('SOD_DATE_ALLOW_DELIVERY');?>:</td>
							<td><?=$arOrder["DATE_ALLOW_DELIVERY"]?>
								<?if (!$crmMode && IntVal($arOrder["EMP_ALLOW_DELIVERY_ID"]) > 0)
									echo fShowEditor($arOrder["EMP_ALLOW_DELIVERY_ID"]);
								?>
							</td>
						</tr>
						<?endif;?>
					<?else:?>
						<?if(strlen($arOrder["DELIVERY_DOC_NUM"]) > 0 || strlen($arOrder["DELIVERY_DOC_DATE"]) > 0):?>
						<tr>
							<td valign="top"><?=GetMessage('SOD_NUMBER_ALLOW_DELIVERY');?>:</td>
							<td valign="middle"><?=GetMessage("SOD_DELIV_DOC", Array("#NUM#" => htmlspecialcharsEx($arOrder["DELIVERY_DOC_NUM"]), "#DATE#" => htmlspecialcharsEx($arOrder["DELIVERY_DOC_DATE"]))) ?></td>
						</tr>
						<?endif;?>
						<tr>
							<td><?=GetMessage('SOD_DATE_ALLOW_DELIVERY2');?>:</td>
							<td><?=$arOrder["DATE_ALLOW_DELIVERY"]?>
								<?if (!$crmMode && IntVal($arOrder["EMP_ALLOW_DELIVERY_ID"]) > 0)
									echo fShowEditor($arOrder["EMP_ALLOW_DELIVERY_ID"]);
								?>
							</td>
						</tr>
						<tr>
							<td><span class="alloy_payed_left"><?=GetMessage("SOD_DELIVERY_IS_ALLOW")?>:</span></td>
							<td><span class="alloy_payed_right"><?=GetMessage("SOD_DELIVERY_YES")?></span><?if($bUserCanDeliverOrder):?>&nbsp;&nbsp;<a href="javascript:void(0);" onClick="fShowAllowDelivery(this, 'cancel');"><?=GetMessage('SOD_DELIVERY_EDIT');?></a><?endif;?></td>
						</tr>
					<?endif;?>
					<tr>
						<td colspan="2">
							<input type="hidden" name="DELIVERY_DOC_NUM" id="DELIVERY_DOC_NUM" value="<?= htmlspecialcharsEx($arOrder["DELIVERY_DOC_NUM"]) ?>" maxlength="20">
							<input type="hidden" name="DELIVERY_DOC_DATE" id="DELIVERY_DOC_DATE" value="<?= htmlspecialcharsEx($arOrder["DELIVERY_DOC_DATE"]) ?>" maxlength="20">
							<input type="hidden" name="change_delivery_form" id="id_change_delivery_form_hidden" value="N">
							<input type="hidden" name="ALLOW_DELIVERY" id="ALLOW_DELIVERY" value="Y">

							<div id="popup_form" class="sale_popup_form adm-workarea" style="display:none; font-size:13px;">
								<table>
									<tr>
										<td class="head"><?=GetMessage('SOD_POPUP_ORDER_STATUS')?>:</td>
										<td><select name="FORM_STATUS_ID" id="FORM_STATUS_ID" onChange="fChangeOrderStatus();"><?=$statusOrder?></select></td>
									</tr>
									<tr>
										<td class="head"><?=GetMessage('SOD_POPUP_NUMBER_DOC')?>:</td>
										<td>
											<input type="text" class="popup_input" id="FORM_DELIVERY_DOC_NUM" name="FORM_DELIVERY_DOC_NUM" value="<?= htmlspecialcharsEx($arOrder["DELIVERY_DOC_NUM"]) ?>" size="30" maxlength="20" class="typeinput">
										</td>
									</tr>
									<tr>
										<td class="head"><?=GetMessage('SOD_POPUP_DATE_DOC')?>:</td>
										<td>
											<?= CalendarDate("FROM_DELIVERY_DOC_DATE", $arOrder["DELIVERY_DOC_DATE"], "change_delivery_form", "10", "class=\"typeinput\""); ?>
										</td>
									</tr>

									<tr id="cancel_allow_delivery" style="display:none;">
										<td class="head"><label for="FORM_ALLOW_DELIVERY_CANCEL"><?=GetMessage('SOD_POPUP_DELIVERY_CANCEL')?>:</label></td>
										<td>
											<input type="checkbox" name="ALLOW_DELIVERY_CANCEL" id="FORM_ALLOW_DELIVERY_CANCEL" value="N" />
										</td>
									</tr>
								</table>
							</div>
							<script>
								function fChangeOrderStatus()
								{
									BX('change_status').value='Y';
									BX('change_status_popup').value='Y';
									BX('STATUS_ID').value = BX.findChild(BX('sale-popup-delivery'), {'attr': {id: 'FORM_STATUS_ID'}}, true, false).value;
								}

								function fShowAllowDelivery(el, type)
								{
									if (type == 'cancel')
										document.getElementById("cancel_allow_delivery").style.display = 'table-row';

									formAllowDelivery = BX.PopupWindowManager.create("sale-popup-delivery", el, {
										offsetTop : -100,
										offsetLeft : -150,
										autoHide : true,
										closeByEsc : true,
										closeIcon : true,
										titleBar : true,
										draggable: {restrict:true},
										titleBar: {content: BX.create("span", {html: '<?=GetMessageJS('SOD_POPUP_DELIVE_TITLE')?>', 'props': {'className': 'sale-popup-title-bar'}})},

										content : document.getElementById("popup_form")
									});
									formAllowDelivery.setButtons([
										new BX.PopupWindowButton({
											text : "<?=GetMessage('SOD_POPUP_SAVE')?>",
											className : "",
											events : {
												click : function()
												{
													BX('DELIVERY_DOC_NUM').value = BX.findChild(BX('sale-popup-delivery'), {'attr': {id: 'FORM_DELIVERY_DOC_NUM'}}, true, false).value;
													BX('DELIVERY_DOC_DATE').value = BX.findChild(BX('sale-popup-delivery'), {'attr': {name: 'FROM_DELIVERY_DOC_DATE'}}, true, false).value;
													BX('id_change_delivery_form_hidden').value = 'Y';

													if (BX.findChild(BX('sale-popup-delivery'), {'attr': {id: 'FORM_ALLOW_DELIVERY_CANCEL'}}, true, false).checked)
														BX('ALLOW_DELIVERY').value = 'N';
													else
														BX('ALLOW_DELIVERY').value = "Y";


													BX.submit(BX('order_view_form'));
												}
											}
										}),
										new BX.PopupWindowButton({
											text : "<?=GetMessage('SOD_POPUP_CANCEL')?>",
											className : "",
											events : {
												click : function()
												{
													BX('ALLOW_DELIVERY').value = 'Y';
													BX('change_status_popup').value='N';
													formAllowDelivery.close();
												}
											}
										})
									]);

									formAllowDelivery.show();
									BX('FORM_DELIVERY_DOC_NUM').focus();
								}
							</script>
						</td>
					</tr>
				<?
				$tabControl->EndCustomField("ORDER_ALLOW_DELIVERY", '');

			$tabControl->AddSection("order_payment", GetMessage("P_ORDER_PAYMENT"));

				$tabControl->BeginCustomField("ORDER_PAYMENT", GetMessage("P_ORDER_PAYMENT"));
				?>
					<tr>
						<td valign="top"><?=GetMessage("P_ORDER_PAY_SYSTEM")?>:</td>
						<td valign="middle"><?
							if (IntVal($arOrder["PAY_SYSTEM_ID"]) > 0)
							{
								$arPaySys = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"], $arOrder["PERSON_TYPE_ID"]);
								if ($arPaySys)
								{
									echo '[';
									if ($saleModulePermissions >= "W")
										echo '<a href="/bitrix/admin/sale_pay_system_edit.php?ID='.$arPaySys["ID"].'&lang='.LANGUAGE_ID.'">';
									echo $arPaySys["ID"];
									if ($saleModulePermissions >= "W")
										echo '</a>';
									echo '] '.htmlspecialcharsEx($arPaySys["NAME"]."");
								}
								else
									echo "<font color=\"#FF0000\">".GetMessage("SOD_PAY_SYS_DISC")."</font>";
							}
							else
								GetMessage("SOD_NONE");
							?>
						</td>
					</tr>
				<?
				$tabControl->EndCustomField("ORDER_PAYMENT", '');

				$tabControl->BeginCustomField("ORDER_PAYED", GetMessage("P_ORDER_PAYED"));
				?>
					<?if ($arOrder["PAYED"] == "N"):?>
						<tr>
							<td valign="top"><?=GetMessage('SOD_PAYED_SUM');?>:</td>
							<td valign="middle"><?=SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"])?></td>
						</tr>
						<?if(strlen($arOrder["DATE_PAYED"]) > 0):?>
						<tr>
							<td><?=GetMessage('SOD_DATE_ALLOW_PAY_CHANGE');?>:</td>
							<td><?=$arOrder["DATE_PAYED"]?>
								<?if (!$crmMode && IntVal($arOrder["EMP_PAYED_ID"]) > 0)
									echo fShowEditor($arOrder["EMP_PAYED_ID"]);
								?>
							</td>
						</tr>
						<?endif;?>
						<?if($bUserCanPayOrder):?>
							<tr>
								<td>&nbsp;</td>
								<td valign="middle" class="btn_order">
									<a title="<?=GetMessage('SOD_DO_PAY_ORDER')?>" onClick="fShowAllowPay(this);" class="adm-btn adm-btn-green" href="javascript:void(0);"><?=GetMessage('SOD_DO_PAY_ORDER')?></a>
								</td>
							</tr>
						<?else:?>
							<tr>
								<td><?=GetMessage("SOD_PAYED_IS_ALLOW")?>:</td>
								<td><?=GetMessage("SALE_NO")?></td>
							</tr>
						<?endif;?>
					<?else:?>
						<?if(strlen($arOrder["PAY_VOUCHER_NUM"]) > 0):?>
						<tr>
							<td><?=GetMessage('SOD_NUMBER_ALLOW_PAY');?>:</td>
							<td><?= str_replace("#DATE#", $arOrder["PAY_VOUCHER_DATE"], str_replace("#NUM#", htmlspecialcharsEx($arOrder["PAY_VOUCHER_NUM"]), GetMessage("SOD_PAY_DOC"))) ?></td>
						</tr>
						<?endif;?>
						<tr>
							<td><?=GetMessage('SOD_DATE_ALLOW_PAY_CHANGE');?>:</td>
							<td><?=$arOrder["DATE_PAYED"]?>
								<?if (!$crmMode && IntVal($arOrder["EMP_PAYED_ID"]) > 0)
									echo fShowEditor($arOrder["EMP_PAYED_ID"]);
								?>
							</td>
						</tr>
						<tr>
							<td><span class="alloy_payed_left"><?=GetMessage("SOD_PAYED_IS_ALLOW")?>:</span></td>
							<td>
								<span class="alloy_payed_right"><?=GetMessage("SOD_PAYED_YES")?></span>
								<?if($bUserCanPayOrder):?>&nbsp;&nbsp;<a href="javascript:void(0);" onClick="fShowAllowPay(this);"><?=GetMessage('SOD_DELIVERY_EDIT');?><?endif;?></a>
							</td>
						</tr>
					<?endif?>

					<tr>
						<td colspan="2">
							<input type="hidden" name="PAY_VOUCHER_NUM" id="PAY_VOUCHER_NUM" value="<?= htmlspecialcharsEx($arOrder["PAY_VOUCHER_NUM"]) ?>" maxlength="20">
							<input type="hidden" name="PAY_VOUCHER_DATE" id="PAY_VOUCHER_DATE" value="<?= htmlspecialcharsEx($arOrder["PAY_VOUCHER_DATE"]) ?>" maxlength="20">
							<input type="hidden" name="change_pay_form" id="id_change_pay_form_hidden" value="N">
							<input type="hidden" name="PAYED" id="PAYED" value="Y">
							<input type="hidden" name="PAY_FROM_ACCOUNT_BACK" id="PAY_FROM_ACCOUNT_BACK" value="N">
							<input type="hidden" name="PAY_FROM_ACCOUNT" id="PAY_FROM_ACCOUNT" value="N">

							<div id="popup_form_pay" class="sale_popup_form adm-workarea" style="display:none; font-size:13px;">
								<table>
									<tr>
										<td class="head"><?=GetMessage('SOD_POPUP_PAY_STATUS')?>:</td>
										<td><select name="FORM_PAY_STATUS_ID" id="FORM_PAY_STATUS_ID" onChange="fPayChangeOrderStatus();"><?=$statusOrder?></select></td>
									</tr>
									<tr>
										<td class="head"><?=GetMessage('SOD_POPUP_PAY_NUMBER_DOC')?>:</td>
										<td>
											<input type="text" id="FORM_PAY_VOUCHER_NUM" class="popup_input" name="FORM_PAY_VOUCHER_NUM" value="<?= htmlspecialcharsEx($arOrder["PAY_VOUCHER_NUM"]) ?>" size="30" maxlength="20" class="typeinput">
										</td>
									</tr>
									<tr>
										<td class="head"><?=GetMessage('SOD_POPUP_PAY_DATE_DOC')?>:</td>
										<td>
											<?= CalendarDate("FROM_PAY_VOUCHER_DATE", $arOrder["PAY_VOUCHER_DATE"], "change_pay_form", "10", "class=\"typeinput\""); ?>
										</td>
									</tr>

									<?
									$dbUserAccount = CSaleUserAccount::GetList(
										array(),
										array(
											"USER_ID" => $arOrder["USER_ID"],
											"CURRENCY" => $arOrder["CURRENCY"],
										)
									);
									$arUserAccount = $dbUserAccount->GetNext();
									?>
									<?if ($arOrder["PAYED"] == "N" && floatval($arUserAccount["CURRENT_BUDGET"]) >= $arOrder["PRICE"]):?>
										<tr>
											<td class="head" nowrap><?=GetMessage('SOD_ORDER_USER_BUDGET')?>:</td>
											<td><b><?=SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $arOrder["CURRENCY"]);?></b></td>
										</tr>
										<tr id="pay_from_account">
											<td class="head" nowrap><label for="FORM_PAY_FROM_ACCOUNT"><?=GetMessage('SOD_PAY_ACCOUNT')?>:</label></td>
											<td>
												<input type="checkbox" value="Y" name="FORM_PAY_FROM_ACCOUNT" id="FORM_PAY_FROM_ACCOUNT" onChange="fPayFromAccount();" />
											</td>
										</tr>
									<?endif;?>

									<?if ($arOrder["PAYED"] == "Y"):?>
										<tr id="cancel_allow_pay">
											<td class="head"><label for="FORM_ALLOW_PAY_CANCEL"><?=GetMessage('SOD_POPUP_PAY_CANCEL')?>:</label></td>
											<td>
												<input type="checkbox" name="FORM_ALLOW_PAY_CANCEL" id="FORM_ALLOW_PAY_CANCEL" value="N" onChange="fPayChangeAllow();" />
											</td>
										</tr>
										<tr id="repay_to_account">
											<td class="head"><label for="FORM_PAY_FROM_ACCOUNT_BACK"><?=GetMessage('SOD_PAY_ACCOUNT_BACK')?>:</label></td>
											<td>
												<input type="checkbox" name="FORM_PAY_FROM_ACCOUNT_BACK" id="FORM_PAY_FROM_ACCOUNT_BACK" value="N" onChange="fPayRetriveAccount();" />
											</td>
										</tr>
									<?endif;?>
								</table>
							</div>
							<script>
								function fPayFromAccount()
								{
									if (BX.findChild(BX('sale-popup-pay'), {'attr': {id: 'FORM_PAY_FROM_ACCOUNT'}}, true, false).checked)
										BX('PAY_FROM_ACCOUNT').value = 'Y';
									else
										BX('PAY_FROM_ACCOUNT').value = "N";
								}
								function fPayRetriveAccount()
								{
									if (BX.findChild(BX('sale-popup-pay'), {'attr': {id: 'FORM_PAY_FROM_ACCOUNT_BACK'}}, true, false).checked)
										BX('PAY_FROM_ACCOUNT_BACK').value = 'Y';
									else
										BX('PAY_FROM_ACCOUNT_BACK').value = "N";
								}
								function fPayChangeAllow()
								{
									if (BX.findChild(BX('sale-popup-pay'), {'attr': {id: 'FORM_ALLOW_PAY_CANCEL'}}, true, false).checked)
										BX('PAYED').value = 'N';
									else
										BX('PAYED').value = "Y";
								}
								function fPayChangeOrderStatus()
								{
									BX('change_status').value='Y';
									BX('change_status_popup').value='Y';
									BX('STATUS_ID').value = BX.findChild(BX('sale-popup-pay'), {'attr': {id: 'FORM_PAY_STATUS_ID'}}, true, false).value;
								}

								function fShowAllowPay(el)
								{
									formAllowPay = BX.PopupWindowManager.create("sale-popup-pay", el, {
										offsetTop : -100,
										offsetLeft : -150,
										autoHide : true,
										closeByEsc : true,
										closeIcon : true,
										titleBar : true,
										draggable: {restrict:true},
										titleBar: {content: BX.create("span", {html: '<?=GetMessageJS('SOD_POPUP_PAY_TITLE')?>', 'props': {'className': 'sale-popup-title-bar'}})},
										content : document.getElementById("popup_form_pay")
									});
									formAllowPay.setButtons([
										new BX.PopupWindowButton({
											text : "<?=GetMessage('SOD_POPUP_SAVE')?>",
											className : "",
											events : {
												click : function()
												{
													BX('PAY_VOUCHER_NUM').value = BX.findChild(BX('popup_form_pay'), {'attr': {id: 'FORM_PAY_VOUCHER_NUM'}}, true, false).value;
													BX('PAY_VOUCHER_DATE').value = BX.findChild(BX('popup_form_pay'), {'attr': {name: 'FROM_PAY_VOUCHER_DATE'}}, true, false).value;
													BX('id_change_pay_form_hidden').value = 'Y';

													BX.submit(BX('order_view_form'));
												}
											}
										}),
										new BX.PopupWindowButton({
											text : "<?=GetMessage('SOD_POPUP_CANCEL')?>",
											className : "",
											events : {
												click : function()
												{
													BX('PAYED').value = 'Y';
													BX('change_status_popup').value='N';
													formAllowPay.close();
												}
											}
										})
									]);

									formAllowPay.show();
									BX('FORM_PAY_VOUCHER_NUM').focus();
								}
							</script>
						</td>
					</tr>
				<?
				$tabControl->EndCustomField("ORDER_PAYED", '');

			
				$arPaySys = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"], $arOrder["PERSON_TYPE_ID"]);
				if (strlen($arOrder["PS_STATUS"]) > 0)
				{
					$tabControl->BeginCustomField("ORDER_PS_STATUS", GetMessage("P_ORDER_PS_STATUS"));
					?>
					<tr>
						<td><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td>
							<?
							echo (($arOrder["PS_STATUS"]=="Y") ? "OK" : "N");

							if ($arPaySys["PSA_HAVE_RESULT"] == "Y" || strlen($arPaySys["PSA_RESULT_FILE"]) > 0)
							{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="/bitrix/admin/sale_order_detail.php?ID=<?= $ID ?>&action=ps_update&lang=<?= LANG ?><?echo GetFilterParams("filter_")?>&<?= bitrix_sessid_get() ?>"><?echo GetMessage("P_ORDER_PS_STATUS_UPDATE") ?> &gt;&gt;</a>
								<?
							}
							?>
						</td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_PS_STATUS", '');

					$tabControl->BeginCustomField("ORDER_PS_STATUS_CODE", GetMessage("P_ORDER_PS_STATUS"));
					?>
					<tr>
						<td><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td><?echo $arOrder["PS_STATUS_CODE"] ;?></td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_PS_STATUS_CODE", '');

					$tabControl->BeginCustomField("ORDER_PS_STATUS_DESCRIPTION", GetMessage("P_ORDER_PS_STATUS_DESCRIPTION"));
					?>
					<tr>
						<td><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td><?echo $arOrder["PS_STATUS_DESCRIPTION"] ;?></td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_PS_STATUS_DESCRIPTION", '');

					$tabControl->BeginCustomField("ORDER_PS_STATUS_MESSAGE", GetMessage("P_ORDER_PS_STATUS_MESSAGE"));
					?>
					<tr>
						<td><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td><?echo $arOrder["PS_STATUS_MESSAGE"] ;?></td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_PS_STATUS_MESSAGE", '');

					$tabControl->BeginCustomField("ORDER_PS_SUM", GetMessage("P_ORDER_PS_SUM"));
					?>
					<tr>
						<td><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td><?echo $arOrder["PS_SUM"] ;?></td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_PS_SUM", '');

					$tabControl->BeginCustomField("ORDER_PS_CURRENCY", GetMessage("P_ORDER_PS_CURRENCY"));
					?>
					<tr>
						<td><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td><?echo $arOrder["PS_CURRENCY"] ;?></td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_PS_CURRENCY", '');

					$tabControl->BeginCustomField("ORDER_PS_RESPONSE_DATE", GetMessage("P_ORDER_PS_RESPONSE_DATE"));
					?>
					<tr>
						<td><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td><?echo $arOrder["PS_RESPONSE_DATE"]; ?></td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_PS_RESPONSE_DATE", '');
				}
				elseif ($arPaySys["PSA_HAVE_RESULT"] == "Y" || strlen($arPaySys["PSA_RESULT_FILE"]) > 0)
				{
					$tabControl->BeginCustomField("ORDER_PS_STATUS_REC", GetMessage("P_ORDER_PS_STATUS"));
					?>
					<tr>
						<td><?echo $tabControl->GetCustomLabelHTML()?>:</td>
						<td><a href="/bitrix/admin/sale_order_detail.php?ID=<?= $ID ?>&action=ps_update&lang=<?= LANG ?><?= GetFilterParams("filter_") ?>&<?= bitrix_sessid_get() ?>"><?= GetMessage("P_ORDER_PS_STATUS_UPDATE") ?> &gt;&gt;</a></td>
					</tr>
					<?
					$tabControl->EndCustomField("ORDER_PS_STATUS_REC", '');
				}

			$tabControl->AddSection("order_comments", GetMessage("SOD_COMMENTS"));

				$tabControl->BeginCustomField("ORDER_COMMENTS", GetMessage("SOD_COMMENTS"));

					if (strlen($arOrder["USER_DESCRIPTION"])>0)
					{
						?>
						<tr>
							<td valign="top"><?echo GetMessage("P_ORDER_ADDITIONAL_INFO")?>:</td>
							<td valign="middle"><?echo htmlspecialcharsEx($arOrder["USER_DESCRIPTION"]); ?></td>
						</tr>
						<?
					}

					if (strlen($arOrder["ADDITIONAL_INFO"])>0)
					{
						?>
						<tr>
							<td valign="top"><?echo GetMessage("P_ORDER_ADDITIONAL_INFO")?>:</td>
							<td valign="middle"><?echo htmlspecialcharsEx($arOrder["ADDITIONAL_INFO"]); ?></td>
						</tr>
						<?
					}
				?>
				<tr>
					<td valign="top"><?echo GetMessage('SOD_ORDER_COMMENT_MANAGER_TITLE');?>:</td>
					<td valign="middle">
						<div id="hover_comment">
							<span id="manager-comment-title" onClick="fShowComment(this);">
								<? 
								if(strlen($arOrder["COMMENTS"]) > 0) 
									echo htmlspecialcharsbx($arOrder["COMMENTS"]);
								else 
									echo GetMessage('SOD_ORDER_COMMENT_MANAGER');
								?>
							</span>
							<span class="pencil"></span>
						</div>
						
						<textarea id="manager-comment-text"  name="COMMENTS" class="comment" onChange="fEditComment(this, 'change');" onblur="fEditComment(this, 'exit');"><?= htmlspecialcharsbx($arOrder["COMMENTS"]) ?></textarea>
						<input type="hidden" name="change_comments" id="id_change_comments_hidden" value="N">
						
						<script>
							function fShowComment(el)
							{
								BX(el).style.display = 'none';
								BX('manager-comment-text').style.display = 'block';
								BX('manager-comment-text').focus();

							}
							function fEditComment(el, type)
							{
								if (type == 'change')
								{
									BX.showWait();

									BX.ajax.post('/bitrix/admin/sale_order_detail.php', '<?=CUtil::JSEscape(bitrix_sessid_get())?>&ORDER_AJAX=Y&'+'&comment='+el.value+'&change=Y&id=<?=$ID?>', fEditCommentResult);
									if (BX('manager-comment-text').value.length > 0)
										BX('manager-comment-title').innerHTML = BX('manager-comment-text').value;
									else
										BX('manager-comment-title').innerHTML = '<?=GetMessage('SOD_ORDER_COMMENT_MANAGER')?>';
								}

								BX('manager-comment-title').style.display = 'inline-block';
								BX('manager-comment-text').style.display = 'none';
							}
							
							function fEditCommentResult(res)
							{
								BX.closeWait();
							}
						</script>
					</td>
				</tr>
				<?
				$tabControl->EndCustomField("ORDER_COMMENTS", '');

//order list
			$tabControl->AddSection("buyer_order", GetMessage("SOD_ORDER"));

				$tabControl->BeginCustomField("orders_list", GetMessage("SOD_ORDER"));
				?>
				<tr>
					<td colspan="2" valign="top">
						<table cellpadding="3" cellspacing="1" border="0" width="100%" class="internal" id="BASKET_TABLE">
						<tr class="heading">
							<td><?echo GetMessage("SOD_ORDER_PHOTO")?></td>
							<td><?echo GetMessage("SOD_ORDER_NAME")?></td>
							<td><?echo GetMessage("SOD_ORDER_QUANTITY")?></td>
							<td><?echo GetMessage("SOD_ORDER_BALANCE")?></td>
							<td><?echo GetMessage("SOD_ORDER_PROPS")?></td>
							<td><?echo GetMessage("SOD_ORDER_PRICE")?></td>
							<td><?echo GetMessage("SOD_ORDER_SUMMA")?></td>
						</tr>

						<?
						$arCurFormat = CCurrencyLang::GetCurrencyFormat($arOrder["CURRENCY"]);
						$CURRENCY_FORMAT = trim(str_replace("#", '', $arCurFormat["FORMAT_STRING"]));

						$ORDER_TOTAL_PRICE = 0;
						$arFilterRecomendet = array();
						$dbBasket = CSaleBasket::GetList(
							array("ID" => "ASC"),
							array("ORDER_ID" => $ID),
							false,
							false,
							array("ID", "PRODUCT_ID", "PRODUCT_PRICE_ID", "PRICE", "CURRENCY", "WEIGHT", "QUANTITY", "NAME", "MODULE", "CALLBACK_FUNC", "NOTES", "DETAIL_PAGE_URL", "DISCOUNT_PRICE", "DISCOUNT_VALUE", "ORDER_CALLBACK_FUNC", "CANCEL_CALLBACK_FUNC", "PAY_CALLBACK_FUNC", "CATALOG_XML_ID", "PRODUCT_XML_ID", "VAT_RATE")
						);
						while ($arBasket = $dbBasket->GetNext())
						{
							$ORDER_TOTAL_PRICE += ($arBasket["PRICE"] + $arBasket["DISCOUNT_PRICE"]) * $arBasket["QUANTITY"];
							$arFilterRecomendet[] = $arBasket["PRODUCT_ID"];
						?>
						<tr>
							<td class="photo">
								<?
								$productImg = "";
								if (CModule::IncludeModule('iblock'))
								{
									$rsProductInfo = CIBlockElement::GetByID($arBasket["PRODUCT_ID"]);
									$arProductInfo = $rsProductInfo->GetNext();

									if($arProductInfo["PREVIEW_PICTURE"] != "")
										$productImg = $arProductInfo["PREVIEW_PICTURE"];
									elseif($arProductInfo["DETAIL_PICTURE"] != "")
										$productImg = $arProductInfo["DETAIL_PICTURE"];

									if ($arProductInfo["IBLOCK_ID"] > 0 && $arProductInfo["IBLOCK_SECTION_ID"] > 0)
										$arBasket["EDIT_PAGE_URL"] = "/bitrix/admin/iblock_element_edit.php?ID=".$arBasket["PRODUCT_ID"]."&type=catalog&lang=".LANG."&IBLOCK_ID=".$arProductInfo["IBLOCK_ID"]."&find_section_section=".$arProductInfo["IBLOCK_SECTION_ID"];
								}

								if ($productImg != "")
								{
									$arFile = CFile::GetFileArray($productImg);
									$productImg = CFile::ResizeImageGet($arFile, array('width'=>80, 'height'=>80), BX_RESIZE_IMAGE_PROPORTIONAL, false, false);
									$arBasket["PICTURE"] = $productImg;
								}

								if (is_array($arBasket["PICTURE"]))
										echo "<img src=\"".$arBasket["PICTURE"]["src"]."\" alt=\"\" width=\"80\" border=\"0\" />";
									else
										echo "<div class=\"no_foto\">".GetMessage('SOD_NO_FOTO')."</div>";
								?>
							</td>
							<td class="order_name">
								<?if (strlen($arBasket["EDIT_PAGE_URL"]) > 0):?>
									<a href="<?echo $arBasket["EDIT_PAGE_URL"]?>" target="_blank">
								<?endif;?>
								<?echo trim($arBasket["NAME"])?>
								<?if (strlen($arBasket["EDIT_PAGE_URL"]) > 0):?>
									</a>
								<?endif;?>
							</td>
							<td class="order_count">
								<?echo $arBasket["QUANTITY"];?>
							</td>
							<td class="order_count">
								<?
								$balance = 0;
								if ($val["MODULE"] == "catalog" && CModule::IncludeModule('catalog'))
								{
									$ar_res = CCatalogProduct::GetByID($arBasket["PRODUCT_ID"]);
									$balance = FloatVal($ar_res["QUANTITY"]);
								}
								?>
								<?echo $balance?>
							</td>
							<td class="props">
								<?
								$dbBasketProps = CSaleBasket::GetPropsList(
										array("SORT" => "ASC", "NAME" => "ASC"),
										array("BASKET_ID" => $arBasket["ID"]),
										false,
										false,
										array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
									);
								while ($arBasketProps = $dbBasketProps->GetNext())
									echo htmlspecialcharsEx($arBasketProps["NAME"]).": ".htmlspecialcharsEx($arBasketProps["VALUE"])."<br />";
								?>
							</td>

							<td class="order_price" nowrap>
									<?
									$priceDiscount = $priceBase = ($arBasket["DISCOUNT_PRICE"] + $arBasket["PRICE"]);
									if(DoubleVal($priceBase) > 0)
										$priceDiscount = IntVal(($arBasket["DISCOUNT_PRICE"] * 100) / $priceBase);
									?>

									<div class="edit_price">
										<span class="default_price_product" >
											<span class="formated_price"><?=CurrencyFormatNumber($arBasket["PRICE"], $arBasket["CURRENCY"]);?></span>
										</span>
										<span class='currency_price'><?=$CURRENCY_FORMAT?></span>
									</div>
									<?if ($priceDiscount > 0):?>
										<div class="base_price" id="DIV_BASE_PRICE_WITH_DISCOUNT_<?=$arBasket["PRODUCT_ID"]?>">
											<?=CurrencyFormatNumber($priceBase, $arBasket["CURRENCY"]);?>
											<span class='currency_price'><?=$CURRENCY_FORMAT?></span>
										</div>
										<div class="discount">(<?=getMessage('SOD_PRICE_DISCOUNT')." ".$priceDiscount?>%)</div>
									<?endif;?>
									<div class="base_price_title"><?=GetMessage('SOD_DASE_PRICE');?></div>
							</td>
							<td class="product_summa" nowrap>
								<div><?=CurrencyFormatNumber(($arBasket["QUANTITY"] * $arBasket["PRICE"]), $arBasket["CURRENCY"]);?> <span><?=$CURRENCY_FORMAT?></span></div>
							</td>
						</tr>
						<?
						}//end while order
						?>
						</table>
					</td>
				</tr>
				<?
				$tabControl->EndCustomField("orders_list");

				$tabControl->BeginCustomField("orders_itog", GetMessage("SOD_ORDER_ITOG"));
				?>
				<tr>
					<td colspan="2" valign="top">
						<br>
						<table width="100%" class="order_summary">
						<tr>
							<td class="load_product" valign="top">
								<table width="100%" class="itog_header"><tr><td><?=GetMessage('SOD_SUBTAB_RECOM_REQUEST');?></td></tr></table>
								<br>

								<div id="tabs">
									<?
									$displayNone = "block";
									$displayNoneBasket = "block";
									$displayNoneViewed = "block";

									$arRecomendetResultTmp = CSaleProduct::GetRecommendetProduct($arOrder["USER_ID"], $arOrder["LID"], $arFilterRecomendet);
									$arRecomendetResult = array();
									if (count($arRecomendetResultTmp) > 2)
									{
										$arRecomendetResult[] = $arRecomendetResultTmp[0];
										$arRecomendetResult[] = $arRecomendetResultTmp[1];
									}
									else
										$arRecomendetResult = $arRecomendetResultTmp;

									if (count($arRecomendetResult) <= 0)
										$displayNone = "none";

									$arErrors = array();
									$arFuserItems = CSaleUser::GetList(array("USER_ID" => intval($arOrder["USER_ID"])));

									$arShoppingCart = CSaleBasket::DoGetUserShoppingCart($arOrder["LID"], $arOrder["USER_ID"], $arFuserItems["ID"], $arErrors, array());
									$busketCnt = count($arShoppingCart);
									if (count($arShoppingCart) > 2)
									{
										$arTmp = array();
										$arTmp[] = $arShoppingCart[0];
										$arTmp[] = $arShoppingCart[1];
										$arShoppingCart = $arTmp;
									}
									if (count($arShoppingCart) <= 0)
										$displayNoneBasket = "none";

									$arViewed = array();
									$dbViewsList = CSaleViewedProduct::GetList(
											array(),
											array("FUSER_ID" => $arFuserItems["ID"], ">PRICE" => 0, "!CURRENCY" => "", "LID" => $arOrder["LID"]),
											array("COUNT" => "PRODUCT_ID"),
											false,
											array()
											);
									$arViewsTmp = $dbViewsList->Fetch();
									$viewedCnt = $arViewsTmp['PRODUCT_ID'];

									$dbViewsList = CSaleViewedProduct::GetList(
											array("DATE_VISIT"=>"DESC"),
											array("FUSER_ID" => $arFuserItems["ID"], ">PRICE" => 0, "!CURRENCY" => "", "LID" => $arOrder["LID"]),
											false,
											array('nTopCount' => 2),
											array('ID', 'PRODUCT_ID', 'LID', 'MODULE', 'NAME', 'DETAIL_PAGE_URL', 'PRICE', 'CURRENCY', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
										);
									while ($arViews = $dbViewsList->Fetch())
										$arViewed[] = $arViews;
									if (count($arViewed) <= 0)
										$displayNoneViewed = "none";

									$tabBasket = "tabs";
									$tabViewed = "tabs";

									if ($displayNoneBasket == 'none' && $displayNone == 'none' && $displayNoneViewed == 'block')
										$tabViewed .= " active";
									if ($displayNoneBasket == 'block' && $displayNone == 'none')
										$tabBasket .= " active";

									?>
									<div id="tab_1" style="display:<?=$displayNone?>"       class="tabs active"     onClick="fTabsSelect('buyer_recmon', this);" ><?=GetMessage('SOD_SUBTAB_RECOMENET')?></div>
									<div id="tab_2" style="display:<?=$displayNoneBasket?>" class="<?=$tabBasket?>" onClick="fTabsSelect('buyer_busket', this);"><?=GetMessage('SOD_SUBTAB_BUSKET')?></div>
									<div id="tab_3" style="display:<?=$displayNoneViewed?>" class="<?=$tabViewed?>" onClick="fTabsSelect('buyer_viewed', this);"><?=GetMessage('SOD_SUBTAB_LOOKED')?></div>

									<?
									if ($displayNone == 'block')
									{
										$displayNoneBasket = 'none';
										$displayNoneViewed = 'none';
									}
									if ($displayNoneBasket == 'block')
									{
										$displayNone = 'none';
										$displayNoneViewed = 'none';
									}
									if ($displayNoneViewed == 'block')
									{
										$displayNone = 'none';
										$displayNoneBasket = 'none';
									}
									?>
									<div id="buyer_recmon" class="tabstext active" style="display:<?=$displayNone?>">
										<?echo fGetFormatedProduct($arOrder["USER_ID"], $arOrder["LID"], $arRecomendetResult, count($arRecomendetResultTmp), $arOrder["CURRENCY"], $crmMode, 'recom');?>
									</div>

									<div id="buyer_busket" class="tabstext active" style="display:<?=$displayNoneBasket?>">
									<?
										if (count($arShoppingCart) > 0)
											echo fGetFormatedProduct($arOrder["USER_ID"], $arOrder["LID"], $arShoppingCart, $busketCnt, $arOrder["CURRENCY"], $crmMode, 'busket');
									?>
									</div>

									<div id="buyer_viewed" class="tabstext active" style="display:<?=$displayNoneViewed?>">
									<?
										if (count($arViewed) > 0)
											echo fGetFormatedProduct($arOrder["USER_ID"], $arOrder["LID"], $arViewed, $viewedCnt, $arOrder["CURRENCY"], $crmMode, 'viewed');
									?>
									</div>
								</div>
								<script>
								function fTabsSelect(tabText, el)
								{
									BX('tab_1').className = "tabs";
									BX('tab_2').className = "tabs";
									BX('tab_3').className = "tabs";

									BX(el).className = "tabs active";
									BX(el).className = "tabs active";
									BX(el).style.display = 'block';

									BX('buyer_recmon').className = "tabstext";
									BX('buyer_busket').className = "tabstext";
									BX('buyer_viewed').className = "tabstext";
									BX('buyer_recmon').style.display = 'none';
									BX('buyer_busket').style.display = 'none';
									BX('buyer_viewed').style.display = 'none';

									BX(tabText).style.display = 'block';
									BX(tabText).className = "tabstext active";
								}
								</script>
							</td>
							<td class="summary" valign="top">
								<div class="order-itog">
									<table>
										<tr>
											<td class="title"><?echo GetMessage("SOD_TOTAL_PRICE")?></td>
											<td nowrap style="white-space:nowrap;"><?=SaleFormatCurrency($ORDER_TOTAL_PRICE, $arOrder["CURRENCY"]);?></td>
										</tr>
										<tr class="price">
											<td class="title"><?echo GetMessage("SOD_TOTAL_PRICE_WITH_DISCOUNT_MARGIN")?></td>
											<td nowrap style="white-space:nowrap;"><?=SaleFormatCurrency($arOrder["DISCOUNT_VALUE"] + $arOrder["PRICE"]-$arOrder["PRICE_DELIVERY"], $arOrder["CURRENCY"]);?></td>
										</tr>
										<tr>
											<td class="title"><?echo GetMessage("SOD_TOTAL_PRICE_DELIVERY")?></td>
											<td nowrap style="white-space:nowrap;"><?=SaleFormatCurrency($arOrder["PRICE_DELIVERY"], $arOrder["CURRENCY"]);?></td>
										</tr>

										<?if (floatval($arOrder["DISCOUNT_VALUE"]) > 0):?>
										<tr class="price">
											<td class="title" >
												<?echo GetMessage("NEWO_TOTAL_DISCOUNT_PRICE_VALUE")?>
											</td>
											<td nowrap style="white-space:nowrap;">
													<div><?=SaleFormatCurrency($arOrder["DISCOUNT_VALUE"], $arOrder["CURRENCY"]);?></div>
											</td>
										</tr>
										<?endif;?>
										<tr class="itog">
											<td class='ileft'><div style="white-space:nowrap;"><?echo GetMessage("SOD_TOTAL_PRICE_TOTAL")?></div></td>
											<td class='iright' nowrap><div style="white-space:nowrap;"><?=SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]);?></div></td>
										</tr>
										<?if (floatval($arOrder["SUM_PAID"]) > 0):?>
											<tr class="price">
												<td class="title"><?echo GetMessage("SOD_TOTAL_PRICE_PAYED")?></td>
												<td nowrap style="white-space:nowrap;"><?=SaleFormatCurrency($arOrder["SUM_PAID"], $arOrder["CURRENCY"]);?></td>
											</tr>
										<?endif;?>
										<?if ($arOrder["PAYED"] == "Y"):?>
											<tr class="price">
												<td class="title"><?echo GetMessage("SOD_TOTAL_PRICE_PAYED")?></td>
												<td nowrap style="white-space:nowrap;"><?=SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]);?></td>
											</tr>
										<?endif;?>
									</table>
								</div>
							</td>
						</tr>
						</table>

						<script>
								/*
								* click on recommendet More
								*/
								function fGetMoreProduct(type)
								{
									BX.showWait();
									productData = <?=CUtil::PhpToJSObject($arFilterRecomendet)?>;
									var userId = '<?=$arOrder["USER_ID"]?>';
									var fUserId = '<?=$arFuserItems["ID"]?>';
									var currency = '<?=$arOrder["CURRENCY"]?>';
									var lid = '<?=$arOrder["LID"]?>';

									BX.ajax.post('/bitrix/admin/sale_order_detail.php', '<?=CUtil::JSEscape(bitrix_sessid_get())?>&ORDER_AJAX=Y&type='+type+'&arProduct='+productData+'&currency='+currency+'&LID='+lid+'&userId=' + userId+'&fUserId='+fUserId, fGetMoreBusketResult);
								}
								function fGetMoreBusketResult(res)
								{
									BX.closeWait();
									var rs = eval( '('+res+')' );

									if (rs["ITEMS"].length > 0)
									{
										if (rs["TYPE"] == 'busket')
											document.getElementById("buyer_busket").innerHTML = rs["ITEMS"];
										if (rs["TYPE"] == 'recom')
											document.getElementById("buyer_recmon").innerHTML = rs["ITEMS"];
										if (rs["TYPE"] == 'viewed')
											document.getElementById("buyer_viewed").innerHTML = rs["ITEMS"];
									}
								}
						</script>
					</td>
				</tr>
				<?
				$tabControl->EndCustomField("orders_itog");

		$tabControl->BeginNextFormTab();

			$tabControl->BeginCustomField("TRANSACT", GetMessage("SODN_TAB_TRANSACT"));
				?>
				<tr>
					<td colspan="2">
					<?
					$dbTransact = CSaleUserTransact::GetList(
							array("TRANSACT_DATE" => "ASC"),
							array("ORDER_ID" => $ID),
							false,
							false,
							array("ID", "USER_ID", "AMOUNT", "CURRENCY", "DEBIT", "ORDER_ID", "DESCRIPTION", "NOTES", "TIMESTAMP_X", "TRANSACT_DATE")
						);
					?>
					<table cellpadding="3" cellspacing="1" border="0" width="100%" class="internal">
						<tr class="heading">
							<td><?echo GetMessage("SOD_TRANS_DATE")?></td>
							<td><?echo GetMessage("SOD_TRANS_USER")?></td>
							<td><?echo GetMessage("SOD_TRANS_SUM")?></td>
							<td><?echo GetMessage("SOD_TRANS_DESCR")?></td>
							<td><?echo GetMessage("SOD_TRANS_COMMENT")?></td>
						</tr>
						<?
						$bNoTransact = True;
						while ($arTransact = $dbTransact->Fetch())
						{
							$bNoTransact = False;
							?>
							<tr>
								<td><?= $arTransact["TRANSACT_DATE"]; ?></td>
								<td>
									<?echo fShowEditor($arTransact["USER_ID"]);?>
								</td>
								<td>
									<?
									echo (($arTransact["DEBIT"] == "Y") ? "+" : "-");
									echo SaleFormatCurrency($arTransact["AMOUNT"], $arTransact["CURRENCY"]);
									?>
								</td>
								<td>
									<?
									if (array_key_exists($arTransact["DESCRIPTION"], $arTransactTypes))
										echo htmlspecialcharsEx($arTransactTypes[$arTransact["DESCRIPTION"]]);
									else
										echo htmlspecialcharsEx($arTransact["DESCRIPTION"]);
									?>
								</td>
								<td align="right">
									<?echo htmlspecialcharsEx($arTransact["NOTES"]) ?>
								</td>
							</tr>
							<?
						}

						if ($bNoTransact)
						{
							?>
							<tr>
								<td colspan="5" align="center">
									<?echo GetMessage("SOD_NO_TRANS")?>
								</td>
							</tr>
							<?
						}
						?>
					</table>
					</td>
				</tr>
				<?
			$tabControl->EndCustomField("TRANSACT", '');

		$tabControl->Show();
	}
}
?>

<div class="sale_popup_form" id="popup_form_sku_order" style="display:none;">
	<table width="100%">
		<tr><td></td></tr>
		<tr>
			<td><small><span id="listItemPrice"></span>&nbsp;<span id="listItemOldPrice"></span></small></td>
		</tr>
		<tr>
			<td><hr></td>
		</tr>
	</table>

	<table width="100%" id="sku_selectors_list">
		<tr>
			<td colspan="2"></td>
		</tr>
	</table>

	<span id="prod_order_button"></span>
	<input type="hidden" value="" name="popup-params-product" id="popup-params-product" >
</div>

	<script>
			var wind = new BX.PopupWindow('popup_sku', this, {
				offsetTop : 10,
				offsetLeft : 0,
				autoHide : true,
				closeByEsc : true,
				closeIcon : true,
				titleBar : true,
				draggable: {restrict:true},
				titleBar: {content: BX.create("span", {html: '', 'props': {'className': 'sale-popup-title-bar'}})},
				content : BX("popup_form_sku_order"),
				buttons: [
					new BX.PopupWindowButton({
						text : '<?=GetMessageJS('SOD_POPUP_CAN_BUY_NOT');?>',
						id : "popup_sku_save",
						events : {
							click : function() {
								if (BX('popup-params-product') && BX('popup-params-product').value.length > 0)
								{
									window.location = BX('popup-params-product').value;
									wind.close();
								}
							}
						}
					}),
					new BX.PopupWindowButton({
						text : '<?=GetMessageJS('SOD_POPUP_CLOSE');?>',
						id : "popup_sku_cancel",
						events : {
							click : function() {
								wind.close();
							}
						}
					})
				]
			});
			function fAddToBusketMoreProductSku(arSKU, arProperties, type, message)
			{
				BX.message(message);
				wind.show();
				buildSelect("sku_selectors_list", 0, arSKU, arProperties, type);
				var properties_num = arProperties.length;
				var lastPropCode = arProperties[properties_num-1].CODE;
				addHtml(lastPropCode, arSKU, type);
			}
			function buildSelect(cont_name, prop_num, arSKU, arProperties, type)
			{
				var properties_num = arProperties.length;
				var lastPropCode = arProperties[properties_num-1].CODE;

				for (var i = prop_num; i < properties_num; i++)
				{
					var q = BX('prop_' + i);
					if (q)
						q.parentNode.removeChild(q);
				}

				var select = BX.create('SELECT', {
					props: {
						name: arProperties[prop_num].CODE,
						id :  arProperties[prop_num].CODE
					},
					events: {
						change: (prop_num < properties_num-1)
							? function() {
								buildSelect(cont_name, prop_num + 1, arSKU, arProperties, type);
								if (this.value != "null")
									BX(arProperties[prop_num+1].CODE).disabled = false;
								addHtml(lastPropCode, arSKU, type);
							}
							: function() {
								if (this.value != "null")
									addHtml(lastPropCode, arSKU, type)
							}
					}
				});
				if (prop_num != 0) select.disabled = true;

				var ar = [];
				select.add(new Option(arProperties[prop_num].NAME, 'null'));

				for (var i = 0; i < arSKU.length; i++)
				{
					if (checkSKU(arSKU[i], prop_num, arProperties) && !BX.util.in_array(arSKU[i][prop_num], ar))
					{
						select.add(new Option(
								arSKU[i][prop_num],
								prop_num < properties_num-1 ? arSKU[i][prop_num] : arSKU[i]["ID"]
						));
						ar.push(arSKU[i][prop_num]);
					}
				}

				var cont = BX.create('tr', {
					props: {id: 'prop_' + prop_num},
					children:[
						BX.create('td', {html: arProperties[prop_num].NAME + ': '}),
						BX.create('td', { children:[
							select
						]}),
					]
				});

				var tmp = BX.findChild(BX(cont_name), {tagName:'tbody'}, false, false);

				tmp.appendChild(cont);

				if (prop_num < properties_num-1)
					buildSelect(cont_name, prop_num + 1, arSKU, arProperties, type);
			}

			function checkSKU(SKU, prop_num, arProperties)
			{
				for (var i = 0; i < prop_num; i++)
				{
					code = BX.findChild(BX('popup_sku'), {'attr': {name: arProperties[i].CODE}}, true, false).value;
					if (SKU[i] != code)
						return false;
				}
				return true;
			}
			function addHtml(lastPropCode, arSKU, type)
			{
				var selectedSkuId = BX(lastPropCode).value;
				var btnText = '';

				BX('popup-window-titlebar-popup_sku').innerHTML = '<span class="sale-popup-title-bar">'+arSKU[0]["PRODUCT_NAME"]+'</span>';
				BX("listItemPrice").innerHTML = BX.message('PRODUCT_PRICE_FROM')+" "+arSKU[0]["MIN_PRICE"];
				BX("listItemOldPrice").innerHTML = '';

				for (var i = 0; i < arSKU.length; i++)
				{
					if (arSKU[i]["ID"] == selectedSkuId)
					{
						BX('popup-window-titlebar-popup_sku').innerHTML = '<span class="sale-popup-title-bar">'+arSKU[i]["NAME"]+'</span>';

						if (arSKU[i]["DISCOUNT_PRICE"] != "")
						{
							BX("listItemPrice").innerHTML = arSKU[i]["DISCOUNT_PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
							BX("listItemOldPrice").innerHTML = arSKU[i]["PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
							summaFormated = arSKU[i]["DISCOUNT_PRICE_FORMATED"];
							price = arSKU[i]["DISCOUNT_PRICE"];
							priceFormated = arSKU[i]["DISCOUNT_PRICE_FORMATED"];
							priceDiscount = arSKU[i]["PRICE"] - arSKU[i]["DISCOUNT_PRICE"];
						}
						else
						{
							BX("listItemPrice").innerHTML = arSKU[i]["PRICE_FORMATED"]+" "+arSKU[i]["VALUTA_FORMAT"];
							BX("listItemOldPrice").innerHTML = "";
							summaFormated = arSKU[i]["PRICE_FORMATED"];
							price = arSKU[i]["PRICE"];
							priceFormated = arSKU[i]["PRICE_FORMATED"];
							priceDiscount = 0;
						}

						if (arSKU[i]["CAN_BUY"] == "Y")
						{
							BX('popup-params-product').value = "/bitrix/admin/sale_order_new.php?lang=<?=LANG?>&user_id="+arSKU[i]["USER_ID"]+"&LID="+arSKU[i]["LID"]+"&product[]="+arSKU[i]["ID"];
							message = BX.message('PRODUCT_ADD');
						}
						else
						{
							BX('popup-params-product').value = '';
							message = BX.message('PRODUCT_NOT_ADD');
						}

						BX.findChild(BX('popup_sku_save'), {'attr': {class: 'popup-window-button-text'}}, true, false).innerHTML = message;
					}

					if (arSKU[i]["ID"] == selectedSkuId)
						break;
				}
			}
	</script>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>