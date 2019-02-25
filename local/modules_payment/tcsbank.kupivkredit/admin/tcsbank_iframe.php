<?define('STOP_STATISTICS', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/include.php");
$arRights = $obModule->GetGroupRight();

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ( $arRights <= "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

ClearVars();

$ID = IntVal($ID);

if ($ID <= 0)
	LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));

$customTabber = new CAdminTabEngine("OnAdminSaleOrderEdit", array("ID" => $ID));

$errorMessage = "";

$bVarsFromForm = false;
$PARTIAL_SUBMIT = (($PARTIAL_SUBMIT == "Y") ? "Y" : "N");
if ($PARTIAL_SUBMIT == "Y")
{
	$bVarsFromForm = true;
	$arInd = array();
	$ids = Array();
	$allIDs = Array();
	
	$arIDs = explode(",", trim($_POST["BASKET_IDS"]));
	
	foreach($arIDs as $v)
	{
		$ids[] = $v;
		$allIDs[] = $v;
	}

}

$bUserCanViewOrder = CSaleOrder::CanUserViewOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanEditOrder = CSaleOrder::CanUserUpdateOrder($ID, $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanCancelOrder = CSaleOrder::CanUserCancelOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());
$bUserCanPayOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "P", $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanDeliverOrder = CSaleOrder::CanUserChangeOrderFlag($ID, "D", $GLOBALS["USER"]->GetUserGroupArray());
$bUserCanDeleteOrder = CSaleOrder::CanUserDeleteOrder($ID, $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID());


$simpleForm = COption::GetOptionString("sale", "lock_catalog", "Y");
$bSimpleForm = (($simpleForm=="Y") ? True : False);

