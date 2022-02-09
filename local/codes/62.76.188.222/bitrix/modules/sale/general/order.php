<?
IncludeModuleLangFile(__FILE__);

$GLOBALS["SALE_ORDER"] = Array();

class CAllSaleOrder
{
	static function DoCalculateOrder($siteId, $userId, $arShoppingCart, $personTypeId, $arOrderPropsValues,
		$deliveryId, $paySystemId, $arOptions, &$arErrors, &$arWarnings)
	{
		$siteId = trim($siteId);
		if (empty($siteId))
		{
			$arErrors[] = array("CODE" => "PARAM", "TEXT" => GetMessage('SKGO_CALC_PARAM_ERROR'));
			return null;
		}

		$userId = intval($userId);

		if (!is_array($arShoppingCart) || (count($arShoppingCart) <= 0))
		{
			$arErrors[] = array("CODE" => "PARAM", "TEXT" => GetMessage('SKGO_SHOPPING_CART_EMPTY'));
			return null;
		}

		$arOrder = array(
			"ORDER_PRICE" => 0,
			"ORDER_WEIGHT" => 0,
			"CURRENCY" => CSaleLang::GetLangCurrency($siteId),
			"WEIGHT_UNIT" => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', false, $siteId)),
			"WEIGHT_KOEF" => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, $siteId)),
			"BASKET_ITEMS" => $arShoppingCart,
			"SITE_ID" => $siteId,
			"LID" => $siteId,
			"USER_ID" => $userId,
			"USE_VAT" => false,
			"VAT_RATE" => 0,
			"VAT_SUM" => 0,
			"DELIVERY_ID" => false,
		);

		foreach ($arShoppingCart as $arShoppingCartItem)
		{
			if (array_key_exists('CUSTOM_PRICE', $arShoppingCartItem) && 'Y' == $arShoppingCartItem['CUSTOM_PRICE'])
			{
				$arShoppingCartItem['DISCOUNT_PRICE'] = $arShoppingCartItem['DEFAULT_PRICE'] - $arShoppingCartItem['PRICE'];
				if (0 > $arShoppingCartItem['DISCOUNT_PRICE'])
					$arShoppingCartItem['DISCOUNT_PRICE'] = 0;
				$arShoppingCartItem['DISCOUNT_PRICE_PERCENT'] = $arShoppingCartItem['DISCOUNT_PRICE']*100/$arShoppingCartItem['DEFAULT_PRICE'];
				if ($arShoppingCartItem["VAT_RATE"] > 0)
				{
					$arShoppingCartItem["VAT_VALUE"] = (($arShoppingCartItem["PRICE"] / ($arShoppingCartItem["VAT_RATE"] + 1)) * $arShoppingCartItem["VAT_RATE"]);
				}
			}

			$arOrder["ORDER_PRICE"] += $arShoppingCartItem["PRICE"] * $arShoppingCartItem["QUANTITY"];
			$arOrder["ORDER_WEIGHT"] += $arShoppingCartItem["WEIGHT"] * $arShoppingCartItem["QUANTITY"];

			if ($arShoppingCartItem["VAT_RATE"] > 0)
			{
				$arOrder["USE_VAT"] = true;
				if ($arShoppingCartItem["VAT_RATE"] > $arOrder["VAT_RATE"])
					$arOrder["VAT_RATE"] = $arShoppingCartItem["VAT_RATE"];

				$arOrder["VAT_SUM"] += $arShoppingCartItem["VAT_VALUE"] * $arShoppingCartItem["QUANTITY"];
			}
		}

		foreach(GetModuleEvents("sale", "OnSaleCalculateOrderShoppingCart", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arOrder));

		CSalePersonType::DoProcessOrder($arOrder, $personTypeId, $arErrors);
		if (count($arErrors) > 0)
			return null;

		foreach(GetModuleEvents("sale", "OnSaleCalculateOrderPersonType", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arOrder));

		CSaleOrderProps::DoProcessOrder($arOrder, $arOrderPropsValues, $arErrors, $arWarnings);
		if (count($arErrors) > 0)
			return null;

		foreach(GetModuleEvents("sale", "OnSaleCalculateOrderProps", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arOrder));

		CSaleDelivery::DoProcessOrder($arOrder, $deliveryId, $arErrors);
		if (count($arErrors) > 0)
			return null;

		$arOrder["PRICE_DELIVERY"] = $arOrder["DELIVERY_PRICE"];

		foreach(GetModuleEvents("sale", "OnSaleCalculateOrderDelivery", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arOrder));

		CSalePaySystem::DoProcessOrder($arOrder, $paySystemId, $arErrors);
		if (count($arErrors) > 0)
			return null;

		foreach(GetModuleEvents("sale", "OnSaleCalculateOrderPaySystem", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arOrder));

		if (!array_key_exists('CART_FIX', $arOptions) || 'Y' != $arOptions['CART_FIX'])
		{
			CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
			if (count($arErrors) > 0)
				return null;

			foreach(GetModuleEvents("sale", "OnSaleCalculateOrderDiscount", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array(&$arOrder));
		}

		CSaleTax::DoProcessOrderBasket($arOrder, $arOptions, $arErrors);
		if (count($arErrors) > 0)
			return null;

		foreach(GetModuleEvents("sale", "OnSaleCalculateOrderShoppingCartTax", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arOrder));

		CSaleTax::DoProcessOrderDelivery($arOrder, $arOptions, $arErrors);
		if (count($arErrors) > 0)
			return null;

		foreach(GetModuleEvents("sale", "OnSaleCalculateOrderDeliveryTax", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arOrder));

		$arOrder["PRICE"] = $arOrder["ORDER_PRICE"] + $arOrder["DELIVERY_PRICE"] + $arOrder["TAX_PRICE"] - $arOrder["DISCOUNT_PRICE"];
		$arOrder["TAX_VALUE"] = ($arOrder["USE_VAT"] ? $arOrder["VAT_SUM"] : $arOrder["TAX_PRICE"]);

		foreach(GetModuleEvents("sale", "OnSaleCalculateOrder", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arOrder));

		return $arOrder;
	}

	static function DoSaveOrder(&$arOrder, $arAdditionalFields, $orderId, &$arErrors, $arCoupons = array(), $arStoreBarcodeOrderFormData = array(), $bSaveBarcodes = false)
	{
		global $APPLICATION;

		$orderId = intval($orderId);

		$arFields = array(
			"LID" => $arOrder["SITE_ID"],
			"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"],
			"PRICE" => $arOrder["PRICE"],
			"CURRENCY" => $arOrder["CURRENCY"],
			"USER_ID" => $arOrder["USER_ID"],
			"PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
			"PRICE_DELIVERY" => $arOrder["DELIVERY_PRICE"],
			"DELIVERY_ID" => (strlen($arOrder["DELIVERY_ID"]) > 0 ? $arOrder["DELIVERY_ID"] : false),
			"DISCOUNT_VALUE" => $arOrder["DISCOUNT_PRICE"],
			"TAX_VALUE" => $arOrder["TAX_VALUE"],
		);

		if ($orderId <= 0)
		{
			$arFields["PAYED"] = "N";
			$arFields["CANCELED"] = "N";
			$arFields["STATUS_ID"] = "N";
		}
		$arFields = array_merge($arFields, $arAdditionalFields);

		if ($orderId > 0)
		{
			$orderId = CSaleOrder::Update($orderId, $arFields);
		}
		else
		{
			if (COption::GetOptionString("sale", "product_reserve_condition", "O") == "O")
			{
				$arFields["RESERVED"] = "Y";
			}

			$orderId = CSaleOrder::Add($arFields);
		}

		$orderId = intval($orderId);
		if ($orderId <= 0)
		{
			if ($ex = $APPLICATION->GetException())
				$arErrors[] = $ex->GetString();
			else
				$arErrors[] = GetMessage("SOA_ERROR_ORDER");
		}

		if (count($arErrors) > 0)
			return null;

		CSaleBasket::DoSaveOrderBasket($orderId, $arOrder["SITE_ID"], $arOrder["USER_ID"], $arOrder["BASKET_ITEMS"], $arErrors, $arCoupons, $arStoreBarcodeOrderFormData, $bSaveBarcodes);
		CSaleTax::DoSaveOrderTax($orderId, $arOrder["TAX_LIST"], $arErrors);
		CSaleOrderProps::DoSaveOrderProps($orderId, $arOrder["PERSON_TYPE_ID"], $arOrder["ORDER_PROP"], $arErrors);

