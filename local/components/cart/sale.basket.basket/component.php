<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

$arParams["PATH_TO_ORDER"] = Trim($arParams["PATH_TO_ORDER"]);
if (strlen($arParams["PATH_TO_ORDER"]) <= 0)
	$arParams["PATH_TO_ORDER"] = "order.php";

if($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("SBB_TITLE"));

if (!isset($arParams["COLUMNS_LIST"]) || !is_array($arParams["COLUMNS_LIST"]) || count($arParams["COLUMNS_LIST"]) <= 0)
	$arParams["COLUMNS_LIST"] = array("NAME", "PRICE", "TYPE", "QUANTITY", "DELETE", "DELAY", "WEIGHT");

$arParams["HIDE_COUPON"] = (($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N");
if (!CModule::IncludeModule("catalog"))
	$arParams["HIDE_COUPON"] = "Y";

if (!isset($arParams['QUANTITY_FLOAT']))
	$arParams['QUANTITY_FLOAT'] = 'N';
$arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] = (($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N");


//$arParams['PRICE_VAT_INCLUDE'] = $arParams['PRICE_VAT_INCLUDE'] == 'N' ? 'N' : 'Y';
$arParams['PRICE_VAT_SHOW_VALUE'] = $arParams['PRICE_VAT_SHOW_VALUE'] == 'N' ? 'N' : 'Y';
$arParams["USE_PREPAYMENT"] = $arParams["USE_PREPAYMENT"] == 'Y' ? 'Y' : 'N';

$arParams["WEIGHT_UNIT"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
$arParams["WEIGHT_KOEF"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));

$arResult["WARNING_MESSAGE"] = Array();

$GLOBALS['CATALOG_ONETIME_COUPONS_BASKET'] = null;
$GLOBALS['CATALOG_ONETIME_COUPONS_ORDER']=null;

if (strlen($_REQUEST["BasketRefresh"]) > 0 || strlen($_REQUEST["BasketOrder"]) > 0)
{
	if ($arParams["HIDE_COUPON"] != "Y")
	{
		$COUPON = Trim($_REQUEST["COUPON"]);
		if (strlen($COUPON) > 0)
			CCatalogDiscount::SetCoupon($COUPON);
		else
			CCatalogDiscount::ClearCoupon();
	}

	$dbBasketItems = CSaleBasket::GetList(
			array("PRICE" => "DESC"),
			array(
					"FUSER_ID" => CSaleBasket::GetBasketUserID(),
					"LID" => SITE_ID,
					"ORDER_ID" => "NULL"
				),
			false,
			false,
			array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "CURRENCY", "SUBSCRIBE")
		);
	while ($arBasketItems = $dbBasketItems->Fetch())
	{
		$arBasketItems['QUANTITY'] = $arParams['QUANTITY_FLOAT'] == 'Y' ? DoubleVal($arBasketItems['QUANTITY']) : IntVal($arBasketItems['QUANTITY']);

		if(!isset($_REQUEST["QUANTITY_".$arBasketItems["ID"]]))
			$quantityTmp = $arBasketItems['QUANTITY'];
		else
			$quantityTmp = $arParams['QUANTITY_FLOAT'] == 'Y' ? DoubleVal($_REQUEST["QUANTITY_".$arBasketItems["ID"]]) : IntVal($_REQUEST["QUANTITY_".$arBasketItems["ID"]]);

		$deleteTmp = (($_REQUEST["DELETE_".$arBasketItems["ID"]] == "Y") ? "Y" : "N");
		$delayTmp = (($_REQUEST["DELAY_".$arBasketItems["ID"]] == "Y") ? "Y" : "N");

		if ($deleteTmp == "Y" && in_array("DELETE", $arParams["COLUMNS_LIST"]))
		{
			if ($arBasketItems["SUBSCRIBE"] == "Y" && is_array($_SESSION["NOTIFY_PRODUCT"][$USER->GetID()]))
			{
				unset($_SESSION["NOTIFY_PRODUCT"][$USER->GetID()][$arBasketItems["PRODUCT_ID"]]);
			}
			CSaleBasket::Delete($arBasketItems["ID"]);
		}
		elseif ($arBasketItems["DELAY"] == "N" && $arBasketItems["CAN_BUY"] == "Y")
		{
			UnSet($arFields);
			$arFields = array();
			if (in_array("QUANTITY", $arParams["COLUMNS_LIST"]))
				$arFields["QUANTITY"] = $quantityTmp;
			if (in_array("DELAY", $arParams["COLUMNS_LIST"]))
				$arFields["DELAY"] = $delayTmp;

			if (count($arFields) > 0
				&&
					($arBasketItems["QUANTITY"] != $arFields["QUANTITY"] && in_array("QUANTITY", $arParams["COLUMNS_LIST"])
						|| $arBasketItems["DELAY"] != $arFields["DELAY"] && in_array("DELAY", $arParams["COLUMNS_LIST"]))
				)
				CSaleBasket::Update($arBasketItems["ID"], $arFields);
		}
		elseif ($arBasketItems["DELAY"] == "Y" && $arBasketItems["CAN_BUY"] == "Y")
		{
			UnSet($arFields);
			$arFields = array();
			if (in_array("DELAY", $arParams["COLUMNS_LIST"]))
				$arFields["DELAY"] = $delayTmp;

			if (count($arFields) > 0
				&&
					($arBasketItems["DELAY"] != $arFields["DELAY"] && in_array("DELAY", $arParams["COLUMNS_LIST"]))
				)
				CSaleBasket::Update($arBasketItems["ID"], $arFields);
		}
	}

	unset($_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]);

	if (strlen($_REQUEST["BasketOrder"]) > 0)
	{
		LocalRedirect($arParams["PATH_TO_ORDER"]);
	}
	else
	{
		unset($_REQUEST["BasketRefresh"]);
		unset($_REQUEST["BasketOrder"]);
		LocalRedirect($APPLICATION->GetCurPage());
	}
}

CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);

