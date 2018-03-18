<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}
if (!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm(GetMessage("SALE_ACCESS_DENIED"));
}

$ID = IntVal($arParams["ID"]);

$arParams["PATH_TO_LIST"] = Trim($arParams["PATH_TO_LIST"]);
if (strlen($arParams["PATH_TO_LIST"]) <= 0)
	$arParams["PATH_TO_LIST"] = htmlspecialchars($APPLICATION->GetCurPage());

$arParams["PATH_TO_CANCEL"] = Trim($arParams["PATH_TO_CANCEL"]);
if (strlen($arParams["PATH_TO_CANCEL"]) <= 0)
	$arParams["PATH_TO_CANCEL"] = htmlspecialchars($APPLICATION->GetCurPage()."?"."ID=#ID#");

$arParams["PATH_TO_PAYMENT"] = Trim($arParams["PATH_TO_PAYMENT"]);
if (strlen($arParams["PATH_TO_PAYMENT"]) <= 0)
	$arParams["PATH_TO_PAYMENT"] = "payment.php";

$arParams["PATH_TO_CANCEL"] .= (strpos($arParams["PATH_TO_CANCEL"], "?") === false ? "?" : "&");

if($arParams["SET_TITLE"] == 'Y')
	$APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("SPOD_TITLE")));

if ($ID <= 0)
	LocalRedirect($arParams["PATH_TO_LIST"]);

//Preparing filter
$arFilter = Array(
		"USER_ID" 	=> $USER->GetID(),
		"ID" 		=> $ID,
	);