/*
			// mail message
			if (empty($arResult["ERROR"]))
			{
				$strOrderList = "";
				$dbBasketItems = CSaleBasket::GetList(
						array("NAME" => "ASC"),
						array("ORDER_ID" => $arResult["ORDER_ID"]),
						false,
						false,
						array("ID", "NAME", "QUANTITY", "PRICE", "CURRENCY")
					);
				while ($arBasketItems = $dbBasketItems->Fetch())
				{
					$strOrderList .= $arBasketItems["NAME"]." - ".$arBasketItems["QUANTITY"]." ".GetMessage("SOA_SHT").": ".SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]);
					$strOrderList .= "\n";
				}

				$arFields = Array(
					"ORDER_ID" => $arResult["ORDER_ID"],
					"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", SITE_ID))),
					"ORDER_USER" => ( (strlen($arUserResult["PAYER_NAME"]) > 0) ? $arUserResult["PAYER_NAME"] : $USER->GetFullName() ),
					"PRICE" => SaleFormatCurrency($totalOrderPrice, $arResult["BASE_LANG_CURRENCY"]),
					"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
					"EMAIL" => (strlen($arUserResult["USER_EMAIL"])>0 ? $arUserResult["USER_EMAIL"] : $USER->GetEmail()),
					"ORDER_LIST" => $strOrderList,
					"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
					"DELIVERY_PRICE" => $arResult["DELIVERY_PRICE"],
				);
				$eventName = "SALE_NEW_ORDER";

				$bSend = true;
				foreach(GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
					if (ExecuteModuleEventEx($arEvent, Array($arResult["ORDER_ID"], &$eventName, &$arFields))===false)
						$bSend = false;

				if($bSend)
				{
					$event = new CEvent;
					$event->Send($eventName, SITE_ID, $arFields, "N");
				}
			}

			if(CModule::IncludeModule("statistic"))
			{
				$event1 = "eStore";
				$event2 = "order_confirm";
				$event3 = $arResult["ORDER_ID"];

				$e = $event1."/".$event2."/".$event3;

				if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"])))
				{
						CStatistic::Set_Event($event1, $event2, $event3);
						$_SESSION["ORDER_EVENTS"][] = $e;
				}
			}
			$arOrder = CSaleOrder::GetByID($arResult["ORDER_ID"]);
			foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepComplete", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, Array($arResult["ORDER_ID"], $arOrder));
*/

		return $orderId;
	}

	//*************** USER PERMISSIONS *********************/
	function CanUserViewOrder($ID, $arUserGroups = false, $userID = 0)
	{
		$ID = IntVal($ID);
		$userID = IntVal($userID);

		$userRights = CMain::GetUserRight("sale", $arUserGroups, "Y", "Y");
		if ($userRights >= "W")
			return True;

		$arOrder = CSaleOrder::GetByID($ID);
		if ($arOrder)
		{
			if (IntVal($arOrder["USER_ID"]) == $userID)
				return True;

			if ($userRights == "U")
			{
				$num = CSaleGroupAccessToSite::GetList(
						array(),
						array(
								"SITE_ID" => $arOrder["LID"],
								"GROUP_ID" => $arUserGroups
							),
						array()
					);
				if (IntVal($num) > 0)
				{
					$dbStatusPerms = CSaleStatus::GetPermissionsList(
						array(),
						array(
							"STATUS_ID" => $arOrder["STATUS_ID"],
							"GROUP_ID" => $arUserGroups
						),
						array("MAX" => "PERM_VIEW")
					);
					if ($arStatusPerms = $dbStatusPerms->Fetch())
					{
						if ($arStatusPerms["PERM_VIEW"] == "Y")
							return True;
					}
				}
			}
		}

		return False;
	}

	function CanUserUpdateOrder($ID, $arUserGroups = false)
	{
		$ID = IntVal($ID);

		$userRights = CMain::GetUserRight("sale", $arUserGroups, "Y", "Y");

		if ($userRights >= "W")
			return True;

		if ($userRights == "U")
		{
			$arOrder = CSaleOrder::GetByID($ID);
			if ($arOrder)
			{
				$num = CSaleGroupAccessToSite::GetList(
						array(),
						array(
								"SITE_ID" => $arOrder["LID"],
								"GROUP_ID" => $arUserGroups
							),
						array()
					);

				if (IntVal($num) > 0)
				{
					$dbStatusPerms = CSaleStatus::GetPermissionsList(
						array(),
						array(
							"STATUS_ID" => $arOrder["STATUS_ID"],
							"GROUP_ID" => $arUserGroups
						),
						array("MAX" => "PERM_UPDATE")
					);
					if ($arStatusPerms = $dbStatusPerms->Fetch())
						if ($arStatusPerms["PERM_UPDATE"] == "Y")
							return True;
				}
			}
		}

		return False;
	}

	function CanUserCancelOrder($ID, $arUserGroups = false, $userID = 0)
	{
		$ID = IntVal($ID);
		$userID = IntVal($userID);

		$userRights = CMain::GetUserRight("sale", $arUserGroups, "Y", "Y");
		if ($userRights >= "W")
			return True;

		$arOrder = CSaleOrder::GetByID($ID);
		if ($arOrder)
		{
			if (IntVal($arOrder["USER_ID"]) == $userID)
				return True;

			if ($userRights == "U")
			{
				$num = CSaleGroupAccessToSite::GetList(
						array(),
						array(
								"SITE_ID" => $arOrder["LID"],
								"GROUP_ID" => $arUserGroups
							),
						array()
					);

				if (IntVal($num) > 0)
				{
					$dbStatusPerms = CSaleStatus::GetPermissionsList(
						array(),
						array(
							"STATUS_ID" => $arOrder["STATUS_ID"],
							"GROUP_ID" => $arUserGroups
						),
						array("MAX" => "PERM_CANCEL")
					);
					if ($arStatusPerms = $dbStatusPerms->Fetch())
						if ($arStatusPerms["PERM_CANCEL"] == "Y")
							return True;
				}
			}
		}

		return False;
	}

	function CanUserMarkOrder($ID, $arUserGroups = false, $userID = 0)
	{
		$ID = IntVal($ID);
		$userID = IntVal($userID);

		$userRights = CMain::GetUserRight("sale", $arUserGroups, "Y", "Y");
		if ($userRights >= "W")
			return True;

		$arOrder = CSaleOrder::GetByID($ID);
		if ($arOrder)
		{
			if (IntVal($arOrder["USER_ID"]) == $userID)
				return True;

			if ($userRights == "U")
			{
				$num = CSaleGroupAccessToSite::GetList(
						array(),
						array(
								"SITE_ID" => $arOrder["LID"],
								"GROUP_ID" => $arUserGroups
							),
						array()
					);

				if (IntVal($num) > 0)
				{
					$dbStatusPerms = CSaleStatus::GetPermissionsList(
						array(),
						array(
							"STATUS_ID" => $arOrder["STATUS_ID"],
							"GROUP_ID" => $arUserGroups
						),
						array("MAX" => "PERM_MARK")
					);
					if ($arStatusPerms = $dbStatusPerms->Fetch())
						if ($arStatusPerms["PERM_MARK"] == "Y")
							return True;
				}
			}
		}

		return False;
	}

	function CanUserChangeOrderFlag($ID, $flag, $arUserGroups = false)
	{
		$ID = IntVal($ID);
		$flag = trim($flag);

		$userRights = CMain::GetUserRight("sale", $arUserGroups, "Y", "Y");
		if ($userRights >= "W")
			return True;

		if ($userRights == "U")
		{
			$arOrder = CSaleOrder::GetByID($ID);
			if ($arOrder)
			{
				$num = CSaleGroupAccessToSite::GetList(
						array(),
						array(
								"SITE_ID" => $arOrder["LID"],
								"GROUP_ID" => $arUserGroups
							),
						array()
					);

				if (IntVal($num) > 0)
				{
					if ($flag == "P" || $flag == "PERM_PAYMENT")
						$fieldName = "PERM_PAYMENT";
					elseif ($flag == "PERM_DEDUCTION")
						$fieldName = "PERM_DEDUCTION";
					else
						$fieldName = "PERM_DELIVERY";

					$dbStatusPerms = CSaleStatus::GetPermissionsList(
						array(),
						array(
							"STATUS_ID" => $arOrder["STATUS_ID"],
							"GROUP_ID" => $arUserGroups
						),
						array("MAX" => $fieldName)
					);
					if ($arStatusPerms = $dbStatusPerms->Fetch())
						if ($arStatusPerms[$fieldName] == "Y")
							return True;
				}
			}
		}

		return False;
	}

	function CanUserChangeOrderStatus($ID, $statusID, $arUserGroups = false)
	{
		$ID = IntVal($ID);
		$statusID = Trim($statusID);

		$userRights = CMain::GetUserRight("sale", $arUserGroups, "Y", "Y");
		if ($userRights >= "W")
			return True;

		if ($userRights == "U")
		{
			$arOrder = CSaleOrder::GetByID($ID);
			if ($arOrder)
			{
				$num = CSaleGroupAccessToSite::GetList(
						array(),
						array(
								"SITE_ID" => $arOrder["LID"],
								"GROUP_ID" => $arUserGroups
							),
						array()
					);

				if (IntVal($num) > 0)
				{
					$dbStatusPerms = CSaleStatus::GetPermissionsList(
						array(),
						array(
							"STATUS_ID" => $arOrder["STATUS_ID"],
							"GROUP_ID" => $arUserGroups
						),
						array("MAX" => "PERM_STATUS_FROM")
					);
					if ($arStatusPerms = $dbStatusPerms->Fetch())
					{
						if ($arStatusPerms["PERM_STATUS_FROM"] == "Y")
						{
							$dbStatusPerms = CSaleStatus::GetPermissionsList(
								array(),
								array(
									"STATUS_ID" => $statusID,
									"GROUP_ID" => $arUserGroups
								),
								array("MAX" => "PERM_STATUS")
							);
							if ($arStatusPerms = $dbStatusPerms->Fetch())
								if ($arStatusPerms["PERM_STATUS"] == "Y")
									return True;
						}
					}
				}
			}
		}

		return False;
	}

	function CanUserDeleteOrder($ID, $arUserGroups = false, $userID = 0)
	{
		$ID = IntVal($ID);
		$userID = IntVal($userID);

		$userRights = CMain::GetUserRight("sale", $arUserGroups, "Y", "Y");
		if ($userRights >= "W")
			return True;

		if ($userRights == "U")
		{
			$arOrder = CSaleOrder::GetByID($ID);
			if ($arOrder)
			{
				$num = CSaleGroupAccessToSite::GetList(
						array(),
						array(
								"SITE_ID" => $arOrder["LID"],
								"GROUP_ID" => $arUserGroups
							),
						array()
					);

				if (IntVal($num) > 0)
				{
					$dbStatusPerms = CSaleStatus::GetPermissionsList(
						array(),
						array(
							"STATUS_ID" => $arOrder["STATUS_ID"],
							"GROUP_ID" => $arUserGroups
						),
						array("MAX" => "PERM_DELETE")
					);
					if ($arStatusPerms = $dbStatusPerms->Fetch())
						if ($arStatusPerms["PERM_DELETE"] == "Y")
							return True;
				}
			}
		}

		return False;
	}


	//*************** ADD, UPDATE, DELETE *********************/
	function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		if (is_set($arFields, "SITE_ID") && strlen($arFields["SITE_ID"]) > 0)
			$arFields["LID"] = $arFields["SITE_ID"];

		if ((is_set($arFields, "LID") || $ACTION=="ADD") && strlen($arFields["LID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_EMPTY_SITE"), "EMPTY_SITE_ID");
			return false;
		}
		if ((is_set($arFields, "PERSON_TYPE_ID") || $ACTION=="ADD") && IntVal($arFields["PERSON_TYPE_ID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_EMPTY_PERS_TYPE"), "EMPTY_PERSON_TYPE_ID");
			return false;
		}
		if ((is_set($arFields, "USER_ID") || $ACTION=="ADD") && IntVal($arFields["USER_ID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_EMPTY_USER_ID"), "EMPTY_USER_ID");
			return false;
		}

		if (is_set($arFields, "PAYED") && $arFields["PAYED"]!="Y")
			$arFields["PAYED"]="N";
		if (is_set($arFields, "CANCELED") && $arFields["CANCELED"]!="Y")
			$arFields["CANCELED"]="N";
		if (is_set($arFields, "STATUS_ID") && strlen($arFields["STATUS_ID"])<=0)
			$arFields["STATUS_ID"]="N";
		if (is_set($arFields, "ALLOW_DELIVERY") && $arFields["ALLOW_DELIVERY"]!="Y")
			$arFields["ALLOW_DELIVERY"]="N";

		if (is_set($arFields, "PRICE") || $ACTION=="ADD")
		{
			$arFields["PRICE"] = str_replace(",", ".", $arFields["PRICE"]);
			$arFields["PRICE"] = DoubleVal($arFields["PRICE"]);
		}
		if (is_set($arFields, "PRICE_DELIVERY") || $ACTION=="ADD")
		{
			$arFields["PRICE_DELIVERY"] = str_replace(",", ".", $arFields["PRICE_DELIVERY"]);
			$arFields["PRICE_DELIVERY"] = DoubleVal($arFields["PRICE_DELIVERY"]);
		}
		if (is_set($arFields, "SUM_PAID") || $ACTION=="ADD")
		{
			$arFields["SUM_PAID"] = str_replace(",", ".", $arFields["SUM_PAID"]);
			$arFields["SUM_PAID"] = DoubleVal($arFields["SUM_PAID"]);
		}
		if (is_set($arFields, "DISCOUNT_VALUE") || $ACTION=="ADD")
		{
			$arFields["DISCOUNT_VALUE"] = str_replace(",", ".", $arFields["DISCOUNT_VALUE"]);
			$arFields["DISCOUNT_VALUE"] = DoubleVal($arFields["DISCOUNT_VALUE"]);
		}
		if (is_set($arFields, "TAX_VALUE") || $ACTION=="ADD")
		{
			$arFields["TAX_VALUE"] = str_replace(",", ".", $arFields["TAX_VALUE"]);
			$arFields["TAX_VALUE"] = DoubleVal($arFields["TAX_VALUE"]);
		}

		if(!is_set($arFields, "LOCKED_BY") && (!is_set($arFields, "UPDATED_1C") || (is_set($arFields, "UPDATED_1C") && $arFields["UPDATED_1C"] != "Y")))
		{
			$arFields["UPDATED_1C"] = "N";
		}

		if ((is_set($arFields, "CURRENCY") || $ACTION=="ADD") && strlen($arFields["CURRENCY"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_EMPTY_CURRENCY"), "EMPTY_CURRENCY");
			return false;
		}

		if (is_set($arFields, "CURRENCY"))
		{
			if (!($arCurrency = CCurrency::GetByID($arFields["CURRENCY"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["CURRENCY"], GetMessage("SKGO_WRONG_CURRENCY")), "ERROR_NO_CURRENCY");
				return false;
			}
		}

		if (is_set($arFields, "LID"))
		{
			$dbSite = CSite::GetByID($arFields["LID"]);
			if (!$dbSite->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["LID"], GetMessage("SKGO_WRONG_SITE")), "ERROR_NO_SITE");
				return false;
			}
		}

		if (is_set($arFields, "USER_ID"))
		{
			$dbUser = CUser::GetByID($arFields["USER_ID"]);
			if (!$dbUser->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["USER_ID"], GetMessage("SKGO_WRONG_USER")), "ERROR_NO_USER_ID");
				return false;
			}
		}

		if (is_set($arFields, "PERSON_TYPE_ID"))
		{
			if (!($arPersonType = CSalePersonType::GetByID($arFields["PERSON_TYPE_ID"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["PERSON_TYPE_ID"], GetMessage("SKGO_WRONG_PERSON_TYPE")), "ERROR_NO_PERSON_TYPE");
				return false;
			}
		}

		if (is_set($arFields, "PAY_SYSTEM_ID") && IntVal($arFields["PAY_SYSTEM_ID"]) > 0)
		{
			if (!($arPaySystem = CSalePaySystem::GetByID(IntVal($arFields["PAY_SYSTEM_ID"]))))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["PAY_SYSTEM_ID"], GetMessage("SKGO_WRONG_PS")), "ERROR_NO_PAY_SYSTEM");
				return false;
			}
		}

		if (is_set($arFields, "DELIVERY_ID") && (
				strpos($arFields["DELIVERY_ID"], ":") !== false
				||
				IntVal($arFields["DELIVERY_ID"]) > 0
			)
		)
		{
			if (strpos($arFields["DELIVERY_ID"], ":") !== false)
			{
				$arId = explode(":", $arFields["DELIVERY_ID"]);
				$obDelivery = new CSaleDeliveryHandler();
				if ($arDelivery = $obDelivery->GetBySID($arId[0]))
				{
					if ($arDelivery = $arDelivery->Fetch())
					{
						if (!is_set($arDelivery["PROFILES"], $arId[1]))
						{
							$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["DELIVERY_ID"], GetMessage("SKGO_WRONG_DELIVERY")), "ERROR_NO_DELIVERY");
							return false;
						}
					}
				}
				else
				{
					$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["DELIVERY_ID"], GetMessage("SKGO_WRONG_DELIVERY")), "ERROR_NO_DELIVERY");
					return false;
				}
			}
			else
			{
				if (!($arDelivery = CSaleDelivery::GetByID(IntVal($arFields["DELIVERY_ID"]))))
				{
					$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["DELIVERY_ID"], GetMessage("SKGO_WRONG_DELIVERY")), "ERROR_NO_DELIVERY");
					return false;
				}
			}
		}

		if (is_set($arFields, "STATUS_ID"))
		{
			if (!($arStatus = CSaleStatus::GetByID($arFields["STATUS_ID"])))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $arFields["STATUS_ID"], GetMessage("SKGO_WRONG_STATUS")), "ERROR_NO_STATUS_ID");
				return false;
			}
		}

		return True;
	}

	function _Delete($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		$bSuccess = True;

		foreach(GetModuleEvents("sale", "OnBeforeOrderDelete", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID))===false)
				return false;

		$DB->StartTransaction();

		if ($bSuccess)
		{
			$dbBasket = CSaleBasket::GetList(array(), array("ORDER_ID" => $ID));
			while ($arBasket = $dbBasket->Fetch())
			{
				$bSuccess = CSaleBasket::Delete($arBasket["ID"]);
				if (!$bSuccess)
					break;
			}
		}

		if ($bSuccess)
		{
			$dbRecurring = CSaleRecurring::GetList(array(), array("ORDER_ID" => $ID));
			while ($arRecurring = $dbRecurring->Fetch())
			{
				$bSuccess = CSaleRecurring::Delete($arRecurring["ID"]);
				if (!$bSuccess)
					break;
			}
		}

		if ($bSuccess)
			$bSuccess = CSaleOrderPropsValue::DeleteByOrder($ID);

		if ($bSuccess)
			$bSuccess = CSaleOrderTax::DeleteEx($ID);

		if($bSuccess)
			$bSuccess = CSaleUserTransact::DeleteByOrder($ID);

		if ($bSuccess)
			unset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]);

		if ($bSuccess)
			$bSuccess = $DB->Query("DELETE FROM b_sale_order WHERE ID = ".$ID."", true);

		if ($bSuccess)
			$DB->Commit();
		else
			$DB->Rollback();

		foreach(GetModuleEvents("sale", "OnOrderDelete", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $bSuccess));


		return $bSuccess;
	}

	function Delete($ID)
	{
		global $DB;

		$ID = IntVal($ID);

		$arOrder = CSaleOrder::GetByID($ID);
		if ($arOrder)
		{
			if ($arOrder["CANCELED"] != "Y")
				CSaleBasket::OrderCanceled($ID, "Y"); //used only for old catalog without reservation and deduction

			if ($arOrder["ALLOW_DELIVERY"] == "Y")
				CSaleOrder::DeliverOrder($ID, "N");

			if ($arOrder["DEDUCTED"] == "Y")
			{
				CSaleOrder::DeductOrder($ID, "N");
			}

			if ($arOrder["RESERVED"] == "Y")
			{
				CSaleOrder::ReserveOrder($ID, "N");
			}

			if ($arOrder["PAYED"] != "Y")
			{
				return CSaleOrder::_Delete($ID);
			}
			else
			{
				if (CSaleOrder::PayOrder($ID, "N", True, True))
					return CSaleOrder::_Delete($ID);
			}
		}

		return False;
	}

	//*************** COMMON UTILS *********************/
	function GetFilterOperation($key)
	{
		$strNegative = "N";
		if (substr($key, 0, 1)=="!")
		{
			$key = substr($key, 1);
			$strNegative = "Y";
		}

		$strOrNull = "N";
		if (substr($key, 0, 1)=="+")
		{
			$key = substr($key, 1);
			$strOrNull = "Y";
		}

		if (substr($key, 0, 2)==">=")
		{
			$key = substr($key, 2);
			$strOperation = ">=";
		}
		elseif (substr($key, 0, 1)==">")
		{
			$key = substr($key, 1);
			$strOperation = ">";
		}
		elseif (substr($key, 0, 2)=="<=")
		{
			$key = substr($key, 2);
			$strOperation = "<=";
		}
		elseif (substr($key, 0, 1)=="<")
		{
			$key = substr($key, 1);
			$strOperation = "<";
		}
		elseif (substr($key, 0, 1)=="@")
		{
			$key = substr($key, 1);
			$strOperation = "IN";
		}
		elseif (substr($key, 0, 1)=="~")
		{
			$key = substr($key, 1);
			$strOperation = "LIKE";
		}
		elseif (substr($key, 0, 1)=="%")
		{
			$key = substr($key, 1);
			$strOperation = "QUERY";
		}
		else
		{
			$strOperation = "=";
		}

		return array("FIELD" => $key, "NEGATIVE" => $strNegative, "OPERATION" => $strOperation, "OR_NULL" => $strOrNull);
	}

	function PrepareSql(&$arFields, $arOrder, &$arFilter, $arGroupBy, $arSelectFields)
	{
		global $DB;

		$strSqlSelect = "";
		$strSqlFrom = "";
		$strSqlWhere = "";
		$strSqlGroupBy = "";
		$strSqlOrderBy = "";

		$arGroupByFunct = array("COUNT", "AVG", "MIN", "MAX", "SUM");

		$arAlreadyJoined = array();

		// GROUP BY -->
		if (is_array($arGroupBy) && count($arGroupBy) > 0)
		{
			$arSelectFields = $arGroupBy;
			foreach ($arGroupBy as $key => $val)
			{
				$val = ToUpper($val);
				$key = ToUpper($key);
				if (array_key_exists($val, $arFields) && !in_array($key, $arGroupByFunct))
				{
					if (strlen($strSqlGroupBy) > 0)
						$strSqlGroupBy .= ", ";
					$strSqlGroupBy .= $arFields[$val]["FIELD"];

					if (isset($arFields[$val]["FROM"])
						&& strlen($arFields[$val]["FROM"]) > 0
						&& !in_array($arFields[$val]["FROM"], $arAlreadyJoined))
					{
						if (strlen($strSqlFrom) > 0)
							$strSqlFrom .= " ";
						$strSqlFrom .= $arFields[$val]["FROM"];
						$arAlreadyJoined[] = $arFields[$val]["FROM"];
					}
				}
			}
		}
		// <-- GROUP BY

		// SELECT -->
		$arFieldsKeys = array_keys($arFields);

		if (is_array($arGroupBy) && count($arGroupBy)==0)
		{
			$strSqlSelect = "COUNT(%%_DISTINCT_%% ".$arFields[$arFieldsKeys[0]]["FIELD"].") as CNT ";
		}
		else
		{
			if (isset($arSelectFields) && !is_array($arSelectFields) && is_string($arSelectFields) && strlen($arSelectFields)>0 && array_key_exists($arSelectFields, $arFields))
				$arSelectFields = array($arSelectFields);

			if (!isset($arSelectFields)
				|| !is_array($arSelectFields)
				|| count($arSelectFields)<=0
				|| in_array("*", $arSelectFields))
			{
				$countFieldKey = count($arFieldsKeys);
				for ($i = 0; $i < $countFieldKey; $i++)
				{
					if (isset($arFields[$arFieldsKeys[$i]]["WHERE_ONLY"])
						&& $arFields[$arFieldsKeys[$i]]["WHERE_ONLY"] == "Y")
					{
						continue;
					}

					if (strlen($strSqlSelect) > 0)
						$strSqlSelect .= ", ";

					if ($arFields[$arFieldsKeys[$i]]["TYPE"] == "datetime")
					{
						if ((ToUpper($DB->type)=="ORACLE" || ToUpper($DB->type)=="MSSQL") && (array_key_exists($arFieldsKeys[$i], $arOrder)))
							$strSqlSelect .= $arFields[$arFieldsKeys[$i]]["FIELD"]." as ".$arFieldsKeys[$i]."_X1, ";

						$strSqlSelect .= $DB->DateToCharFunction($arFields[$arFieldsKeys[$i]]["FIELD"], "FULL")." as ".$arFieldsKeys[$i];
					}
					elseif ($arFields[$arFieldsKeys[$i]]["TYPE"] == "date")
					{
						if ((ToUpper($DB->type)=="ORACLE" || ToUpper($DB->type)=="MSSQL") && (array_key_exists($arFieldsKeys[$i], $arOrder)))
							$strSqlSelect .= $arFields[$arFieldsKeys[$i]]["FIELD"]." as ".$arFieldsKeys[$i]."_X1, ";

						$strSqlSelect .= $DB->DateToCharFunction($arFields[$arFieldsKeys[$i]]["FIELD"], "SHORT")." as ".$arFieldsKeys[$i];
					}
					else
						$strSqlSelect .= $arFields[$arFieldsKeys[$i]]["FIELD"]." as ".$arFieldsKeys[$i];

					if (isset($arFields[$arFieldsKeys[$i]]["FROM"])
						&& strlen($arFields[$arFieldsKeys[$i]]["FROM"]) > 0
						&& !in_array($arFields[$arFieldsKeys[$i]]["FROM"], $arAlreadyJoined))
					{
						if (strlen($strSqlFrom) > 0)
							$strSqlFrom .= " ";
						$strSqlFrom .= $arFields[$arFieldsKeys[$i]]["FROM"];
						$arAlreadyJoined[] = $arFields[$arFieldsKeys[$i]]["FROM"];
					}
				}
			}
			else
			{
				foreach ($arSelectFields as $key => $val)
				{
					$val = ToUpper($val);
					$key = ToUpper($key);
					if (array_key_exists($val, $arFields))
					{
						if (strlen($strSqlSelect) > 0)
							$strSqlSelect .= ", ";

						if (in_array($key, $arGroupByFunct))
						{
							$strSqlSelect .= $key."(".$arFields[$val]["FIELD"].") as ".$val;
						}
						else
						{
							if ($arFields[$val]["TYPE"] == "datetime")
							{
								if ((ToUpper($DB->type)=="ORACLE" || ToUpper($DB->type)=="MSSQL") && (array_key_exists($val, $arOrder)))
									$strSqlSelect .= $arFields[$val]["FIELD"]." as ".$val."_X1, ";

								$strSqlSelect .= $DB->DateToCharFunction($arFields[$val]["FIELD"], "FULL")." as ".$val;
							}
							elseif ($arFields[$val]["TYPE"] == "date")
							{
								if ((ToUpper($DB->type)=="ORACLE" || ToUpper($DB->type)=="MSSQL") && (array_key_exists($val, $arOrder)))
									$strSqlSelect .= $arFields[$val]["FIELD"]." as ".$val."_X1, ";

								$strSqlSelect .= $DB->DateToCharFunction($arFields[$val]["FIELD"], "SHORT")." as ".$val;
							}
							else
								$strSqlSelect .= $arFields[$val]["FIELD"]." as ".$val;
						}

						if (isset($arFields[$val]["FROM"])
							&& strlen($arFields[$val]["FROM"]) > 0
							&& !in_array($arFields[$val]["FROM"], $arAlreadyJoined))
						{
							if (strlen($strSqlFrom) > 0)
								$strSqlFrom .= " ";
							$strSqlFrom .= $arFields[$val]["FROM"];
							$arAlreadyJoined[] = $arFields[$val]["FROM"];
						}
					}
				}
			}

			if (strlen($strSqlGroupBy) > 0)
			{
				if (strlen($strSqlSelect) > 0)
					$strSqlSelect .= ", ";
				$strSqlSelect .= "COUNT(%%_DISTINCT_%% ".$arFields[$arFieldsKeys[0]]["FIELD"].") as CNT";
			}
			else
				$strSqlSelect = "%%_DISTINCT_%% ".$strSqlSelect;
		}
		// <-- SELECT

		// WHERE -->
		$arSqlSearch = Array();

		if (!is_array($arFilter))
			$filter_keys = Array();
		else
			$filter_keys = array_keys($arFilter);

		$countFilterKey = count($filter_keys);
		for ($i = 0; $i < $countFilterKey; $i++)
		{
			$vals = $arFilter[$filter_keys[$i]];
			if (!is_array($vals))
				$vals = array($vals);
			else
				$vals = array_values($vals);

			$key = $filter_keys[$i];
			$key_res = CSaleOrder::GetFilterOperation($key);
			$key = $key_res["FIELD"];
			$strNegative = $key_res["NEGATIVE"];
			$strOperation = $key_res["OPERATION"];
			$strOrNull = $key_res["OR_NULL"];

			if (array_key_exists($key, $arFields))
			{
				$arSqlSearch_tmp = array();
				if (count($vals) > 0)
				{
					if ($strOperation == "IN")
					{
						if (isset($arFields[$key]["WHERE"]))
						{
							$arSqlSearch_tmp1 = call_user_func_array(
									$arFields[$key]["WHERE"],
									array($vals, $key, $strOperation, $strNegative, $arFields[$key]["FIELD"], $arFields, $arFilter)
								);
							if ($arSqlSearch_tmp1 !== false)
								$arSqlSearch_tmp[] = $arSqlSearch_tmp1;
						}
						else
						{
							if ($arFields[$key]["TYPE"] == "int")
							{
								array_walk($vals, create_function("&\$item", "\$item=IntVal(\$item);"));
								$vals = array_unique($vals);
								$val = implode(",", $vals);

								if (count($vals) <= 0)
									$arSqlSearch_tmp[] = "(1 = 2)";
								else
									$arSqlSearch_tmp[] = (($strNegative == "Y") ? " NOT " : "")."(".$arFields[$key]["FIELD"]." IN (".$val."))";
							}
							elseif ($arFields[$key]["TYPE"] == "double")
							{
								array_walk($vals, create_function("&\$item", "\$item=DoubleVal(\$item);"));
								$vals = array_unique($vals);
								$val = implode(",", $vals);

								if (count($vals) <= 0)
									$arSqlSearch_tmp[] = "(1 = 2)";
								else
									$arSqlSearch_tmp[] = (($strNegative == "Y") ? " NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." (".$val."))";
							}
							elseif ($arFields[$key]["TYPE"] == "string" || $arFields[$key]["TYPE"] == "char")
							{
								array_walk($vals, create_function("&\$item", "\$item=\"'\".\$GLOBALS[\"DB\"]->ForSql(\$item).\"'\";"));
								$vals = array_unique($vals);
								$val = implode(",", $vals);

								if (count($vals) <= 0)
									$arSqlSearch_tmp[] = "(1 = 2)";
								else
									$arSqlSearch_tmp[] = (($strNegative == "Y") ? " NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." (".$val."))";
							}
							elseif ($arFields[$key]["TYPE"] == "datetime")
							{
								array_walk($vals, create_function("&\$item", "\$item=\"'\".\$GLOBALS[\"DB\"]->CharToDateFunction(\$GLOBALS[\"DB\"]->ForSql(\$item), \"FULL\").\"'\";"));
								$vals = array_unique($vals);
								$val = implode(",", $vals);

								if (count($vals) <= 0)
									$arSqlSearch_tmp[] = "1 = 2";
								else
									$arSqlSearch_tmp[] = ($strNegative=="Y"?" NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." (".$val."))";
							}
							elseif ($arFields[$key]["TYPE"] == "date")
							{
								array_walk($vals, create_function("&\$item", "\$item=\"'\".\$GLOBALS[\"DB\"]->CharToDateFunction(\$GLOBALS[\"DB\"]->ForSql(\$item), \"SHORT\").\"'\";"));
								$vals = array_unique($vals);
								$val = implode(",", $vals);

								if (count($vals) <= 0)
									$arSqlSearch_tmp[] = "1 = 2";
								else
									$arSqlSearch_tmp[] = ($strNegative=="Y"?" NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." (".$val."))";
							}
						}
					}
					else
					{
						$countVals = count($vals);
						for ($j = 0; $j < $countVals; $j++)
						{
							$val = $vals[$j];

							if (isset($arFields[$key]["WHERE"]))
							{
								$arSqlSearch_tmp1 = call_user_func_array(
										$arFields[$key]["WHERE"],
										array($val, $key, $strOperation, $strNegative, $arFields[$key]["FIELD"], $arFields, $arFilter)
									);
								if ($arSqlSearch_tmp1 !== false)
									$arSqlSearch_tmp[] = $arSqlSearch_tmp1;
							}
							else
							{
								if ($arFields[$key]["TYPE"] == "int")
								{
									if ((IntVal($val) == 0) && (strpos($strOperation, "=") !== False))
										$arSqlSearch_tmp[] = "(".$arFields[$key]["FIELD"]." IS ".(($strNegative == "Y") ? "NOT " : "")."NULL) ".(($strNegative == "Y") ? "AND" : "OR")." ".(($strNegative == "Y") ? "NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." 0)";
									else
										$arSqlSearch_tmp[] = (($strNegative == "Y") ? " ".$arFields[$key]["FIELD"]." IS NULL OR NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".IntVal($val)." )";
								}
								elseif ($arFields[$key]["TYPE"] == "double")
								{
									$val = str_replace(",", ".", $val);

									if ((DoubleVal($val) == 0) && (strpos($strOperation, "=") !== False))
										$arSqlSearch_tmp[] = "(".$arFields[$key]["FIELD"]." IS ".(($strNegative == "Y") ? "NOT " : "")."NULL) ".(($strNegative == "Y") ? "AND" : "OR")." ".(($strNegative == "Y") ? "NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." 0)";
									else
										$arSqlSearch_tmp[] = (($strNegative == "Y") ? " ".$arFields[$key]["FIELD"]." IS NULL OR NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".DoubleVal($val)." )";
								}
								elseif ($arFields[$key]["TYPE"] == "string" || $arFields[$key]["TYPE"] == "char")
								{
									if ($strOperation == "QUERY")
									{
										$arSqlSearch_tmp[] = GetFilterQuery($arFields[$key]["FIELD"], $val, "Y");
									}
									else
									{
										if ((strlen($val) == 0) && (strpos($strOperation, "=") !== False))
											$arSqlSearch_tmp[] = "(".$arFields[$key]["FIELD"]." IS ".(($strNegative == "Y") ? "NOT " : "")."NULL) ".(($strNegative == "Y") ? "AND NOT" : "OR")." (".$DB->Length($arFields[$key]["FIELD"])." <= 0) ".(($strNegative == "Y") ? "AND NOT" : "OR")." (".$arFields[$key]["FIELD"]." ".$strOperation." '".$DB->ForSql($val)."' )";
										else
											$arSqlSearch_tmp[] = (($strNegative == "Y") ? " ".$arFields[$key]["FIELD"]." IS NULL OR NOT " : "")."(".$arFields[$key]["FIELD"]." ".$strOperation." '".$DB->ForSql($val)."' )";
									}
								}
								elseif ($arFields[$key]["TYPE"] == "datetime")
								{
									if (strlen($val) <= 0)
										$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL)";
									else
										$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "FULL").")";
								}
								elseif ($arFields[$key]["TYPE"] == "date")
								{
									if (strlen($val) <= 0)
										$arSqlSearch_tmp[] = ($strNegative=="Y"?"NOT":"")."(".$arFields[$key]["FIELD"]." IS NULL)";
									else
										$arSqlSearch_tmp[] = ($strNegative=="Y"?" ".$arFields[$key]["FIELD"]." IS NULL OR NOT ":"")."(".$arFields[$key]["FIELD"]." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "SHORT").")";
								}
							}
						}
					}
				}

				if (isset($arFields[$key]["FROM"])
					&& strlen($arFields[$key]["FROM"]) > 0
					&& !in_array($arFields[$key]["FROM"], $arAlreadyJoined))
				{
					if (strlen($strSqlFrom) > 0)
						$strSqlFrom .= " ";
					$strSqlFrom .= $arFields[$key]["FROM"];
					$arAlreadyJoined[] = $arFields[$key]["FROM"];
				}

				$strSqlSearch_tmp = "";
				$countSqlSearch = count($arSqlSearch_tmp);
				for ($j = 0; $j < $countSqlSearch; $j++)
				{
					if ($j > 0)
						$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
					$strSqlSearch_tmp .= "(".$arSqlSearch_tmp[$j].")";
				}
				if ($strOrNull == "Y")
				{
					if (strlen($strSqlSearch_tmp) > 0)
						$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
					$strSqlSearch_tmp .= "(".$arFields[$key]["FIELD"]." IS ".($strNegative=="Y" ? "NOT " : "")."NULL)";

					if ($arFields[$key]["TYPE"] == "int" || $arFields[$key]["TYPE"] == "double")
					{
						if (strlen($strSqlSearch_tmp) > 0)
							$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
						$strSqlSearch_tmp .= "(".$arFields[$key]["FIELD"]." ".($strNegative=="Y" ? "<>" : "=")." 0)";
					}
					elseif ($arFields[$key]["TYPE"] == "string" || $arFields[$key]["TYPE"] == "char")
					{
						if (strlen($strSqlSearch_tmp) > 0)
							$strSqlSearch_tmp .= ($strNegative=="Y" ? " AND " : " OR ");
						$strSqlSearch_tmp .= "(".$arFields[$key]["FIELD"]." ".($strNegative=="Y" ? "<>" : "=")." '')";
					}
				}

				if ($strSqlSearch_tmp != "")
					$arSqlSearch[] = "(".$strSqlSearch_tmp.")";
			}
		}

		$countSqlSearch = count($arSqlSearch);
		for ($i = 0; $i < $countSqlSearch; $i++)
		{
			if (strlen($strSqlWhere) > 0)
				$strSqlWhere .= " AND ";
			$strSqlWhere .= "(".$arSqlSearch[$i].")";
		}
		// <-- WHERE

		// ORDER BY -->
		$arSqlOrder = Array();
		foreach ($arOrder as $by => $order)
		{
			$by = ToUpper($by);
			$order = ToUpper($order);

			if ($order != "ASC")
				$order = "DESC";
			else
				$order = "ASC";

			if (is_array($arGroupBy) && count($arGroupBy) > 0 && in_array($by, $arGroupBy))
			{
				$arSqlOrder[] = " ".$by." ".$order." ";
			}
			elseif (array_key_exists($by, $arFields))
			{
				$arSqlOrder[] = " ".$arFields[$by]["FIELD"]." ".$order." ";

				if (isset($arFields[$by]["FROM"])
					&& strlen($arFields[$by]["FROM"]) > 0
					&& !in_array($arFields[$by]["FROM"], $arAlreadyJoined))
				{
					if (strlen($strSqlFrom) > 0)
						$strSqlFrom .= " ";
					$strSqlFrom .= $arFields[$by]["FROM"];
					$arAlreadyJoined[] = $arFields[$by]["FROM"];
				}
			}
		}

		$strSqlOrderBy = "";
		DelDuplicateSort($arSqlOrder);
		$countSqlOrder = count($arSqlOrder);
		for ($i=0; $i < $countSqlOrder; $i++)
		{
			if (strlen($strSqlOrderBy) > 0)
				$strSqlOrderBy .= ", ";

			if(ToUpper($DB->type)=="ORACLE")
			{
				if(substr($arSqlOrder[$i], -3)=="ASC")
					$strSqlOrderBy .= $arSqlOrder[$i]." NULLS FIRST";
				else
					$strSqlOrderBy .= $arSqlOrder[$i]." NULLS LAST";
			}
			else
				$strSqlOrderBy .= $arSqlOrder[$i];
		}
		// <-- ORDER BY
		return array(
			"SELECT" => $strSqlSelect,
			"FROM" => $strSqlFrom,
			"WHERE" => $strSqlWhere,
			"GROUPBY" => $strSqlGroupBy,
			"ORDERBY" => $strSqlOrderBy
		);
	}


	//*************** SELECT *********************/
	function GetByID($ID)
	{
		global $DB;

		$ID = IntVal($ID);

		if (isset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]) && is_array($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]) && is_set($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID], "ID"))
		{
			return $GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID];
		}
		else
		{
			$strSql =
				"SELECT O.*, ".
				"	".$DB->DateToCharFunction("O.DATE_STATUS", "FULL")." as DATE_STATUS_FORMAT, ".
				"	".$DB->DateToCharFunction("O.DATE_INSERT", "SHORT")." as DATE_INSERT_FORMAT, ".
				"	".$DB->DateToCharFunction("O.DATE_UPDATE", "FULL")." as DATE_UPDATE_FORMAT, ".
				"	".$DB->DateToCharFunction("O.DATE_LOCK", "FULL")." as DATE_LOCK_FORMAT ".
				"FROM b_sale_order O ".
				"WHERE O.ID = ".$ID."";
			$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			if ($res = $db_res->Fetch())
			{
				$GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID] = $res;
				return $res;
			}
		}

		return False;
	}


	//*************** EVENTS *********************/
	function OnBeforeCurrencyDelete($currency)
	{
		if (strlen($currency)<=0) return false;

		$dbOrders = CSaleOrder::GetList(array(), array("CURRENCY" => $currency), false, array("nTopCount" => 1), array("ID", "CURRENCY"));
		if ($arOrders = $dbOrders->Fetch())
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#CURRENCY#", $currency, GetMessage("SKGO_ERROR_ORDERS_CURRENCY")), "ERROR_ORDERS_CURRENCY");
			return False;
		}

		return True;
	}

	function OnBeforeUserDelete($userID)
	{
		global $DB;

		$userID = IntVal($userID);
		if ($userID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException("Empty user ID", "EMPTY_USER_ID");
			return false;
		}

		$dbOrders = CSaleOrder::GetList(array(), array("USER_ID" => $userID), false, array("nTopCount" => 1), array("ID", "USER_ID"));
		if ($arOrders = $dbOrders->Fetch())
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#USER_ID#", $userID, GetMessage("SKGO_ERROR_ORDERS")), "ERROR_ORDERS");
			return False;
		}

		return True;
	}

	//*************** ACTIONS *********************/
	function PayOrder($ID, $val, $bWithdraw = True, $bPay = True, $recurringID = 0, $arAdditionalFields = array())
	{
		global $DB, $USER;

		$ID = IntVal($ID);
		$val = (($val != "Y") ? "N" : "Y");
		$bWithdraw = ($bWithdraw ? True : False);
		$bPay = ($bPay ? True : False);
		$recurringID = IntVal($recurringID);

		$NO_CHANGE_STATUS = "N";
		if (is_set($arAdditionalFields["NOT_CHANGE_STATUS"]) && $arAdditionalFields["NOT_CHANGE_STATUS"] == "Y")
		{
			$NO_CHANGE_STATUS = "Y";
			unset($arAdditionalFields["NOT_CHANGE_STATUS"]);
		}

		if ($ID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_NO_ORDER_ID"), "NO_ORDER_ID");
			return False;
		}

		$arOrder = CSaleOrder::GetByID($ID);
		if (!$arOrder)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_NO_ORDER")), "NO_ORDER");
			return False;
		}

		if ($arOrder["PAYED"] == $val)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_DUB_PAY")), "ALREADY_FLAG");
			return False;
		}

		foreach(GetModuleEvents("sale", "OnSaleBeforePayOrder", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID, $val, $bWithdraw, $bPay, $recurringID, $arAdditionalFields))===false)
				return false;

		if ($bWithdraw)
		{
			if ($val == "Y")
			{
				$needPaySum = DoubleVal($arOrder["PRICE"]) - DoubleVal($arOrder["SUM_PAID"]);

				if ($bPay)
					if (!CSaleUserAccount::UpdateAccount($arOrder["USER_ID"], $needPaySum, $arOrder["CURRENCY"], "OUT_CHARGE_OFF", $ID))
						return False;

				if ($needPaySum > 0 && !CSaleUserAccount::Pay($arOrder["USER_ID"], $needPaySum, $arOrder["CURRENCY"], $ID, False))
					return False;
			}
			else
			{
				if (!CSaleUserAccount::UpdateAccount($arOrder["USER_ID"], $arOrder["PRICE"], $arOrder["CURRENCY"], "ORDER_UNPAY", $ID))
					return False;
			}
		}

		$arFields = array(
			"PAYED" => $val,
			"=DATE_PAYED" => $DB->GetNowFunction(),
			"EMP_PAYED_ID" => ( IntVal($USER->GetID())>0 ? IntVal($USER->GetID()) : false ),
			"SUM_PAID" => 0
		);
		if (count($arAdditionalFields) > 0)
		{
			foreach ($arAdditionalFields as $addKey => $addValue)
			{
				if (!array_key_exists($addKey, $arFields))
					$arFields[$addKey] = $addValue;
			}
		}
		$res = CSaleOrder::Update($ID, $arFields);

		foreach(GetModuleEvents("sale", "OnSalePayOrder", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $val));

		if ($val == "Y")
		{
			CTimeZone::Disable();
			$arOrder = CSaleOrder::GetByID($ID);
			CTimeZone::Enable();

			if ($NO_CHANGE_STATUS != "Y")
			{
				$orderStatus = COption::GetOptionString("sale", "status_on_paid", "");
				if(strlen($orderStatus) > 0 && $orderStatus != $arOrder["STATUS_ID"])
				{
					$dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID));
					while ($arStatus = $dbStatus->GetNext())
					{
						$arStatuses[$arStatus["ID"]] = $arStatus["SORT"];
					}

					if($arStatuses[$orderStatus] >= $arStatuses[$arOrder["STATUS_ID"]])
						CSaleOrder::StatusOrder($ID, $orderStatus);
				}
			}

			$userEMail = "";
			$dbOrderProp = CSaleOrderPropsValue::GetList(Array(), Array("ORDER_ID" => $arOrder["ID"], "PROP_IS_EMAIL" => "Y"));
			if($arOrderProp = $dbOrderProp->Fetch())
				$userEMail = $arOrderProp["VALUE"];

			if(strlen($userEMail) <= 0)
			{
				$dbUser = CUser::GetByID($arOrder["USER_ID"]);
				if($arUser = $dbUser->Fetch())
					$userEMail = $arUser["EMAIL"];
			}

			$arFields = Array(
				"ORDER_ID" => $ID,
				"ORDER_DATE" => $arOrder["DATE_INSERT_FORMAT"],
				"EMAIL" => $userEMail,
				"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$_SERVER["SERVER_NAME"])
			);
			$eventName = "SALE_ORDER_PAID";

			$bSend = true;
			foreach(GetModuleEvents("sale", "OnOrderPaySendEmail", true) as $arEvent)
			{
				if (ExecuteModuleEventEx($arEvent, Array($ID, &$eventName, &$arFields))===false)
					$bSend = false;
			}

			if($bSend)
			{
				$event = new CEvent;
				$event->Send($eventName, $arOrder["LID"], $arFields, "N");
			}

			if (CModule::IncludeModule("statistic"))
			{
				CStatEvent::AddByEvents("eStore", "order_paid", $ID, "", $arOrder["STAT_GID"], $arOrder["PRICE"], $arOrder["CURRENCY"]);
			}
		}
		else
		{
			if (CModule::IncludeModule("statistic"))
			{
				CStatEvent::AddByEvents("eStore", "order_chargeback", $ID, "", $arOrder["STAT_GID"], $arOrder["PRICE"], $arOrder["CURRENCY"], "Y");
			}
		}

		//reservation
		if (COption::GetOptionString("sale", "product_reserve_condition", "O") == "P")
		{
			//TODO - ERROR?
			if (!CSaleOrder::ReserveOrder($ID, $val))
				return false;
		}

		if ($val == "Y")
		{
			$allowDelivery = COption::GetOptionString("sale", "status_on_payed_2_allow_delivery", "");
			if($allowDelivery == "Y" && $arOrder["ALLOW_DELIVERY"] == "N")
			{
				CSaleOrder::DeliverOrder($ID, "Y");
			}
		}

		return $res;
	}

	function DeliverOrder($ID, $val, $recurringID = 0, $arAdditionalFields = array())
	{
		global $DB, $USER;

		$ID = IntVal($ID);
		$val = (($val != "Y") ? "N" : "Y");
		$recurringID = IntVal($recurringID);

		$NO_CHANGE_STATUS = "N";
		if (is_set($arAdditionalFields["NOT_CHANGE_STATUS"]) && $arAdditionalFields["NOT_CHANGE_STATUS"] == "Y")
		{
			$NO_CHANGE_STATUS = "Y";
			unset($arAdditionalFields["NOT_CHANGE_STATUS"]);
		}

		if ($ID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_NO_ORDER_ID"), "NO_ORDER_ID");
			return False;
		}

		$arOrder = CSaleOrder::GetByID($ID);
		if (!$arOrder)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_NO_ORDER")), "NO_ORDER");
			return False;
		}

		if ($arOrder["ALLOW_DELIVERY"] == $val)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_DUB_DELIVERY")), "ALREADY_FLAG");
			return False;
		}

		foreach(GetModuleEvents("sale", "OnSaleBeforeDeliveryOrder", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID, $val, $recurringID, $arAdditionalFields))===false)
				return false;

		$arFields = array(
			"ALLOW_DELIVERY" => $val,
			"=DATE_ALLOW_DELIVERY" => $DB->GetNowFunction(),
			"EMP_ALLOW_DELIVERY_ID" => ( IntVal($USER->GetID())>0 ? IntVal($USER->GetID()) : false )
		);
		if (count($arAdditionalFields) > 0)
		{
			foreach ($arAdditionalFields as $addKey => $addValue)
			{
				if (!array_key_exists($addKey, $arFields))
					$arFields[$addKey] = $addValue;
			}
		}
		$res = CSaleOrder::Update($ID, $arFields);

		unset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]);

		if ($recurringID <= 0)
		{
			if (IntVal($arOrder["RECURRING_ID"]) > 0)
				$recurringID = IntVal($arOrder["RECURRING_ID"]);
		}

		CSaleBasket::OrderDelivery($ID, (($val=="Y") ? True : False), $recurringID);

		foreach(GetModuleEvents("sale", "OnSaleDeliveryOrder", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $val));

		if ($val == "Y")
		{
			CTimeZone::Disable();
			$arOrder = CSaleOrder::GetByID($ID);
			CTimeZone::Enable();

			if ($NO_CHANGE_STATUS != "Y")
			{
				$orderStatus = COption::GetOptionString("sale", "status_on_allow_delivery", "");
				if(strlen($orderStatus) > 0 && $orderStatus != $arOrder["STATUS_ID"])
				{
					$dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID), false, false, Array("ID", "SORT"));
					while ($arStatus = $dbStatus->GetNext())
					{
						$arStatuses[$arStatus["ID"]] = $arStatus["SORT"];
					}

					if($arStatuses[$orderStatus] >= $arStatuses[$arOrder["STATUS_ID"]])
						CSaleOrder::StatusOrder($ID, $orderStatus);
				}
			}

			$userEMail = "";
			$dbOrderProp = CSaleOrderPropsValue::GetList(Array(), Array("ORDER_ID" => $arOrder["ID"], "PROP_IS_EMAIL" => "Y"));
			if($arOrderProp = $dbOrderProp->Fetch())
				$userEMail = $arOrderProp["VALUE"];

			if(strlen($userEMail) <= 0)
			{
				$dbUser = CUser::GetByID($arOrder["USER_ID"]);
				if($arUser = $dbUser->Fetch())
					$userEMail = $arUser["EMAIL"];
			}

			$eventName = "SALE_ORDER_DELIVERY";
			$arFields = Array(
				"ORDER_ID" => $ID,
				"ORDER_DATE" => $arOrder["DATE_INSERT_FORMAT"],
				"EMAIL" => $userEMail,
				"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$_SERVER["SERVER_NAME"])
			);

			$bSend = true;
			foreach(GetModuleEvents("sale", "OnOrderDeliverSendEmail", true) as $arEvent)
				if (ExecuteModuleEventEx($arEvent, Array($ID, &$eventName, &$arFields))===false)
					$bSend = false;

			if($bSend)
			{
				$event = new CEvent;
				$event->Send($eventName, $arOrder["LID"], $arFields, "N");
			}
		}

		//reservation
		if (COption::GetOptionString("sale", "product_reserve_condition", "O") == "D")
		{
			if (!CSaleOrder::ReserveOrder($ID, $val))
				return false;
		}

		//proceed to deduction
		if ($val == "Y")
		{
			$allowDeduction = COption::GetOptionString("sale", "allow_deduction_on_delivery", "");
			if($allowDeduction == "Y" && $arOrder["DEDUCTED"] == "N")
			{
				CSaleOrder::DeductOrder($ID, "Y");
			}
		}

		return $res;
	}

	function DeductOrder($ID, $val, $description = "", $bAutoDeduction = true, $arStoreBarcodeOrderFormData = array(), $recurringID = 0)
	{
		global $DB, $USER;

		$ID = IntVal($ID);
		$val = (($val != "Y") ? "N" : "Y");
		$description = Trim($description);
		$recurringID = IntVal($recurringID);

		if ($ID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_NO_ORDER_ID"), "NO_ORDER_ID");
			return false;
		}

		$arOrder = CSaleOrder::GetByID($ID);
		if (!$arOrder)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_NO_ORDER")), "NO_ORDER");
			return false;
		}

		if ($arOrder["DEDUCTED"] == $val)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_DUB_DEDUCTION")), "ALREADY_FLAG");
			return false;
		}

		foreach(GetModuleEvents("sale", "OnSaleBeforeDeductOrder", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID, $val, $description, $bAutoDeduction, $arStoreBarcodeOrderFormData, $recurringID))===false)
				return false;

		unset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]);

		if ($recurringID <= 0)
		{
			if (IntVal($arOrder["RECURRING_ID"]) > 0)
				$recurringID = IntVal($arOrder["RECURRING_ID"]);
		}

		$arDeductResult = CSaleBasket::OrderDeduction($ID, (($val == "N") ? true : false), $recurringID, $bAutoDeduction, $arStoreBarcodeOrderFormData);
		if (array_key_exists("ERROR", $arDeductResult))
		{
			CSaleOrder::SetMark($ID, GetMessage("SKGB_DEDUCT_ERROR", array("#MESSAGE#" => $arDeductResult["ERROR"]["MESSAGE"])));
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGB_DEDUCT_ERROR", array("#MESSAGE#" => $arDeductResult["ERROR"]["MESSAGE"])), "DEDUCTION_ERROR");
			return false;
		}
		else
		{
			CSaleOrder::UnsetMark($ID);
		}

		if ($arDeductResult["RESULT"])
		{
			if ($val == "Y")
			{
				$arFields = array(
					"DEDUCTED" => "Y",
					"EMP_DEDUCTED_ID" => ( IntVal($USER->GetID())>0 ? IntVal($USER->GetID()) : false ),
					"=DATE_DEDUCTED" => $DB->GetNowFunction()
				);
			}
			else
			{
				$arFields = array(
					"DEDUCTED" => "N",
					"EMP_DEDUCTED_ID" => ( IntVal($USER->GetID())>0 ? IntVal($USER->GetID()) : false ),
					"=DATE_DEDUCTED" => $DB->GetNowFunction()
				);

				if (strlen($description) > 0)
					$arFields["REASON_UNDO_DEDUCTED"] = $description;
			}
			$res = CSaleOrder::Update($ID, $arFields);
		}
		else
			$res = false;

		foreach(GetModuleEvents("sale", "OnSaleDeductOrder", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $val));

		if ($res)
			return $res;
		else
			return false;
	}

	function ReserveOrder($ID, $val, $recurringID = 0)
	{
		global $DB, $USER;

		$ID = IntVal($ID);
		$val = (($val != "Y") ? "N" : "Y");
		$recurringID = IntVal($recurringID);

		if ($ID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_NO_ORDER_ID"), "NO_ORDER_ID");
			return false;
		}

		$arOrder = CSaleOrder::GetByID($ID);
		if (!$arOrder)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_NO_ORDER")), "NO_ORDER");
			return false;

			//TODO - UnsetMark?
		}

		if ($arOrder["RESERVED"] == $val)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_DUB_RESERVATION")), "ALREADY_FLAG");
			return false;
		}

		foreach(GetModuleEvents("sale", "OnSaleBeforeReserveOrder", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID, $val, $recurringID, $arAdditionalFields))===false)
				return false;

		unset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]);

		if ($recurringID <= 0)
		{
			if (IntVal($arOrder["RECURRING_ID"]) > 0)
				$recurringID = IntVal($arOrder["RECURRING_ID"]);
		}

		//TODO - recurring?
		$res = CSaleOrder::Update($ID, array("RESERVED" => $val));

		$arRes = CSaleBasket::OrderReservation($ID, (($val == "N") ? true : false), $recurringID);
		if (array_key_exists("ERROR", $arRes))
		{
			foreach ($arRes["ERROR"] as $productId => $arError)
			{
				$errorMessage .= " ".$arError["MESSAGE"];
			}

			CSaleOrder::SetMark($ID, GetMessage("SKGB_RESERVE_ERROR", array("#MESSAGE#" => $errorMessage)));
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGB_RESERVE_ERROR", array("#MESSAGE#" => $errorMessage)), "RESERVATION_ERROR");
			return false;
		}
		else
		{
			//don't unset if not set yet
			CSaleOrder::UnsetMark($ID);
		}

		foreach(GetModuleEvents("sale", "OnSaleReserveOrder", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $val));

		return $res;
	}

	function CancelOrder($ID, $val, $description = "")
	{
		global $DB, $USER;

		$ID = IntVal($ID);
		$val = (($val != "Y") ? "N" : "Y");
		$description = Trim($description);

		if ($ID <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("SKGO_NO_ORDER_ID1"), "NO_ORDER_ID");
			return False;
		}

		$arOrder = CSaleOrder::GetByID($ID);
		if (!$arOrder)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_NO_ORDER")), "NO_ORDER");
			return False;
		}

		if ($arOrder["CANCELED"] == $val)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#ID#", $ID, GetMessage("SKGO_DUB_CANCEL")), "ALREADY_FLAG");
			return False;
		}

		foreach(GetModuleEvents("sale", "OnSaleBeforeCancelOrder", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID, $val))===false)
				return false;

		if ($val == "Y")
		{
			if ($arOrder["PAYED"] == "Y")
			{
				if (!CSaleOrder::PayOrder($ID, "N", True, True))
					return False;
			}
			else
			{
				$arOrder["SUM_PAID"] = DoubleVal($arOrder["SUM_PAID"]);
				if ($arOrder["SUM_PAID"] > 0)
				{
					if (!CSaleUserAccount::UpdateAccount($arOrder["USER_ID"], $arOrder["SUM_PAID"], $arOrder["CURRENCY"], "ORDER_CANCEL_PART", $ID))
						return False;
				}
			}

			if ($arOrder["ALLOW_DELIVERY"] == "Y")
			{
				if (!CSaleOrder::DeliverOrder($ID, "N"))
					return False;
			}
		}

		$arFields = array(
			"CANCELED" => $val,
			"=DATE_CANCELED" => $DB->GetNowFunction(),
			"REASON_CANCELED" => ( strlen($description)>0 ? $description : false ),
			"EMP_CANCELED_ID" => ( IntVal($USER->GetID())>0 ? IntVal($USER->GetID()) : false )
		);
		$res = CSaleOrder::Update($ID, $arFields);

		unset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]);

		//this method is used only for catalogs without reservation and deduction support
		CSaleBasket::OrderCanceled($ID, (($val=="Y") ? True : False));

		foreach(GetModuleEvents("sale", "OnSaleCancelOrder", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $val, $description));

		if ($val == "Y")
		{
			CTimeZone::Disable();
			$arOrder = CSaleOrder::GetByID($ID);
			CTimeZone::Enable();

			$userEmail = "";
			$dbOrderProp = CSaleOrderPropsValue::GetList(Array(), Array("ORDER_ID" => $ID, "PROP_IS_EMAIL" => "Y"));
			if($arOrderProp = $dbOrderProp->Fetch())
				$userEmail = $arOrderProp["VALUE"];
			if(strlen($userEmail) <= 0)
			{
				$dbUser = CUser::GetByID($arOrder["USER_ID"]);
				if($arUser = $dbUser->Fetch())
					$userEmail = $arUser["EMAIL"];
			}

			$event = new CEvent;
			$arFields = Array(
				"ORDER_ID" => $ID,
				"ORDER_DATE" => $arOrder["DATE_INSERT_FORMAT"],
				"EMAIL" => $userEmail,
				"ORDER_CANCEL_DESCRIPTION" => $description,
				"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$_SERVER["SERVER_NAME"])
			);

			$eventName = "SALE_ORDER_CANCEL";

			$bSend = true;
			foreach(GetModuleEvents("sale", "OnOrderCancelSendEmail", true) as $arEvent)
				if (ExecuteModuleEventEx($arEvent, Array($ID, &$eventName, &$arFields))===false)
					$bSend = false;

			if($bSend)
			{
				$event = new CEvent;
				$event->Send($eventName, $arOrder["LID"], $arFields, "N");
			}

			if (CModule::IncludeModule("statistic"))
			{
				CStatEvent::AddByEvents("eStore", "order_cancel", $ID, "", $arOrder["STAT_GID"]);
			}
		}

		if ($val == "Y")
		{
			if ($arOrder["DEDUCTED"] == "Y")
			{
				$rs = CSaleOrder::DeductOrder($ID, "N");
				if (!$rs)
					return false;
			}

			if ($arOrder["RESERVED"] == "Y")
			{
				if (!CSaleOrder::ReserveOrder($ID, "N"));
					return false;
			}
		}
		else //if undo cancel
		{
			if (COption::GetOptionString("sale", "product_reserve_condition", "O") == "O" && $arOrder["RESERVED"] != "Y")
			{
				if (!CSaleOrder::ReserveOrder($ID, "Y"));
					return false;
			}
		}

		return $res;
	}

	function StatusOrder($ID, $val)
	{
		global $DB, $USER;

		$ID = IntVal($ID);
		$val = trim($val);

		foreach(GetModuleEvents("sale", "OnSaleBeforeStatusOrder", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID, $val))===false)
				return false;

		$arFields = array(
			"STATUS_ID" => $val,
			"=DATE_STATUS" => $DB->GetNowFunction(),
			"EMP_STATUS_ID" => ( IntVal($USER->GetID())>0 ? IntVal($USER->GetID()) : false )
		);
		$res = CSaleOrder::Update($ID, $arFields);

		unset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]);

		foreach(GetModuleEvents("sale", "OnSaleStatusOrder", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($ID, $val));

		CTimeZone::Disable();
		$arOrder = CSaleOrder::GetByID($ID);
		CTimeZone::Enable();

		$userEmail = "";
		$dbOrderProp = CSaleOrderPropsValue::GetList(Array(), Array("ORDER_ID" => $ID, "PROP_IS_EMAIL" => "Y"));
		if($arOrderProp = $dbOrderProp->Fetch())
			$userEmail = $arOrderProp["VALUE"];
		if(strlen($userEmail) <= 0)
		{
			$dbUser = CUser::GetByID($arOrder["USER_ID"]);
			if($arUser = $dbUser->Fetch())
				$userEmail = $arUser["EMAIL"];
		}
		$dbSite = CSite::GetByID($arOrder["LID"]);
		$arSite = $dbSite->Fetch();
		$arStatus = CSaleStatus::GetByID($arOrder["STATUS_ID"], $arSite["LANGUAGE_ID"]);

		$arFields = Array(
			"ORDER_ID" => $ID,
			"ORDER_DATE" => $arOrder["DATE_INSERT_FORMAT"],
			"ORDER_STATUS" => $arStatus["NAME"],
			"EMAIL" => $userEmail,
			"ORDER_DESCRIPTION" => $arStatus["DESCRIPTION"],
			"TEXT" => "",
			"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$_SERVER["SERVER_NAME"])
		);

		foreach(GetModuleEvents("sale", "OnSaleStatusEMail", true) as $arEvent)
			$arFields["TEXT"] = ExecuteModuleEventEx($arEvent, Array($ID, $arStatus["ID"]));

		$eventName = "SALE_STATUS_CHANGED_".$arOrder["STATUS_ID"];

		$bSend = true;
		foreach(GetModuleEvents("sale", "OnOrderStatusSendEmail", true) as $arEvent)
			if (ExecuteModuleEventEx($arEvent, Array($ID, &$eventName, &$arFields, $arOrder["STATUS_ID"]))===false)
				$bSend = false;

		if($bSend)
		{
			$eventMessage = new CEventMessage;
			$dbEventMessage = $eventMessage->GetList(
				($b = ""),
				($o = ""),
				array(
					"EVENT_NAME" => $eventName,
					"SITE_ID" => $arOrder["LID"]
				)
			);
			if (!($arEventMessage = $dbEventMessage->Fetch()))
				$eventName = "SALE_STATUS_CHANGED";

			$event = new CEvent;
			$event->Send($eventName, $arOrder["LID"], $arFields, "N");
		}

		return $res;
	}

	function CommentsOrder($ID, $val)
	{
		global $DB;

		$ID = IntVal($ID);
		$val = Trim($val);

		$arFields = array(
			"COMMENTS" => ( strlen($val)>0 ? $val : false )
		);
		$res = CSaleOrder::Update($ID, $arFields);

		unset($GLOBALS["SALE_ORDER"]["SALE_ORDER_CACHE_".$ID]);

		return $res;
	}


	function Lock($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
			return False;

		$arFields = array(
			"=DATE_LOCK" => $DB->GetNowFunction(),
			"LOCKED_BY" => $GLOBALS["USER"]->GetID()
		);
		return CSaleOrder::Update($ID, $arFields, false);
	}

	function UnLock($ID)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID <= 0)
			return False;

		$arOrder = CSaleOrder::GetByID($ID);
		if (!$arOrder)
			return False;

		$userRights = CMain::GetUserRight("sale", $GLOBALS["USER"]->GetUserGroupArray(), "Y", "Y");

		if (($userRights >= "W") || ($arOrder["LOCKED_BY"] == $GLOBALS["USER"]->GetID()))
		{
			$arFields = array(
				"DATE_LOCK" => false,
				"LOCKED_BY" => false
			);

			if (!CSaleOrder::Update($ID, $arFields, false))
				return False;
			else
				return True;
		}

		return False;
	}

	function IsLocked($ID, &$lockedBY, &$dateLock)
	{
		$ID = IntVal($ID);

		$lockStatus = CSaleOrder::GetLockStatus($ID, $lockedBY, $dateLock);
		if ($lockStatus == "red")
			return true;

		return false;
	}

	function RemindPayment()
	{
		$reminder = COption::GetOptionString("sale", "pay_reminder", "");
		$arReminder = unserialize($reminder);

		if(!empty($arReminder))
		{
			$arSites = Array();
			$minDay = mktime();
			foreach($arReminder as $key => $val)
			{
				if($val["use"] == "Y")
				{
					$arSites[] = $key;
					$days = Array();

					for($i=0; $i <= floor($val["period"] / $val["frequency"]); $i++)
					{
						$day = AddToTimeStamp(Array("DD" => -($val["after"] + $val["period"] - $val["frequency"]*$i)));
						if($day < mktime())
						{
							if($minDay > $day)
								$minDay = $day;

							$day = ConvertTimeStamp($day);

							$days[] = $day;
						}
					}
					$arReminder[$key]["days"] = $days;
				}
			}

			if(!empty($arSites))
			{
				$bTmpUser = False;
				if (!isset($GLOBALS["USER"]) || !is_object($GLOBALS["USER"]))
				{
					$bTmpUser = True;
					$GLOBALS["USER"] = new CUser;
				}

				$arFilter = Array(
						"LID" => $arSites,
						"PAYED" => "N",
						"CANCELED" => "N",
						"ALLOW_DELIVERY" => "N",
						">=DATE_INSERT" => ConvertTimeStamp($minDay),
					);

				$dbOrder = CSaleOrder::GetList(Array("ID" => "DESC"), $arFilter, false, false, Array("ID", "DATE_INSERT", "PAYED", "USER_ID", "LID", "PRICE", "CURRENCY"));
				while($arOrder = $dbOrder -> GetNext())
				{
					$date_insert = ConvertDateTime($arOrder["DATE_INSERT"], CSite::GetDateFormat("SHORT"));

					if(in_array($date_insert, $arReminder[$arOrder["LID"]]["days"]))
					{

						$strOrderList = "";
						$dbBasketTmp = CSaleBasket::GetList(
								array("NAME" => "ASC"),
								array("ORDER_ID" => $arOrder["ID"]),
								false,
								false,
								array("ID", "NAME", "QUANTITY")
							);
						while ($arBasketTmp = $dbBasketTmp->Fetch())
						{
							$strOrderList .= $arBasketTmp["NAME"]." (".$arBasketTmp["QUANTITY"].")";
							$strOrderList .= "\n";
						}

						$payerEMail = "";
						$dbOrderProp = CSaleOrderPropsValue::GetList(Array(), Array("ORDER_ID" => $arOrder["ID"], "PROP_IS_EMAIL" => "Y"));
						if($arOrderProp = $dbOrderProp->Fetch())
							$payerEMail = $arOrderProp["VALUE"];

						$payerName = "";
						$dbUser = CUser::GetByID($arOrder["USER_ID"]);
						if ($arUser = $dbUser->Fetch())
						{
							if (strlen($payerName) <= 0)
								$payerName = $arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"];
							if (strlen($payerEMail) <= 0)
								$payerEMail = $arUser["EMAIL"];
						}

						$arFields = Array(
							"ORDER_ID" => $arOrder["ID"],
							"ORDER_DATE" => $date_insert,
							"ORDER_USER" => $payerName,
							"PRICE" => SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]),
							"BCC" => COption::GetOptionString("sale", "order_email", "order@".$_SERVER["SERVER_NAME"]),
							"EMAIL" => $payerEMail,
							"ORDER_LIST" => $strOrderList,
							"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME)
						);

						$eventName = "SALE_ORDER_REMIND_PAYMENT";

						$bSend = true;
						foreach(GetModuleEvents("sale", "OnOrderRemindSendEmail", true) as $arEvent)
							if (ExecuteModuleEventEx($arEvent, Array($arOrder["ID"], &$eventName, &$arFields))===false)
								$bSend = false;

						if($bSend)
						{
							$event = new CEvent;
							$event->Send($eventName, $arOrder["LID"], $arFields, "N");
						}
					}
				}

				if ($bTmpUser)
				{
					unset($GLOBALS["USER"]);
				}

			}
		}
		return "CSaleOrder::RemindPayment();";
	}

	function __SaleOrderCount($arFilter, $strCurrency = '')
	{
		$mxResult = false;
		if (is_array($arFilter) && !empty($arFilter))
		{
			$dblPrice = 0;
			$strCurrency = strval($strCurrency);
			$mxLastOrderDate = '';
			$intMaxTimestamp = 0;
			$intTimeStamp = 0;
			$rsSaleOrders = CSaleOrder::GetList(array(),$arFilter,false,false,array('ID','PRICE','CURRENCY','DATE_INSERT'));
			while ($arSaleOrder = $rsSaleOrders->Fetch())
			{
				$intTimeStamp = MakeTimeStamp($arSaleOrder['DATE_INSERT']);
				if ($intMaxTimestamp < $intTimeStamp)
				{
					$intMaxTimestamp = $intTimeStamp;
					$mxLastOrderDate = $arSaleOrder['DATE_INSERT'];
				}
				if (empty($strCurrency))
				{
					$dblPrice += $arSaleOrder['PRICE'];
					$strCurrency = $arSaleOrder['CURRENCY'];
				}
				else
				{
					if ($strCurrency != $arSaleOrder['CURRENCY'])
					{
						$dblPrice += $arSaleOrder['PRICE'];
					}
					else
					{
						$dblPrice += $arSaleOrder['PRICE'];
					}
				}
			}
			$mxResult = array(
				'PRICE' => $dblPrice,
				'CURRENCY' => $strCurrency,
				'LAST_ORDER_DATE' => $mxLastOrderDate,
				'TIMESTAMP' => $intMaxTimestamp,
			);
		}
		return $mxResult;
	}


	/**
	* The function select order history
	*
	* @param array $arOrder - array to sort
	* @param array $arFilter - array to filter
	* @param array $arGroupBy - array to group records
	* @param array $arNavStartParams - array to parameters
	* @param array $arSelectFields - array to selectes fields
	* @return object $dbRes - object result
	*/
	public function GetHistoryList($arOrder = array("ID"=>"DESC"), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (array_key_exists("H_DATE_INSERT_FROM", $arFilter))
		{
			$val = $arFilter["H_DATE_INSERT_FROM"];
			unset($arFilter["H_DATE_INSERT_FROM"]);
			$arFilter[">=H_DATE_INSERT"] = $val;
		}
		if (array_key_exists("H_DATE_INSERT_TO", $arFilter))
		{
			$val = $arFilter["H_DATE_INSERT_TO"];
			unset($arFilter["H_DATE_INSERT_TO"]);
			$arFilter["<=H_DATE_INSERT"] = $val;
		}

		if (!$arSelectFields || count($arSelectFields) <= 0 || in_array("*", $arSelectFields))
		{
			$arSelectFields = array(
				"ID",
				"H_USER_ID",
				"H_DATE_INSERT",
				"H_ORDER_ID",
				"H_CURRENCY",
				"PERSON_TYPE_ID",
				"PAYED",
				"DATE_PAYED",
				"EMP_PAYED_ID",
				"CANCELED",
				"DATE_CANCELED",
				"REASON_CANCELED",
				"MARKED",
				"DATE_MARKED",
				"REASON_MARKED",
				"DEDUCTED",
				"DATE_DEDUCTED",
				"REASON_UNDO_DEDUCTED",
				"RESERVED",
				"STATUS_ID",
				"DATE_STATUS",
				"PRICE_DELIVERY",
				"ALLOW_DELIVERY",
				"DATE_ALLOW_DELIVERY",
				"PRICE",
				"CURRENCY",
				"DISCOUNT_VALUE",
				"USER_ID",
				"PAY_SYSTEM_ID",
				"DELIVERY_ID",
				"PS_STATUS",
				"PS_STATUS_CODE",
				"PS_STATUS_DESCRIPTION",
				"PS_STATUS_MESSAGE",
				"PS_SUM",
				"PS_CURRENCY",
				"PS_RESPONSE_DATE",
				"TAX_VALUE",
				"STAT_GID",
				"SUM_PAID",
				"PAY_VOUCHER_NUM",
				"PAY_VOUCHER_DATE",
				"AFFILIATE_ID",
				"DELIVERY_DOC_NUM",
				"DELIVERY_DOC_DATE"
			);
		}

		$arFields = array(
				"ID" => array("FIELD" => "V.ID", "TYPE" => "int"),
				"H_ORDER_ID" => array("FIELD" => "V.H_ORDER_ID", "TYPE" => "int"),
				"H_USER_ID" => array("FIELD" => "V.H_USER_ID", "TYPE" => "int"),
				"H_DATE_INSERT" => array("FIELD" => "V.H_DATE_INSERT", "TYPE" => "datetime"),
				"H_CURRENCY" => array("FIELD" => "V.H_CURRENCY", "TYPE" => "string"),
				"PERSON_TYPE_ID" => array("FIELD" => "V.PERSON_TYPE_ID", "TYPE" => "int"),
				"PAYED" => array("FIELD" => "V.PAYED", "TYPE" => "char"),
				"DATE_PAYED" => array("FIELD" => "V.DATE_PAYED", "TYPE" => "datetime"),
				"EMP_PAYED_ID" => array("FIELD" => "V.EMP_PAYED_ID", "TYPE" => "int"),
				"CANCELED" => array("FIELD" => "V.CANCELED", "TYPE" => "char"),
				"DATE_CANCELED" => array("FIELD" => "V.DATE_CANCELED", "TYPE" => "datetime"),
				"REASON_CANCELED" => array("FIELD" => "V.REASON_CANCELED", "TYPE" => "string"),
				"MARKED" => array("FIELD" => "V.MARKED", "TYPE" => "char"),
				"DATE_MARKED" => array("FIELD" => "V.DATE_MARKED", "TYPE" => "datetime"),
				"REASON_MARKED" => array("FIELD" => "V.REASON_MARKED", "TYPE" => "string"),
				"DEDUCTED" => array("FIELD" => "V.DEDUCTED", "TYPE" => "char"),
				"DATE_DEDUCTED" => array("FIELD" => "V.DATE_DEDUCTED", "TYPE" => "datetime"),
				"REASON_DEDUCTED" => array("FIELD" => "V.REASON_UNDO_DEDUCTED", "TYPE" => "string"),
				"RESERVED" => array("FIELD" => "V.RESERVED", "TYPE" => "char"),
				"STATUS_ID" => array("FIELD" => "V.STATUS_ID", "TYPE" => "char"),
				"DATE_STATUS" => array("FIELD" => "V.DATE_STATUS", "TYPE" => "datetime"),
				"PAY_VOUCHER_NUM" => array("FIELD" => "V.PAY_VOUCHER_NUM", "TYPE" => "string"),
				"PAY_VOUCHER_DATE" => array("FIELD" => "V.PAY_VOUCHER_DATE", "TYPE" => "date"),
				"PRICE_DELIVERY" => array("FIELD" => "V.PRICE_DELIVERY", "TYPE" => "double"),
				"ALLOW_DELIVERY" => array("FIELD" => "V.ALLOW_DELIVERY", "TYPE" => "char"),
				"DATE_ALLOW_DELIVERY" => array("FIELD" => "V.DATE_ALLOW_DELIVERY", "TYPE" => "datetime"),
				"PRICE" => array("FIELD" => "V.PRICE", "TYPE" => "double"),
				"CURRENCY" => array("FIELD" => "V.CURRENCY", "TYPE" => "string"),
				"DISCOUNT_VALUE" => array("FIELD" => "V.DISCOUNT_VALUE", "TYPE" => "double"),
				"SUM_PAID" => array("FIELD" => "V.SUM_PAID", "TYPE" => "double"),
				"USER_ID" => array("FIELD" => "V.USER_ID", "TYPE" => "int"),
				"PAY_SYSTEM_ID" => array("FIELD" => "V.PAY_SYSTEM_ID", "TYPE" => "int"),
				"DELIVERY_ID" => array("FIELD" => "V.DELIVERY_ID", "TYPE" => "string"),
				"PS_STATUS" => array("FIELD" => "V.PS_STATUS", "TYPE" => "char"),
				"PS_STATUS_CODE" => array("FIELD" => "V.PS_STATUS_CODE", "TYPE" => "string"),
				"PS_STATUS_DESCRIPTION" => array("FIELD" => "V.PS_STATUS_DESCRIPTION", "TYPE" => "string"),
				"PS_STATUS_MESSAGE" => array("FIELD" => "V.PS_STATUS_MESSAGE", "TYPE" => "string"),
				"PS_SUM" => array("FIELD" => "V.PS_SUM", "TYPE" => "double"),
				"PS_CURRENCY" => array("FIELD" => "V.PS_CURRENCY", "TYPE" => "string"),
				"PS_RESPONSE_DATE" => array("FIELD" => "V.PS_RESPONSE_DATE", "TYPE" => "datetime"),
				"TAX_VALUE" => array("FIELD" => "V.TAX_VALUE", "TYPE" => "double"),
				"AFFILIATE_ID" => array("FIELD" => "V.AFFILIATE_ID", "TYPE" => "int"),
				"DELIVERY_DOC_NUM" => array("FIELD" => "V.DELIVERY_DOC_NUM", "TYPE" => "string"),
				"DELIVERY_DOC_DATE" => array("FIELD" => "V.DELIVERY_DOC_DATE", "TYPE" => "date"),
		);

		$arSqls = CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);

		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);
		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sale_order_history V ";

		if (strlen($arSqls["WHERE"]) > 0)
			$strSql .= "WHERE ".$arSqls["WHERE"]." ";
		if (strlen($arSqls["GROUPBY"]) > 0)
			$strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
		if (strlen($arSqls["ORDERBY"]) > 0)
			$strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

		if (is_array($arGroupBy) && count($arGroupBy) == 0)
		{
			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"]) <= 0 )
		{
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sale_order_history B ";
			if (strlen($arSqls["WHERE"]) > 0)
				$strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
			if (strlen($arSqls["GROUPBY"]) > 0)
				$strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (strlen($arSqls["GROUPBY"]) <= 0)
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}
			$dbRes = new CDBResult();

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			$strSql = $DB->TopSql($strSql, $arNavStartParams["nTopCount"]);
			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	public static function SetMark($ID, $comment = "", $userID = 0)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID < 0)
			return false;

		$userID = IntVal($userID);

		$arFields = array(
			"MARKED" => "Y",
			"REASON_MARKED" => $comment,
			"EMP_MARKED_ID" => $userID,
			"=DATE_MARKED" => $DB->GetNowFunction()
		);

		return CSaleOrder::Update($ID, $arFields);
	}

	public static function UnsetMark($ID, $userID = 0)
	{
		global $DB;

		$ID = IntVal($ID);
		if ($ID < 0)
			return false;

		$userID = IntVal($userID);

		$arFields = array(
			"MARKED" => "N",
			"REASON_MARKED" => "",
			"EMP_MARKED_ID" => $userID,
			"=DATE_MARKED" => $DB->GetNowFunction()
		);

		return CSaleOrder::Update($ID, $arFields);
	}
}
?>