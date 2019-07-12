<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// получаем сумму товаров в корзине
$arResult["SUM"] = 0;
if(!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

$arBasketItems = array();

$dbBasketItems = CSaleBasket::GetList(
		array(
				"NAME" => "ASC",
				"ID" => "ASC"
		),
		array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,"DELAY" => "N",
				"ORDER_ID" => "NULL"
		),
		false,
		false,
		array("ID", "CALLBACK_FUNC", "MODULE",
				"PRODUCT_ID", "QUANTITY", "DELAY",
				"CAN_BUY", "PRICE", "WEIGHT")
);
while ($arItems = $dbBasketItems->Fetch())
{
	/*if (strlen($arItems["CALLBACK_FUNC"]) > 0)
	{
		CSaleBasket::UpdatePrice($arItems["ID"],
				$arItems["CALLBACK_FUNC"],
				$arItems["MODULE"],
				$arItems["PRODUCT_ID"],
				$arItems["QUANTITY"]);
		$arItems = CSaleBasket::GetByID($arItems["ID"]);
	}*/
	if ($arItems["DELAY"] == "N" && $arItems["CAN_BUY"] == "Y") {
		$arResult["SUM"] = $arResult["SUM"] + ($arItems["PRICE"]*$arItems["QUANTITY"]);
	}
	//deb($arItems);
	//$arBasketItems[] = $arItems;
}

// получаем базовую валюту
$baseCurrency = CCurrency::GetBaseCurrency();
$arResult["CURRENCY"] = getCurrencyAbbr($baseCurrency);
$arResult["SUM"] = number_format($arResult["SUM"], 0, ".", " ");