if ($action == "update"
	&& $saleModulePermissions >= "U"
	&& $_SERVER["REQUEST_METHOD"] == "POST"
	&& check_bitrix_sessid()
	&& $bUserCanEditOrder
	&& $PARTIAL_SUBMIT != "Y"
	&& empty($dontsave))
{
	// *****************************************************************
	// *****  Preparing  ***********************************************
	// *****************************************************************
	$bTrabsactionStarted = False;
	array_walk_recursive($_POST, Array("CTCSBank","Decode"));
	array_walk_recursive($_REQUEST, Array("CTCSBank","Decode"));
	foreach($_POST as $sKey=>$sValue) $$sKey = $sValue;
	
	// Order params
	$currentDate = Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)));
	$dbOrderTmp = CSaleOrder::GetList(array(), array("ID" => $ID));//GetByID($ID);
	$arOrder = $dbOrderTmp->Fetch();
	if (!$arOrder)
		$errorMessage .= GetMessage("TCS_SOE_NO_ORDER").". ";
	foreach($arOrder as $sKey=>$sValue) $$sKey = $sValue;
	if (CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		$errorMessage .= str_replace("#DATE#", "$dateLock", str_replace("#ID#", "$lockedBY", GetMessage("TCS_SOE_ORDER_LOCKED"))).". ";

	if (!$customTabber->Check())
	{
		if ($ex = $APPLICATION->GetException())
			$errorMessage .= $ex->GetString();
		else
			$errorMessage .= "Error. ";
	}

	$LID = Trim($LID);
	if (strlen($LID) <= 0)
		$errorMessage .= GetMessage("TCS_SOE_EMPTY_SITE").". ";

	$USER_ID = IntVal($USER_ID);
	if ($USER_ID <= 0)
		$errorMessage .= GetMessage("TCS_SOE_EMPTY_USER").". ";

	$PERSON_TYPE_ID = IntVal($PERSON_TYPE_ID);
	if ($PERSON_TYPE_ID <= 0)
		$errorMessage .= GetMessage("TCS_SOE_EMPTY_PERS_TYPE").". ";

	if (($PERSON_TYPE_ID > 0) && !($arPersonType = CSalePersonType::GetByID($PERSON_TYPE_ID)))
		$errorMessage .= GetMessage("TCS_SOE_PERSON_NOT_FOUND")."<br>";

	$STATUS_ID = Trim($STATUS_ID);
	if (strlen($STATUS_ID) > 0)
	{
		if ($saleModulePermissions < "W")
		{
			$dbStatusList = CSaleStatus::GetList(
				array(),
				array(
					"GROUP_ID" => $GLOBALS["USER"]->GetUserGroupArray(),
					"PERM_STATUS" => "Y",
					"ID" => $STATUS_ID
				),
				array("ID", "MAX" => "PERM_STATUS"),
				false,
				array("ID")
			);
			if (!$dbStatusList->Fetch())
				$errorMessage .= str_replace("#STATUS_ID#", $STATUS_ID, GetMessage("TCS_SOE_NO_STATUS_PERMS")).". ";
		}
	}

	$CANCELED = (($CANCELED == "Y") ? "Y" : "N");
	$PAYED = (($PAYED == "Y") ? "Y" : "N");
	$ALLOW_DELIVERY = (($ALLOW_DELIVERY == "Y") ? "Y" : "N");

	$BASE_LANG_CURRENCY = CSaleLang::GetLangCurrency($LID);

	// Basket params
	$arBasketList = array();

	$arOrderPrice = array();
	$basketTotalPrice = 0;

	$arOrderWeight = array();
	$basketTotalWeight = 0;

	$arInd = array();
	$ids = Array();
	$allIDs = Array();
	
	$arIDs = explode(",", trim($_POST["BASKET_IDS"]));
	
	foreach($arIDs as $v)
	{
		if($_POST["product_delete_".$v] != "Y")
			$ids[] = $v;
		$allIDs[] = $v;
	}
	
	
	$BASKET_COUNTER = IntVal($_POST["BASKET_COUNTER"]);

	foreach($ids as $i)
	{
	
		${"PRODUCT_ID_".$i} = IntVal(${"PRODUCT_ID_".$i});

		if (${"PRODUCT_ID_".$i} > 0)
		{		
			${"MODULE_".$i} = Trim(${"MODULE_".$i});
			if (strlen(${"MODULE_".$i}) <= 0)
				$errorMessage .= str_replace("#ID#", ${"PRODUCT_ID_".$i}, GetMessage("TCS_SOE_EMPTY_NODULE")).". ";

			${"NAME_".$i} = Trim(${"NAME_".$i});
			if (strlen(${"NAME_".$i}) <= 0)
				$errorMessage .= str_replace("#ID#", ${"PRODUCT_ID_".$i}, GetMessage("TCS_SOE_EMPTY_NAME")).". ";

			${"CURRENCY_".$i} = Trim(${"CURRENCY_".$i});
			if (strlen(${"CURRENCY_".$i}) <= 0)
				$errorMessage .= str_replace("#ID#", ${"PRODUCT_ID_".$i}, GetMessage("TCS_SOE_EMPTY_ITEM_CUR")).". ";
			
			${"QUANTITY_".$i} = Trim(${"QUANTITY_".$i});
			if (strlen(${"QUANTITY_".$i}) <= 0)
				$errorMessage .= str_replace("#ID#", ${"PRODUCT_ID_".$i}, GetMessage("TCS_SOE_EMPTY_ITEM_QUANTITY")).". ";
			$ind = 0;
			${"MOVE2NEW_ORDER_".$i} = ((${"MOVE2NEW_ORDER_".$i} == "Y") ? "Y" : "N");
			
			if ($BASKET_COUNTER == 1)
				${"MOVE2NEW_ORDER_".$i} = "N";

			if (${"MOVE2NEW_ORDER_".$i} == "Y")
				$ind = 1;
			if (!array_key_exists($ind, $arInd))
				$arInd[$ind] = -1;

			$arInd[$ind]++;

			if (!array_key_exists($ind, $arBasketList))
				$arBasketList[$ind] = array();

			$arBasketList[$ind][$arInd[$ind]] = array(
				"ID" => IntVal(${"ID_".$i}),
				"IND" => IntVal($i),
				"PRODUCT_ID" => ${"PRODUCT_ID_".$i},
				"PRODUCT_PRICE_ID" => ${"PRODUCT_PRICE_ID_".$i},
				"MODULE" => ${"MODULE_".$i},
				"NAME" => ${"NAME_".$i},
				"DETAIL_PAGE_URL" => Trim(${"DETAIL_PAGE_URL_".$i}),
				"PRICE" => roundEx(CCurrencyRates::ConvertCurrency(DoubleVal(str_replace(",", ".", ${"PRICE_".$i})), ${"CURRENCY_".$i}, $BASE_LANG_CURRENCY), SALE_VALUE_PRECISION),
				"CURRENCY" => $BASE_LANG_CURRENCY,
				"DISCOUNT_PRICE" => roundEx(CCurrencyRates::ConvertCurrency(DoubleVal(str_replace(",", ".", ${"DISCOUNT_PRICE_".$i})), ${"CURRENCY_".$i}, $BASE_LANG_CURRENCY), SALE_VALUE_PRECISION),
				"WEIGHT" => DoubleVal(${"WEIGHT_".$i}),
				"QUANTITY" => DoubleVal(${"QUANTITY_".$i}),
				"NOTES" => Trim(${"NOTES_".$i}),
				"CALLBACK_FUNC" => Trim(${"CALLBACK_FUNC_".$i}),
				"ORDER_CALLBACK_FUNC" => Trim(${"ORDER_CALLBACK_FUNC_".$i}),
				"CANCEL_CALLBACK_FUNC" => Trim(${"CANCEL_CALLBACK_FUNC_".$i}),
				"PAY_CALLBACK_FUNC" => Trim(${"PAY_CALLBACK_FUNC_".$i}),
				"CATALOG_XML_ID" => Trim(${"CATALOG_XML_ID_".$i}),
				"PRODUCT_XML_ID" => Trim(${"PRODUCT_XML_ID_".$i}),
				"VAT_RATE" => DoubleVal(${"VAT_RATE_".$i})
			);

			if (!array_key_exists($ind, $arOrderPrice))
				$arOrderPrice[$ind] = 0;
			$arOrderPrice[$ind] += $arBasketList[$ind][$arInd[$ind]]["PRICE"] * $arBasketList[$ind][$arInd[$ind]]["QUANTITY"];
			if (!array_key_exists($ind, $arOrderWeight))
				$arOrderWeight[$ind] = 0;
			$arOrderWeight[$ind] += $arBasketList[$ind][$arInd[$ind]]["WEIGHT"] * $arBasketList[$ind][$arInd[$ind]]["QUANTITY"];
			$basketTotalPrice += $arBasketList[$ind][$arInd[$ind]]["PRICE"] * $arBasketList[$ind][$arInd[$ind]]["QUANTITY"];
			$basketTotalWeight += $arBasketList[$ind][$arInd[$ind]]["WEIGHT"] * $arBasketList[$ind][$arInd[$ind]]["QUANTITY"];

			$arBasketProps = array();
			${"BASKET_PROP_COUNT_".$i} = IntVal(${"BASKET_PROP_COUNT_".$i});
			if (${"BASKET_PROP_COUNT_".$i} > 0)
			{
				$jnd = -1;
				for ($j = 1; $j <= ${"BASKET_PROP_COUNT_".$i}; $j++)
				{
					${"BASKET_PROP_".$i ."_NAME_".$j} = Trim(${"BASKET_PROP_".$i ."_NAME_".$j});
						$jnd++;
						$arBasketProps[$jnd] = array(
							//"ID" => IntVal(${"BASKET_PROP_ID_".$i."_".$j}),
							"NAME" => Trim(${"BASKET_PROP_".$i."_NAME_".$j}),
							"CODE" => Trim(${"BASKET_PROP_".$i."_CODE_".$j}),
							"VALUE" => ${"BASKET_PROP_".$i."_VALUE_".$j},
							"SORT" => IntVal(${"BASKET_PROP_".$i."_SORT_".$j})
						);
				}
			}
			$arBasketList[$ind][$arInd[$ind]]["PROPS"] = $arBasketProps;
		}			
	}

	if (count($arBasketList) <= 0)
		$errorMessage .= GetMessage("TCS_SOE_EMPTY_ITEMS").". ";

	// Order props


	$bNeedReCount = ($RE_COUNT == "Y");
	$bFullOrderDivision = ($FULL_DIVISION == "Y");

	// *****************************************************************
	// *****  Saving  **************************************************
	// *****************************************************************
	if (strlen($errorMessage) <= 0)
	{
		$bTrabsactionStarted = True;
		$DB->StartTransaction();
	}

	if (strlen($errorMessage) <= 0)
	{
		// TAX EXEMPT ---------------------------------------------->
		if ($bNeedReCount)
		{
			$arTaxExempt = array();
			$arUserGroups = CUser::GetUserGroup($USER_ID);

			$dbTaxExemptList = CSaleTax::GetExemptList(array("GROUP_ID" => $arUserGroups));
			while ($arTaxExemptList = $dbTaxExemptList->Fetch())
				if (!in_array(IntVal($arTaxExemptList["TAX_ID"]), $arTaxExempt))
					$arTaxExempt[] = IntVal($arTaxExemptList["TAX_ID"]);
		}

		// PAY SYSTEM ---------------------------------------------->
		$PAY_SYSTEM_ID = IntVal($PAY_SYSTEM_ID);
		if ($PAY_SYSTEM_ID <= 0)
			$errorMessage .= GetMessage("TCS_SOE_PAYSYS_EMPTY")."<br>";
		if (($PAY_SYSTEM_ID > 0) && !($arPaySys = CSalePaySystem::GetByID($PAY_SYSTEM_ID, $PERSON_TYPE_ID)))
			$errorMessage .= GetMessage("TCS_SOE_PAYSYS_NOT_FOUND")."<br>";

		// DISCOUNT ---------------------------------------------->
		for ($i = 0; $i < count($arBasketList); $i++)
			for ($j = 0; $j < count($arBasketList[$i]); $j++)
				$arBasketList[$i][$j]["REAL_PRICE"] = $arBasketList[$i][$j]["PRICE"];

		$arDiscountPrice = array();
		for ($i = 0; $i < count($arBasketList); $i++)
			$arDiscountPrice[$i] = 0;

		if ($bNeedReCount)
		{
			if ($bFullOrderDivision)
			{
				for ($i = 0; $i < count($arBasketList); $i++)
				{
					$dbDiscount = CSaleDiscount::GetList(
							array("SORT" => "ASC"),
							array(
									"LID" => $LID,
									"ACTIVE" => "Y",
									"!>ACTIVE_FROM" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
									"!<ACTIVE_TO" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
									"<=PRICE_FROM" => $arOrderPrice[$i],
									">=PRICE_TO" => $arOrderPrice[$i]
								),
							false,
							false,
							array("*")
						);

					if ($arDiscount = $dbDiscount->Fetch())
					{
						if ($arDiscount["DISCOUNT_TYPE"] == "P")
						{
							for ($j = 0; $j < count($arBasketList[$i]); $j++)
							{
								$curDiscount = roundEx(DoubleVal($arBasketList[$i][$j]["PRICE"]) * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
								$arBasketList[$i][$j]["REAL_PRICE"] = DoubleVal($arBasketList[$i][$j]["PRICE"]) - $curDiscount;
								$arDiscountPrice[$i] += $curDiscount * $arBasketList[$i][$j]["QUANTITY"];
							}
						}
						else
						{
							$discountPrice = CCurrencyRates::ConvertCurrency($arDiscount["DISCOUNT_VALUE"], $arDiscount["CURRENCY"], $BASE_LANG_CURRENCY);
							$discountPrice = roundEx($discountPrice, SALE_VALUE_PRECISION);
							for ($j = 0; $j < count($arBasketList[$i]); $j++)
							{
								$curDiscount = roundEx(DoubleVal($arBasketList[$i][$j]["PRICE"]) * $DISCOUNT_PRICE / $arOrderPrice[$i], SALE_VALUE_PRECISION);
								$arBasketList[$i][$j]["REAL_PRICE"] = DoubleVal($arBasketList[$i][$j]["PRICE"]) - $curDiscount;
								$arDiscountPrice[$i] += $curDiscount * $arBasketList[$i][$j]["QUANTITY"];
							}
						}
					}
				}
			}
			else
			{
				$dbDiscount = CSaleDiscount::GetList(
						array("SORT" => "ASC"),
						array(
								"LID" => $LID,
								"ACTIVE" => "Y",
								"!>ACTIVE_FROM" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
								"!<ACTIVE_TO" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
								"<=PRICE_FROM" => $basketTotalPrice,
								">=PRICE_TO" => $basketTotalPrice
							),
						false,
						false,
						array("*")
					);

				if ($arDiscount = $dbDiscount->Fetch())
				{
					if ($arDiscount["DISCOUNT_TYPE"] == "P")
					{
						for ($i = 0; $i < count($arBasketList); $i++)
						{
							for ($j = 0; $j < count($arBasketList[$i]); $j++)
							{
								$curDiscount = roundEx(DoubleVal($arBasketList[$i][$j]["PRICE"]) * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
								$arBasketList[$i][$j]["REAL_PRICE"] = DoubleVal($arBasketList[$i][$j]["PRICE"]) - $curDiscount;
								$arDiscountPrice[$i] += $curDiscount * $arBasketList[$i][$j]["QUANTITY"];
							}
						}
					}
					else
					{
						$discountPrice = CCurrencyRates::ConvertCurrency($arDiscount["DISCOUNT_VALUE"], $arDiscount["CURRENCY"], $BASE_LANG_CURRENCY);
						$discountPrice = roundEx($discountPrice, SALE_VALUE_PRECISION);
						for ($i = 0; $i < count($arBasketList); $i++)
						{
							for ($j = 0; $j < count($arBasketList[$i]); $j++)
							{
								$curDiscount = roundEx(DoubleVal($arBasketList[$i][$j]["PRICE"]) * $DISCOUNT_PRICE / $basketTotalPrice, SALE_VALUE_PRECISION);
								$arBasketList[$i][$j]["REAL_PRICE"] = DoubleVal($arBasketList[$i][$j]["PRICE"]) - $curDiscount;
								$arDiscountPrice[$i] += $curDiscount * $arBasketList[$i][$j]["QUANTITY"];
							}
						}
					}
				}
			}
		}
		else
		{
			for ($i = 0; $i < count($arBasketList); $i++)
			{
				for ($j = 0; $j < count($arBasketList[$i]); $j++)
				{
					$arBasketList[$i][$j]["REAL_PRICE"] = $arBasketList[$i][$j]["PRICE"] - $arBasketList[$i][$j]["DISCOUNT_PRICE"];
					$arDiscountPrice[$i] += $arBasketList[$i][$j]["DISCOUNT_PRICE"] * $arBasketList[$i][$j]["QUANTITY"];
				}
			}
		}
		
		$bUseVat = false;
		for ($i = 0; $i < count($arBasketList); $i++)
		{
			for ($j = 0; $j < count($arBasketList[$i]); $j++)
			{
				if(DoubleVal($arBasketList[$i][$j]["VAT_RATE"]) > 0)
				{
					$bUseVat = true;
					if($arBasketList[$i][$j]["VAT_RATE"] > $vatRate)
						$vatRate = $arBasketList[$i][$j]["VAT_RATE"];
				}
			}
		}

		// TAX
		$arTaxPrice = array();
		for ($i = 0; $i < count($arBasketList); $i++)
			$arTaxPrice[$i] = 0;

		$arTaxList = array();
		$arOrderTaxList = array();
		for ($i = 0; $i < count($arBasketList); $i++)
			$arOrderTaxList[$i] = array();

		if ($bNeedReCount)
		{
			if(!$bUseVat)
			{
				$dbTaxRate = CSaleTaxRate::GetList(
					array("APPLY_ORDER" => "ASC"),
					array(
						"LID" => $LID,
						"PERSON_TYPE_ID" => $PERSON_TYPE_ID,
						"ACTIVE" => "Y",
						"LOCATION" => $TAX_LOCATION
					)
				);
				$i = -1;
				while ($arTaxRate = $dbTaxRate->Fetch())
				{
					if (!in_array(IntVal($arTaxRate["TAX_ID"]), $arTaxExempt))
					{
						$i++;
						$arTaxList[] = array(
							"ID" => 0,
							"IND" => $i,
							"TAX_NAME" => $arTaxRate["NAME"],
							"VALUE" => $arTaxRate["VALUE"],
							"VALUE_MONEY" => 0,
							"APPLY_ORDER" => $arTaxRate["APPLY_ORDER"],
							"CODE" => $arTaxRate["CODE"],
							"IS_IN_PRICE" => (($arTaxRate["IS_IN_PRICE"] == "Y") ? "Y" : "N")
						);
					}
				}
			}
			else
			{
				$arTaxList[] = Array(
							"ID" => 0,
							"TAX_NAME" => GetMessage("TCS_SOE_VAT"),
							"IS_PERCENT" => "Y",
							"VALUE" => $vatRate*100,
							"VALUE_MONEY" => 0,
							"APPLY_ORDER" => 100,
							"IS_IN_PRICE" => "Y",
							"CODE" => "VAT"
				);

			}
		
		}
		else
		{
			$TAX_COUNTER = IntVal($TAX_COUNTER);
			for ($i = 0; $i <= $TAX_COUNTER; $i++)
			{
				${"TAX_NAME_".$i} = Trim(${"TAX_NAME_".$i});

				if (strlen(${"TAX_NAME_".$i}) > 0)
				{
					${"TAX_VALUE_".$i} = DoubleVal(str_replace(",", ".", ${"TAX_VALUE_".$i}));
					if (${"TAX_VALUE_".$i} <= 0)
						$errorMessage .= str_replace("#NAME#", ${"TAX_NAME_".$i}, GetMessage("TCS_SOE_EMPTY_TAX_NAME")).". ";

					${"TAX_VALUE_".$i} = DoubleVal(str_replace(",", ".", ${"TAX_VALUE_".$i}));
					if (${"TAX_VALUE_".$i} <= 0)
						$errorMessage .= str_replace("#NAME#", ${"TAX_NAME_".$i}, GetMessage("TCS_SOE_EMPTY_TAX_SUM")).". ";

					$arTaxList[] = array(
						"ID" => IntVal(${"TAX_ID_".$i}),
						"IND" => IntVal($i),
						"TAX_NAME" => Trim(${"TAX_NAME_".$i}),
						"VALUE" => ${"TAX_VALUE_".$i},
						"VALUE_MONEY" => 0,
						"APPLY_ORDER" => IntVal(${"TAX_APPLY_ORDER_".$i}),
						"CODE" => Trim(${"TAX_CODE_".$i}),
						"IS_IN_PRICE" => ((${"TAX_IS_IN_PRICE_".$i} == "Y") ? "Y" : "N")
					);
				}
			}
		}

		if (count($arTaxList) > 0)
		{
			for ($i = 0; $i < count($arBasketList); $i++)
			{
				for ($j = 0; $j < count($arBasketList[$i]); $j++)
				{
					if(!$bUseVat)
					{
						$taxPrice = CSaleOrderTax::CountTaxes(
								$arBasketList[$i][$j]["REAL_PRICE"] * $arBasketList[$i][$j]["QUANTITY"],
								$arTaxList,
								$BASE_LANG_CURRENCY
							);

						for ($k = 0; $k < count($arTaxList); $k++)
							$arOrderTaxList[$i][$k]["VALUE_MONEY"] += $arTaxList[$k]["TAX_VAL"];
					}
					else
					{
						$vatRate = roundEx(($arBasketList[$i][$j]["REAL_PRICE"] / ($arBasketList[$i][$j]["VAT_RATE"] +1)) * $arBasketList[$i][$j]["VAT_RATE"], SALE_VALUE_PRECISION);
						$arOrderTaxList[$i][0]["VALUE_MONEY"] +=  roundEx($vatRate * $arBasketList[$i][$j]["QUANTITY"], SALE_VALUE_PRECISION);
					}
				}
			}

			for ($i = 0; $i < count($arBasketList); $i++)
			{
				for ($j = 0; $j < count($arTaxList); $j++)
				{
					if ($arTaxList[$j]["IS_IN_PRICE"] != "Y")
						$arTaxPrice[$i] += $arOrderTaxList[$i][$j]["VALUE_MONEY"];
				}
			}
		}
			
			
		// DELIVERY ---------------------------------------------->
		
		if ($DELIVERY_type == 'handler')
		{
			$DELIVERY_ID = $DELIVERY_ID_handler;
			$PRICE_DELIVERY = $PRICE_DELIVERY_handler;
		}
		
		if (strstr($DELIVERY_ID, ':') === false)
		{
			$DELIVERY_ID = IntVal($DELIVERY_ID);
			$bUseOldDelivery = true;
		}
		else
		{
			$bUseOldDelivery = false;
		}

		$arDeliveryPrice = array();
		for ($i = 0; $i < count($arBasketList); $i++)
			$arDeliveryPrice[$i] = 0;

		$deliveryPrice = 0;
		if ($bNeedReCount)
		{
			if ($bUseOldDelivery)
			{
				if ($DELIVERY_ID > 0)
				{
					if ($arDelivery = CSaleDelivery::GetByID($DELIVERY_ID))
						$deliveryPrice = roundEx(CCurrencyRates::ConvertCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"], $BASE_LANG_CURRENCY), SALE_VALUE_PRECISION);
					else
						$errorMessage .= GetMessage("TCS_SOE_DELIVERY_NOT_FOUND")."<br>";
				}
			}
			else
			{
				list($DELIVERY_SERVICE, $DELIVERY_PROFILE) = explode(':', $DELIVERY_ID);
				
				if (strlen($DELIVERY_SERVICE) > 0 && strlen($DELIVERY_PROFILE) > 0)
				{
					$arDeliveryOrder = array(
						"LOCATION_TO" => $DELIVERY_LOCATION,
						"LOCATION_FROM" => COption::GetOptionInt('sale', 'location', '', $LID),
						"WEIGHT" => $basketTotalWeight,
						"PRICE" => $basketTotalPrice + array_sum($arTaxPrice),
					);
					$arDeliveryResult = CSaleDeliveryHandler::CalculateFull($DELIVERY_SERVICE, $DELIVERY_PROFILE, $arDeliveryOrder, $BASE_LANG_CURRENCY, $LID);
					
					//echo '<pre>'; print_r($arDeliveryResult ); echo '</pre>';
//					die();

					if ($arDeliveryResult["RESULT"] != 'OK')
					{
						if ($ex = $APPLICATION->GetException())
							$errorMessage .= $ex->GetString().'<br>';
						else
							$errorMessage .= GetMessage("TCS_SOE_DELIVERY_ERROR").'<br>';
					}
					else
					{
						$deliveryPrice = roundEx($arDeliveryResult["VALUE"], SALE_VALUE_PRECISION);
					}
				}
			}
		}
		else
		{
			$PRICE_DELIVERY = DoubleVal(str_replace(",", ".", $PRICE_DELIVERY));

			if ($PRICE_DELIVERY > 0 && strlen($PRICE_DELIVERY_CURRENCY) > 0)
				$deliveryPrice = roundEx(CCurrencyRates::ConvertCurrency($PRICE_DELIVERY, $PRICE_DELIVERY_CURRENCY, $BASE_LANG_CURRENCY), SALE_VALUE_PRECISION);
			else
				$deliveryPrice = roundEx($PRICE_DELIVERY, SALE_VALUE_PRECISION);
		}

		if ($bFullOrderDivision)
		{
			if ($deliveryPrice > 0)
			{
				for ($i = 0; $i < count($arDeliveryPrice); $i++)
					$arDeliveryPrice[$i] = $deliveryPrice;
			}
		}
		else
		{
			// !!!!!!!!!!!!!!!!!!!!!! this block is ABSOLUTELY WRONG!
			if ($deliveryPrice > 0)
			{	
				/*
				if ($basketTotalWeight > 0)
				{
					for ($i = 0; $i < count($arDeliveryPrice); $i++)
						$arDeliveryPrice[$i] = roundEx($deliveryPrice * $arOrderWeight[$i] / $basketTotalWeight, SALE_VALUE_PRECISION);
				}
				else
				{
					for ($i = 0; $i < count($arDeliveryPrice); $i++)
						$arDeliveryPrice[$i] = roundEx($deliveryPrice / count($arDeliveryPrice), SALE_VALUE_PRECISION);
				}
				*/
				for ($i = 0; $i < count($arDeliveryPrice); $i++)
					$arDeliveryPrice[$i] = $deliveryPrice;

			}
			$checkDeliverySum = 0;
			for ($i = 0; $i < count($arDeliveryPrice); $i++)
				$checkDeliverySum += $arDeliveryPrice[$i];
			if ($deliveryPrice > $checkDeliverySum)
				$arDeliveryPrice[0] += $deliveryPrice - $checkDeliverySum;
		}
		
		if (count($arTaxList) > 0)
		{
			//print_r($arDeliveryPrice);
			if(!empty($deliveryPrice) && $COUNT_TAX_FOR_DELIVERY == "Y")
			{
				foreach($arDeliveryPrice as $i => $delPrice)
				{
					$taxPrice = CSaleOrderTax::CountTaxes(
							$delPrice,
							$arTaxList,
							$BASE_LANG_CURRENCY
						);

					for ($j = 0; $j < count($arTaxList); $j++)
					{
						$arOrderTaxList[$i][$j]["VALUE_MONEY"] += $arTaxList[$j]["TAX_VAL"];
						$arTaxPrice[$i] = $arOrderTaxList[$i][0]["VALUE_MONEY"];

						$arTaxList[$j]["VALUE_MONEY"] += $arTaxList[$j]["TAX_VAL"];
					}
				}
			}

		}
		
	}
	
/***** /DELIVERY *****/
	
	
	if (strlen($errorMessage) <= 0)
	{
		$arIDs = array();
		$arIDs[0] = $ID;

		$sumPaid = DoubleVal(str_replace(",", ".", $SUM_PAID));

		for ($i = 0; $i < count($arBasketList); $i++)
		{
			$totalOrderPrice = $arOrderPrice[$i] + $arDeliveryPrice[$i] + $arTaxPrice[$i] - $arDiscountPrice[$i];
			if ($sumPaid > $totalOrderPrice)
			{
				$sumPaid = $sumPaid - $totalOrderPrice;
				$sumPaid1 = $totalOrderPrice;
			}
			else
			{
				$sumPaid1 = $sumPaid;
				$sumPaid = 0;
			}

			$arFields = array(
				"LID" => $LID,
				"PERSON_TYPE_ID" => $PERSON_TYPE_ID,
				"PRICE" => $totalOrderPrice,
				"CURRENCY" => $BASE_LANG_CURRENCY,
				"USER_ID" => $USER_ID,
				"PAY_SYSTEM_ID" => $PAY_SYSTEM_ID,
				"PRICE_DELIVERY" => $arDeliveryPrice[$i],
				"DELIVERY_ID" => (strstr($DELIVERY_ID, ':') === false ? ($DELIVERY_ID > 0 ? $DELIVERY_ID : false) : $DELIVERY_ID),
				"DISCOUNT_VALUE" => $arDiscountPrice[$i],
				"TAX_VALUE" => $arTaxPrice[$i],
				"USER_DESCRIPTION" => $USER_DESCRIPTION,
				"SUM_PAID" => $sumPaid1,
				"ADDITIONAL_INFO" => $ADDITIONAL_INFO,
				"COMMENTS" => $COMMENTS,
				"RECOUNT_FLAG" => (($RE_COUNT == "Y") ? "Y" : "N"),
				"PAY_VOUCHER_NUM" => $PAY_VOUCHER_NUM,
				"PAY_VOUCHER_DATE" => $PAY_VOUCHER_DATE,
				"DELIVERY_DOC_NUM" => $DELIVERY_DOC_NUM,
				"DELIVERY_DOC_DATE" => $DELIVERY_DOC_DATE,
			);

			if ($i == 0)
			{
		//	echo "<pre>";
		//	print_r($arFields);
		//	die();
				$res = CSaleOrder::Update($arIDs[0], $arFields);
			}
			else
			{
				$arFields["PAYED"] = $arOrder["PAYED"];
				$arFields["DATE_PAYED"] = $arOrder["DATE_PAYED"];
				$arFields["PAY_VOUCHER_NUM"] = $arOrder["PAY_VOUCHER_NUM"];
				$arFields["PAY_VOUCHER_DATE"] = $arOrder["PAY_VOUCHER_DATE"];
				$arFields["DELIVERY_DOC_NUM"] = $arOrder["DELIVERY_DOC_NUM"];
				$arFields["DELIVERY_DOC_DATE"] = $arOrder["DELIVERY_DOC_DATE"];
				$arFields["EMP_PAYED_ID"] = $arOrder["EMP_PAYED_ID"];

				$arFields["CANCELED"] = $arOrder["CANCELED"];
				$arFields["REASON_CANCELED"] = $arOrder["REASON_CANCELED"];
				$arFields["DATE_CANCELED"] = $arOrder["DATE_CANCELED"];
				$arFields["EMP_CANCELED_ID"] = $arOrder["EMP_CANCELED_ID"];

				$arFields["STATUS_ID"] = $arOrder["STATUS_ID"];
				$arFields["DATE_STATUS"] = $arOrder["DATE_STATUS"];
				$arFields["EMP_STATUS_ID"] = $arOrder["EMP_STATUS_ID"];

				$arFields["ALLOW_DELIVERY"] = $arOrder["ALLOW_DELIVERY"];
				$arFields["DATE_ALLOW_DELIVERY"] = $arOrder["DATE_ALLOW_DELIVERY"];
				$arFields["EMP_ALLOW_DELIVERY_ID"] = $arOrder["EMP_ALLOW_DELIVERY_ID"];

				$arIDs[$i] = CSaleOrder::Add($arFields);
				$arIDs[$i] = IntVal($arIDs[$i]);
				$res = ($arIDs[$i] > 0);
			}

			if (!$res)
			{
				if ($ex = $APPLICATION->GetException())
					$errorMessage .= $ex->GetString();
				else
					$errorMessage .= GetMessage("TCS_SOE_ERROR_UPDATE").". ";
			}
		}
	}

	if (StrLen($errorMessage) <= 0)
	{
		for ($i = 0; $i < count($arIDs); $i++)
		{
			if (IntVal($arIDs[$i]) > 0 && strlen($STATUS_ID) > 0 && $arOrder["STATUS_ID"] != $STATUS_ID)
			{
				if (!CSaleOrder::StatusOrder($arIDs[$i], $STATUS_ID))
				{
					if ($ex = $APPLICATION->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= GetMessage("TCS_SOE_ERROR_STATUS_EDIT").". ";
				}
			}
		}
	}

	if (StrLen($errorMessage) <= 0)
	{
		for ($i = 0; $i < count($arIDs); $i++)
		{
			if (IntVal($arIDs[$i]) > 0 && $bUserCanCancelOrder && $arOrder["CANCELED"] != $CANCELED)
			{
				if (!CSaleOrder::CancelOrder($arIDs[$i], $CANCELED, $REASON_CANCELED))
				{
					if ($ex = $APPLICATION->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= GetMessage("TCS_SOE_ERROR_CANCEL_EDIT").". ";
				}
			}
		}
	}

	if (StrLen($errorMessage) <= 0)
	{
		for ($i = 0; $i < count($arIDs); $i++)
		{
			if (IntVal($arIDs[$i]) > 0 && $bUserCanDeliverOrder && $arOrder["ALLOW_DELIVERY"] != $ALLOW_DELIVERY)
			{
				$arAdditionalFields = array(
						"DELIVERY_DOC_NUM" => ((strlen($DELIVERY_DOC_NUM) > 0) ? $DELIVERY_DOC_NUM : False),
						"DELIVERY_DOC_DATE" => ((strlen($DELIVERY_DOC_DATE) > 0) ? $DELIVERY_DOC_DATE : False)
					);

				if (!CSaleOrder::DeliverOrder($arIDs[$i], $ALLOW_DELIVERY, 0, $arAdditionalFields))
				{
					if ($ex = $APPLICATION->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= GetMessage("TCS_SOE_ERROR_DELIVERY_EDIT").". ";
				}
			}
		}
	}

	if (StrLen($errorMessage) <= 0)
	{
		for ($i = 0; $i < count($arIDs); $i++)
		{
			if (IntVal($arIDs[$i]) > 0 && $bUserCanPayOrder && $arOrder["PAYED"] != $PAYED)
			{
				$arAdditionalFields = array(
						"PAY_VOUCHER_NUM" => ((strlen($PAY_VOUCHER_NUM) > 0) ? $PAY_VOUCHER_NUM : False),
						"PAY_VOUCHER_DATE" => ((strlen($PAY_VOUCHER_DATE) > 0) ? $PAY_VOUCHER_DATE : False)
					);
				if (!CSaleOrder::PayOrder($arIDs[$i], $PAYED, false, false, 0, $arAdditionalFields))
				{
					if ($ex = $APPLICATION->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= GetMessage("TCS_SOE_ERROR_PAY_EDIT").". ";
				}
			}
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		$arOldBasketList = array();

		$dbBasket = CSaleBasket::GetList(
				array("NAME" => "ASC"),
				array("ORDER_ID" => $ID),
				false,
				false,
				array("ID", "NAME", "CANCEL_CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY")
			);
		while ($arBasket = $dbBasket->Fetch())
			$arOldBasketList[IntVal($arBasket["ID"])] = $arBasket;

		for ($i = 0; $i < count($ids); $i++)
		{
			for ($j = 0; $j < count($arBasketList[$i]); $j++)
			{
				$arFields = array(
						"ORDER_ID" => $arIDs[$i],
						"PRODUCT_ID" => $arBasketList[$i][$j]["PRODUCT_ID"],
						"PRODUCT_PRICE_ID" => $arBasketList[$i][$j]["PRODUCT_PRICE_ID"],
						"PRICE" => $arBasketList[$i][$j]["PRICE"],
						"CURRENCY" => $arBasketList[$i][$j]["CURRENCY"],
						"WEIGHT" => $arBasketList[$i][$j]["WEIGHT"],
						"QUANTITY" => $arBasketList[$i][$j]["QUANTITY"],
						"LID" => $LID,
						"NAME" => $arBasketList[$i][$j]["NAME"],
						"MODULE" => $arBasketList[$i][$j]["MODULE"],
						"NOTES" => $arBasketList[$i][$j]["NOTES"],
						"DETAIL_PAGE_URL" => $arBasketList[$i][$j]["DETAIL_PAGE_URL"],
						"DISCOUNT_PRICE" => ($arBasketList[$i][$j]["PRICE"] - $arBasketList[$i][$j]["REAL_PRICE"]),
						"PROPS" => $arBasketList[$i][$j]["PROPS"],
						"CALLBACK_FUNC" => $arBasketList[$i][$j]["CALLBACK_FUNC"],
						"ORDER_CALLBACK_FUNC" => $arBasketList[$i][$j]["ORDER_CALLBACK_FUNC"],
						"CANCEL_CALLBACK_FUNC" => $arBasketList[$i][$j]["CANCEL_CALLBACK_FUNC"],
						"PAY_CALLBACK_FUNC" => $arBasketList[$i][$j]["PAY_CALLBACK_FUNC"],
						"CATALOG_XML_ID" => $arBasketList[$i][$j]["CATALOG_XML_ID"],
						"PRODUCT_XML_ID" => $arBasketList[$i][$j]["PRODUCT_XML_ID"],
						"VAT_RATE" => $arBasketList[$i][$j]["VAT_RATE"],
						"IGNORE_CALLBACK_FUNC" => "Y"
					);
				$res = False;
				if ($arBasketList[$i][$j]["ID"] > 0)
				{
					if (array_key_exists($arBasketList[$i][$j]["ID"], $arOldBasketList))
					{
						$res = CSaleBasket::Update($arBasketList[$i][$j]["ID"], $arFields);
						
						if($arOldBasketList[$arBasketList[$i][$j]["ID"]]["QUANTITY"] > $arFields["QUANTITY"])
						{
							if (strlen($arFields["CANCEL_CALLBACK_FUNC"]) > 0)
							{
								$arFields = CSaleBasket::ExecuteCallbackFunction(
										$arFields["CANCEL_CALLBACK_FUNC"],
										$arFields["MODULE"],
										$arFields["PRODUCT_ID"],
										($arOldBasketList[$arBasketList[$i][$j]["ID"]]["QUANTITY"] - $arFields["QUANTITY"]),
										true
									);
							}
						}
						elseif($arOldBasketList[$arBasketList[$i][$j]["ID"]]["QUANTITY"] < $arFields["QUANTITY"])
						{
							if (strlen($arFields["ORDER_CALLBACK_FUNC"]) > 0)
							{
								$arFields = CSaleBasket::ExecuteCallbackFunction(
										$arFields["ORDER_CALLBACK_FUNC"],
										$arFields["MODULE"],
										$arFields["PRODUCT_ID"],
										($arFields["QUANTITY"] - $arOldBasketList[$arBasketList[$i][$j]["ID"]]["QUANTITY"])
										
									);
							}
						}
						unset($arOldBasketList[$arBasketList[$i][$j]["ID"]]);
					}
					else
					{
						$errorMessage .= GetMessage("TCS_SOE_INTERNAL_RFITH67").". ";
					}
				}
				else
				{
					$res = (CSaleBasket::Add($arFields) > 0);
					if (strlen($arFields["ORDER_CALLBACK_FUNC"]) > 0)
					{
						$arFields = CSaleBasket::ExecuteCallbackFunction(
								$arFields["ORDER_CALLBACK_FUNC"],
								$arFields["MODULE"],
								$arFields["PRODUCT_ID"],
								$arFields["QUANTITY"]
								
							);
					}
				}

				if (!$res)
				{
					if ($ex = $APPLICATION->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= str_replace("#ID#", $arBasketList[$i][$j]["PRODUCT_ID"], GetMessage("TCS_SOE_ERROR_SAVE_ITEM")).". ";
				}
			}
		}

		foreach ($arOldBasketList as $key => $value)
		{
			if (strlen($value["CANCEL_CALLBACK_FUNC"]) > 0)
			{
				$arFields = CSaleBasket::ExecuteCallbackFunction(
						$value["CANCEL_CALLBACK_FUNC"],
						$value["MODULE"],
						$value["PRODUCT_ID"],
						$value["QUANTITY"],
						true
					);
			}
			
			CSaleBasket::Delete($key);
		}
		
	}

	if (strlen($errorMessage) <= 0)
	{
		$arOldTaxList = array();
		$dbTax = CSaleOrderTax::GetList(
				array("APPLY_ORDER" => "ASC"),
				array("ORDER_ID" => $ID),
				false,
				false,
				array("*")
			);
		while ($arTax = $dbTax->Fetch())
			$arOldTaxList[IntVal($arTax["ID"])] = "Y";

		for ($i = 0; $i < count($arIDs); $i++)
		{
			for ($j = 0; $j < count($arTaxList); $j++)
			{
				$arFields = array(
						"ORDER_ID" => $arIDs[$i],
						"TAX_NAME" => $arTaxList[$j]["TAX_NAME"],
						"VALUE" => $arTaxList[$j]["VALUE"],
						"VALUE_MONEY" => $arOrderTaxList[$i][$j]["VALUE_MONEY"],
						"APPLY_ORDER" => $arTaxList[$j]["APPLY_ORDER"],
						"IS_PERCENT" => "Y",
						"IS_IN_PRICE" => $arTaxList[$j]["IS_IN_PRICE"],
						"CODE" => $arTaxList[$j]["CODE"]
					);

				$res = False;
				if ($arTaxList[$j]["ID"] > 0)
				{
					if (array_key_exists($arTaxList[$j]["ID"], $arOldTaxList))
					{
						$res = CSaleOrderTax::Update($arTaxList[$j]["ID"], $arFields);
						unset($arOldTaxList[$arTaxList[$j]["ID"]]);
					}
					else
					{
						$errorMessage .= GetMessage("TCS_SOE_INTERNAL_RFITH68").". ";
					}
				}
				else
					$res = (CSaleOrderTax::Add($arFields) > 0);

				if (!$res)
				{
					if ($ex = $APPLICATION->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= str_replace("#NAME#", $arTaxList[$j]["TAX_NAME"], GetMessage("TCS_SOE_ERROR_SAVE_TAX")).". ";
				}
			}
		}

		foreach ($arOldTaxList as $key => $value)
			CSaleOrderTax::Delete($key);
	}

	if (strlen($errorMessage) <= 0)
	{
		for ($i = 0; $i < count($arIDs); $i++)
		{
			CSaleOrderPropsValue::DeleteByOrder($arIDs[$i]);

			for ($j = 0; $j < count($arPropsList); $j++)
			{
				$arFields = array(
						"ORDER_ID" => $arIDs[$i],
						"ORDER_PROPS_ID" => $arPropsList[$j]["ORDER_PROPS_ID"],
						"NAME" => $arPropsList[$j]["NAME"],
						"CODE" => $arPropsList[$j]["CODE"],
						"VALUE" => $arPropsList[$j]["VALUE"]
					);

				$res = (CSaleOrderPropsValue::Add($arFields) > 0);

				if (!$res)
				{
					if ($ex = $APPLICATION->GetException())
						$errorMessage .= $ex->GetString();
					else
						$errorMessage .= str_replace("#NAME#", $arPropsList[$j]["NAME"], GetMessage("TCS_SOE_ERROR_SAVE_PROP")).". ";
				}
			}
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		if (!$customTabber->Action())
		{
			if ($ex = $APPLICATION->GetException())
				$errorMessage .= $ex->GetString();
			else
				$errorMessage .= "Error. ";
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		$DB->Commit();
		CSaleOrder::UnLock($ID);

	}
	else
	{
		if ($bTrabsactionStarted)
			$DB->Rollback();

		$bVarsFromForm = True;
	}

	// *****************************************************************
	// *****  End  *****************************************************
	// *****************************************************************
}
elseif(
	$action == "update"
	&& $saleModulePermissions >= "U"
	&& $_SERVER["REQUEST_METHOD"] == "POST"
	&& !check_bitrix_sessid()
	&& $bUserCanEditOrder
	&& $PARTIAL_SUBMIT != "Y"
	&& empty($dontsave)
	)
{
	$bVarsFromForm = true;
	$errorMessage = GetMessage("TCS_SOE_SESSID_FAIL");
	
	$arInd = array();
	$ids = Array();
	$allIDs = Array();
	
	$arIDs = explode(",", trim($_POST["BASKET_IDS"]));
	
	foreach($arIDs as $v)
	{
		$ids[] = $v;
		$allIDs[] = $v;
	}

}

if (!empty($dontsave))
{
	CSaleOrder::UnLock($ID);
	LocalRedirect("/bitrix/admin/sale_order.php?lang=".LANG.GetFilterParams("filter_", false));
}


$dbOrder = CSaleOrder::GetList(
	array("ID" => "DESC"),
	array("ID" => $ID),
	false,
	false,
	array("ID", "LID", "PERSON_TYPE_ID", "PAYED", "DATE_PAYED", "EMP_PAYED_ID", "CANCELED", "DATE_CANCELED", "EMP_CANCELED_ID", "REASON_CANCELED", "STATUS_ID", "DATE_STATUS", "PAY_VOUCHER_NUM", "PAY_VOUCHER_DATE", "EMP_STATUS_ID", "PRICE_DELIVERY", "ALLOW_DELIVERY", "DATE_ALLOW_DELIVERY", "EMP_ALLOW_DELIVERY_ID", "PRICE", "CURRENCY", "DISCOUNT_VALUE", "SUM_PAID", "USER_ID", "PAY_SYSTEM_ID", "DELIVERY_ID", "DATE_INSERT", "DATE_INSERT_FORMAT", "DATE_UPDATE", "USER_DESCRIPTION", "ADDITIONAL_INFO", "PS_STATUS", "PS_STATUS_CODE", "PS_STATUS_DESCRIPTION", "PS_STATUS_MESSAGE", "PS_SUM", "PS_CURRENCY", "PS_RESPONSE_DATE", "COMMENTS", "TAX_VALUE", "STAT_GID", "RECURRING_ID", "RECOUNT_FLAG", "LOCK_STATUS", "USER_LOGIN", "USER_NAME", "USER_LAST_NAME", "USER_EMAIL", "DELIVERY_DOC_NUM", "DELIVERY_DOC_DATE")
);

//echo '<pre>'; print_r($dbOrder->Fetch()); echo '</pre>';
//die();

if (!($arOrderOldTmp = $dbOrder->ExtractFields("str_")))
	LocalRedirect("sale_order.php?lang=".LANG.GetFilterParams("filter_", false));

if ($bVarsFromForm)
	$DB->InitTableVarsForEdit("b_sale_order", "", "str_");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("TCS_SOE_TITLE")));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/
$APPLICATION->RestartBuffer();
 
if (!$bUserCanViewOrder)
{
	CAdminMessage::ShowMessage(str_replace("#ID#", $ID, GetMessage("TCS_SOE_NO_VIEW_PERMS")).". ");
}
else
{
	if (!CSaleOrder::IsLocked($ID, $lockedBY, $dateLock))
		CSaleOrder::Lock($ID);

	$aMenu = array(
			array(
				"TEXT" => GetMessage("TCS_SOE_TO_LIST"),
				"LINK" => "/bitrix/admin/sale_order_edit.php?ID=".$ID."&dontsave=Y&lang=".LANGUAGE_ID.GetFilterParams("filter_")
			)
		);
	$aMenu[] = array("SEPARATOR" => "Y");

	if ($bUserCanViewOrder)
	{
		$aMenu[] = array(
				"TEXT" => GetMessage("TCS_SOE_TO_DETAIL"),
				"LINK" => "/bitrix/admin/sale_order_detail.php?ID=".$ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_")
			);
	}
	$aMenu[] = array(
			"TEXT" => GetMessage("TCS_SOE_TO_PRINT"),
			"LINK" => "/bitrix/admin/sale_order_print.php?ID=".$ID."&lang=".LANGUAGE_ID.GetFilterParams("filter_")
		);

	if ($saleModulePermissions == "W" || $str_PAYED != "Y" && $bUserCanDeleteOrder)
	{
		$aMenu[] = array(
				"TEXT" => GetMessage("TCS_SOEN_CONFIRM_DEL"),
				"LINK" => "javascript:if(confirm('".GetMessage("TCS_SOEN_CONFIRM_DEL_MESSAGE")."')) window.location='sale_order.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get().GetFilterParams("filter_")."'",
				"WARNING" => "Y"
			);
	}
	
	?>
<div class = "dOrderFull">
	<?
	CAdminMessage::ShowMessage($errorMessage);

	$customOrderEdit = COption::GetOptionString("sale", "path2custom_edit_order", "");
	if (strlen($customOrderEdit) > 0
		&& file_exists($_SERVER["DOCUMENT_ROOT"].$customOrderEdit)
		&& is_file($_SERVER["DOCUMENT_ROOT"].$customOrderEdit))
	{
		include($_SERVER["DOCUMENT_ROOT"].$customOrderEdit);
	}
	else
	{
		?>
		<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" class = "fUpdateOrder" name="forder_edit">
		<?= bitrix_sessid_post(); ?>
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
		<input type="hidden" name="ID" value="<?= $ID ?>">
		<input type="hidden" name="USER_ID" value="<?= $USER->GetID() ?>">
		<input type="hidden" name="RE_COUNT" value="<?= $RE_COUNT=="Y"?"Y":"N" ?>">
		<div class = "dError"></div>
		<?

		
		$arPersonTypeList = array();
		$arSitePersonTypeCnt = array();
		$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array());
		while ($arPersonType = $dbPersonType->GetNext())
		{
			$arPersonTypeList[$arPersonType["ID"]] = $arPersonType;
			foreach($arPersonType["LIDS"] as $s)
			{
				if(IntVal($arSitePersonTypeCnt[$s]) <= 0)
					$arSitePersonTypeCnt[$s] = 0;
				$arSitePersonTypeCnt[$s]++;
			}
		}
		?>
			<script>
				var USER_ID = <?=$USER->GetID()?>;
				var RE_COUNT = '<?=$RE_COUNT=="Y"?"Y":"N"?>';
			</script>
			<table class = "tReform">
			<tr>
				<td colspan="2" id="ID_BASKET_CONTAINER">

					<script language="JavaScript">
					<!--
					function ModuleChange()
					{
						var m = document.getElementById("BASKET_MODULE");
						if (!m)
							return false;

						if (m.value == "catalog")
						{
							document.getElementById("basket_product_button").disabled = false;
						}
						else
							document.getElementById("basket_product_button").disabled = true;
					}

					var iblockIDTmp = 0;

					function ProductSearchOpen(index)
					{
						var index = document.getElementById("BASKET_ID").value;
						var quantity = document.getElementById("BASKET_QUANTITY").value;
						window.open('sale_product_search.php?lang=<?=LANGUAGE_ID?>&func_name=FillProductFields&index=' + index + '&QUANTITY=' + quantity + '&BUYER_ID=' + document.forder_edit.USER_ID.value + '&IBLOCK_ID=' + iblockIDTmp, '', 'scrollbars=yes,resizable=yes,width=600,height=500,top='+parseInt((screen.height - 500)/2-14)+',left='+parseInt((screen.width - 600)/2-5));
					}

					function CheckFullDivision()
					{
						var bNeedFullDivision = false;
						var bAllChecked = true;
						for (i = 0; i < idS.length; i++)
						{
							if(!document.getElementById('product_delete_'+idS[i]))
							{
								var fld = document.getElementById("MOVE2NEW_ORDER_" + idS[i]);
								if (fld)
								{
									if (fld.checked)
										bNeedFullDivision = true;
									else
										bAllChecked = false;
								}
							}
						}

						if (bAllChecked)
						{
							for (i = 0; i < idS.length; i++)
							{
								if(!document.getElementById('product_delete_'+idS[i]))
								{
									var fld = document.getElementById("MOVE2NEW_ORDER_" + idS[i]);
									if (fld)
										fld.checked = false;
									var fld = document.getElementById("ID_FULL_DIVISION_TD");
									fld.disabled = true;
									document.forder_edit.ID_FULL_DIVISION.disabled = true;
								}
							}
						}
						else
						{
							var fld = document.getElementById("ID_FULL_DIVISION_TD");
							fld.disabled = !bNeedFullDivision;
							document.forder_edit.ID_FULL_DIVISION.disabled = !bNeedFullDivision;
							if (bNeedFullDivision)
								alert('<?echo GetMessage("TCS_SALE_F_ALERT_ORDER_DIV")?>');
						}
					}

					//-->
					</script>


					<table cellpadding="3" cellspacing="1" border="0" width="100%" class="internal" id="BASKET_TABLE">
						<tr class="heading">
							<td><?echo GetMessage("TCS_SALE_F_NAME")?></td>
							<td><?echo GetMessage("TCS_SALE_F_QUANTITY")?></td>
							<td><?echo GetMessage("TCS_SALE_F_PRICE")?></td>
							<td>&nbsp;</td>
						</tr>

			<?
			$arBasketItem = array();
			if ($bVarsFromForm)
			{
			
				foreach($allIDs as $i)
				{
					${"PRODUCT_ID_".$i} = IntVal(${"PRODUCT_ID_".$i});
					if (${"PRODUCT_ID_".$i} > 0)
					{		
						$arBasketItem[IntVal(${"ID_".$i})] = array(
							"ID" => IntVal(${"ID_".$i}),
							"IND" => IntVal($i),
							"PRODUCT_ID" => ${"PRODUCT_ID_".$i},
							"PRODUCT_PRICE_ID" => ${"PRODUCT_PRICE_ID_".$i},
							"MODULE" => htmlspecialcharsEx(${"MODULE_".$i}),
							"NAME" => htmlspecialcharsEx(${"NAME_".$i}),
							"DETAIL_PAGE_URL" => htmlspecialcharsEx(Trim(${"DETAIL_PAGE_URL_".$i})),
							"PRICE" => DoubleVal(${"PRICE_".$i}),
							"CURRENCY" => htmlspecialcharsEx(${"CURRENCY_".$i}),
							"DISCOUNT_PRICE" => DoubleVal(${"DISCOUNT_PRICE_".$i}),
							"WEIGHT" => DoubleVal(${"WEIGHT_".$i}),
							"QUANTITY" => DoubleVal(${"QUANTITY_".$i}),
							"NOTES" => htmlspecialcharsEx(Trim(${"NOTES_".$i})),
							"CALLBACK_FUNC" => htmlspecialcharsEx(Trim(${"CALLBACK_FUNC_".$i})),
							"ORDER_CALLBACK_FUNC" => htmlspecialcharsEx(Trim(${"ORDER_CALLBACK_FUNC_".$i})),
							"CANCEL_CALLBACK_FUNC" => htmlspecialcharsEx(Trim(${"CANCEL_CALLBACK_FUNC_".$i})),
							"PAY_CALLBACK_FUNC" => htmlspecialcharsEx(Trim(${"PAY_CALLBACK_FUNC_".$i})),
							"CATALOG_XML_ID" => htmlspecialcharsEx(Trim(${"CATALOG_XML_ID_".$i})),
							"PRODUCT_XML_ID" => htmlspecialcharsEx(Trim(${"PRODUCT_XML_ID_".$i})),
							"VAT_RATE" => DoubleVal(${"VAT_RATE_".$i})
						);

						$arBasketProps = array();
						${"BASKET_PROP_COUNT_".$i} = IntVal(${"BASKET_PROP_COUNT_".$i});
						if (${"BASKET_PROP_COUNT_".$i} > 0)
						{
							$jnd = -1;
							for ($j = 1; $j <= ${"BASKET_PROP_COUNT_".$i}; $j++)
							{
								${"BASKET_PROP_".$i ."_NAME_".$j} = Trim(${"BASKET_PROP_".$i ."_NAME_".$j});
									$jnd++;
									$arBasketProps[$jnd] = array(
										//"ID" => IntVal(${"BASKET_PROP_ID_".$i."_".$j}),
										"NAME" => htmlspecialcharsEx(Trim(${"BASKET_PROP_".$i."_NAME_".$j})),
										"CODE" => htmlspecialcharsEx(Trim(${"BASKET_PROP_".$i."_CODE_".$j})),
										"VALUE" => htmlspecialcharsEx(${"BASKET_PROP_".$i."_VALUE_".$j}),
										"SORT" => IntVal(${"BASKET_PROP_".$i."_SORT_".$j})
									);
							}
						}
						$arBasketItem[IntVal(${"ID_".$i})]["PROPS"] = $arBasketProps;
					}			
				}
			}
			else
			{
				$dbBasket = CSaleBasket::GetList(
						array("NAME" => "ASC"),
						array("ORDER_ID" => $ID),
						false,
						false,
						array("ID", "PRODUCT_ID", "PRODUCT_PRICE_ID", "PRICE", "CURRENCY", "WEIGHT", "QUANTITY", "NAME", "MODULE", "CALLBACK_FUNC", "NOTES", "DETAIL_PAGE_URL", "DISCOUNT_PRICE", "ORDER_CALLBACK_FUNC", "CANCEL_CALLBACK_FUNC", "PAY_CALLBACK_FUNC", "CATALOG_XML_ID", "PRODUCT_XML_ID", "VAT_RATE")
					);
				while ($arBasket = $dbBasket->GetNext())
				{
					$arBasket["PROPS"] = Array();
					$dbBasketProps = CSaleBasket::GetPropsList(
							array("SORT" => "ASC"),
							array("BASKET_ID" => $arBasket["ID"]),
							false,
							false,
							array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
						);
					while ($arBasketProps = $dbBasketProps->GetNext())
					{
						$arBasket["PROPS"][$arBasketProps["ID"]] = $arBasketProps;
					}
					$arBasketItem[$arBasket["ID"]] = $arBasket;
				}
			}
			
			$IDs = "";
			$fields = "";
			$bFields = false;
			
			$basketTotalWeight = 0;

			foreach($arBasketItem as $val)
			{
				$IDs .= "'".$val["ID"]."',";
				$basketTotalWeight += $val['WEIGHT']*$val['QUANTITY'];
				?>
				<tr id="BASKET_TABLE_ROW_<?=$val["ID"]?>">
					<td><div id="DIV_NAME_<?=$val["ID"]?>"><?=$val["NAME"]?></div></td>
					<td><div id="DIV_QUANTITY_<?=$val["ID"]?>"><?=$val["QUANTITY"]?></td></div>
					<td><div id="DIV_PRICE_<?=$val["ID"]?>"><?=$val["CURRENCY"]?> <?=$val["PRICE"]?></div></td>
					<td>
						<?if($saleModulePermissions >= "U"):?>
							<a href="javascript: ShowProductEdit(<?=$val["ID"]?>);"><?=GetMessage("TCS_SOE_JS_EDIT")?></a><br />
							<a href="javascript: DeleteProduct(<?=$val["ID"]?>);"><?=GetMessage("TCS_SOE_JS_DEL")?></a>
						<?endif;?>
					</td>
				</tr>

				<?
				foreach($val as $k => $v)
				{
					if($k != "PROPS" && strpos($k, "~") === false)
					{
						if(!$bFields)
							$fields .= "'".$k."',";
						?>
						<input type="hidden" id="<?=$k?>_<?=$val["ID"]?>" name="<?=$k?>_<?=$val["ID"]?>" value="<?=$v?>">
						<?
					}
					elseif($k == "PROPS")
					{
						$i = 1;
						foreach($v as $vv1)
						{
							foreach($vv1 as $kk => $vv)
							{
								if(strpos($kk, "~") === false)
								{
									?>
									<input type="hidden" id="BASKET_PROP_<?=$val["ID"]?>_<?=$kk?>_<?=$i?>" name="BASKET_PROP_<?=$val["ID"]?>_<?=$kk?>_<?=$i?>" value="<?=$vv?>">
									<?
								}
							}
							$i++;
						}
						?>
						<input type="hidden" name="BASKET_PROP_COUNT_<?=$val["ID"]?>" id="BASKET_PROP_COUNT_<?=$val["ID"]?>" value="<?=--$i?>">
						<?
					}
				}
				$bFields = true;
			}
			if (strlen($fields) <= 0) 
				$fields = "'ID','PRODUCT_ID','PRODUCT_PRICE_ID','PRICE','CURRENCY','WEIGHT','QUANTITY','NAME','MODULE','CALLBACK_FUNC','NOTES','DETAIL_PAGE_URL','DISCOUNT_PRICE','ORDER_CALLBACK_FUNC','CANCEL_CALLBACK_FUNC','PAY_CALLBACK_FUNC','CATALOG_XML_ID','PRODUCT_XML_ID','VAT_RATE',";
			?>
				<tr>
					<td colspan="2"><?=GetMessage("TCS_DELIVERY_SUMM")?></td>
					<td><?=$str_CURRENCY?> <?= roundEx($str_PRICE_DELIVERY, SALE_VALUE_PRECISION) ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align = "right"><?=GetMessage("TCS_TOTAL_SUMM")?>:</td>
					<td colspan="2" align="left">
						<?
							$obOrder = CSaleOrder::GetList(Array(),Array("ID"=>$ID),false,false,Array("PRICE","CURRENCY"));
							$arOrder = $obOrder->Fetch();
						?>
						<span id = "sOrderPrice"><?=$arOrder["CURRENCY"]?> <?=$arOrder["PRICE"];?></span>
						<?if($saleModulePermissions >= "U"):?>
							<button style = "display:none;" id = "sNeedSave" onclick = "UpdateOrder(); return false;"><?=GetMessage("TCS_NEED_SAVE")?></button>
						<?endif;?>
					</td>
				</tr>
			</table>

		
			<input type="hidden" name="BASKET_COUNTER" id="BASKET_COUNTER" value="<?=count($arBasketItem)?>">
			<input type="hidden" name="BASKET_IDS" id="BASKET_IDS" value="<?=substr(str_replace( "'", "", $IDs), 0, -1)?>">
			
			<div id="additional-fields"></div>
			<script>
			var basketFields = Array(<?=substr($fields, 0, -1)?>);
			var idS = Array(<?=substr($IDs, 0, -1)?>);
			var basketTotalWeight = <?echo $basketTotalWeight;?>;
			</script>
			
		<?
		$formTemplate = '
				<input id="BASKET_ID" name="BASKET_ID" value="" type="hidden">
				<table class="edit-table" style="background-color:#F8F9FC; border: 1px solid #B8C1DD; width: 600px;" >
				<tr class="heading">
					<td colspan="2">'.GetMessage("TCS_SOE_BASKET_EDIT").'</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
					<div id="basketError" style="display:none;">
						<table class="message message-error" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<table class="content" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td valign="top"><div class="icon-error"></div></td>
											<td>

												<span class="message-title">'.GetMessage("TCS_SOE_BASKET_ERROR").'</span><br>
												<div class="empty" style="height: 5px;"></div><div id="basketErrorText"></div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div></td>
				</tr>
				<tr>
					<td width="200" class="field-name"><span class="required">*</span>'.GetMessage("TCS_SOE_MODULE").':</td>
					<td width="400">';
					if($bSimpleForm)
					{
						$formTemplate .= '<input type="hidden" name="BASKET_MODULE" id="BASKET_MODULE" value="catalog">'.GetMessage("TCS_SOE_MODULE_CATALOG");
					}
					else
					{
						$formTemplate .= '<select name="BASKET_MODULE" onchange="ModuleChange(this.value)" id="BASKET_MODULE">';
						
						$dbModuleList = CModule::GetList();
						while ($arModuleList = $dbModuleList->Fetch())
						{
							$formTemplate .= '<option value="'.$arModuleList["ID"].'">'.htmlspecialcharsEx($arModuleList["ID"]).'</option>';
						}
						
						$formTemplate .= '</select>';
					}
					
				$formTemplate .= '</td>
				</tr>
				<tr>
					<td class="field-name"><span class="required">*</span>'.GetMessage("TCS_SOE_PRODUCT_ID").':</td>
					<td><input id="BASKET_PRODUCT_ID" name="BASKET_PRODUCT_ID" value="" size="5" type="text">&nbsp;<input disabled="disabled" id="basket_product_button" value="..." onclick="ProductSearchOpen(1)" type="button"></td>
				</tr>
				<tr>
					<td class="field-name"><span class="required">*</span>'.GetMessage("TCS_SOE_ITEM_NAME").':</td>
					<td><input size="40" id="BASKET_NAME" name="BASKET_NAME" type="text" value=""></td>
				</tr>
				<tr>	
					<td class="field-name">'.GetMessage("TCS_SOE_ITEM_PATH").':</td>
					<td><input id="BASKET_DETAIL_PAGE_URL" name="BASKET_DETAIL_PAGE_URL" value="" size="40" type="text"></td>
				</tr>
				<tr>
					<td class="field-name">'.GetMessage("TCS_SOE_JS_CAT_XML_ID").':</td>
					<td><input id="BASKET_CATALOG_XML_ID" name="BASKET_CATALOG_XML_ID" value="" size="40" type="text"></td>
				</tr>
				<tr>
					<td class="field-name">'.GetMessage("TCS_SOE_JS_PROD_XML_ID").':</td>
					<td><input id="BASKET_PRODUCT_XML_ID" name="BASKET_PRODUCT_XML_ID" value="" size="40" type="text"></td>
				</tr>
				<tr>
					<td class="field-name" valign="top" width="40%">'.GetMessage("TCS_SOE_ITEM_PROPS").':</td>
					<td width="60%">
						<table id="BASKET_PROP_TABLE" class="internal" border="0" cellpadding="3" cellspacing="1">
							<tr class="heading">
								<td><span class="required">*</span>'.GetMessage("TCS_SOE_IP_NAME").'</td>
								<td><span class="required">*</span>'.GetMessage("TCS_SOE_IP_VALUE").'</td>
								<td>'.GetMessage("TCS_SOE_IP_CODE").'</td>
								<td>'.GetMessage("TCS_SOE_IP_SORT").'</td>
							</tr>
						</table>
						
						<input value="0" name="BASKET_PROP_COUNT" id="BASKET_PROP_COUNT" type="hidden">
						<input value="'.GetMessage("TCS_SOE_IP_MORE").'" onclick="BasketAddPropSection(-1)" type="button">
					</td>
				</tr>
				<tr>
					<td class="field-name"><span class="required">*</span>'.GetMessage("TCS_SOE_ITEM_PRICE").':</td>
					<td>
						<input id="BASKET_PRICE" name="BASKET_PRICE" size="10" maxlength="20" value="" type="text">
						<select name="BASKET_CURRENCY" id="BASKET_CURRENCY">';
							
							$dbCurrency = CCurrency::GetList(($by="sort"), ($order="asc"));
							while ($arCurrency = $dbCurrency->Fetch())
							{
								$formTemplate .= '<option value="'.$arCurrency["CURRENCY"].'">'.htmlspecialcharsEx($arCurrency["CURRENCY"]).'</option>';
							}
							
			$formTemplate .= '</select>
					</td>
				</tr>
				<tr>
					<td class="field-name">'.GetMessage("TCS_SOE_ITEM_DISCOUNT").':</td>
					<td><input disabled="disabled" name="BASKET_DISCOUNT_PRICE" id="BASKET_DISCOUNT_PRICE" size="10" maxlength="20" value="" type="text"></td>
				</tr>
				<tr>
					<td class="field-name">'.GetMessage("TCS_SOE_VAT").':</td>
					<td><input name="BASKET_VAT_RATE" id="BASKET_VAT_RATE" size="10" maxlength="20" value="" type="text"></td>
				</tr>
				<tr>
					<td class="field-name">'.GetMessage("TCS_SOE_WEIGHT").':</td>
					<td><input name="BASKET_WEIGHT" id="BASKET_WEIGHT" size="4" maxlength="20" value="" type="text"></td>
				</tr>
				<tr>
					<td class="field-name"><span class="required">*</span>'.GetMessage("TCS_SOE_ITEM_QUANTITY").':</td>
					<td><input name="BASKET_QUANTITY" id="BASKET_QUANTITY" size="4" maxlength="20" value="" type="text"></td>
				</tr>
				<tr>
					<td class="field-name">'.GetMessage("TCS_SOE_ITEM_DESCR").':</td>
					<td><input name="BASKET_NOTES" id="BASKET_NOTES" size="40" maxlength="250" value="" type="text"></td>
				</tr>
				';
				if($bSimpleForm)
				{
					$formTemplate .= '
						<input name="BASKET_CALLBACK_FUNC" id="BASKET_CALLBACK_FUNC" size="40" maxlength="250" value="" type="hidden">
						<input name="BASKET_ORDER_CALLBACK_FUNC" id="BASKET_ORDER_CALLBACK_FUNC" size="40" maxlength="250" value="" type="hidden">
						<input name="BASKET_CANCEL_CALLBACK_FUNC" id="BASKET_CANCEL_CALLBACK_FUNC" size="40" maxlength="250" value="" type="hidden">
						<input name="BASKET_PAY_CALLBACK_FUNC" id="BASKET_PAY_CALLBACK_FUNC" size="40" maxlength="250" value="" type="hidden">';
				}
				else
				{
					$formTemplate .= '
						<tr>
							<td class="field-name">'.GetMessage("TCS_SOE_BASKET_CALLBACK_FUNC").':</td>
							<td><input name="BASKET_CALLBACK_FUNC" id="BASKET_CALLBACK_FUNC" size="40" maxlength="250" value="" type="text"></td>
						</tr>
						<tr>
							<td class="field-name">'.GetMessage("TCS_SOE_BASKET_ORDER_CALLBACK_FUNC").':</td>
							<td><input name="BASKET_ORDER_CALLBACK_FUNC" id="BASKET_ORDER_CALLBACK_FUNC" size="40" maxlength="250" value="" type="text"></td>
						</tr>
						<tr>
							<td class="field-name">'.GetMessage("TCS_SOE_BASKET_CANCEL_CALLBACK_FUNC").':</td>
							<td><input name="BASKET_CANCEL_CALLBACK_FUNC" id="BASKET_CANCEL_CALLBACK_FUNC" size="40" maxlength="250" value="" type="text"></td>
						</tr>
						<tr>
							<td class="field-name">'.GetMessage("TCS_SOE_BASKET_PAY_CALLBACK_FUNC").':</td>
							<td><input name="BASKET_PAY_CALLBACK_FUNC" id="BASKET_PAY_CALLBACK_FUNC" size="40" maxlength="250" value="" type="text"></td>
						</tr>';
				}
				
				$formTemplate .= '<tr>
					<td colspan="2" align="center"><input name="btn1" value="'.GetMessage("TCS_SOE_APPLY").'" onclick="SaveProduct();" type="button"> <input name="btn2" value="'.GetMessage("TCS_SALE_CANCEL").'" onclick="SaleBasketEditTool.PopupHide();" type="button"></td>
				</tr>
				</table>';

		$formTemplate = CUtil::JSEscape($formTemplate);
		?>
		<script>			
		function bxsalehtmlspecialchars(str)
		{
			if(!(typeof(str) == "string" || str instanceof String))
				return str;

			str = str.replace(/&/g, '&amp;');
			str = str.replace(/"/g, '&quot;');
			str = str.replace(/</g, '&lt;');
			str = str.replace(/>/g, '&gt;');
			str = str.replace(/\#/g, '&#35;');
			str = str.replace(/\!/g, '&#33;');
			str = str.replace(/\$/g, '&#36;');
			str = str.replace(/\%/g, '&#37;');
			str = str.replace(/\~/g, '&#126;');
			return str;
		}
		
		function bxsalequote(str)
		{
			if(!(typeof(str) == "string" || str instanceof String))
				return str;

			str = str.replace(/&/g, '&amp;');
			str = str.replace(/"/g, '&quot;');
			str = str.replace(/</g, '&lt;');
			str = str.replace(/>/g, '&gt;');
			return str;
		}
		
		function BasketAddPropSection(ind, propName, propValue, propCode, propSort)
		{
			var oTbl = document.getElementById("BASKET_PROP_TABLE");
			if (!oTbl)
				return;

			if (!propName)
				propName = "";
			if (!propValue)
				propValue = "";
			if (!propCode)
				propCode = "";
			if (!propSort)
				propSort = "";
				
			if(ind < 0)
				ind = parseInt(document.getElementById("BASKET_PROP_COUNT").value) + 1;
				
			var oRow = oTbl.insertRow(-1);
			var oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input type="text" id="BASKET_PROP_NAME_' + ind + '" name="BASKET_PROP_NAME_' + ind + '" size="15" maxlength="250" value="">';
			document.getElementById('BASKET_PROP_NAME_' + ind).value = propName;

			var oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input type="text" id="BASKET_PROP_VALUE_' + ind + '" name="BASKET_PROP_VALUE_' + ind + '" size="20" maxlength="250" value="">';
			document.getElementById('BASKET_PROP_VALUE_' + ind).value = propValue;

			var oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input type="text" id="BASKET_PROP_CODE_' + ind + '" name="BASKET_PROP_CODE_' + ind + '" size="3" maxlength="250" value="">';
			document.getElementById('BASKET_PROP_CODE_' + ind).value = propCode;

			var oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input type="text" id="BASKET_PROP_SORT_' + ind + '" name="BASKET_PROP_SORT_' + ind + '" size="2" maxlength="10" value="' + propSort + '">';
			
			document.getElementById("BASKET_PROP_COUNT").value = ind;
		}
		
		function pJCFloatDiv() 
		{
			var _this = this;
			this.floatDiv = null;
			this.x = this.y = 0;

			this.Show = function(div, left, top)
			{
				var zIndex = parseInt(div.style.zIndex);
				if(zIndex <= 0 || isNaN(zIndex))
					zIndex = 100;
				div.style.zIndex = zIndex;
				div.style.left = left + "px";
				div.style.top = top + "px";

				if(jsUtils.IsIE())
				{
					var frame = document.getElementById(div.id+"_frame");
					if(!frame)
					{
						frame = document.createElement("IFRAME");
						frame.src = "javascript:''";
						frame.id = div.id+"_frame";
						frame.style.position = 'absolute';
						frame.style.zIndex = zIndex-1;
						document.body.appendChild(frame);
					}
					frame.style.width = div.offsetWidth + "px";
					frame.style.height = div.offsetHeight + "px";
					frame.style.left = div.style.left;
					frame.style.top = div.style.top;
					frame.style.visibility = 'visible';
				}
			}
				
			this.Close = function(div)
			{
				if(!div)
					return;

				var frame = document.getElementById(div.id+"_frame");
				if(frame)
					frame.style.visibility = 'hidden';
			}
				
		}
		var pjsFloatDiv = new pJCFloatDiv();

		/************************************************/

		function SaleBasketEdit()
		{
			var _this = this;
			this.active = null;
			
			this.PopupShow = function(div, pos)
			{
				this.PopupHide();
				if(!div)
					return;
				if (typeof(pos) != "object")
					pos = {};
					
				this.active = div.id;
				div.ondrag = jsUtils.False;
				
				jsUtils.addEvent(document, "keypress", _this.OnKeyPress);
				
				div.style.width = div.offsetWidth + 'px';
				div.style.visibility = 'visible';
				
				var res = jsUtils.GetWindowSize();
				pos['top'] = parseInt(res["scrollTop"] + res["innerHeight"]/2 - div.offsetHeight/2);
				pos['left'] = parseInt(res["scrollLeft"] + res["innerWidth"]/2 - div.offsetWidth/2);
				if(pos['top'] < 5)
					pos['top'] = 5;				
				if(pos['left'] < 5)
					pos['left'] = 5;

				pjsFloatDiv.Show(div, pos["left"], pos["top"]);
			}

			this.PopupHide = function()
			{
				var div = document.getElementById(_this.active);
				if(div)
				{
					pjsFloatDiv.Close(div);
					div.parentNode.removeChild(div);
				}

				this.active = null;
				jsUtils.removeEvent(document, "keypress", _this.OnKeyPress);
			}

			this.OnKeyPress = function(e)
			{
				if(!e) e = window.event
				if(!e) return;
				if(e.keyCode == 27)
					_this.PopupHide();
			},

			this.IsVisible = function()
			{
				return (document.getElementById(this.active).style.visibility != 'hidden');
			}
		}


		function SaveProduct()
		{
			var error = '';
			if(document.getElementById('BASKET_PRODUCT_ID').value.length <= 0)
				error += '<?=GetMessage("TCS_SOE_BASKET_ERROR_PRODUCT_ID")?><br />';			
			if(document.getElementById('BASKET_NAME').value.length <= 0)
				error += '<?=GetMessage("TCS_SOE_BASKET_ERROR_NAME")?><br />';			
			if(document.getElementById('BASKET_PRICE').value.length <= 0)
				error += '<?=GetMessage("TCS_SOE_BASKET_ERROR_PRICE")?><br />';
			if(document.getElementById('BASKET_QUANTITY').value.length <= 0)
				error += '<?=GetMessage("TCS_SOE_BASKET_ERROR_QUANTITY")?><br />';
							
			if(error.length > 0)
			{
				document.getElementById('basketError').style.display = 'block';
				document.getElementById('basketErrorText').innerHTML = error;
			}
			else
			{
				id = document.getElementById("BASKET_ID").value;
				if(id.length > 0)
				{
					//values
					for (i = 0; i < basketFields.length; i++)
					{
					
						if(document.getElementById(basketFields[i]+'_'+id))
						{
							if(document.getElementById('BASKET_'+basketFields[i]))
								document.getElementById(basketFields[i]+'_'+id).value = document.getElementById('BASKET_'+basketFields[i]).value;
						}
						else
						{
							if(document.getElementById('BASKET_'+basketFields[i]))
								val = document.getElementById('BASKET_'+basketFields[i]).value;
							else
								val = '';
								
							document.getElementById('additional-fields').innerHTML += '<input type="hidden" name="' + basketFields[i]+'_'+id + '" id="' + basketFields[i]+'_'+id + '" value="' + bxsalequote(val) + '">';;
						}
					}

					//props
					propCnt = document.getElementById('BASKET_PROP_COUNT').value;
					propCnt = parseInt(propCnt);
					
					var propsHTML = "";
					if(propCnt > 0)
					{
						for (i=1; i <= propCnt; i++)
						{
							propName = document.getElementById('BASKET_PROP_NAME_' + i).value;
							propCode = document.getElementById('BASKET_PROP_CODE_' + i).value;
							propValue = document.getElementById('BASKET_PROP_VALUE_' + i).value;
							propSort = document.getElementById('BASKET_PROP_SORT_' + i).value;
							
							if(document.getElementById('BASKET_PROP_' +id + '_NAME_' + i))
							{
								document.getElementById('BASKET_PROP_' +id + '_NAME_' + i).value = (document.getElementById('BASKET_PROP_NAME_' + i).value);
								document.getElementById('BASKET_PROP_' +id + '_CODE_' + i).value = (document.getElementById('BASKET_PROP_CODE_' + i).value);
								document.getElementById('BASKET_PROP_' +id + '_VALUE_' + i).value = (document.getElementById('BASKET_PROP_VALUE_' + i).value);
								document.getElementById('BASKET_PROP_' +id + '_SORT_' + i).value = (document.getElementById('BASKET_PROP_SORT_' + i).value);
							}
							else
							{
								var newProps = '';
								newProps += '<input type="hidden" name="BASKET_PROP_' +id + '_NAME_' + i + '" id="BASKET_PROP_' +id + '_NAME_' + i + '" value="' + bxsalequote(document.getElementById('BASKET_PROP_NAME_' + i).value) + '">';
								newProps += '<input type="hidden" name="BASKET_PROP_' +id + '_CODE_' + i + '" id="BASKET_PROP_' +id + '_CODE_' + i + '" value="' + bxsalequote(document.getElementById('BASKET_PROP_CODE_' + i).value) + '">';
								newProps += '<input type="hidden" name="BASKET_PROP_' +id + '_SORT_' + i + '" id="BASKET_PROP_' +id + '_SORT_' + i + '" value="' + bxsalequote(document.getElementById('BASKET_PROP_SORT_' + i).value) + '">';
								newProps += '<input type="hidden" name="BASKET_PROP_' +id + '_VALUE_' + i + '" id="BASKET_PROP_' +id + '_VALUE_' + i + '" value="' + bxsalequote(document.getElementById('BASKET_PROP_VALUE_' + i).value) + '">';
								document.getElementById('additional-fields').innerHTML += newProps;
							}

							//visible props
							if(document.getElementById('BASKET_PROP_NAME_' + i).value.length > 0)
								propsHTML +=  bxsalehtmlspecialchars(document.getElementById('BASKET_PROP_NAME_' + i).value + ': ' + document.getElementById('BASKET_PROP_VALUE_' + i).value) + '<br />';
						}
						
						if(document.getElementById('BASKET_PROP_COUNT_'+id))
							document.getElementById('BASKET_PROP_COUNT_'+id).value = propCnt;
						else
							document.getElementById('additional-fields').innerHTML += '<input type="hidden" name="BASKET_PROP_COUNT_' + id + '" id="BASKET_PROP_COUNT_' +id + '" value="' + propCnt + '">';
					}
					
					if(!document.getElementById('BASKET_TABLE_ROW_' + id))
					{
						var oTbl = document.getElementById("BASKET_TABLE");
						var oRow = oTbl.insertRow(1);
						oRow.id = "BASKET_TABLE_ROW_" + id;

						/*var oCell = oRow.insertCell(-1);
						oCell.innerHTML = '<input name="MOVE2NEW_ORDER_' + id + '" id="MOVE2NEW_ORDER_' + id + '" value="Y" type="checkbox">';
						
						fld = document.getElementById('MOVE2NEW_ORDER_' + id);
						if (fld.addEventListener)
							fld.addEventListener('click', CheckFullDivision, false);
						else
							fld.attachEvent('onclick', CheckFullDivision);*/
						
						var oCell = oRow.insertCell(-1);
						oCell.innerHTML = '<div id="DIV_NAME_'+ id + '"></div>';

						/*var oCell = oRow.insertCell(-1);
						oCell.innerHTML = '<div id="DIV_PRODUCT_XML_ID_'+ id + '"></div>';

						var oCell = oRow.insertCell(-1);
						oCell.innerHTML = '<div id="DIV_PROPS_'+ id + '"></div>';
						
						var oCell = oRow.insertCell(-1);
						oCell.innerHTML = '<div id="DIV_NOTES_'+ id + '"></div>';*/

						var oCell = oRow.insertCell(-1);
						oCell.innerHTML = '<div id="DIV_QUANTITY_'+ id + '"></div>';
						
						var oCell = oRow.insertCell(-1);
						oCell.innerHTML = '<div id="DIV_PRICE_'+ id + '"></div>';
						
						var oCell = oRow.insertCell(-1);
						oCell.innerHTML = '<a href="javascript: ShowProductEdit(\'' + id + '\');"><?=GetMessage("TCS_SOE_JS_EDIT")?></a><br /><a href="javascript: DeleteProduct(\'' + id + '\');"><?=GetMessage("TCS_SOE_JS_DEL")?></a>';
						
						document.getElementById('BASKET_COUNTER').value = parseInt(document.getElementById('BASKET_COUNTER').value) + 1;
						idS[idS.length] = id;
						document.getElementById('BASKET_IDS').value += ',' + id;
					}
						
					//visible values
					document.getElementById('DIV_NAME_'+id).innerHTML = bxsalehtmlspecialchars(document.getElementById('BASKET_NAME').value);
					//document.getElementById('DIV_PRODUCT_XML_ID_'+id).innerHTML = bxsalehtmlspecialchars(document.getElementById('BASKET_PRODUCT_XML_ID').value);
					document.getElementById('DIV_PRICE_'+id).innerHTML = bxsalehtmlspecialchars(document.getElementById('BASKET_CURRENCY').value + ' ' +document.getElementById('BASKET_PRICE').value);
					//document.getElementById('DIV_NOTES_'+id).innerHTML = bxsalehtmlspecialchars(document.getElementById('BASKET_NOTES').value);
					document.getElementById('DIV_QUANTITY_'+id).innerHTML = bxsalehtmlspecialchars(document.getElementById('BASKET_QUANTITY').value);
					//if(propsHTML.length > 0)
						//document.getElementById('DIV_PROPS_'+id).innerHTML = propsHTML;
					NeedSave(true);
				}

				SaleBasketEditTool.PopupHide();
			}

		}

		check_ctrl_enter = function(e)
		{
			if(!e)
				e = window.event;

			if((e.keyCode == 13 || e.keyCode == 10) && e.ctrlKey)
			{
				alert('submit!');
			}
		}

		SaleBasketEditTool = new SaleBasketEdit();
		
		function ShowProductEdit(id)
		{
			var bDeleted = false;
			if(document.getElementById("product_delete_" + id))
				bDeleted = true;
		
			var data = '<?=$formTemplate?>';
			var div = document.createElement("DIV");
			div.id = "product_edit";
			div.style.visible = 'hidden';
			div.style.position = 'absolute';
			div.style.zIndex = '1000';
			div.innerHTML = data;
			
			var scripts = div.getElementsByTagName('script');
			
			for (var i = 0; i < scripts.length; i++)
			{
				var thisScript = scripts[i];
				var text;
				var sSrc = thisScript.src.replace(/http\:\/\/[^\/]+\//gi, '');
				if (thisScript.src && sSrc != 'bitrix/js/main/utils.js' && sSrc != 'bitrix/js/main/admin_tools.js' &&
					sSrc != '/bitrix/js/main/utils.js' && sSrc != '/bitrix/js/main/admin_tools.js') 
				{
					var newScript = document.createElement("script");
					newScript.type = 'text/javascript';
					newScript.src = thisScript.src;
					document.body.appendChild(newScript);
				}
				else if (thisScript.text || thisScript.innerHTML) 
				{
					text = (thisScript.text ? thisScript.text : thisScript.innerHTML);
					text = (""+text).replace(/^\s*<!\-\-/, '').replace(/\-\->\s*$/, '');
					eval(text);
				}
			}
			
			document.body.appendChild(div);
			SaleBasketEditTool.PopupShow(div);
			
			document.getElementById('basketError').style.display = 'none';
			//document.getElementById("BASKET_DISCOUNT_PRICE").disabled = document.forder_edit.RE_COUNT.checked;
			
			if(!bDeleted)
			{
				for (i = 0; i < basketFields.length; i++)
				{
				
					if(document.getElementById('BASKET_'+basketFields[i]) && document.getElementById(basketFields[i]+'_'+id))
					{
						if( val = document.getElementById(basketFields[i]+'_'+id).value)
							document.getElementById('BASKET_'+basketFields[i]).value = val;
						
					}
				}
				
				if(document.getElementById('BASKET_PROP_COUNT_' + id))
					propCnt = document.getElementById('BASKET_PROP_COUNT_' + id).value;
				else
					propCnt = 0;
			}
			else
			{
				propCnt = 0;
			}
			
			if(propCnt > 0)
			{
				for (i=1; i <= propCnt; i++)
				{
					propName = document.getElementById('BASKET_PROP_' +id + '_NAME_' + i).value;
					propValue = document.getElementById('BASKET_PROP_' +id + '_VALUE_' + i).value;
					propCode = document.getElementById('BASKET_PROP_' +id + '_CODE_' + i).value;
					propSort = document.getElementById('BASKET_PROP_' +id + '_SORT_' + i).value;
					
					BasketAddPropSection(i, propName, propValue, propCode, propSort);
				}
				//BasketAddPropSection(i);
			}
			else
			{
				BasketAddPropSection(1);
			}
			
			ModuleChange();			
		}
		
		function AddProduct()
		{
			if(document.getElementById("BASKET_COUNTER"))
			{
				id = parseInt(document.getElementById("BASKET_COUNTER").value) + 1;
			}
			else
			{
				document.getElementById("additional-fields").innerHTML += '<input type="hiden" name="BASKET_COUNTER" id="BASKET_COUNTER" value="1">';
				id = 0;
			}
			
			id = 'n' + id;
			ShowProductEdit(id);
			document.getElementById("BASKET_ID").value = id;
		}
		
		function DeleteProduct(id)
		{
			document.getElementById('additional-fields').innerHTML += '<input type="hidden" name="product_delete_' + id + '" id="product_delete_' + id + '" value="Y">';
			var oTbl = document.getElementById("BASKET_TABLE");
			ind = -1;
			for (var i = 0; i < oTbl.rows.length; i++)
			{
				if (oTbl.rows[i].id == "BASKET_TABLE_ROW_" + id)
				{
					ind = i;
					break;
				}
			}
			if (ind >= 0)
				oTbl.deleteRow(ind);
				
			document.getElementById("BASKET_COUNTER").value = parseInt(document.getElementById("BASKET_COUNTER").value) - 1;
			NeedSave(true);
		}
		
		function NeedSave(bNeed)
		{
			if(bNeed)
			{
				$('#sNeedSave').show();
				$('#sOrderPrice').hide();
				$(".sReform").attr("disabled","disabled");
			}
			else 
			{
				$('#sNeedSave').hide();
				$('#sOrderPrice').show();
				$(".sReform").removeAttr("disabled");
			}
		}
		
		function FillProductFields(index, arParams, iblockID)
		{
			for (key in arParams)
			{
				var fld = null;

				if (key == "id")
					fld = document.getElementById("BASKET_PRODUCT_ID");
				else if (key == "catalogXmlID")
					fld = document.getElementById("BASKET_CATALOG_XML_ID");
				else if (key == "productXmlID")
					fld = document.getElementById("BASKET_PRODUCT_XML_ID");
				else if (key == "name")
					fld = document.getElementById("BASKET_NAME");
				else if (key == "url")
					fld = document.getElementById("BASKET_DETAIL_PAGE_URL");
				else if (key == "price")
					fld = document.getElementById("BASKET_PRICE");
				else if (key == "weight")
					fld = document.getElementById("BASKET_WEIGHT");
				else if (key == "priceType")
					fld = document.getElementById("BASKET_NOTES");
				else if (key == "discountPrice")
					fld = document.getElementById("BASKET_DISCOUNT_PRICE");
				else if (key == "vatRate")
					fld = document.getElementById("BASKET_VAT_RATE");
				else if (key == "quantity")
					fld = document.getElementById("BASKET_QUANTITY");
				else if (key == "callback")
					fld = document.getElementById("BASKET_CALLBACK_FUNC");
				else if (key == "orderCallback")
					fld = document.getElementById("BASKET_ORDER_CALLBACK_FUNC");
				else if (key == "cancelCallback")
					fld = document.getElementById("BASKET_CANCEL_CALLBACK_FUNC");
				else if (key == "payCallback")
					fld = document.getElementById("BASKET_PAY_CALLBACK_FUNC");
				else if (key == "module")
					fld = document.getElementById("BASKET_MODULE");

				if (fld != null)
					fld.value = arParams[key];
			}

			for (key in arParams)
			{
				var fld = null;

				if (key == "currency")
					fld = document.getElementById("BASKET_CURRENCY");
				else if (key == "module")
					fld = document.getElementById("BASKET_MODULE");

				if (fld != null)
				{
					for (var i = 0; i < fld.options.length; i++)
					{
						if (fld.options[i].value == arParams[key])
						{
							fld.selectedIndex = i;
							break;
						}
					}
					fld.value = arParams[key];
				}
			}

			if (arParams["props"])
			{
				for (var i = 0; i < arParams["props"].length; i++)
					BasketAddPropSection(0, index, arParams["props"][i][0], arParams["props"][i][1], arParams["props"][i][2], arParams["props"][i][3]);
			}

			iblockIDTmp = iblockID;
		}
		</script>
			<?
			
			if($ind < 1)
			{
				?>
				<script>
				<!--
				if(chFullDiv = document.getElementById('MOVE2NEW_ORDER_0'))
					chFullDiv.disabled = true;
				//-->
				</script>
				<?
			}
			?>
			<?if($saleModulePermissions >= "U"):?>
				<tr>
					<td valign="top" align="center" colspan="2">
						<input type="button" value="<?= GetMessage("TCS_SOE_MORE_ITEMS") ?>" OnClick="AddProduct();">
					</td>
				</tr>
			<?endif;?>
			</table>

			<?$arTCSOrder = $obTCSOrder->TouchOrder($ID,Array());?>
			<p class = "pAllowed"><?=GetMessage("TCS_MAX_LOAN_AMOUNT")?>: <?=$arTCSOrder["MAX_LOAN_AMOUNT"]?> <?=GetMessage("TCS_RUB_")?></p>
			
			<div class = "dSlideWrapper">
				<div class = "dTitle"><?=GetMessage("TCS_DOWN_PAYMENT")?>:</div>
				<?
					$iOrderPrice = CCurrencyRates::ConvertCurrency($arOrder["PRICE"],$arOrder["CURRENCY"],"RUB");
					
					$iMax = $iOrderPrice-3000;
					$iMax = floor($iMax);
					$iDownPayment = IntVal($arTCSOrder["DOWN_PAYMENT"]);
					$iCreditSumm = $iOrderPrice-$iDownPayment;
					$iMonths = $arTCSOrder["PAYMENT_COUNT"];
					$iMonthPayment = ceil($obModule->CalculateMonthPayment($iMonths)*$iCreditSumm/10)*10;
				?>
				<div class = "dSliderOuter">
					<div class = "dLeft">0 <?=GetMessage("TCS_RUB_")?></div>
					<div class = "dRight"><?=$iMax?> <?=GetMessage("TCS_RUB_")?></div>
					<div class = "clear"></div>
					<div max="<?=$iMax?>" default="<?=$iDownPayment?>" id="slider_down_payment" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
						<div class="ui-slider-range ui-widget-header ui-slider-range-min" style = "width:<?=($iDownPayment/$iMax)*100?>%"></div>
					</div>
				</div>
				<script>
					var iOrderPrice = <?=$iOrderPrice?>;
					var inpMonths = $(".iMonths");
					var inpDownPayment = $(".iDownPayment");
					var ssCreditSumm = $(".sCreditSumm");
					var ssPerMonth = $(".sPerMonth");					
					DivSlider1 = $("#slider_down_payment");
					DivSlider1.slider({
						min:0,
						max:DivSlider1.attr("max"),
						value:parseInt(DivSlider1.attr("default")),
						step:100,
						slide:function(event, ui)
						{
							iValue = ui.value;
							iPercent = (iValue/$(this).attr("max"))*100;
							$(this).parents(".dSlideWrapper").find("input").val(iValue);
							$(this).find(".ui-slider-range").css({"width":iPercent+"%"});
							iMonths = parseInt(inpMonths.val());
							iDownPayment = inpDownPayment.val();
							sCreditSumm = iOrderPrice-iDownPayment;
							sMonthPayment = Math.ceil(CalculateMonthPayment(iMonths)*sCreditSumm/10)*10;
							ssCreditSumm.text(sCreditSumm);
							ssPerMonth.text(sMonthPayment);
							
						}
					});
					//alert(DivSlider1.attr("default"));
				</script>
				
				<div class = "dInput">
					<input type = "text" class = "iDownPayment iPay" disabled="y" name = "DOWN_PAYMENT" value = "<?=$iDownPayment?>"/>
				</div>
				<div class = "clear"></div>
			</div>
			
			<div class = "dSlideWrapper">
				<div class = "dTitle"><?=GetMessage("TCS_CREDIT_TIME")?>:</div>
				<?
					$iYear = $arTCSOrder["PAYMENT_COUNT"];
				?>
				<div class = "dSliderOuter">
					<div class = "clear"></div>
					<div max="24" default="<?=$iYear?>" min="3" id="slider_month" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
						<div class="ui-slider-range ui-widget-header ui-slider-range-min" style = "width:<?=(($iYear-3)/21)*100?>%"></div>
					</div>
				</div>
				<script>
					DivSlider2 = $("#slider_month");

					DivSlider2.slider({
						min:3,
						max:24,
						value:parseInt(DivSlider2.attr("default")),
						step:1,
						slide:function(event, ui)
						{
							iValue = ui.value;
							iPercent = ((iValue-3)/($(this).attr("max")-$(this).attr("min")))*100;
							$(this).parents(".dSlideWrapper").find("input").val(iValue);
							$(this).find(".ui-slider-range").css({"width":iPercent+"%"});
							iMonths = parseInt(inpMonths.val());
							iDownPayment = inpDownPayment.val();
							sCreditSumm = iOrderPrice-iDownPayment;
							sMonthPayment = Math.ceil(CalculateMonthPayment(iMonths)*sCreditSumm/10)*10;
							ssCreditSumm.text(sCreditSumm);
							ssPerMonth.text(sMonthPayment);							
						}
					});
				</script>
				
				<div class = "dInput">
					<input type = "text" class = "iMonths iPay" disabled="y" name = "PAYMENT_COUNT" value = "<?=$iYear?>"/>
				</div>
				<div class = "clear"></div>
			</div>
			<div class = "dInform">
				<div class = "dLeft">
					<?=GetMessage("TCS_MONTH_PAYMENT")?>: <span class = "sPerMonth"><?=$iMonthPayment?></span> <?=GetMessage("TCS_RUB_")?>
				</div>
				<div class = "dRight">
					<?=GetMessage("TCS_CREDIT_SUMM")?>: <span class = "sCreditSumm"><?=$iCreditSumm?></span> <?=GetMessage("TCS_RUB_")?>
				</div>
				<div class = "clear"></div>
			</div>
			<input name = "TYPE" type="hidden" value = "reform"/>
			<button class = "sReform" onclick = "ReformDialog(this, <?=$ID?>); return false;"><?=GetMessage("TCS_ORDER_REFORM")?></button>
		</form>
		<script language="JavaScript">
			function CalculateMonthPayment(iMonths)
			{
				iKoef = <?=$obModule->iKoef?>;
				iMonths = parseInt(iMonths);
				return iKoef*Math.pow((1+iKoef),iMonths)/(Math.pow(1+iKoef,iMonths)-1);
			}
			function ReformDialog(obj, iOrderID)
			{
				$('.iPay').removeAttr('disabled'); 
				MakeRequest($(obj).parents('form').serializeArray(),iOrderID);			
			
			}
		<!--
		//PayedClicked();
		//ReCountClicked(false);
		//CheckFullDivision();
		//-->
		</script>
		<?
	}		// if (strlen($customOrderView) > 0 ...
}
?>
</div>
