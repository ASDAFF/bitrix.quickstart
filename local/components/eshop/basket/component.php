<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("BEONO_BASKET_MODULE_NOT_INSTALL"));
	return;
}

$arParams["PATH_TO_ORDER"] = Trim($arParams["PATH_TO_ORDER"]);
if (strlen($arParams["PATH_TO_ORDER"]) <= 0) {
	$arParams["PATH_TO_ORDER"] = "order.php";
}


if (!isset($arParams["COLUMNS_LIST"]) || !is_array($arParams["COLUMNS_LIST"]) || count($arParams["COLUMNS_LIST"]) <= 0)
	$arParams["COLUMNS_LIST"] = array("NAME", "PRICE", "TYPE", "QUANTITY", "DELETE", "DELAY", "WEIGHT");

$arParams["HIDE_COUPON"] = (($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N");
if (!CModule::IncludeModule("catalog"))
	$arParams["HIDE_COUPON"] = "Y";

if (!isset($arParams['QUANTITY_FLOAT']))
	$arParams['QUANTITY_FLOAT'] = 'N';
$arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] = (($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N");

$arParams['PRICE_VAT_SHOW_VALUE'] = $arParams['PRICE_VAT_SHOW_VALUE'] == 'N' ? 'N' : 'Y';

$arParams["WEIGHT_UNIT"] = htmlspecialchars(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
$arParams["WEIGHT_KOEF"] = htmlspecialchars(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));

$arResult["WARNING_MESSAGE"] = Array();

$GLOBALS['CATALOG_ONETIME_COUPONS_BASKET'] = null;

if ((strlen($_REQUEST["basketrefresh"]) > 0 || strlen($_REQUEST["basketorder"]) > 0) && check_bitrix_sessid())
{
	if ($arParams["HIDE_COUPON"] != "Y")
	{
		$COUPON = Trim($_POST["coupon"]);
		if (strlen($COUPON) > 0) {
			if(!CCatalogDiscount::SetCoupon($COUPON)) {
				$arResult["WARNING_MESSAGE"][] = GetMessage('BEONO_BASKET_WRONG_COUPON');
			}
		} else {
			CCatalogDiscount::ClearCoupon();
		}
	}

	$dbBasketItems = CSaleBasket::GetList(
			array("PRICE" => "DESC"),
			array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
			false,
			false,
			array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "CURRENCY")
		);
	while ($arBasketItems = $dbBasketItems->Fetch())
	{
		$arBasketItems['QUANTITY'] = $arParams['QUANTITY_FLOAT'] == 'Y' ? DoubleVal($arBasketItems['QUANTITY']) : IntVal($arBasketItems['QUANTITY']);
		
		if (strlen($arBasketItems["CALLBACK_FUNC"])>0) {
			CSaleBasket::UpdatePrice($arBasketItems["ID"], $arBasketItems["CALLBACK_FUNC"], $arBasketItems["MODULE"], $arBasketItems["PRODUCT_ID"], $arBasketItems["QUANTITY"]);
			$arBasketItems = CSaleBasket::GetByID($arBasketItems["ID"]);
		}

		if ($_REQUEST['action'] == 'delete' && in_array($arBasketItems["ID"], $_REQUEST['action_items'])) {
			
			CSaleBasket::Delete($arBasketItems["ID"]);
			
		} elseif ($arBasketItems["DELAY"] == "N" && $arBasketItems["CAN_BUY"] == "Y") {
			$arFields = array();
			if ($_REQUEST['quantity'][$arBasketItems["ID"]] && in_array("QUANTITY", $arParams["COLUMNS_LIST"])) {
				$arFields["QUANTITY"] = doubleval($_REQUEST['quantity'][$arBasketItems["ID"]]);
				if ($arParams['QUANTITY_FLOAT'] == 'N') {
					$arFields["QUANTITY"] = intval($arFields["QUANTITY"]);
				}
			}
			if ($_REQUEST['action'] == 'delay' && in_array("DELAY", $arParams["COLUMNS_LIST"])) {
				$arFields["DELAY"] = (in_array($arBasketItems["ID"], $_REQUEST['action_items'])) ? "Y" : "N";
			}

			if ((isset($arFields["QUANTITY"]) && $arBasketItems["QUANTITY"] != $arFields["QUANTITY"]) || (isset($arFields["DELAY"]) && $arBasketItems["DELAY"] != $arFields["DELAY"])) {
				CSaleBasket::Update($arBasketItems["ID"], $arFields);
			}
		}
		elseif ($arBasketItems["DELAY"] == "Y" && $arBasketItems["CAN_BUY"] == "Y")
		{
			$arFields = array();
			if ($_REQUEST['action'] == 'recover' && in_array($arBasketItems["ID"], $_REQUEST['action_items']) && in_array("DELAY", $arParams["COLUMNS_LIST"])) {
				$arFields["DELAY"] = 'N';
				CSaleBasket::Update($arBasketItems["ID"], $arFields);
			}
		}
	}
	
	unset($_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]);
	
	if (strlen($_REQUEST["basketorder"]) > 0)
	{
		if (empty($arResult["WARNING_MESSAGE"])) {
			LocalRedirect($arParams["PATH_TO_ORDER"]);
		}
	}
	else
	{
		unset($_REQUEST["basketrefresh"]);
		unset($_REQUEST["basketorder"]);
		if (empty($arResult["WARNING_MESSAGE"])) {
			if ($_GET['tab']) {
				LocalRedirect($APPLICATION->GetCurPage().'?tab='.htmlspecialchars($_GET['tab']));
			} else {
				LocalRedirect($APPLICATION->GetCurPage());
			}
		}
	}
}

$arBasketItems = array();
$dbBasketItems = CSaleBasket::GetList(
		array("DATE_INSERT" => "ASC", "NAME" => "ASC", "ID" => "ASC"),
		array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
		false,
		false,
		array("ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "DATE_INSERT")
	);
$arProductsID = array();
while ($arItems = $dbBasketItems->GetNext())
{
	if (strlen($arItems["CALLBACK_FUNC"]) > 0)
	{
		CSaleBasket::UpdatePrice($arItems["ID"], $arItems["CALLBACK_FUNC"], $arItems["MODULE"], $arItems["PRODUCT_ID"], $arItems["QUANTITY"]);
		$arItems = CSaleBasket::GetByID($arItems["ID"]);

		if($ex = $APPLICATION->GetException())
		{
			$str = $ex->GetString();
			if(!in_array($str, $arResult["WARNING_MESSAGE"]))
				$arResult["WARNING_MESSAGE"][] = $ex->GetString();
		}
	}
	
	$arItems['QUANTITY'] = $arParams['QUANTITY_FLOAT'] == 'Y' ? number_format(DoubleVal($arItems['QUANTITY']), 2, '.', '') : IntVal($arItems['QUANTITY']);
	
	$arItems["PROPS"] = Array();
	if(in_array("PROPS", $arParams["COLUMNS_LIST"]))
	{
		$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arItems["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
		while($arProp = $dbProp -> GetNext())
			$arItems["PROPS"][] = $arProp;
	}
	
	$arBasketItems[] = $arItems;
	$arProductsId[] = $arItems["PRODUCT_ID"];
}


if (in_array("IMAGE", $arParams["COLUMNS_LIST"]) && !empty($arProductsId)) {
	CModule::IncludeModule('iblock');
	$rsProducts = CIBlockElement::GetList(Array(), Array("ID" => $arProductsId), false, false, Array("ID", "PREVIEW_PICTURE", "DETAIL_PICTURE"));
	while($arProduct = $rsProducts->GetNext())
	{
		if ($arProduct['PREVIEW_PICTURE']) {
			$arProductImage[$arProduct['ID']] = $arProduct['PREVIEW_PICTURE'];
		} else if ($arProduct['DETAIL_PICTURE']) {
			$arProductImage[$arProduct['ID']] = $arProduct['DETAIL_PICTURE'];
		}
		
	}
}

$bShowReady = False;
$bShowDelay = False;
$bShowNotAvail = False;
$allSum = 0;
$allWeight = 0;
$allCurrency = CSaleLang::GetLangCurrency(SITE_ID);
$allVATSum = 0;

for ($i = 0; $i < count($arBasketItems); $i++)
{
	if ($arBasketItems[$i]["DELAY"] == "N" && $arBasketItems[$i]["CAN_BUY"] == "Y")
		$bShowReady = True;
	elseif ($arBasketItems[$i]["DELAY"] == "Y" && $arBasketItems[$i]["CAN_BUY"] == "Y")
		$bShowDelay = True;
	elseif ($arBasketItems[$i]["CAN_BUY"] == "N")
		$bShowNotAvail = True;
		
	$arBasketItems[$i]['IMAGE'] = $arProductImage[$arBasketItems[$i]['PRODUCT_ID']];

	$arBasketItems[$i]["PRICE_VAT_VALUE"] = (($arBasketItems[$i]["PRICE"] / ($arBasketItems[$i]["VAT_RATE"] +1)) * $arBasketItems[$i]["VAT_RATE"]);

	$arBasketItems[$i]["PRICE_FORMATED"] = SaleFormatCurrency($arBasketItems[$i]["PRICE"], $arBasketItems[$i]["CURRENCY"]);
	$arBasketItems[$i]["TOTAL_PRICE_FORMATED"] = SaleFormatCurrency($arBasketItems[$i]["PRICE"] * $arBasketItems[$i]["QUANTITY"], $arBasketItems[$i]["CURRENCY"]);
	$arBasketItems[$i]["WEIGHT"] = DoubleVal($arBasketItems[$i]["WEIGHT"]);
	$arBasketItems[$i]["WEIGHT_FORMATED"] = roundEx(DoubleVal($arBasketItems[$i]["WEIGHT"]/$arParams["WEIGHT_KOEF"]), SALE_VALUE_PRECISION)." ".$arParams["WEIGHT_UNIT"];

	if ($arBasketItems[$i]["DELAY"] == "N" && $arBasketItems[$i]["CAN_BUY"] == "Y")
	{
		$allSum += ($arBasketItems[$i]["PRICE"] * $arBasketItems[$i]["QUANTITY"]);
		$allWeight += ($arBasketItems[$i]["WEIGHT"] * $arBasketItems[$i]["QUANTITY"]);
		$allVATSum += roundEx($arBasketItems[$i]["PRICE_VAT_VALUE"] * $arBasketItems[$i]["QUANTITY"], SALE_VALUE_PRECISION);
	}
}

$arResult["ITEMS"]["AnDelCanBuy"] = Array();
$arResult["ITEMS"]["DelDelCanBuy"] = Array();
$arResult["ITEMS"]["nAnCanBuy"] = Array();

foreach($arBasketItems as $val)
{
	$val['NAME'] = htmlspecialcharsEx($val['NAME']);
	$val['NOTES'] = htmlspecialcharsEx($val['NOTES']);

	if($bShowReady)
	{
		if($val["DELAY"] == "N" && $val["CAN_BUY"] == "Y")
		{
			if(in_array("DISCOUNT", $arParams["COLUMNS_LIST"]) && DoubleVal($val["DISCOUNT_PRICE"]) > 0)
			{
				$val["DISCOUNT_PRICE_PERCENT"] = $val["DISCOUNT_PRICE"]*100 / ($val["DISCOUNT_PRICE"] + $val["PRICE"]);
				$val["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($val["DISCOUNT_PRICE_PERCENT"], 0)."%";
			}
			$arResult["ITEMS"]["AnDelCanBuy"][] = $val;
		}
	}
	if($bShowDelay)
	{
		if($val["DELAY"] == "Y" && $val["CAN_BUY"] == "Y")
		{
			$arResult["ITEMS"]["DelDelCanBuy"][] = $val;
		}
	}
	if($bShowNotAvail)
	{
		if($val["CAN_BUY"] == "N")
		{
			$arResult["ITEMS"]["nAnCanBuy"][] = $val;
		}
	}
}

$arResult["ShowReady"] = (($bShowReady)?"Y":"N");
$arResult["ShowDelay"] = (($bShowDelay)?"Y":"N");
$arResult["ShowNotAvail"] = (($bShowNotAvail)?"Y":"N");

$dbDiscount = CSaleDiscount::GetList(
		array("SORT" => "ASC"),
		array(
				"LID" => SITE_ID,
				"ACTIVE" => "Y",
				"!>ACTIVE_FROM" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
				"!<ACTIVE_TO" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
				"<=PRICE_FROM" => $allSum,
				">=PRICE_TO" => $allSum
			),
		false,
		false,
		array("*")
	);
$arResult["DISCOUNT_PRICE"] = 0;
$arResult["DISCOUNT_PERCENT"] = 0;
if ($arDiscount = $dbDiscount->Fetch())
{
	if ($arDiscount["DISCOUNT_TYPE"] == "P")
	{
		$arResult["DISCOUNT_PERCENT"] = $arDiscount["DISCOUNT_VALUE"];
		for ($bi = 0; $bi < count($arResult["ITEMS"]["AnDelCanBuy"]); $bi++)
		{
			if($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y")
			{
				$curDiscount = roundEx(DoubleVal($arResult["ITEMS"]["AnDelCanBuy"][$bi]["PRICE"]) * DoubleVal($arResult["ITEMS"]["AnDelCanBuy"][$bi]["QUANTITY"]) * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
				$arResult["DISCOUNT_PRICE"] += $curDiscount;
			}
			else
			{
				$curDiscount = roundEx(DoubleVal($arResult["ITEMS"]["AnDelCanBuy"][$bi]["PRICE"]) * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
				$arResult["DISCOUNT_PRICE"] += $curDiscount * DoubleVal($arResult["ITEMS"]["AnDelCanBuy"][$bi]["QUANTITY"]);
			}
			$arResult["ITEMS"]["AnDelCanBuy"][$bi]["DISCOUNT_PRICE"] = DoubleVal($arResult["ITEMS"]["AnDelCanBuy"][$bi]["PRICE"]) - $curDiscount;
		}
		$arResult["DISCOUNT_PERCENT_FORMATED"] = DoubleVal($arResult["DISCOUNT_PERCENT"])."%";
		$arResult["DISCOUNT_VALUE_FORMATED"] = $arResult["DISCOUNT_PERCENT_FORMATED"];
	}
	else
	{
		$arResult["DISCOUNT_PRICE"] = CCurrencyRates::ConvertCurrency($arDiscount["DISCOUNT_VALUE"], $arDiscount["CURRENCY"], $allCurrency);
		$arResult["DISCOUNT_PRICE"] = roundEx($arResult["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);
		$arResult["DISCOUNT_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DISCOUNT_PRICE"], $allCurrency);
		$arResult["DISCOUNT_VALUE_FORMATED"] = $arResult["DISCOUNT_PRICE_FORMATED"];
	}
	$allSum = $allSum - $arResult["DISCOUNT_PRICE"];
}

$arResult["allSum"] = $allSum;
$arResult["allWeight"] = $allWeight;
$arResult["allWeight_FORMATED"] = roundEx(DoubleVal($allWeight/$arParams["WEIGHT_KOEF"]), SALE_VALUE_PRECISION)." ".$arParams["WEIGHT_UNIT"];
$arResult["allSum_FORMATED"] = SaleFormatCurrency($allSum, $allCurrency);

if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y')
{
	$arResult["allVATSum"] = $allVATSum;
	$arResult["allVATSum_FORMATED"] = SaleFormatCurrency($allVATSum, $allCurrency);
	$arResult["allSum_wVAT_FORMATED"] = SaleFormatCurrency($arResult["allSum_wVAT"], $allCurrency);
}

if ($arParams["HIDE_COUPON"] != "Y")
	$arCoupons = CCatalogDiscount::GetCoupons();
	
if (count($arCoupons) > 0)
	$arResult["COUPON"] = htmlspecialchars($arCoupons[0]);
if(count($arBasketItems)<=0)
	$arResult["ERROR_MESSAGE"] = GetMessage("BEONO_BASKET_EMPTY_BASKET");

$this->IncludeComponentTemplate();
?>