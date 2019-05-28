<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SAP_MODULE_NOT_INSTALL"));
	return;
}
if (!CBXFeatures::IsFeatureEnabled('SaleAccounts'))
	return;

$arAmount = unserialize(COption::GetOptionString("sale", "pay_amount", 'a:4:{i:1;a:2:{s:6:"AMOUNT";s:2:"10";s:8:"CURRENCY";s:3:"EUR";}i:2;a:2:{s:6:"AMOUNT";s:2:"20";s:8:"CURRENCY";s:3:"EUR";}i:3;a:2:{s:6:"AMOUNT";s:2:"30";s:8:"CURRENCY";s:3:"EUR";}i:4;a:2:{s:6:"AMOUNT";s:2:"40";s:8:"CURRENCY";s:3:"EUR";}}'));
if(empty($arParams["SELL_AMOUNT"]))
{
	$arResult["PAY_ACCOUNT_AMOUNT"] = $arAmount;
}
else
{
	foreach($arParams["SELL_AMOUNT"] as $val)
	{
		if(!empty($arAmount[$val]))
			$arResult["PAY_ACCOUNT_AMOUNT"][$val] = $arAmount[$val];
	}
}

$arParams["SELL_CURRENCY"] = strlen($arParams["SELL_CURRENCY"])>0 ? trim($arParams["SELL_CURRENCY"]): "";
if(strLen($arParams["VAR"])<=0)
	$arParams["VAR"] = "buyMoney";
if(strLen($arParams["CALLBACK_NAME"])<=0)
	$arParams["CALLBACK_NAME"] = "PayUserAccountDeliveryOrderCallback";
$arParams["PATH_TO_BASKET"] = Trim($arParams["PATH_TO_BASKET"]);
if (strlen($arParams["PATH_TO_BASKET"]) <= 0)
	$arParams["PATH_TO_BASKET"] = "/personal/basket.php";
if($arParams["SET_TITLE"]!="N")
	$APPLICATION->SetTitle(GetMessage('SAP_TITLE'));
if($arParams["REDIRECT_TO_CURRENT_PAGE"]=="Y")
	$arResult["CURRENT_PAGE"] = htmlspecialcharsEx($APPLICATION->GetCurPageParam());

if(CModule::IncludeModule("currency"))
	$baseCurrency = CCurrency::GetBaseCurrency();

if(strlen($_REQUEST[$arParams["VAR"]]) > 0)
{
	$productID = $_REQUEST[$arParams["VAR"]];
	if(!empty($arResult["PAY_ACCOUNT_AMOUNT"][$productID]))
	{
		$price = $arResult["PAY_ACCOUNT_AMOUNT"][$productID]["AMOUNT"];
		$currency = $arResult["PAY_ACCOUNT_AMOUNT"][$productID]["CURRENCY"];

		$tmpPrice = $price;
		$tmpCurrency = $currency;
		if($currency != $arParams["SELL_CURRENCY"] && strlen($arParams["SELL_CURRENCY"]) > 0)
		{
			$tmpPrice = CCurrencyRates::ConvertCurrency($price, $currency, $arParams["SELL_CURRENCY"]);
			$tmpCurrency = $arParams["SELL_CURRENCY"];
		}
		elseif($currency != $baseCurrency)
		{
			$tmpPrice = CCurrencyRates::ConvertCurrency($price, $currency, $baseCurrency);
			$tmpCurrency = $baseCurrency;
		}
			
		$arFields = array(
			"PRODUCT_ID" => $productID,
			"PRICE" => $tmpPrice,
			"CURRENCY" => $tmpCurrency,
			"QUANTITY" => 1,
			"LID" => SITE_ID,
			"DELAY" => "N",
			"CAN_BUY" => "Y",
			"NAME" => str_replace("#SUM#", SaleFormatCurrency($price, $currency), GetMessage("SAP_BASKET_NAME")),
			"MODULE" => "sale",
			"PAY_CALLBACK_FUNC" => $arParams["CALLBACK_NAME"]
		);

		$basketID = CSaleBasket::Add($arFields);
		if ($basketID)
		{
			if (CModule::IncludeModule("statistic"))
				CStatistic::Set_Event("sale2basket", "sale", $productID);

			if($arParams["REDIRECT_TO_CURRENT_PAGE"] == "Y")
				LocalRedirect($arResult["CURRENT_PAGE"]);
			elseif($arParams["REDIRECT_TO_CURRENT_PAGE"] != "Y")
				LocalRedirect($arParams["PATH_TO_BASKET"]);
		}
		else
		{
			$arResult["errorMessage"] = GetMessage("SAP_ERROR_ADD_BASKET")."<br>";
			if ($ex = $GLOBALS["APPLICATION"]->GetException())
				$arResult["errorMessage"] .= $ex->GetString();
		}
	}
	else
	{
		$arResult["errorMessage"] = GetMessage("SAP_WRONG_ID");
	}
}

foreach($arResult["PAY_ACCOUNT_AMOUNT"] as $k => $v)
{
	$tmp = $v;
	if(strlen($arParams["SELL_CURRENCY"]) > 0)
	{
		if($v["CURRENCY"] != $arParams["SELL_CURRENCY"])
			$tmp = Array("AMOUNT" => CCurrencyRates::ConvertCurrency($v["AMOUNT"], $v["CURRENCY"], $arParams["SELL_CURRENCY"]), "CURRENCY" => $arParams["SELL_CURRENCY"]);
	}
	$arResult["AMOUNT_TO_SHOW"][] = Array(
			"ID" => $k, 
			"NAME" => SaleFormatCurrency($tmp["AMOUNT"], $tmp["CURRENCY"]), 
			"LINK" => $APPLICATION->GetCurPageParam($arParams["VAR"]."=".$k, Array("buyMoney"))
		);
}

if(!empty($arResult["AMOUNT_TO_SHOW"]))
	$this->IncludeComponentTemplate();
?>