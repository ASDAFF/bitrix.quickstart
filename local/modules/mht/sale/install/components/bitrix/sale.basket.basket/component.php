<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixBasketComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
use Bitrix\Main\Loader;

if (!Loader::includeModule('sale'))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

include_once(dirname(__FILE__)."/functions.php");

$arResult["WARNING_MESSAGE"] = array();

$headersData = $this->getCustomColumns(); // custom product table columns

// BASKET REFRESH
if (strlen($_REQUEST["BasketRefresh"]) > 0 || strlen($_REQUEST["BasketOrder"]) > 0 || strlen($_REQUEST[$arParams["ACTION_VARIABLE"]]) > 0 )
{
	// todo: tmp hack until ajax recalculation is made
	if (isset($_REQUEST["BasketRefresh"]) && strlen($_REQUEST["BasketRefresh"]) > 0)
		unset($_REQUEST["BasketOrder"]);

	// if action is performed
	if (strlen($_REQUEST[$arParams["ACTION_VARIABLE"]]) > 0)
	{
		$id = intval($_REQUEST["id"]);
		if ($id > 0)
		{
			$dbBasketItems = CSaleBasket::GetList(
				array(),
				array(
					"FUSER_ID" => CSaleBasket::GetBasketUserID(),
					"LID" => SITE_ID,
					"ORDER_ID" => "NULL",
					"ID" => $id,
				),
				false,
				false,
				array('ID', 'DELAY', 'CAN_BUY', 'SET_PARENT_ID', 'TYPE')
			);
			$arItem = $dbBasketItems->Fetch();
			if ($arItem && !CSaleBasketHelper::isSetItem($arItem))
			{
				if ($_REQUEST[$arParams["ACTION_VARIABLE"]] == "delete" && in_array("DELETE", $arParams["COLUMNS_LIST"]))
				{
					CSaleBasket::Delete($arItem["ID"]);
				}
				elseif ($_REQUEST[$arParams["ACTION_VARIABLE"]] == "delay" && in_array("DELAY", $arParams["COLUMNS_LIST"]))
				{
					if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y")
						CSaleBasket::Update($arItem["ID"], array("DELAY" => "Y"));
				}
				elseif ($_REQUEST[$arParams["ACTION_VARIABLE"]] == "add" && in_array("DELAY", $arParams["COLUMNS_LIST"]))
				{
					if ($arItem["DELAY"] == "Y" && $arItem["CAN_BUY"] == "Y")
						CSaleBasket::Update($arItem["ID"], array("DELAY" => "N"));
				}
				unset($_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]);
			}
		}

		LocalRedirect($APPLICATION->GetCurPage());
	}
	else // if quantity is changed or coupon is set
	{
		if (isset($_REQUEST["COUPON"]))
			$_REQUEST["coupon"] = $_REQUEST["COUPON"];

		$arRes = $this->recalculateBasket($_REQUEST);

		foreach ($arRes as $key => $value)
			$arResult[$key] = $value;

		unset($_SESSION["SALE_BASKET_NUM_PRODUCTS"][SITE_ID]);

		if (!empty($_REQUEST["BasketOrder"]) && empty($arResult["WARNING_MESSAGE"]))
		{
			LocalRedirect($arParams["PATH_TO_ORDER"]);
		}
		else
		{
			unset($_REQUEST["BasketRefresh"], $_REQUEST["BasketOrder"]);

			if (!empty($arResult["WARNING_MESSAGE"]))
				$_SESSION["SALE_BASKET_MESSAGE"] = $arResult["WARNING_MESSAGE"];

			LocalRedirect($APPLICATION->GetCurPage());
		}
	}
}

$basketData = $this->getBasketItems();

$arResult = array_merge($arResult, $basketData);

$arResult["GRID"]["HEADERS"] = $headersData;

if (is_array($_SESSION["SALE_BASKET_MESSAGE"]))
{
	foreach ($_SESSION["SALE_BASKET_MESSAGE"] as $message)
		$arResult["WARNING_MESSAGE"][] = $message;

	unset($_SESSION["SALE_BASKET_MESSAGE"]);
}

$this->IncludeComponentTemplate();