$bShowReady = False;
$bShowDelay = False;
$bShowSubscribe = False;
$bShowNotAvail = False;
$allSum = 0;
$allWeight = 0;
$allCurrency = CSaleLang::GetLangCurrency(SITE_ID);
$allVATSum = 0;

$arResult["ITEMS"]["AnDelCanBuy"] = Array();
$arResult["ITEMS"]["DelDelCanBuy"] = Array();
$arResult["ITEMS"]["nAnCanBuy"] = Array();
$arResult["ITEMS"]["ProdSubscribe"] = Array();
$DISCOUNT_PRICE_ALL = 0;

$arBasketItems = array();
$dbBasketItems = CSaleBasket::GetList(
		array(
				"NAME" => "ASC",
				"ID" => "ASC"
			),
		array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL"
			),
		false,
		false,
		array("ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID", "PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE")
	);
while ($arItems = $dbBasketItems->GetNext())
{
	$arItems['QUANTITY'] = $arParams['QUANTITY_FLOAT'] == 'Y' ? number_format(DoubleVal($arItems['QUANTITY']), 2, '.', '') : IntVal($arItems['QUANTITY']);

	$arItems["PROPS"] = Array();
	if(in_array("PROPS", $arParams["COLUMNS_LIST"]))
	{
		$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arItems["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
		while($arProp = $dbProp -> GetNext())
			$arItems["PROPS"][] = $arProp;
	}

	$arItems["PRICE_VAT_VALUE"] = (($arItems["PRICE"] / ($arItems["VAT_RATE"] +1)) * $arItems["VAT_RATE"]);
	$arItems["PRICE_FORMATED"] = SaleFormatCurrency($arItems["PRICE"], $arItems["CURRENCY"]);
	$arItems["WEIGHT"] = DoubleVal($arItems["WEIGHT"]);
	$arItems["WEIGHT_FORMATED"] = roundEx(DoubleVal($arItems["WEIGHT"]/$arParams["WEIGHT_KOEF"]), SALE_VALUE_PRECISION)." ".$arParams["WEIGHT_UNIT"];

	if ($arItems["DELAY"] == "N" && $arItems["CAN_BUY"] == "Y")
	{
		$allSum += ($arItems["PRICE"] * $arItems["QUANTITY"]);
		$allWeight += ($arItems["WEIGHT"] * $arItems["QUANTITY"]);
		$allVATSum += roundEx($arItems["PRICE_VAT_VALUE"] * $arItems["QUANTITY"], SALE_VALUE_PRECISION);
	}

	if ($arItems["DELAY"] == "N" && $arItems["CAN_BUY"] == "Y")
	{
		$bShowReady = True;
		if(DoubleVal($arItems["DISCOUNT_PRICE"]) > 0)
		{
			$arItems["DISCOUNT_PRICE_PERCENT"] = $arItems["DISCOUNT_PRICE"]*100 / ($arItems["DISCOUNT_PRICE"] + $arItems["PRICE"]);
			$arItems["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arItems["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
			$DISCOUNT_PRICE_ALL += $arItems["DISCOUNT_PRICE"] * $arItems["QUANTITY"];
		}
		$arResult["ITEMS"]["AnDelCanBuy"][] = $arItems;
	}
	elseif ($arItems["DELAY"] == "Y" && $arItems["CAN_BUY"] == "Y")
	{
		$bShowDelay = True;
		$arResult["ITEMS"]["DelDelCanBuy"][] = $arItems;
	}
	elseif ($arItems["CAN_BUY"] == "N" && $arItems["SUBSCRIBE"] == "Y")
	{
		$bShowSubscribe = True;
		$arResult["ITEMS"]["ProdSubscribe"][] = $arItems;
	}
	else
	{
		$bShowNotAvail = True;
		$arResult["ITEMS"]["nAnCanBuy"][] = $arItems;
	}

	$arBasketItems[] = $arItems;
}

$arResult["ShowReady"] = (($bShowReady)?"Y":"N");
$arResult["ShowDelay"] = (($bShowDelay)?"Y":"N");
$arResult["ShowNotAvail"] = (($bShowNotAvail)?"Y":"N");
$arResult["ShowSubscribe"] = (($bShowSubscribe)?"Y":"N");

$arOrder = array(
	'SITE_ID' => SITE_ID,
	'USER_ID' => $USER->GetID(),
	'ORDER_PRICE' => $allSum,
	'ORDER_WEIGHT' => $allWeight,
	'BASKET_ITEMS' => $arResult["ITEMS"]["AnDelCanBuy"]
);

$arOptions = array(
	'COUNT_DISCOUNT_4_ALL_QUANTITY' => $arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"],
);

$arErrors = array();

CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

$allSum = 0;
$allWeight = 0;
$allVATSum = 0;

$DISCOUNT_PRICE_ALL = 0;
foreach ($arOrder['BASKET_ITEMS'] as &$arOneItem)
{
	$allSum += ($arOneItem["PRICE"] * $arOneItem["QUANTITY"]);
	$allWeight += ($arOneItem["WEIGHT"] * $arOneItem["QUANTITY"]);
	$arOneItem["PRICE_VAT_VALUE"] = 0;
	if (array_key_exists('VAT_VALUE', $arOneItem))
		$arOneItem["PRICE_VAT_VALUE"] = $arOneItem["VAT_VALUE"];
	$allVATSum += roundEx($arOneItem["PRICE_VAT_VALUE"] * $arOneItem["QUANTITY"], SALE_VALUE_PRECISION);
	$arOneItem["PRICE_FORMATED"] = SaleFormatCurrency($arOneItem["PRICE"], $arOneItem["CURRENCY"]);
	$arOneItem["DISCOUNT_PRICE_PERCENT"] = $arOneItem["DISCOUNT_PRICE"]*100 / ($arOneItem["DISCOUNT_PRICE"] + $arOneItem["PRICE"]);
	$arOneItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arOneItem["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
	$DISCOUNT_PRICE_ALL += $arOneItem["DISCOUNT_PRICE"] * $arOneItem["QUANTITY"];
}
if (isset($arOneItem))
	unset($arOneItem);

$arResult["ITEMS"]["AnDelCanBuy"] = $arOrder['BASKET_ITEMS'];

$arResult["allSum"] = $allSum;
$arResult["allWeight"] = $allWeight;
$arResult["allWeight_FORMATED"] = roundEx(DoubleVal($allWeight/$arParams["WEIGHT_KOEF"]), SALE_VALUE_PRECISION)." ".$arParams["WEIGHT_UNIT"];
$arResult["allSum_FORMATED"] = SaleFormatCurrency($allSum, $allCurrency);
$arResult["DISCOUNT_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DISCOUNT_PRICE"], $allCurrency);

if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y')
{
	$arResult["allVATSum"] = $allVATSum;
	$arResult["allVATSum_FORMATED"] = SaleFormatCurrency($allVATSum, $allCurrency);
	$arResult["allSum_wVAT_FORMATED"] = SaleFormatCurrency($arResult["allSum_wVAT"], $allCurrency);
}

if ($arParams["HIDE_COUPON"] != "Y")
	$arCoupons = CCatalogDiscount::GetCoupons();

if (count($arCoupons) > 0)
	$arResult["COUPON"] = htmlspecialcharsbx($arCoupons[0]);
if(count($arBasketItems)<=0)
	$arResult["ERROR_MESSAGE"] = GetMessage("SALE_EMPTY_BASKET");

$arResult["DISCOUNT_PRICE_ALL"] = $DISCOUNT_PRICE_ALL;
$arResult["DISCOUNT_PRICE_ALL_FORMATED"] = SaleFormatCurrency($DISCOUNT_PRICE_ALL, $allCurrency);


if($arParams["USE_PREPAYMENT"] == "Y")
{
	if(doubleval($arResult["allSum"]) > 0)
	{
		$personType = array();
		$dbPersonType = CSalePersonType::GetList(Array("SORT" => "ASC", "NAME" => "ASC"), Array("LID" => SITE_ID, "ACTIVE" => "Y"));
		while($arPersonType = $dbPersonType->GetNext())
		{
			$personType[] = $arPersonType["ID"];
		}

		if(!empty($personType))
		{
			$dbPaySysAction = CSalePaySystemAction::GetList(
					array(),
					array(
							"PS_ACTIVE" => "Y",
							"HAVE_PREPAY" => "Y",
							"PERSON_TYPE_ID" => $personType,
						),
					false,
					false,
					array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "NAME", "ACTION_FILE", "RESULT_FILE", "NEW_WINDOW", "PARAMS", "ENCODING", "LOGOTIP")
				);
			if ($arPaySysAction = $dbPaySysAction->Fetch())
			{
				CSalePaySystemAction::InitParamArrays(false, false, $arPaySysAction["PARAMS"]);

				$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];

				$pathToAction = str_replace("\\", "/", $pathToAction);
				while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
					$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

				if (file_exists($pathToAction))
				{
					if (is_dir($pathToAction) && file_exists($pathToAction."/pre_payment.php"))
						$pathToAction .= "/pre_payment.php";

					include_once($pathToAction);
					$psPreAction = new CSalePaySystemPrePayment;
					if($psPreAction->init())
					{
						$orderData = array(
								"PATH_TO_ORDER" => $arParams["PATH_TO_ORDER"],
								"AMOUNT" => $arResult["allSum"],
							);
						if(!$psPreAction->BasketButtonAction($orderData))
						{
							if($e = $APPLICATION->GetException())
								$arResult["WARNING_MESSAGE"][] = $e->GetString();
						}

						$arResult["PREPAY_BUTTON"] = $psPreAction->BasketButtonShow();
					}
				}

			}
		}
	}
}

$this->IncludeComponentTemplate();
?>