$dbOrder = CSaleOrder::GetList(Array("ID" => "ASC"), $arFilter);
if($arOrder = $dbOrder->GetNext())
{
// UnitellerPlugin add
	// Определяет ID обработчика платёжной системы Uniteller
	$uniteller_payment_id = -1;
	$dbPaySystem = CSalePaySystem::GetList();
	while ($arPaySystem = $dbPaySystem->Fetch()) {
		if (strtolower($arPaySystem['NAME']) == 'uniteller') {
			$uniteller_payment_id = (int)$arPaySystem['ID'];
		}
	}
	if ($arOrder['PAY_SYSTEM_ID'] == $uniteller_payment_id) {
		ps_uniteller::doSyncStatus($arOrder);
		$arOrder = CSaleOrder::GetByID($arOrder['ID']);
	}
// /UnitellerPlugin add
	$arResult = $arOrder;
	$arResult["WEIGHT_UNIT"] = htmlspecialchars(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
	$arResult["WEIGHT_KOEF"] = htmlspecialchars(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));

	$arResult["PRICE_FORMATED"] = SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]);
	if(DoubleVal($arOrder["DISCOUNT_VALUE"]) > 0)
		$arResult["DISCOUNT_VALUE_FORMATED"] = SaleFormatCurrency($arOrder["DISCOUNT_VALUE"], $arOrder["CURRENCY"]);
	$arResult["CAN_CANCEL"] = (($arOrder["CANCELED"]!="Y" && $arOrder["STATUS_ID"]!="F" && $arOrder["PAYED"]!="Y") ? "Y" : "N");
	if($arResult["CAN_CANCEL"] == "Y")
		$arResult["URL_TO_CANCEL"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_CANCEL"], Array("ID" => $arOrder["ID"])).'CANCEL=Y';
	$arResult["URL_TO_LIST"] = $arParams["PATH_TO_LIST"];
	$arResult["SITE_ID"] = $arOrder["LID"];
	$arCurrentStatus = CSaleStatus::GetByID($arOrder["STATUS_ID"]);
	$arResult["STATUS"] = $arCurrentStatus;
	$arResult["STATUS"]["NAME"] = htmlspecialcharsEx($arResult["STATUS"]["NAME"]);
	if (DoubleVal($arOrder["SUM_PAID"]) > 0)
		$arResult["SUM_PAID_FORMATED"] = SaleFormatCurrency($arOrder["SUM_PAID"], $arOrder["CURRENCY"]);
	$dbUser = CUser::GetByID($arOrder["USER_ID"]);
	if($arUser = $dbUser->GetNext())
	{
		$arResult["USER"] = $arUser;
		$arResult["USER_NAME"] = $arUser["NAME"].((strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0) ? "" : " ").$arUser["LAST_NAME"];
	}
	$arPersonType = CSalePersonType::GetByID($arOrder["PERSON_TYPE_ID"]);
	$arResult["PERSON_TYPE"] = $arPersonType;
	$arResult["PERSON_TYPE"]["NAME"] = htmlspecialcharsEx($arResult["PERSON_TYPE"]["NAME"]);

	$dbOrderProps = CSaleOrderPropsValue::GetOrderProps($ID);
	$iGroup = -1;
	while ($arOrderProps = $dbOrderProps->GetNext())
	{

		if(empty($arParams["PROP_".$arOrder["PERSON_TYPE_ID"]]) || !in_array($arOrderProps["ORDER_PROPS_ID"], $arParams["PROP_".$arOrder["PERSON_TYPE_ID"]]))
		{
			$arOrderPropsTmp = Array();
			$arOrderPropsTmp = $arOrderProps;

			if ($iGroup != IntVal($arOrderProps["PROPS_GROUP_ID"]))
			{
				$arOrderPropsTmp["SHOW_GROUP_NAME"] = "Y";
				$iGroup = IntVal($arOrderProps["PROPS_GROUP_ID"]);
			}
			if ($arOrderProps["TYPE"] == "SELECT" || $arOrderProps["TYPE"] == "RADIO")
			{
				$arVal = CSaleOrderPropsVariant::GetByValue($arOrderProps["ORDER_PROPS_ID"], $arOrderProps["VALUE"]);
				$arOrderPropsTmp["VALUE"] = htmlspecialcharsEx($arVal["NAME"]);
			}
			elseif ($arOrderProps["TYPE"] == "MULTISELECT")
			{
				$arOrderPropsTmp["VALUE"] = "";
				$curVal = explode(",", $arOrderProps["VALUE"]);
				for ($i = 0; $i < count($curVal); $i++)
				{
					$arVal = CSaleOrderPropsVariant::GetByValue($arOrderProps["ORDER_PROPS_ID"], $curVal[$i]);
					if ($i > 0)
						$arOrderPropsTmp["VALUE"] .= ", ";
					$arOrderPropsTmp["VALUE"] .= htmlspecialcharsEx($arVal["NAME"]);
				}
			}
			elseif ($arOrderProps["TYPE"] == "LOCATION")
			{
				$arVal = CSaleLocation::GetByID($arOrderProps["VALUE"], SITE_ID);
				$arOrderPropsTmp["VALUE"] = htmlspecialcharsEx($arVal["COUNTRY_NAME"].((strlen($arVal["COUNTRY_NAME"])<=0 || strlen($arVal["CITY_NAME"])<=0) ? "" : " - ").$arVal["CITY_NAME"]);
			}
			$arResult["ORDER_PROPS"][] = $arOrderPropsTmp;
		}
	}

	if (IntVal($arOrder["PAY_SYSTEM_ID"]) > 0)
	{
		$arPaySys = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"], $arOrder["PERSON_TYPE_ID"]);
		$arResult["PAY_SYSTEM"] = $arPaySys;
		$arResult["PAY_SYSTEM"]["NAME"] = htmlspecialcharsEx($arResult["PAY_SYSTEM"]["NAME"]);
	}

	if ($arOrder["PAYED"] != "Y" && $arOrder["CANCELED"] != "Y")
	{
		if (IntVal($arOrder["PAY_SYSTEM_ID"]) > 0)
		{
			$dbPaySysAction = CSalePaySystemAction::GetList(
					array(),
					array(
							"PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
							"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"]
						),
					false,
					false,
					array("NAME", "ACTION_FILE", "NEW_WINDOW", "PARAMS", "ENCODING")
				);
			if ($arPaySysAction = $dbPaySysAction->Fetch())
			{
				if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
				{
					$arResult["CAN_REPAY"] = "Y";
					if ($arPaySysAction["NEW_WINDOW"] == "Y")
					{
						$arResult["PAY_SYSTEM"]["PSA_ACTION_FILE"] = htmlspecialchars($arParams["PATH_TO_PAYMENT"]).'?ORDER_ID='.$ID;
					}
					else
					{
						CSalePaySystemAction::InitParamArrays($arOrder, $ID, $arPaySysAction["PARAMS"]);

						$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];
						$pathToAction = str_replace("\\", "/", $pathToAction);
						while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
							$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);
						if (file_exists($pathToAction))
						{
							if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
								$pathToAction .= "/payment.php";
							$arResult["PAY_SYSTEM"]["PSA_ACTION_FILE"] = $pathToAction;
						}

						if(strlen($arPaySysAction["ENCODING"]) > 0)
						{
							define("BX_SALE_ENCODING", $arPaySysAction["ENCODING"]);
							AddEventHandler("main", "OnEndBufferContent", "ChangeEncoding");
							function ChangeEncoding($content)
							{
								global $APPLICATION;
								header("Content-Type: text/html; charset=".BX_SALE_ENCODING);
								$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
								$content = str_replace("charset=".SITE_CHARSET, "charset=".BX_SALE_ENCODING, $content);
							}
						}

					}
				}
			}
		}
	}

	if (strpos($arOrder["DELIVERY_ID"], ":") !== false)
	{
		$arId = explode(":", $arOrder["DELIVERY_ID"]);
		$dbDelivery = CSaleDeliveryHandler::GetBySID($arId[0]);
		$arResult["DELIVERY"] = $dbDelivery->Fetch();

		$arResult["DELIVERY"]["NAME"] = htmlspecialcharsEx($arResult["DELIVERY"]["NAME"]." (".$arResult["DELIVERY"]["PROFILES"][$arId[1]]["TITLE"].")");
	}
	elseif (IntVal($arOrder["DELIVERY_ID"]) > 0)
	{
		$arDelivery = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
		$arResult["DELIVERY"] = $arDelivery;
		$arResult["DELIVERY"]["NAME"] = htmlspecialcharsEx($arResult["DELIVERY"]["NAME"]);
	}

	$dbBasket = CSaleBasket::GetList(
			array("NAME" => "ASC"),
			array("ORDER_ID" => $ID),
			false,
			false,
			array("ID", "DETAIL_PAGE_URL", "NAME", "NOTES", "QUANTITY", "PRICE", "CURRENCY", "PRODUCT_ID", "DISCOUNT_PRICE", "WEIGHT")
		);
	$arResult["BASKET"] = Array();
	while ($arBasket = $dbBasket->Fetch())
	{
		$arBasketTmp = Array();
		$arBasketTmp = $arBasket;
		$arBasketTmp["QUANTITY"] = DoubleVal($arBasketTmp["QUANTITY"]);
		$arBasketTmp["WEIGHT_FORMATED"] = DoubleVal($arBasketTmp["WEIGHT"]/$arResult["WEIGHT_KOEF"])." ".$arResult["WEIGHT_UNIT"];

		$arBasketTmp["PRICE_FORMATED"] = SaleFormatCurrency($arBasket["PRICE"], $arBasket["CURRENCY"]);
		$arResult["ORDER_WEIGHT"] += $arBasket["WEIGHT"] * $arBasket["QUANTITY"];
		if(DoubleVal($arBasketTmp["DISCOUNT_PRICE"]) > 0)
		{
			$arBasketTmp["DISCOUNT_PRICE_PERCENT"] = $arBasketTmp["DISCOUNT_PRICE"]*100 / ($arBasketTmp["DISCOUNT_PRICE"] + $arBasketTmp["PRICE"]);
			$arBasketTmp["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arBasketTmp["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
		}

		$arBasketTmp["PROPS"] = Array();
		$dbBasketProps = CSaleBasket::GetPropsList(
				array("SORT" => "ASC", "ID" => "DESC"),
				array(
						"BASKET_ID" => $arBasketTmp["ID"],
						"!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")
					),
				false,
				false,
				array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
		);
		while($arBasketProps = $dbBasketProps->GetNext())
		{
			$arBasketTmp["PROPS"][] = $arBasketProps;
		}
		$arResult["BASKET"][] = $arBasketTmp;
	}

	$dbTaxList = CSaleOrderTax::GetList(
			array("APPLY_ORDER" => "ASC"),
			array("ORDER_ID" => $ID)
		);
	$arResult["TAX_LIST"] = Array();
	while ($arTaxList = $dbTaxList->GetNext())
	{
		if ($arTaxList["IS_IN_PRICE"]=="Y")
			$arTaxList["VALUE_FORMATED"] = " (".(($arTaxList["IS_PERCENT"]=="Y") ? "".DoubleVal($arTaxList["VALUE"])."%, " : "").GetMessage("SALE_TAX_INPRICE").")";
		else
			$arTaxList["VALUE_FORMATED"] = " (".(($arTaxList["IS_PERCENT"]=="Y") ? "".DoubleVal($arTaxList["VALUE"])."%" : "").")";
		if(DoubleVal($arTaxList["VALUE_MONEY"]) > 0)
			$arTaxList["VALUE_MONEY_FORMATED"] = SaleFormatCurrency($arTaxList["VALUE_MONEY"], $arOrder["CURRENCY"]);
		$arResult["TAX_LIST"][] = $arTaxList;
	}
	$arResult["TAX_VALUE_FORMATED"] = SaleFormatCurrency($arOrder["TAX_VALUE"], $arOrder["CURRENCY"]);
	if(DoubleVal($arOrder["PRICE_DELIVERY"]) > 0)
	$arResult["PRICE_DELIVERY_FORMATED"] = SaleFormatCurrency($arOrder["PRICE_DELIVERY"], $arOrder["CURRENCY"]);
	if(DoubleVal($arResult["ORDER_WEIGHT"]) > 0)
		$arResult["ORDER_WEIGHT_FORMATED"] = DoubleVal($arResult["ORDER_WEIGHT"]/$arResult["WEIGHT_KOEF"])." ".$arResult["WEIGHT_UNIT"];
}
else
	$arResult["ERROR_MESSAGE"] = str_replace("#ID#", $ID, GetMessage("SPOD_NO_ORDER"));

$this->IncludeComponentTemplate();
?>