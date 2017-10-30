<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
use Bitrix\Sale\DiscountCouponsManager;

if (!Loader::includeModule("sale"))
{
	ShowError(GetMessage("SOA_MODULE_NOT_INSTALL"));
	return;
}

$bUseCatalog = Loader::includeModule("catalog");
$bUseIblock = $bUseCatalog;

$isAjaxRequest = (bool)($_REQUEST["AJAX_CALL"] == "Y" || $_REQUEST["is_ajax_post"] == "Y");

if($isAjaxRequest)
{
	$APPLICATION->RestartBuffer();
}

include(dirname(__FILE__)."/functions.php");

if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("SOA_TITLE"));

$arParams["PATH_TO_BASKET"] = Trim($arParams["PATH_TO_BASKET"]);
if (strlen($arParams["PATH_TO_BASKET"]) <= 0)
	$arParams["PATH_TO_BASKET"] = "basket.php";

$arParams["PATH_TO_PERSONAL"] = Trim($arParams["PATH_TO_PERSONAL"]);
if (strlen($arParams["PATH_TO_PERSONAL"]) <= 0)
	$arParams["PATH_TO_PERSONAL"] = "index.php";

$arParams["PATH_TO_PAYMENT"] = Trim($arParams["PATH_TO_PAYMENT"]);
if (strlen($arParams["PATH_TO_PAYMENT"]) <= 0)
	$arParams["PATH_TO_PAYMENT"] = "payment.php";

$arParams["PATH_TO_AUTH"] = Trim($arParams["PATH_TO_AUTH"]);
if (strlen($arParams["PATH_TO_AUTH"]) <= 0)
	$arParams["PATH_TO_AUTH"] = "/auth/";

$arParams["PAY_FROM_ACCOUNT"] = (($arParams["PAY_FROM_ACCOUNT"] == "N") ? "N" : "Y");
$arParams["COUNT_DELIVERY_TAX"] = (($arParams["COUNT_DELIVERY_TAX"] == "Y") ? "Y" : "N");
$arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] = (($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N");
$arParams["DELIVERY_NO_AJAX"] = (($arParams["DELIVERY_NO_AJAX"] == "Y") ? "Y" : "N");
$arParams["USE_PREPAYMENT"] = $arParams["USE_PREPAYMENT"] == 'Y' ? 'Y' : 'N';
$arParams["DISPLAY_IMG_HEIGHT"] = Intval($arParams["DISPLAY_IMG_HEIGHT"]) <= 0  ? 90 : Intval($arParams["DISPLAY_IMG_HEIGHT"]);

$arParams["DELIVERY_TO_PAYSYSTEM"] = ((strlen($arParams["DELIVERY_TO_PAYSYSTEM"]) <= 0) ? "d2p" : trim($arParams["DELIVERY_TO_PAYSYSTEM"]));

if (!isset($arParams["DISABLE_BASKET_REDIRECT"]) || 'Y' !== $arParams["DISABLE_BASKET_REDIRECT"])
	$arParams["DISABLE_BASKET_REDIRECT"] = "N";

$bUseAccountNumber = (COption::GetOptionString("sale", "account_number_template", "") !== "");

$arResult = array(
		"PERSON_TYPE" => array(),
		"PAY_SYSTEM" => array(),
		"ORDER_PROP" => array(),
		"DELIVERY" => array(),
		"TAX" => array(),
		"ERROR" => array(),
		"ORDER_PRICE" => 0,
		"ORDER_WEIGHT" => 0,
		"VATE_RATE" => 0,
		"VAT_SUM" => 0,
		"bUsingVat" => false,
		"BASKET_ITEMS" => array(),
		"BASE_LANG_CURRENCY" => CSaleLang::GetLangCurrency(SITE_ID),
		"WEIGHT_UNIT" => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', false, SITE_ID)),
		"WEIGHT_KOEF" => htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID)),
		"TaxExempt" => array(),
		"DISCOUNT_PRICE" => 0,
		"DISCOUNT_PERCENT" => 0,
		"DELIVERY_PRICE" => 0,
		"TAX_PRICE" => 0,
		"PAYED_FROM_ACCOUNT_FORMATED" => false,
		"ORDER_TOTAL_PRICE_FORMATED" => false,
		"ORDER_WEIGHT_FORMATED" => false,
		"ORDER_PRICE_FORMATED" => false,
		"VAT_SUM_FORMATED" => false,
		"DELIVERY_SUM" => false,
		"DELIVERY_PROFILE_SUM" => false,
		"DELIVERY_PRICE_FORMATED" => false,
		"DISCOUNT_PERCENT_FORMATED" => false,
		"PAY_FROM_ACCOUNT" => false,
		"CURRENT_BUDGET_FORMATED" => false,
		"USER_ACCOUNT" => false,
		"DISCOUNTS" => array(),
		"AUTH" => array(),
		"HAVE_PREPAYMENT" => false,
		"PREPAY_PS" => array(),
		"PREPAY_ADIT_FIELDS" => "",
		"PREPAY_ORDER_PROPS" => array(),
);

$arUserResult = array(
		"PERSON_TYPE_ID" => false,
		"PAY_SYSTEM_ID" => false,
		"DELIVERY_ID" => false,
		"ORDER_PROP" => false,
		"DELIVERY_LOCATION" => false,
		"TAX_LOCATION" => false,
		"PAYER_NAME" => false,
		"USER_EMAIL" => false,
		"PROFILE_NAME" => false,
		"PAY_CURRENT_ACCOUNT" => false,
		"CONFIRM_ORDER" => false,
		"FINAL_STEP" => false,
		"ORDER_DESCRIPTION" => false,
		"PROFILE_ID" => false,
		"PROFILE_CHANGE" => false,
		"DELIVERY_LOCATION_ZIP" => false,
	);

$arResult["DELIVERY_EXTRA"] =  isset($_POST["DELIVERY_ID"]) && isset($_POST["DELIVERY_EXTRA"][$_POST["DELIVERY_ID"]]) ? $_POST["DELIVERY_EXTRA"][$_POST["DELIVERY_ID"]] : array();
$arResult["AUTH"]["new_user_registration_email_confirmation"] = ((COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") == "Y") ? "Y" : "N");
$arResult["AUTH"]["new_user_registration"] = ((COption::GetOptionString("main", "new_user_registration", "Y") == "Y") ? "Y" : "N");

$arParams["ALLOW_AUTO_REGISTER"] = (($arParams["ALLOW_AUTO_REGISTER"] == "Y") ? "Y" : "N");
if($arParams["ALLOW_AUTO_REGISTER"] == "Y" && ($arResult["AUTH"]["new_user_registration_email_confirmation"] == "Y" || $arResult["AUTH"]["new_user_registration"] == "N"))
	$arParams["ALLOW_AUTO_REGISTER"] = "N";
$arParams["SEND_NEW_USER_NOTIFY"] = (($arParams["SEND_NEW_USER_NOTIFY"] == "N") ? "N" : "Y");

$arParams["ALLOW_NEW_PROFILE"] = ($arParams["ALLOW_NEW_PROFILE"] == "N") ? "N" : "Y";

$allCurrency = $arResult['BASE_LANG_CURRENCY'];

if (!$arParams["DELIVERY_NO_SESSION"])
	$arParams["DELIVERY_NO_SESSION"] = "N";

$arResult["BUYER_STORE"] = "";
if (isset($_POST["BUYER_STORE"]))
	$arResult["BUYER_STORE"] = intval($_POST["BUYER_STORE"]);

$arResult["GRID"]["HEADERS"] = array();
$arResult["GRID"]["ROWS"] = array();

// grid product table columns
$bIblockEnabled = false;
$arResult["GRID"]["DEFAULT_COLUMNS"] = false;

if (empty($arParams["PRODUCT_COLUMNS"]))
{
	$arParams["PRODUCT_COLUMNS"] = array(
		"NAME" => GetMessage("SOA_NAME_DEFAULT_COLUMN"),
		"PROPS" => GetMessage("SOA_PROPS_DEFAULT_COLUMN"),
		"DISCOUNT_PRICE_PERCENT_FORMATED" => GetMessage("SOA_DISCOUNT_DEFAULT_COLUMN"),
		"PRICE_FORMATED" => GetMessage("SOA_PRICE_DEFAULT_COLUMN"),
		"QUANTITY" => GetMessage("SOA_QUANTITY_DEFAULT_COLUMN"),
		"SUM" => GetMessage("SOA_SUM_DEFAULT_COLUMN")
	);

	$arResult["GRID"]["DEFAULT_COLUMNS"] = true;
}
else
{
	if ($bUseCatalog)
		$bIblockEnabled = true;

	// processing default or certain iblock fields if they are selected
	if (($key = array_search("PREVIEW_TEXT", $arParams["PRODUCT_COLUMNS"])) !== false)
	{
		unset($arParams["PRODUCT_COLUMNS"][$key]);
		$arParams["PRODUCT_COLUMNS"]["PREVIEW_TEXT"] = GetMessage("SOA_NAME_COLUMN_PREVIEW_TEXT");
	}

	if (($key = array_search("PREVIEW_PICTURE", $arParams["PRODUCT_COLUMNS"])) !== false)
	{
		unset($arParams["PRODUCT_COLUMNS"][$key]);
		$arParams["PRODUCT_COLUMNS"]["PREVIEW_PICTURE"] = GetMessage("SOA_NAME_COLUMN_PREVIEW_PICTURE");
	}

	if (($key = array_search("DETAIL_PICTURE", $arParams["PRODUCT_COLUMNS"])) !== false)
	{
		unset($arParams["PRODUCT_COLUMNS"][$key]);
		$arParams["PRODUCT_COLUMNS"]["DETAIL_PICTURE"] = GetMessage("SOA_NAME_COLUMN_DETAIL_PICTURE");
	}

	if (($key = array_search("PROPS", $arParams["PRODUCT_COLUMNS"])) !== false)
	{
		unset($arParams["PRODUCT_COLUMNS"][$key]);
		$arParams["PRODUCT_COLUMNS"]["PROPS"] = GetMessage("SOA_PROPS_DEFAULT_COLUMN");
	}

	if (($key = array_search("NOTES", $arParams["PRODUCT_COLUMNS"])) !== false)
	{
		unset($arParams["PRODUCT_COLUMNS"][$key]);
		$arParams["PRODUCT_COLUMNS"]["NOTES"] = GetMessage("SOA_PRICE_TYPE_DEFAULT_COLUMN");
	}

	if (($key = array_search("DISCOUNT_PRICE_PERCENT_FORMATED", $arParams["PRODUCT_COLUMNS"])) !== false)
	{
		unset($arParams["PRODUCT_COLUMNS"][$key]);
		$arParams["PRODUCT_COLUMNS"]["DISCOUNT_PRICE_PERCENT_FORMATED"] = GetMessage("SOA_DISCOUNT_DEFAULT_COLUMN");
	}

	if (($key = array_search("WEIGHT_FORMATED", $arParams["PRODUCT_COLUMNS"])) !== false)
	{
		unset($arParams["PRODUCT_COLUMNS"][$key]);
		$arParams["PRODUCT_COLUMNS"]["WEIGHT_FORMATED"] = GetMessage("SOA_WEIGHT_DEFAULT_COLUMN");
	}
}

// required grid columns
if (!array_key_exists("NAME", $arParams["PRODUCT_COLUMNS"]))
	$arParams["PRODUCT_COLUMNS"] = array("NAME" => GetMessage("SOA_NAME_DEFAULT_COLUMN")) + $arParams["PRODUCT_COLUMNS"];
if (!array_key_exists("PRICE_FORMATED", $arParams["PRODUCT_COLUMNS"]))
	$arParams["PRODUCT_COLUMNS"]["PRICE_FORMATED"] = GetMessage("SOA_PRICE_DEFAULT_COLUMN");
if (!array_key_exists("QUANTITY", $arParams["PRODUCT_COLUMNS"]))
	$arParams["PRODUCT_COLUMNS"]["QUANTITY"] = GetMessage("SOA_QUANTITY_DEFAULT_COLUMN");
if (!array_key_exists("SUM", $arParams["PRODUCT_COLUMNS"]))
	$arParams["PRODUCT_COLUMNS"]["SUM"] = GetMessage("SOA_SUM_DEFAULT_COLUMN");

$arCustomSelectFields = array();
$arIblockProps = array();
$propertyCount = 0;
define("PROPERTY_COUNT_LIMIT", 24); // too much properties cause sql join error

foreach ($arParams["PRODUCT_COLUMNS"] as $key => $value) // making grid headers array
{
	// processing iblock properties
	if (strncmp($value, "PROPERTY_", 9) == 0)
	{
		$propertyCount++;
		if ($propertyCount > PROPERTY_COUNT_LIMIT)
			continue;

		$propCode = substr($value, 9);
		if ($propCode == '')
			continue;
		$arCustomSelectFields[] = $value; // array of iblock properties to select
		$id = $value."_VALUE";
		$name = $value;

		if ($bIblockEnabled)
		{
			$dbres = CIBlockProperty::GetList(array(), array("CODE" => $propCode));
			if ($arres = $dbres->GetNext())
			{
				$name = $arres["NAME"];
				$arIblockProps[$propCode] = $arres;
			}
		}
	}
	else
	{
		$id = $key;
		$name = $value;
	}

	$arColumn = array(
		"id" => $id,
		"name" => $name
	);

	if ($key == "PRICE_FORMATED")
		$arColumn["align"] = "right";

	$arResult["GRID"]["HEADERS"][] = $arColumn;
}

if (!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
{
	$arResult["AUTH"]["USER_LOGIN"] = ((strlen($_POST["USER_LOGIN"]) > 0) ? htmlspecialcharsbx($_POST["USER_LOGIN"]) : htmlspecialcharsbx(${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"}));
	$arResult["AUTH"]["captcha_registration"] = ((COption::GetOptionString("main", "captcha_registration", "N") == "Y") ? "Y" : "N");
	if($arResult["AUTH"]["captcha_registration"] == "Y")
		$arResult["AUTH"]["capCode"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

	$arResult["POST"] = array();

	if ($_SERVER["REQUEST_METHOD"] == "POST" && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid()))
	{
		foreach ($_POST as $vname=>$vvalue)
		{
			if (in_array($vname, array("USER_LOGIN", "USER_PASSWORD", "do_authorize", "NEW_NAME", "NEW_LAST_NAME", "NEW_EMAIL", "NEW_GENERATE", "NEW_LOGIN", "NEW_PASSWORD", "NEW_PASSWORD_CONFIRM", "captcha_sid", "captcha_word", "do_register", "AJAX_CALL", "is_ajax_post")))
				continue;
			if(is_array($vvalue))
			{
				foreach($vvalue as $k => $v)
					$arResult["POST"][htmlspecialcharsbx($vname."[".$k."]")] = htmlspecialcharsbx($v);
			}
			else
				$arResult["POST"][htmlspecialcharsbx($vname)] = htmlspecialcharsbx($vvalue);
		}
		if ($_POST["do_authorize"] == "Y")
		{
			if (strlen($_POST["USER_LOGIN"]) <= 0)
				$arResult["ERROR"][] = GetMessage("STOF_ERROR_AUTH_LOGIN");

			if (empty($arResult["ERROR"]))
			{
				$arAuthResult = $USER->Login($_POST["USER_LOGIN"], $_POST["USER_PASSWORD"], "N");
				if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
					$arResult["ERROR"][] = GetMessage("STOF_ERROR_AUTH").((strlen($arAuthResult["MESSAGE"]) > 0) ? ": ".$arAuthResult["MESSAGE"] : "" );
			}
		}
		elseif ($_POST["do_register"] == "Y" && $arResult["AUTH"]["new_user_registration"] == "Y")
		{
			if (strlen($_POST["NEW_NAME"]) <= 0)
				$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_NAME");

			if (strlen($_POST["NEW_LAST_NAME"]) <= 0)
				$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_LASTNAME");

			if (strlen($_POST["NEW_EMAIL"]) <= 0)
				$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_EMAIL");
			elseif (!check_email($_POST["NEW_EMAIL"]))
				$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_BAD_EMAIL");

			$arResult["AUTH"]["NEW_EMAIL"] = $_POST["NEW_EMAIL"];

			if (empty($arResult["ERROR"]))
			{

				if ($_POST["NEW_GENERATE"] == "Y")
				{
					$arResult["AUTH"]["NEW_EMAIL"] = $_POST["NEW_EMAIL"];
					$arResult["AUTH"]["NEW_LOGIN"] = $_POST["NEW_EMAIL"];

					$pos = strpos($arResult["AUTH"]["NEW_LOGIN"], "@");
					if ($pos !== false)
						$_POST["NEW_LOGIN"] = substr($arResult["AUTH"]["NEW_LOGIN"], 0, $pos);

					if (strlen($arResult["AUTH"]["NEW_LOGIN"]) > 47)
						$_POST["NEW_LOGIN"] = substr($arResult["AUTH"]["NEW_LOGIN"], 0, 47);

					if (strlen($arResult["AUTH"]["NEW_LOGIN"]) < 3)
						$arResult["AUTH"]["NEW_LOGIN"] .= "_";

					if (strlen($arResult["AUTH"]["NEW_LOGIN"]) < 3)
						$arResult["AUTH"]["NEW_LOGIN"] .= "_";

					$dbUserLogin = CUser::GetByLogin($arResult["AUTH"]["NEW_LOGIN"]);
					if ($arUserLogin = $dbUserLogin->Fetch())
					{
						$newLoginTmp = $arResult["AUTH"]["NEW_LOGIN"];
						$uind = 0;
						do
						{
							$uind++;
							if ($uind == 10)
							{
								$arResult["AUTH"]["NEW_LOGIN"] = $arResult["AUTH"]["NEW_EMAIL"];
								$newLoginTmp = $arResult["AUTH"]["NEW_LOGIN"];
							}
							elseif ($uind > 10)
							{
								$arResult["AUTH"]["NEW_LOGIN"] = "buyer".time().GetRandomCode(2);
								$newLoginTmp = $arResult["AUTH"]["NEW_LOGIN"];
								break;
							}
							else
							{
								$newLoginTmp = $arResult["AUTH"]["NEW_LOGIN"].$uind;
							}
							$dbUserLogin = CUser::GetByLogin($newLoginTmp);
						}
						while ($arUserLogin = $dbUserLogin->Fetch());
						$arResult["AUTH"]["NEW_LOGIN"] = $newLoginTmp;
					}

					$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
					if($def_group!="")
					{
						$GROUP_ID = explode(",", $def_group);
						$arPolicy = $USER->GetGroupPolicy($GROUP_ID);
					}
					else
					{
						$arPolicy = $USER->GetGroupPolicy(array());
					}

					$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
					if($password_min_length <= 0)
						$password_min_length = 6;
					$password_chars = array(
						"abcdefghijklnmopqrstuvwxyz",
						"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
						"0123456789",
					);
					if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
						$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
					$arResult["AUTH"]["NEW_PASSWORD"] = $arResult["AUTH"]["NEW_PASSWORD_CONFIRM"] = randString($password_min_length+2, $password_chars);
				}
				else
				{
					if (strlen($_POST["NEW_LOGIN"]) <= 0)
						$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_FLAG");

					if (strlen($_POST["NEW_PASSWORD"]) <= 0)
						$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_FLAG1");

					if (strlen($_POST["NEW_PASSWORD"]) > 0 && strlen($_POST["NEW_PASSWORD_CONFIRM"]) <= 0)
						$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_FLAG1");

					if (strlen($_POST["NEW_PASSWORD"]) > 0
						&& strlen($_POST["NEW_PASSWORD_CONFIRM"]) > 0
						&& $_POST["NEW_PASSWORD"] != $_POST["NEW_PASSWORD_CONFIRM"])
						$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_PASS");

					$arResult["AUTH"]["NEW_LOGIN"] = $_POST["NEW_LOGIN"];
					$arResult["AUTH"]["NEW_NAME"] = $_POST["NEW_NAME"];
					$arResult["AUTH"]["NEW_PASSWORD"] = $_POST["NEW_PASSWORD"];
					$arResult["AUTH"]["NEW_PASSWORD_CONFIRM"] = $_POST["NEW_PASSWORD_CONFIRM"];
				}
			}

			if (empty($arResult["ERROR"]))
			{

				$arAuthResult = $USER->Register($arResult["AUTH"]["NEW_LOGIN"], $_POST["NEW_NAME"], $_POST["NEW_LAST_NAME"], $arResult["AUTH"]["NEW_PASSWORD"], $arResult["AUTH"]["NEW_PASSWORD_CONFIRM"], $arResult["AUTH"]["NEW_EMAIL"], LANG, $_POST["captcha_word"], $_POST["captcha_sid"]);
				if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
					$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG").((strlen($arAuthResult["MESSAGE"]) > 0) ? ": ".$arAuthResult["MESSAGE"] : "" );
				else
				{
					if ($USER->IsAuthorized())
					{
						if($arParams["SEND_NEW_USER_NOTIFY"] == "Y")
							CUser::SendUserInfo($USER->GetID(), SITE_ID, GetMessage("INFO_REQ"), true);
						LocalRedirect($APPLICATION->GetCurPageParam());
					}
					else
					{
						$arResult["OK_MESSAGE"][] = GetMessage("STOF_ERROR_REG_CONFIRM");
					}
				}
			}
			$arResult["AUTH"]["~NEW_LOGIN"] = $arResult["AUTH"]["NEW_LOGIN"];
			$arResult["AUTH"]["NEW_LOGIN"] = htmlspecialcharsEx($arResult["AUTH"]["NEW_LOGIN"]);
			$arResult["AUTH"]["~NEW_NAME"] = $_POST["NEW_NAME"];
			$arResult["AUTH"]["NEW_NAME"] = htmlspecialcharsEx($_POST["NEW_NAME"]);
			$arResult["AUTH"]["~NEW_LAST_NAME"] = $_POST["NEW_LAST_NAME"];
			$arResult["AUTH"]["NEW_LAST_NAME"] = htmlspecialcharsEx($_POST["NEW_LAST_NAME"]);
			$arResult["AUTH"]["~NEW_EMAIL"] = $arResult["AUTH"]["NEW_EMAIL"];
			$arResult["AUTH"]["NEW_EMAIL"] = htmlspecialcharsEx($arResult["AUTH"]["NEW_EMAIL"]);
		}
	}
}

if ($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y" )
{
	if(strlen($_REQUEST["ORDER_ID"]) <= 0)
	{
		$arElementId = array();
		$arSku2Parent = array();
		$arSetParentWeight = array();
		$DISCOUNT_PRICE_ALL = 0;
		$arResult["MAX_DIMENSIONS"] = $arResult["ITEMS_DIMENSIONS"] = array();

		CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);
		/* Check Values Begin */

		$arSelFields = array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY",
			"CAN_BUY", "PRICE", "WEIGHT", "NAME", "CURRENCY", "CATALOG_XML_ID", "VAT_RATE",
			"NOTES", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "DIMENSIONS", "TYPE", "SET_PARENT_ID", "DETAIL_PAGE_URL"
		);
		$dbBasketItems = CSaleBasket::GetList(
				array("ID" => "ASC"),
				array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => "NULL"
					),
				false,
				false,
				$arSelFields
			);
		while ($arItem = $dbBasketItems->GetNext())
		{
			if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y")
			{
				$arItem["PRICE"] = roundEx($arItem["PRICE"], SALE_VALUE_PRECISION);
				$arItem["QUANTITY"] = DoubleVal($arItem["QUANTITY"]);

				$arItem["WEIGHT"] = DoubleVal($arItem["WEIGHT"]);
				$arItem["VAT_RATE"] = DoubleVal($arItem["VAT_RATE"]);

				$arDim = unserialize($arItem["~DIMENSIONS"]);

				if(is_array($arDim))
				{
					$arItem["DIMENSIONS"] = $arDim;
					unset($arItem["~DIMENSIONS"]);

					$arResult["MAX_DIMENSIONS"] = CSaleDeliveryHelper::getMaxDimensions(
																			array(
																				$arDim["WIDTH"],
																				$arDim["HEIGHT"],
																				$arDim["LENGTH"]
																				),
																			$arResult["MAX_DIMENSIONS"]);

					$arResult["ITEMS_DIMENSIONS"][] = $arDim;
				}

				if($arItem["VAT_RATE"] > 0 && !CSaleBasketHelper::isSetItem($arItem))
				{
					$arResult["bUsingVat"] = "Y";
					if($arItem["VAT_RATE"] > $arResult["VAT_RATE"])
						$arResult["VAT_RATE"] = $arItem["VAT_RATE"];
					//$arItem["VAT_VALUE"] = roundEx((($arItem["PRICE"] / ($arItem["VAT_RATE"] +1)) * $arItem["VAT_RATE"]), SALE_VALUE_PRECISION);
					$arItem["VAT_VALUE"] = roundEx((($arItem["PRICE"] / ($arItem["VAT_RATE"] +1)) * $arItem["VAT_RATE"]), SALE_VALUE_PRECISION);

					$arResult["VAT_SUM"] += roundEx($arItem["VAT_VALUE"] * $arItem["QUANTITY"], SALE_VALUE_PRECISION);
				}
				$arItem["PRICE_FORMATED"] = SaleFormatCurrency($arItem["PRICE"], $arItem["CURRENCY"]);
				$arItem["WEIGHT_FORMATED"] = roundEx(DoubleVal($arItem["WEIGHT"]/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];

				if($arItem["DISCOUNT_PRICE"] > 0)
				{
					$arItem["DISCOUNT_PRICE_PERCENT"] = $arItem["DISCOUNT_PRICE"]*100 / ($arItem["DISCOUNT_PRICE"] + $arItem["PRICE"]);
					$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arItem["DISCOUNT_PRICE_PERCENT"], 0)."%";
				}

				$arItem["PROPS"] = array();
				$dbProp = CSaleBasket::GetPropsList(array("SORT" => "ASC", "ID" => "ASC"), array("BASKET_ID" => $arItem["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
				while($arProp = $dbProp -> GetNext())
				{
					if (array_key_exists('BASKET_ID', $arProp))
					{
						unset($arProp['BASKET_ID']);
					}
					if (array_key_exists('~BASKET_ID', $arProp))
					{
						unset($arProp['~BASKET_ID']);
					}

					$arProp = array_filter($arProp, array("CSaleBasketHelper", "filterFields"));

					$arItem["PROPS"][] = $arProp;
				}

				if (!CSaleBasketHelper::isSetItem($arItem))
				{
					$DISCOUNT_PRICE_ALL += $arItem["DISCOUNT_PRICE"] * $arItem["QUANTITY"];
					$arItem["DISCOUNT_PRICE"] = roundEx($arItem["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);
					$arResult["ORDER_PRICE"] += $arItem["PRICE"] * $arItem["QUANTITY"];
				}

				if (!CSaleBasketHelper::isSetItem($arItem))
				{
					$arResult["ORDER_WEIGHT"] += $arItem["WEIGHT"] * $arItem["QUANTITY"];
				}

				if (CSaleBasketHelper::isSetItem($arItem))
					$arSetParentWeight[$arItem["SET_PARENT_ID"]] += $arItem["WEIGHT"] * $arItem['QUANTITY'];

				$arResult["BASKET_ITEMS"][] = $arItem;
			}

			$arResult["PRICE_WITHOUT_DISCOUNT"] = SaleFormatCurrency($arResult["ORDER_PRICE"] + $DISCOUNT_PRICE_ALL, $allCurrency);

			// count weight for set parent products
			foreach ($arResult["BASKET_ITEMS"] as &$arItem)
			{
				if (CSaleBasketHelper::isSetParent($arItem))
				{
					$arItem["WEIGHT"] = $arSetParentWeight[$arItem["ID"]] / $arItem["QUANTITY"];
					$arItem["WEIGHT_FORMATED"] = roundEx(doubleval($arItem["WEIGHT"] / $arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];
				}
			}

			$arResult["ORDER_WEIGHT_FORMATED"] = roundEx(DoubleVal($arResult["ORDER_WEIGHT"]/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];
			$arResult["ORDER_PRICE_FORMATED"] = SaleFormatCurrency($arResult["ORDER_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
			$arResult["VAT_SUM_FORMATED"] = SaleFormatCurrency($arResult["VAT_SUM"], $arResult["BASE_LANG_CURRENCY"]);

			$arElementId[] = $arItem["PRODUCT_ID"];

			if ($bUseCatalog)
			{
				$arParent = CCatalogSku::GetProductInfo($arItem["PRODUCT_ID"]);
				if ($arParent)
				{
					$arElementId[] = $arParent["ID"];
					$arSku2Parent[$arItem["PRODUCT_ID"]] = $arParent["ID"];
				}
			}
			unset($arItem);
		}

		if (!empty($arResult["BASKET_ITEMS"]))
		{
			if ($bUseCatalog)
				$arResult["BASKET_ITEMS"] = getMeasures($arResult["BASKET_ITEMS"]); // get measures
		}
		if (empty($arResult["BASKET_ITEMS"]) || !is_array($arResult["BASKET_ITEMS"]))
		{
			if ($arParams["DISABLE_BASKET_REDIRECT"] == 'Y')
			{
				return;
			}
			else
			{
				if (isset($_REQUEST['json']) && $_REQUEST['json'] == "Y")
				{
					$APPLICATION->RestartBuffer();
					echo json_encode(array("success" => "N", "redirect" => $arParams["PATH_TO_BASKET"]));
					die();
				}
				LocalRedirect($arParams["PATH_TO_BASKET"]);
				die();
			}
		}

		if($arParams["USE_PREPAYMENT"] == "Y")
		{
			$PSpersonType = array();
			$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("LID" => SITE_ID, "ACTIVE" => "Y"));
			while($arPersonType = $dbPersonType->GetNext())
			{
				$PSpersonType[] = $arPersonType["ID"];
			}

			if(!empty($PSpersonType))
			{
				$dbPaySysAction = CSalePaySystemAction::GetList(
						array(),
						array(
								"PS_ACTIVE" => "Y",
								"HAVE_PREPAY" => "Y",
								"PERSON_TYPE_ID" => $PSpersonType,
							),
						false,
						false,
						array("ID", "PAY_SYSTEM_ID", "PERSON_TYPE_ID", "NAME", "ACTION_FILE", "RESULT_FILE", "NEW_WINDOW", "PARAMS", "ENCODING", "LOGOTIP")
					);
				if ($arPaySysAction = $dbPaySysAction->Fetch())
				{
					$arResult["PREPAY_PS"] = $arPaySysAction;
					$arResult["HAVE_PREPAYMENT"] = true;
					CSalePaySystemAction::InitParamArrays(false, false, $arPaySysAction["PARAMS"]);

					$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];

					$pathToAction = str_replace("\\", "/", $pathToAction);
					while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
						$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

					if (file_exists($pathToAction))
					{
						if (is_dir($pathToAction) && file_exists($pathToAction."/pre_payment.php"))
							$pathToAction .= "/pre_payment.php";

						try
						{
							include_once($pathToAction);
						}
						catch(\Bitrix\Main\SystemException $e)
						{
							if($e->getCode() == CSalePaySystemAction::GET_PARAM_VALUE)
								$arResult["ERROR"][] = GetMessage("SOA_TEMPL_ORDER_PS_ERROR");
							else
								$arResult["ERROR"][] = $e->getMessage();
						}

						$psPreAction = new CSalePaySystemPrePayment;
						if($psPreAction->init())
						{
							$psPreAction->encoding = $arPaySysAction["ENCODING"];
							if($psPreAction->IsAction())
							{
								$arResult["PREPAY_ORDER_PROPS"] = $psPreAction->getProps();
								if(IntVal($arUserResult["PAY_SYSTEM_ID"]) <= 0)
								{
									$arUserResult["PERSON_TYPE_ID"] = $arResult["PREPAY_PS"]["PERSON_TYPE_ID"];
								}
								$arUserResult["PREPAYMENT_MODE"] = true;
								$arUserResult["PAY_SYSTEM_ID"] = $arResult["PREPAY_PS"]["PAY_SYSTEM_ID"];
							}
							elseif($_POST["PAY_SYSTEM_ID"] == $arResult["PREPAY_PS"]["PAY_SYSTEM_ID"])
							{
								$orderData = array(
										"PATH_TO_ORDER" => $APPLICATION->GetCurPage(),
										"AMOUNT" => $arResult["ORDER_PRICE"],
										"ORDER_REQUEST" => "Y",
										"BASKET_ITEMS" => $arResult["BASKET_ITEMS"],
									);
								$arResult["REDIRECT_URL"] = $psPreAction->BasketButtonAction($orderData);

								if(strlen($arResult["REDIRECT_URL"]) > 1)
									$arResult["NEED_REDIRECT"] = "Y";
							}

							$arResult["PREPAY_ADIT_FIELDS"] = $psPreAction->getHiddenInputs();
						}
					}
				}
			}
		}

		$isOrderPlaced = ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirmorder"]) && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid()));

		$isPersonTypeChanged = (isset($_POST["PERSON_TYPE_OLD"]) && IntVal($_POST["PERSON_TYPE"]) != IntVal($_POST["PERSON_TYPE_OLD"]));


		// when order is placed
		if($isOrderPlaced || $isPersonTypeChanged)
		{
			if(IntVal($_POST["PERSON_TYPE"]) > 0)
				$arUserResult["PERSON_TYPE_ID"] = IntVal($_POST["PERSON_TYPE"]);
			if(IntVal($_POST["PERSON_TYPE_OLD"]) == $arUserResult["PERSON_TYPE_ID"])
			{
				if(isset($_POST["PROFILE_ID"]))
					$arUserResult["PROFILE_ID"] = IntVal($_POST["PROFILE_ID"]);
				if(isset($_POST["PAY_SYSTEM_ID"]))
					$arUserResult["PAY_SYSTEM_ID"] = IntVal($_POST["PAY_SYSTEM_ID"]);
				if(isset($_POST["DELIVERY_ID"]))
					$arUserResult["DELIVERY_ID"] = $_POST["DELIVERY_ID"];
				if(strlen($_POST["ORDER_DESCRIPTION"]) > 0)
				{
					$arUserResult["~ORDER_DESCRIPTION"] = $_POST["ORDER_DESCRIPTION"];
					$arUserResult["ORDER_DESCRIPTION"] = htmlspecialcharsbx($arUserResult["~ORDER_DESCRIPTION"]);
				}
				if($_POST["PAY_CURRENT_ACCOUNT"] == "Y")
					$arUserResult["PAY_CURRENT_ACCOUNT"] = "Y";
				if($_POST["confirmorder"] == "Y")
				{
					$arUserResult["CONFIRM_ORDER"] = "Y";
					$arUserResult["FINAL_STEP"] = "Y";
				}
				if($_POST["profile_change"] == "Y")
					$arUserResult["PROFILE_CHANGE"] = "Y";
				else
					$arUserResult["PROFILE_CHANGE"] = "N";
			}

			if(IntVal($arUserResult["PERSON_TYPE_ID"]) <= 0)
				$arResult["ERROR"][] = GetMessage("SOA_ERROR_PERSON_TYPE");
		}

		if (isset($_POST["PERSON_TYPE_OLD"]) && IntVal($_POST["PERSON_TYPE"]) != IntVal($_POST["PERSON_TYPE_OLD"]))
		{
			$isOrderPlaced = false;
		}

		if($isOrderPlaced)
		{
			foreach($_POST as $k => $v)
			{
				if(strpos($k, "ORDER_PROP_") !== false)
				{
					if(strpos($k, "[]") !== false)
						$orderPropId = IntVal(substr($k, strlen("ORDER_PROP_"), strlen($k)-2));
					else
						$orderPropId = IntVal(substr($k, strlen("ORDER_PROP_")));

					if($orderPropId > 0)
						$arUserResult["ORDER_PROP"][$orderPropId] = $v;
					elseif(strpos($k, "COUNTRY_ORDER_PROP_") !== false)
						$arUserResult["ORDER_PROP"]["COUNTRY_".IntVal(substr($k, strlen("COUNTRY_ORDER_PROP_")))] = $v;
					elseif(strpos($k, "REGION_ORDER_PROP_") !== false)
						$arUserResult["ORDER_PROP"]["REGION_".IntVal(substr($k, strlen("REGION_ORDER_PROP_")))] = $v;
					elseif(strpos($k, "COUNTRYORDER_PROP_") !== false)
						$arUserResult["ORDER_PROP"]["COUNTRY_".IntVal(substr($k, strlen("COUNTRYORDER_PROP_")))] = $v;
					elseif(strpos($k, "REGIONORDER_PROP_") !== false)
						$arUserResult["ORDER_PROP"]["REGION_".IntVal(substr($k, strlen("REGIONORDER_PROP_")))] = $v;
				}

				if(strpos($k, "NEW_LOCATION_") !== false && intval($v) > 0)
				{
					$orderPropId = IntVal(substr($k, strlen("NEW_LOCATION_")));
					$arUserResult["ORDER_PROP"][$orderPropId] = $v;
				}
			}

			foreach ($_FILES as $k => $arFileData)
			{
				if(strpos($k, "ORDER_PROP_") !== false)
				{
					$orderPropId = intval(substr($k, strlen("ORDER_PROP_")));
					$arUserResult["ORDER_PROP"][$orderPropId][0] = array();

					if (is_array($arFileData))
					{
						foreach ($arFileData as $param_name => $arValues)
						{
							foreach ($arValues as $nIndex => $val)
							{
								if (strlen($arFileData["name"][$nIndex]) > 0)
									$arUserResult["ORDER_PROP"][$orderPropId][$nIndex][$param_name] = $val;
							}
						}
					}
				}
			}

			getFormatedProperties($arUserResult["PERSON_TYPE_ID"], $arResult, $arUserResult, $arParams);

			$arFilter = array();
			if (isset($_POST["PAY_SYSTEM_ID"]) && strlen($_POST["PAY_SYSTEM_ID"]) > 0 && isset($_POST["PAY_CURRENT_ACCOUNT"]) && $_POST["PAY_CURRENT_ACCOUNT"] != "Y")
			{
				$arFilter["RELATED"]["PAYSYSTEM_ID"] = $_POST["PAY_SYSTEM_ID"];
				$arFilter["RELATED"]["TYPE"] = "WITH_NOT_RELATED";
			}

			if (isset($_POST["DELIVERY_ID"]) && strlen($_POST["DELIVERY_ID"]) > 0)
			{
				$arFilter["RELATED"]["DELIVERY_ID"] = $_POST["DELIVERY_ID"];
				$arFilter["RELATED"]["TYPE"] = "WITH_NOT_RELATED";
			}

			$arFilter["PERSON_TYPE_ID"] = $arUserResult["PERSON_TYPE_ID"];
			$arFilter["ACTIVE"] = "Y";
			$arFilter["UTIL"] = "N";

			if(!empty($arParams["PROP_".$arUserResult["PERSON_TYPE_ID"]]))
				$arFilter["!ID"] = $arParams["PROP_".$arUserResult["PERSON_TYPE_ID"]];

			$dbOrderProps = CSaleOrderProps::GetList(
				array("SORT" => "ASC"),
				$arFilter,
				false,
				false,
				array("ID", "NAME", "TYPE", "IS_LOCATION", "IS_LOCATION4TAX", "IS_PROFILE_NAME", "IS_PAYER", "IS_EMAIL", "REQUIED", "SORT", "IS_ZIP", "CODE", "MULTIPLE")
			);
			while ($arOrderProps = $dbOrderProps->GetNext())
			{
				//if(isset($arUserResult["ORDER_PROP"][$arOrderProps["ID"]]) || isset($arUserResult["ORDER_PROP"]["COUNTRY_".$arOrderProps["ID"]]))
				//{
					$bErrorField = False;
					$curVal = $arUserResult["ORDER_PROP"][$arOrderProps["ID"]];

					if ($arOrderProps["TYPE"]=="LOCATION" && ($arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_LOCATION4TAX"]=="Y"))
					{
						if (IntVal($curVal)<=0 && IntVal($arUserResult["ORDER_PROP"]["REGION_".$arOrderProps["ID"]]) > 0)
						{
							$dbLoc = CSaleLocation::GetList(array(), array("REGION_ID" => $arUserResult["ORDER_PROP"]["REGION_".$arOrderProps["ID"]], "CITY_ID" => false), false, false, array("ID", "REGION_ID", "CITY_ID"));
							if($arLoc = $dbLoc->Fetch())
							{
								$curVal = $arLoc["ID"];
							}
						}
						if(IntVal($curVal)<=0 && IntVal($arUserResult["ORDER_PROP"]["COUNTRY_".$arOrderProps["ID"]]) > 0)
						{
							$dbLoc = CSaleLocation::GetList(array(), array("COUNTRY_ID" => $arUserResult["ORDER_PROP"]["COUNTRY_".$arOrderProps["ID"]], "REGION_ID" => false, "CITY_ID" => false), false, false, array("ID", "COUNTRY_ID", "REGION_ID", "CITY_ID"));
							if($arLoc = $dbLoc->Fetch())
							{
								$curVal = $arLoc["ID"];
							}
						}

						if (IntVal($curVal)<=0)
							$bErrorField = True;
						else
							$arUserResult["ORDER_PROP"][$arOrderProps["ID"]] = $curVal;
					}
					elseif ($arOrderProps["IS_PROFILE_NAME"]=="Y" || $arOrderProps["IS_PAYER"]=="Y" || $arOrderProps["IS_EMAIL"]=="Y" || $arOrderProps["IS_ZIP"]=="Y")
					{
						if ($arOrderProps["IS_PROFILE_NAME"]=="Y")
						{
							$arUserResult["PROFILE_NAME"] = Trim($curVal);
							if (strlen($arUserResult["PROFILE_NAME"])<=0)
								$bErrorField = True;
						}
						if ($arOrderProps["IS_PAYER"]=="Y")
						{
							$arUserResult["PAYER_NAME"] = Trim($curVal);
							if (strlen($arUserResult["PAYER_NAME"])<=0)
								$bErrorField = True;
						}
						if ($arOrderProps["IS_EMAIL"]=="Y")
						{
							$arUserResult["USER_EMAIL"] = Trim($curVal);
							if (strlen($arUserResult["USER_EMAIL"])<=0)
								$bErrorField = True;
							elseif(!check_email($arUserResult["USER_EMAIL"]))
								$arResult["ERROR"][] = GetMessage("SOA_ERROR_EMAIL");
						}
						if ($arOrderProps["IS_ZIP"]=="Y")
						{
							$arUserResult["DELIVERY_LOCATION_ZIP"] = Trim($curVal);
							if (strlen($arUserResult["DELIVERY_LOCATION_ZIP"])<=0)
								$bErrorField = True;
						}
					}
					elseif ($arOrderProps["REQUIED"]=="Y")
					{
						if ($arOrderProps["TYPE"]=="TEXT" || $arOrderProps["TYPE"]=="TEXTAREA" || $arOrderProps["TYPE"]=="RADIO" || $arOrderProps["TYPE"]=="SELECT" || $arOrderProps["TYPE"] == "CHECKBOX")
						{
							if (strlen($curVal)<=0)
								$bErrorField = True;
						}
						elseif ($arOrderProps["TYPE"]=="LOCATION")
						{
							if (IntVal($curVal)<=0)
								$bErrorField = True;
						}
						elseif ($arOrderProps["TYPE"]=="MULTISELECT")
						{
							if (!is_array($curVal) || count($curVal)<=0)
								$bErrorField = True;
						}
						elseif ($arOrderProps["TYPE"]=="FILE")
						{
							if (is_array($curVal))
							{
								foreach ($curVal as $index => $arFileData)
								{
									if (!array_key_exists("name", $arFileData) || strlen($arFileData["name"]) <= 0)
										$bErrorField = true;
								}
							}
						}
					}

					if ($bErrorField)
						$arResult["ERROR"][] = GetMessage("SOA_ERROR_REQUIRE")." \"".$arOrderProps["NAME"]."\"";

				//}//end isset
			}//end while
		}
		/* Check Values End */

		// get properties for iblock elements and their parents (if any)
		$arSelect = array_merge(array("ID", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT"), $arCustomSelectFields);
		$arProductData = getProductProps($arElementId, $arSelect);

		foreach ($arResult["BASKET_ITEMS"] as &$arResultItem)
		{
			$productId = $arResultItem["PRODUCT_ID"];
			$arParent = CCatalogSku::GetProductInfo($productId);
			if ((int)$arProductData[$productId]["PREVIEW_PICTURE"] <= 0
					&& (int)$arProductData[$productId]["DETAIL_PICTURE"] <= 0
					&& $arParent)
			{
				$productId = $arParent["ID"];
			}

			if((int)$arProductData[$productId]["PREVIEW_PICTURE"] > 0)
				$arResultItem["PREVIEW_PICTURE"] = $arProductData[$productId]["PREVIEW_PICTURE"];
			if((int)$arProductData[$productId]["DETAIL_PICTURE"] > 0)
				$arResultItem["DETAIL_PICTURE"] = $arProductData[$productId]["DETAIL_PICTURE"];
			if($arProductData[$productId]["PREVIEW_TEXT"] != '')
				$arResultItem["PREVIEW_TEXT"] = $arProductData[$productId]["PREVIEW_TEXT"];

			foreach ($arProductData[$arResultItem["PRODUCT_ID"]] as $key => $value)
			{
				if (strpos($key, "PROPERTY_") !== false)
					$arResultItem[$key] = $value;
			}

			if (array_key_exists($arResultItem["PRODUCT_ID"], $arSku2Parent)) // if sku element doesn't have some property value - we'll show parent element value instead
			{
				foreach ($arCustomSelectFields as $field)
				{
					$fieldVal = $field."_VALUE";
					$parentId = $arSku2Parent[$arResultItem["PRODUCT_ID"]];

					if ((!isset($arResultItem[$fieldVal]) || (isset($arResultItem[$fieldVal]) && strlen($arResultItem[$fieldVal]) == 0))
						&& (isset($arProductData[$parentId][$fieldVal]) && !empty($arProductData[$parentId][$fieldVal]))) // can be array or string
					{
						$arResultItem[$fieldVal] = $arProductData[$parentId][$fieldVal];
					}
				}
			}

			$arResultItem["PREVIEW_PICTURE_SRC"] = "";
			if (isset($arResultItem["PREVIEW_PICTURE"]) && intval($arResultItem["PREVIEW_PICTURE"]) > 0)
			{
				$arImage = CFile::GetFileArray($arResultItem["PREVIEW_PICTURE"]);
				if ($arImage)
				{
					$arFileTmp = CFile::ResizeImageGet(
						$arImage,
						array("width" => "110", "height" =>"110"),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);

					$arResultItem["PREVIEW_PICTURE_SRC"] = $arFileTmp["src"];
				}
			}

			$arResultItem["DETAIL_PICTURE_SRC"] = "";
			if (isset($arResultItem["DETAIL_PICTURE"]) && intval($arResultItem["DETAIL_PICTURE"]) > 0)
			{
				$arImage = CFile::GetFileArray($arResultItem["DETAIL_PICTURE"]);
				if ($arImage)
				{
					$arFileTmp = CFile::ResizeImageGet(
						$arImage,
						array("width" => "110", "height" =>"110"),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);

					$arResultItem["DETAIL_PICTURE_SRC"] = $arFileTmp["src"];
				}
			}
		}
		if (isset($arResultItem))
			unset($arResultItem);

		/* Person Type Begin */
		$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("LID" => SITE_ID, "ACTIVE" => "Y"));
		while($arPersonType = $dbPersonType->GetNext())
		{
			if($arUserResult["PERSON_TYPE_ID"] == $arPersonType["ID"] || IntVal($arUserResult["PERSON_TYPE_ID"]) <= 0)
			{
				$arUserResult["PERSON_TYPE_ID"] = $arPersonType["ID"];
				$arPersonType["CHECKED"] = "Y";
			}
			$arResult["PERSON_TYPE"][$arPersonType["ID"]] = $arPersonType;
		}

		foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepPersonType", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arResult, &$arUserResult, &$arParams));
		/* Person Type End */


		/* User Profiles Begin */
		$bFirst = false;
		$dbUserProfiles = CSaleOrderUserProps::GetList(
				array("DATE_UPDATE" => "DESC"),
				array(
						"PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
						"USER_ID" => IntVal($USER->GetID())
					)
			);
		while($arUserProfiles = $dbUserProfiles->GetNext())
		{
			if(!$bFirst && empty($arUserResult["PROFILE_CHANGE"]))
			{
				$bFirst = true;
				$arUserResult["PROFILE_ID"] = IntVal($arUserProfiles["ID"]);
				$arUserResult["PROFILE_CHANGE"] = "Y";
				$arUserResult["PROFILE_DEFAULT"] = "Y";
			}
			if (IntVal($arUserResult["PROFILE_ID"])==IntVal($arUserProfiles["ID"]))
				$arUserProfiles["CHECKED"] = "Y";
			$arResult["ORDER_PROP"]["USER_PROFILES"][$arUserProfiles["ID"]] = $arUserProfiles;
		}


		if (!$isOrderPlaced)
		{
			getFormatedProperties($arUserResult["PERSON_TYPE_ID"], $arResult, $arUserResult, $arParams);
		}

		/* Delivery Begin */
		if ((int)$arUserResult["DELIVERY_LOCATION"] > 0)
		{
			$locFrom = COption::GetOptionString('sale', 'location', false, SITE_ID);

			$arFilter = array(
				"COMPABILITY" => array(
					"WEIGHT" => $arResult["ORDER_WEIGHT"],
					"PRICE" => $arResult["ORDER_PRICE"],
					"LOCATION_FROM" => $locFrom,
					"LOCATION_TO" => $arUserResult["DELIVERY_LOCATION"],
					"LOCATION_ZIP" => $arUserResult["DELIVERY_LOCATION_ZIP"],
					"MAX_DIMENSIONS" => $arResult["MAX_DIMENSIONS"],
					"ITEMS" => $arResult["BASKET_ITEMS"]
				)
			);

			$bFirst = true;
			$arDeliveryServiceAll = array();
			$bFound = false;

			$rsDeliveryServicesList = CSaleDeliveryHandler::GetList(array("SORT" => "ASC"), $arFilter);

			while ($arDeliveryService = $rsDeliveryServicesList->Fetch())
			{
				if (!is_array($arDeliveryService) || !is_array($arDeliveryService["PROFILES"])) continue;

				if(!empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") !== false)
				{
					foreach ($arDeliveryService["PROFILES"] as $profile_id => $arDeliveryProfile)
					{
						if($arDeliveryProfile["ACTIVE"] == "Y")
						{
							$delivery_id = $arDeliveryService["SID"];
							if($arUserResult["DELIVERY_ID"] == $delivery_id.":".$profile_id)
								$bFound = true;
						}
					}
				}

				$arDeliveryServiceAll[] = $arDeliveryService;
			}

			if(!$bFound && !empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") !== false)
			{
				$arUserResult["DELIVERY_ID"] = "";
				$arResult["DELIVERY_PRICE"] = 0;
				$arResult["DELIVERY_PRICE_FORMATED"] = "";
			}

			//select delivery to paysystem
			$arUserResult["PAY_SYSTEM_ID"] = IntVal($arUserResult["PAY_SYSTEM_ID"]);
			$arUserResult["DELIVERY_ID"] = trim($arUserResult["DELIVERY_ID"]);
			$bShowDefaultSelected = True;
			$arD2P = array();
			$arP2D = array();
			$delivery = "";
			$bSelected = false;

			$dbRes = CSaleDelivery::GetDelivery2PaySystem(array());
			while ($arRes = $dbRes->Fetch())
			{
				$arD2P[$arRes["DELIVERY_ID"]][$arRes["PAYSYSTEM_ID"]] = $arRes["PAYSYSTEM_ID"];
				$arP2D[$arRes["PAYSYSTEM_ID"]][$arRes["DELIVERY_ID"]] = $arRes["DELIVERY_ID"];
				$bShowDefaultSelected = False;
			}

			if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p")
				$arP2D = array();

			if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d")
			{
				if(IntVal($arUserResult["PAY_SYSTEM_ID"]) <= 0)
				{
					$bFirst = True;
					$arFilter = array(
						"ACTIVE" => "Y",
						"PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
						"PSA_HAVE_PAYMENT" => "Y"
					);
					$dbPaySystem = CSalePaySystem::GetList(
								array("SORT" => "ASC", "PSA_NAME" => "ASC"),
								$arFilter
						);
					while ($arPaySystem = $dbPaySystem->Fetch())
					{
						if (IntVal($arUserResult["PAY_SYSTEM_ID"]) <= 0 && $bFirst)
						{
							$arPaySystem["CHECKED"] = "Y";
							$arUserResult["PAY_SYSTEM_ID"] = $arPaySystem["ID"];
						}
						$bFirst = false;
					}
				}
			}

			$bFirst = True;
			$bFound = false;
			$_SESSION["SALE_DELIVERY_EXTRA_PARAMS"] = array(); // here we will store params for params dialog

			//select calc delivery
			foreach($arDeliveryServiceAll as $arDeliveryService)
			{
				foreach ($arDeliveryService["PROFILES"] as $profile_id => $arDeliveryProfile)
				{
					if ($arDeliveryProfile["ACTIVE"] == "Y"
							&& (count($arP2D[$arUserResult["PAY_SYSTEM_ID"]]) <= 0
							|| in_array($arDeliveryService["SID"], $arP2D[$arUserResult["PAY_SYSTEM_ID"]])
							|| empty($arD2P[$arDeliveryService["SID"]])
							))
					{
						$delivery_id = $arDeliveryService["SID"];
						$arProfile = array(
							"SID" => $profile_id,
							"TITLE" => $arDeliveryProfile["TITLE"],
							"DESCRIPTION" => $arDeliveryProfile["DESCRIPTION"],
							"FIELD_NAME" => "DELIVERY_ID",
						);


						if((strlen($arUserResult["DELIVERY_ID"]) > 0 && $arUserResult["DELIVERY_ID"] == $delivery_id.":".$profile_id))
						{
							$arProfile["CHECKED"] = "Y";
							$arUserResult["DELIVERY_ID"] = $delivery_id.":".$profile_id;
							$bSelected = true;

							$arOrderTmpDel = array(
								"PRICE" => $arResult["ORDER_PRICE"],
								"WEIGHT" => $arResult["ORDER_WEIGHT"],
								"DIMENSIONS" => $arResult["ORDER_DIMENSIONS"],
								"LOCATION_FROM" => COption::GetOptionString('sale', 'location'),
								"LOCATION_TO" => $arUserResult["DELIVERY_LOCATION"],
								"LOCATION_ZIP" => $arUserResult["DELIVERY_LOCATION_ZIP"],
								"ITEMS" => $arResult["BASKET_ITEMS"],
								"EXTRA_PARAMS" => $arResult["DELIVERY_EXTRA"]
							);

							$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($delivery_id, $profile_id, $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

							if ($arDeliveryPrice["RESULT"] == "ERROR")
							{
								$arResult["ERROR"][] = $arDeliveryPrice["TEXT"];
							}
							else
							{
								$arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
								$arResult["PACKS_COUNT"] = $arDeliveryPrice["PACKS_COUNT"];
							}
						}

						if (empty($arResult["DELIVERY"][$delivery_id]))
						{
							$arResult["DELIVERY"][$delivery_id] = array(
								"SID" => $delivery_id,
								"SORT" => $arDeliveryService["SORT"],
								"TITLE" => $arDeliveryService["NAME"],
								"DESCRIPTION" => $arDeliveryService["DESCRIPTION"],
								"PROFILES" => array(),
							);
						}

						$arDeliveryExtraParams = CSaleDeliveryHandler::GetHandlerExtraParams($delivery_id, $profile_id, $arOrderTmpDel, SITE_ID);

						if(!empty($arDeliveryExtraParams))
						{
							$_SESSION["SALE_DELIVERY_EXTRA_PARAMS"][$delivery_id.":".$profile_id] = $arDeliveryExtraParams;
							$arResult["DELIVERY"][$delivery_id]["ISNEEDEXTRAINFO"] = "Y";
						}
						else
						{
							$arResult["DELIVERY"][$delivery_id]["ISNEEDEXTRAINFO"] = "N";
						}

						if(!empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") !== false)
						{
							if($arUserResult["DELIVERY_ID"] == $delivery_id.":".$profile_id)
								$bFound = true;
						}

						$arResult["DELIVERY"][$delivery_id]["LOGOTIP"] = $arDeliveryService["LOGOTIP"];
						$arResult["DELIVERY"][$delivery_id]["PROFILES"][$profile_id] = $arProfile;
						$bFirst = false;
					}
				}
			}
			if(!$bFound && !empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") !== false)
				$arUserResult["DELIVERY_ID"] = "";

			/*Old Delivery*/
			$arStoreId = array();
			$arDeliveryAll = array();
			$bFound = false;
			$bFirst = true;

			$dbDelivery = CSaleDelivery::GetList(
				array("SORT"=>"ASC", "NAME"=>"ASC"),
				array(
					"LID" => SITE_ID,
					"+<=WEIGHT_FROM" => $arResult["ORDER_WEIGHT"],
					"+>=WEIGHT_TO" => $arResult["ORDER_WEIGHT"],
					"+<=ORDER_PRICE_FROM" => $arResult["ORDER_PRICE"],
					"+>=ORDER_PRICE_TO" => $arResult["ORDER_PRICE"],
					"ACTIVE" => "Y",
					"LOCATION" => $arUserResult["DELIVERY_LOCATION"],
				)
			);
			while ($arDelivery = $dbDelivery->Fetch())
			{
				$arStore = array();
				if (strlen($arDelivery["STORE"]) > 0)
				{
					$arStore = unserialize($arDelivery["STORE"]);
					foreach ($arStore as $val)
						$arStoreId[$val] = $val;
				}

				$arDelivery["STORE"] = $arStore;

				if (isset($_POST["BUYER_STORE"]) && in_array($_POST["BUYER_STORE"], $arStore))
				{
					$arUserResult['DELIVERY_STORE'] = $arDelivery["ID"];
				}

				$arDeliveryDescription = CSaleDelivery::GetByID($arDelivery["ID"]);
				$arDelivery["DESCRIPTION"] = htmlspecialcharsbx($arDeliveryDescription["DESCRIPTION"]);

				$arDeliveryAll[] = $arDelivery;

				if(!empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") === false)
				{
					if(IntVal($arUserResult["DELIVERY_ID"]) == IntVal($arDelivery["ID"]))
						$bFound = true;
				}
				if(IntVal($arUserResult["DELIVERY_ID"]) == IntVal($arDelivery["ID"]))
				{
					$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
				}
			}
			if(!$bFound && !empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") === false)
			{
				$arUserResult["DELIVERY_ID"] = "";
			}

			$arStore = array();
			$dbList = CCatalogStore::GetList(
				array("SORT" => "DESC", "ID" => "DESC"),
				array("ACTIVE" => "Y", "ID" => $arStoreId, "ISSUING_CENTER" => "Y", "+SITE_ID" => SITE_ID),
				false,
				false,
				array("ID", "TITLE", "ADDRESS", "DESCRIPTION", "IMAGE_ID", "PHONE", "SCHEDULE", "GPS_N", "GPS_S", "ISSUING_CENTER", "SITE_ID")
			);
			while ($arStoreTmp = $dbList->Fetch())
			{
				if ($arStoreTmp["IMAGE_ID"] > 0)
					$arStoreTmp["IMAGE_ID"] = CFile::GetFileArray($arStoreTmp["IMAGE_ID"]);

				$arStore[$arStoreTmp["ID"]] = $arStoreTmp;
			}

			$arResult["STORE_LIST"] = $arStore;

			if(!$bFound && !empty($arUserResult["DELIVERY_ID"]) && strpos($arUserResult["DELIVERY_ID"], ":") === false)
				$arUserResult["DELIVERY_ID"] = "";

			foreach($arDeliveryAll as $arDelivery)
			{
				if (count($arP2D[$arUserResult["PAY_SYSTEM_ID"]]) <= 0 || in_array($arDelivery["ID"], $arP2D[$arUserResult["PAY_SYSTEM_ID"]]))
				{
					$arDelivery["FIELD_NAME"] = "DELIVERY_ID";
					if ((IntVal($arUserResult["DELIVERY_ID"]) == IntVal($arDelivery["ID"])))
					{
						$arDelivery["CHECKED"] = "Y";
						$arUserResult["DELIVERY_ID"] = $arDelivery["ID"];
						$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
						$bSelected = true;
					}
					if (IntVal($arDelivery["PERIOD_FROM"]) > 0 || IntVal($arDelivery["PERIOD_TO"]) > 0)
					{
						$arDelivery["PERIOD_TEXT"] = GetMessage("SALE_DELIV_PERIOD");
						if (IntVal($arDelivery["PERIOD_FROM"]) > 0)
							$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SOA_FROM")." ".IntVal($arDelivery["PERIOD_FROM"]);
						if (IntVal($arDelivery["PERIOD_TO"]) > 0)
							$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SOA_TO")." ".IntVal($arDelivery["PERIOD_TO"]);
						if ($arDelivery["PERIOD_TYPE"] == "H")
							$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SOA_HOUR")." ";
						elseif ($arDelivery["PERIOD_TYPE"]=="M")
							$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SOA_MONTH")." ";
						else
							$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SOA_DAY")." ";
					}

					if (intval($arDelivery["LOGOTIP"]) > 0)
						$arDelivery["LOGOTIP"] = CFile::GetFileArray($arDelivery["LOGOTIP"]);

					$arDelivery["PRICE_FORMATED"] = SaleFormatCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"]);
					$arResult["DELIVERY"][$arDelivery["ID"]] = $arDelivery;
					$bFirst = false;
				}
			}

			uasort($arResult["DELIVERY"], array('CSaleBasketHelper', 'cmpBySort')); // resort delivery arrays according to SORT value

			if(!$bSelected && !empty($arResult["DELIVERY"]))
			{
				$bf = true;
				foreach($arResult["DELIVERY"] as $k => $v)
				{
					if($bf)
					{
						if(IntVal($k) > 0)
						{
							$arResult["DELIVERY"][$k]["CHECKED"] = "Y";
							$arUserResult["DELIVERY_ID"] = $k;
							$bf = false;

							$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arResult["DELIVERY"][$k]["PRICE"], $arResult["DELIVERY"][$k]["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
						}
						else
						{
							foreach($v["PROFILES"] as $kk => $vv)
							{
								if($bf)
								{
									$arResult["DELIVERY"][$k]["PROFILES"][$kk]["CHECKED"] = "Y";
									$arUserResult["DELIVERY_ID"] = $k.":".$kk;
									$bf = false;

									$arOrderTmpDel = array(
										"PRICE" => $arResult["ORDER_PRICE"],
										"WEIGHT" => $arResult["ORDER_WEIGHT"],
										"DIMENSIONS" => $arResult["ORDER_DIMENSIONS"],
										"LOCATION_FROM" => COption::GetOptionString('sale', 'location'),
										"LOCATION_TO" => $arUserResult["DELIVERY_LOCATION"],
										"LOCATION_ZIP" => $arUserResult["DELIVERY_LOCATION_ZIP"],
										"ITEMS" => $arResult["BASKET_ITEMS"],
										"EXTRA_PARAMS" => $arResult["DELIVERY_EXTRA"]
									);

									$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($k, $kk, $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

									if ($arDeliveryPrice["RESULT"] == "ERROR")
									{
										$arResult["ERROR"][] = $arDeliveryPrice["TEXT"];
									}
									else
									{
										$arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
										$arResult["PACKS_COUNT"] = $arDeliveryPrice["PACKS_COUNT"];
									}
									break;
								}
							}
						}
					}
				}
			}

			if ($arUserResult["PAY_SYSTEM_ID"] > 0 || strlen($arUserResult["DELIVERY_ID"]) > 0)
			{
				if (strlen($arUserResult["DELIVERY_ID"]) > 0 && $arParams["DELIVERY_TO_PAYSYSTEM"] == "d2p")
				{
					if (strpos($arUserResult["DELIVERY_ID"], ":"))
					{
						$tmp = explode(":", $arUserResult["DELIVERY_ID"]);
						$delivery = trim($tmp[0]);
					}
					else
						$delivery = intval($arUserResult["DELIVERY_ID"]);
				}
			}

			if(DoubleVal($arResult["DELIVERY_PRICE"]) > 0)
				$arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);

			foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepDelivery", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array(&$arResult, &$arUserResult, &$arParams));
		}
		/* Delivery End */

		/* Pay Systems Begin */
		$arFilter = array(
							"ACTIVE" => "Y",
							"PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
							"PSA_HAVE_PAYMENT" => "Y"
						);
		if(!empty($arParams["DELIVERY2PAY_SYSTEM"]))
		{
			foreach($arParams["DELIVERY2PAY_SYSTEM"] as $val)
			{
				if(is_array($val[$arUserResult["DELIVERY_ID"]]))
				{
					foreach($val[$arUserResult["DELIVERY_ID"]] as $v)
						$arFilter["ID"][] = $v;
				}
				elseif(IntVal($val[$arUserResult["DELIVERY_ID"]]) > 0)
				{
					$arFilter["ID"][] = $val[$arUserResult["DELIVERY_ID"]];
				}
			}
		}
		if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d")
		{
			$arD2P = array();
		}

		if($arUserResult["PREPAYMENT_MODE"] && IntVal($arUserResult["PAY_SYSTEM_ID"]) > 0)
			$arFilter["ID"] = $arUserResult["PAY_SYSTEM_ID"];

		$bFirst = true;
		$dbPaySystem = CSalePaySystem::GetList(
					array("SORT" => "ASC", "PSA_NAME" => "ASC"),
					$arFilter
			);

		while ($arPaySystem = $dbPaySystem->Fetch())
		{
			//if (count($arD2P[$delivery]) <= 0 || in_array($arPaySystem["ID"], $arD2P[$delivery]))
			//{
			if(strlen($arUserResult["DELIVERY_ID"]) <= 0
					|| $arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d"
					|| CSaleDelivery2PaySystem::isPaySystemApplicable($arPaySystem["ID"], $arUserResult["DELIVERY_ID"])
					)
			{

				if(!CSalePaySystemsHelper::checkPSCompability(
																$arPaySystem["PSA_ACTION_FILE"],
																$arOrder,
																$arResult["ORDER_PRICE"],
																$arResult["DELIVERY_PRICE"],
																$arUserResult["DELIVERY_LOCATION"]
															))
				{
					continue;
				}

				if ($arPaySystem["PSA_LOGOTIP"] > 0)
					$arPaySystem["PSA_LOGOTIP"] = CFile::GetFileArray($arPaySystem["PSA_LOGOTIP"]);

				$arPaySystem["PSA_NAME"] = htmlspecialcharsEx($arPaySystem["PSA_NAME"]);
				$arResult["PAY_SYSTEM"][$arPaySystem["ID"]] = $arPaySystem;
				$arResult["PAY_SYSTEM"][$arPaySystem["ID"]]["PRICE"] = CSalePaySystemsHelper::getPSPrice(
																			$arPaySystem,
																			$arResult["ORDER_PRICE"],
																			$arResult["DELIVERY_PRICE"],
																			$arUserResult["DELIVERY_LOCATION"]);

				if (IntVal($arUserResult["PAY_SYSTEM_ID"]) == IntVal($arPaySystem["ID"]) || IntVal($arUserResult["PAY_SYSTEM_ID"]) <= 0 && $bFirst)
				{
					//$arPaySystem["CHECKED"] = "Y";
					$arResult["PAY_SYSTEM"][$arPaySystem["ID"]]["CHECKED"] = "Y";
					$arUserResult["PAY_SYSTEM_ID"] = $arPaySystem["ID"];
				}

				$bFirst = false;
			}
		}

		if(IntVal($arUserResult["PAY_SYSTEM_ID"]) > 0 && empty($arResult["PAY_SYSTEM"][$arUserResult["PAY_SYSTEM_ID"]]))
		{
			$bF = true;
			foreach($arResult["PAY_SYSTEM"] as $k => $v)
			{
				if($bF)
				{
					$arResult["PAY_SYSTEM"][$k]["CHECKED"] = "Y";
					$arUserResult["PAY_SYSTEM_ID"] = $arResult["PAY_SYSTEM"][$k]["ID"];
					$bF = false;
				}
			}
		}

		$arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);

		if(empty($arResult["PAY_SYSTEM"]) && $arUserResult["PAY_CURRENT_ACCOUNT"] != "Y")
			$arResult["ERROR"][] = GetMessage("SOA_ERROR_PAY_SYSTEM");

		foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepPaySystem", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arResult, &$arUserResult, &$arParams));
		/* Pay Systems End */

		/* Order properties related to the pay system and delivery system */
		if (count($arResult["ORDER_PROP"]["RELATED"]) == 0)
		{
			$arRelFilter = array(
				"PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
				"ACTIVE" => "Y",
				"UTIL" => "N"
			);

			if (intval($arUserResult["PAY_SYSTEM_ID"]) != 0 && $arUserResult["PAY_CURRENT_ACCOUNT"] != "Y")
				$arRelFilter["RELATED"]["PAYSYSTEM_ID"] = $arUserResult["PAY_SYSTEM_ID"];

			if ($arUserResult["DELIVERY_ID"] != false)
				$arRelFilter["RELATED"]["DELIVERY_ID"] = $arUserResult["DELIVERY_ID"];

			if (isset($arRelFilter["RELATED"]) && count($arRelFilter["RELATED"]) > 0)
			{
				$arRes = array();
				$dbRelatedProps = CSaleOrderProps::GetList(array(), $arRelFilter, false, false, array("*"));
				while ($arRelatedProps = $dbRelatedProps->GetNext())
					$arRes[] = getOrderPropFormated($arRelatedProps, $arResult, $arUserResult);

				$arResult["ORDER_PROP"]["RELATED"] = $arRes;
			}
		}
		/* End of related order properties */

		/* New discount */
		DiscountCouponsManager::init();

		foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepDiscountBefore", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arResult, &$arUserResult, &$arParams));

		// use later only items not part of the sets
		foreach ($arResult["BASKET_ITEMS"] as $id => $arItem)
		{
			if (CSaleBasketHelper::isSetItem($arItem))
				unset($arResult["BASKET_ITEMS"][$id]);
		}

		$arOrderDat = CSaleOrder::DoCalculateOrder(
			SITE_ID,
			$USER->GetID(),
			$arResult["BASKET_ITEMS"],
			$arUserResult['PERSON_TYPE_ID'],
			$arUserResult["ORDER_PROP"],
			$arUserResult["DELIVERY_ID"],
			$arUserResult["PAY_SYSTEM_ID"],
			array(),
			$arErrors,
			$arWarnings
		);

		$orderTotalSum = 0;
		if (empty($arOrderDat))
		{
			$arResult['ERROR'][] = GetMessage('SOA_ORDER_CALCULATE_ERROR');

			if (!empty($arResult["BASKET_ITEMS"]))
			{
				foreach ($arResult["BASKET_ITEMS"] as $key => &$arItem)
				{
					$arItem["SUM"] = SaleFormatCurrency($arItem["PRICE"] * $arItem["QUANTITY"], $arResult["BASE_LANG_CURRENCY"]);
					// prepare values for custom-looking columns
					$arCols = array("PROPS" => getPropsInfo($arItem));

					if (isset($arItem["PREVIEW_PICTURE"]) && intval($arItem["PREVIEW_PICTURE"]) > 0)
						$arCols["PREVIEW_PICTURE"] = CSaleHelper::getFileInfo($arItem["PREVIEW_PICTURE"], array("WIDTH" => 110, "HEIGHT" => 110));

					if (isset($arItem["DETAIL_PICTURE"]) && intval($arItem["DETAIL_PICTURE"]) > 0)
						$arCols["DETAIL_PICTURE"] = CSaleHelper::getFileInfo($arItem["DETAIL_PICTURE"], array("WIDTH" => 110, "HEIGHT" => 110));

					if (isset($arItem["MEASURE_TEXT"]) && strlen($arItem["MEASURE_TEXT"]) > 0)
						$arCols["QUANTITY"] = $arItem["QUANTITY"]."&nbsp;".$arItem["MEASURE_TEXT"];

					foreach ($arItem as $tmpKey => $value)
					{
						if ((strpos($tmpKey, "PROPERTY_", 0) === 0) && (strrpos($tmpKey, "_VALUE") == strlen($tmpKey) - 6))
						{
							$code = str_replace(array("PROPERTY_", "_VALUE"), "", $tmpKey);
							$propData = $arIblockProps[$code];
							$arCols[$tmpKey] = getIblockProps($value, $propData, array("WIDTH" => 110, "HEIGHT" => 110));
						}
					}

					$arResult["GRID"]["ROWS"][$arItem["ID"]] = array(
						"id" => $arItem["ID"],
						"data" => $arItem,
						"actions" => array(),
						"columns" => $arCols,
						"editable" => true
					);
				}
				unset($arItem);

				$oldOrder = CSaleOrder::CalculateOrderPrices($arResult["BASKET_ITEMS"]);
				if (!empty($oldOrder))
				{
					$arResult['ORDER_PRICE'] = $oldOrder['ORDER_PRICE'];
					$arResult["ORDER_PRICE_FORMATED"] = SaleFormatCurrency($arResult["ORDER_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
					$arResult["ORDER_WEIGHT"] = $oldOrder["ORDER_WEIGHT"];
					$arResult['VAT_SUM'] = $oldOrder['VAT_SUM'];
					$arResult["USE_VAT"] = ($oldOrder['USE_VAT'] == "Y");
					$arResult["VAT_SUM_FORMATED"] = SaleFormatCurrency($arResult["VAT_SUM"], $arResult["BASE_LANG_CURRENCY"]);
				}
				unset($oldOrder);
			}
		}
		else
		{
			$arResult["ORDER_PRICE"] = $arOrderDat['ORDER_PRICE'];
			$arResult["ORDER_PRICE_FORMATED"] = SaleFormatCurrency($arResult["ORDER_PRICE"], $arResult["BASE_LANG_CURRENCY"]);

			$arResult["USE_VAT"] = $arOrderDat['USE_VAT'];
			$arResult["VAT_SUM"] = $arOrderDat["VAT_SUM"];
			$arResult["VAT_SUM_FORMATED"] = SaleFormatCurrency($arResult["VAT_SUM"], $arResult["BASE_LANG_CURRENCY"]);


			$arResult['TAX_PRICE'] = $arOrderDat["TAX_PRICE"];
			$arResult['TAX_LIST'] = $arOrderDat["TAX_LIST"];

			$arResult['DISCOUNT_PRICE'] = $arOrderDat["DISCOUNT_PRICE"];

			$arResult['DELIVERY_PRICE'] = $arOrderDat['PRICE_DELIVERY'];
			$arResult['DELIVERY_PRICE_FORMATED'] = SaleFormatCurrency($arOrderDat["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);

			$arResult['BASKET_ITEMS'] = $arOrderDat['BASKET_ITEMS'];

			/* New discount end */

			if (!empty($arResult["BASKET_ITEMS"]))
			{
				foreach ($arResult["BASKET_ITEMS"] as $key => &$arItem)
				{
					$arItem["SUM"] = SaleFormatCurrency($arItem["PRICE"] * $arItem["QUANTITY"], $arResult["BASE_LANG_CURRENCY"]);
					// prepare values for custom-looking columns
					$arCols = array("PROPS" => getPropsInfo($arItem));

					if (isset($arItem["PREVIEW_PICTURE"]) && intval($arItem["PREVIEW_PICTURE"]) > 0)
						$arCols["PREVIEW_PICTURE"] = CSaleHelper::getFileInfo($arItem["PREVIEW_PICTURE"], array("WIDTH" => 110, "HEIGHT" => 110));

					if (isset($arItem["DETAIL_PICTURE"]) && intval($arItem["DETAIL_PICTURE"]) > 0)
						$arCols["DETAIL_PICTURE"] = CSaleHelper::getFileInfo($arItem["DETAIL_PICTURE"], array("WIDTH" => 110, "HEIGHT" => 110));

					if (isset($arItem["MEASURE_TEXT"]) && strlen($arItem["MEASURE_TEXT"]) > 0)
						$arCols["QUANTITY"] = $arItem["QUANTITY"]."&nbsp;".$arItem["MEASURE_TEXT"];

					foreach ($arItem as $tmpKey => $value)
					{
						if ((strpos($tmpKey, "PROPERTY_", 0) === 0) && (strrpos($tmpKey, "_VALUE") == strlen($tmpKey) - 6))
						{
							$code = str_replace(array("PROPERTY_", "_VALUE"), "", $tmpKey);
							$propData = $arIblockProps[$code];
							$arCols[$tmpKey] = getIblockProps($value, $propData, array("WIDTH" => 110, "HEIGHT" => 110));
						}
					}

					$arResult["GRID"]["ROWS"][$arItem["ID"]] = array(
						"id" => $arItem["ID"],
						"data" => $arItem,
						"actions" => array(),
						"columns" => $arCols,
						"editable" => true
					);
				}
				unset($arItem);
			}

			/* Tax Begin */

			$orderTotalSum = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];

			if($arParams["PAY_FROM_ACCOUNT"] == "Y")
			{
				$dbUserAccount = CSaleUserAccount::GetList(
						array(),
						array(
								"USER_ID" => $USER->GetID(),
								"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
							)
					);
				if ($arUserAccount = $dbUserAccount->GetNext())
				{
					if ($arUserAccount["CURRENT_BUDGET"] <= 0)
					{
						$arResult["PAY_FROM_ACCOUNT"] = "N";
					}
					else
					{
						if($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y")
						{
							if(DoubleVal($arUserAccount["CURRENT_BUDGET"]) >= DoubleVal($orderTotalSum))
							{
								$arResult["PAY_FROM_ACCOUNT"] = "Y";
								$arResult["CURRENT_BUDGET_FORMATED"] = SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $arResult["BASE_LANG_CURRENCY"]);
								$arResult["USER_ACCOUNT"] = $arUserAccount;
							}
							else
								$arResult["PAY_FROM_ACCOUNT"] = "N";
						}
						else
						{
							$arResult["PAY_FROM_ACCOUNT"] = "Y";
							$arResult["CURRENT_BUDGET_FORMATED"] = SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $arResult["BASE_LANG_CURRENCY"]);
							$arResult["USER_ACCOUNT"] = $arUserAccount;
						}
					}

				}
				else
					$arResult["PAY_FROM_ACCOUNT"] = "N";
			}
			if($arUserResult["PAY_CURRENT_ACCOUNT"] == "Y")
			{
				if ($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"] > 0)
				{
					$arResult["PAYED_FROM_ACCOUNT_FORMATED"] = SaleFormatCurrency((($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"] >= $orderTotalSum) ? $orderTotalSum : $arResult["USER_ACCOUNT"]["CURRENT_BUDGET"]), $arResult["BASE_LANG_CURRENCY"]);

				}
			}

			$arResult["ORDER_TOTAL_PRICE_FORMATED"] = SaleFormatCurrency($orderTotalSum, $arResult["BASE_LANG_CURRENCY"]);
			/* Tax End */

			foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepProcess", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, Array(&$arResult, &$arUserResult, &$arParams));

			$orderTotalSum = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
		}

		if($arUserResult["CONFIRM_ORDER"] == "Y" && empty($arResult["ERROR"]))
		{
			if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "Y")
			{
				if(strlen($arUserResult["USER_EMAIL"]) > 0)
				{
					$NEW_LOGIN = $arUserResult["USER_EMAIL"];
					$NEW_EMAIL = $arUserResult["USER_EMAIL"];
					$NEW_NAME = "";
					$NEW_LAST_NAME = "";

					if(strlen($arUserResult["PAYER_NAME"]) > 0)
					{
						$arNames = explode(" ", $arUserResult["PAYER_NAME"]);
						$NEW_NAME = $arNames[1];
						$NEW_LAST_NAME = $arNames[0];
					}

					$pos = strpos($NEW_LOGIN, "@");
					if ($pos !== false)
						$NEW_LOGIN = substr($NEW_LOGIN, 0, $pos);

					if (strlen($NEW_LOGIN) > 47)
						$NEW_LOGIN = substr($NEW_LOGIN, 0, 47);

					if (strlen($NEW_LOGIN) < 3)
						$NEW_LOGIN .= "_";

					if (strlen($NEW_LOGIN) < 3)
						$NEW_LOGIN .= "_";

					$dbUserLogin = CUser::GetByLogin($NEW_LOGIN);
					if ($arUserLogin = $dbUserLogin->Fetch())
					{
						$newLoginTmp = $NEW_LOGIN;
						$uind = 0;
						do
						{
							$uind++;
							if ($uind == 10)
							{
								$NEW_LOGIN = $arUserResult["USER_EMAIL"];
								$newLoginTmp = $NEW_LOGIN;
							}
							elseif ($uind > 10)
							{
								$NEW_LOGIN = "buyer".time().GetRandomCode(2);
								$newLoginTmp = $NEW_LOGIN;
								break;
							}
							else
							{
								$newLoginTmp = $NEW_LOGIN.$uind;
							}
							$dbUserLogin = CUser::GetByLogin($newLoginTmp);
						}
						while ($arUserLogin = $dbUserLogin->Fetch());
						$NEW_LOGIN = $newLoginTmp;
					}

					$GROUP_ID = array(2);
					$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
					if($def_group!="")
					{
						$GROUP_ID = explode(",", $def_group);
						$arPolicy = $USER->GetGroupPolicy($GROUP_ID);
					}
					else
					{
						$arPolicy = $USER->GetGroupPolicy(array());
					}

					$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
					if($password_min_length <= 0)
						$password_min_length = 6;
					$password_chars = array(
						"abcdefghijklnmopqrstuvwxyz",
						"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
						"0123456789",
					);
					if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
						$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
					$NEW_PASSWORD = $NEW_PASSWORD_CONFIRM = randString($password_min_length+2, $password_chars);

					$user = new CUser;
					$arAuthResult = $user->Add(Array(
						"LOGIN" => $NEW_LOGIN,
						"NAME" => $NEW_NAME,
						"LAST_NAME" => $NEW_LAST_NAME,
						"PASSWORD" => $NEW_PASSWORD,
						"CONFIRM_PASSWORD" => $NEW_PASSWORD_CONFIRM,
						"EMAIL" => $NEW_EMAIL,
						"GROUP_ID" => $GROUP_ID,
						"ACTIVE" => "Y",
						"LID" => SITE_ID,
						)
						);

					if (IntVal($arAuthResult) <= 0)
					{
						$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG").((strlen($user->LAST_ERROR) > 0) ? ": ".$user->LAST_ERROR : "" );
					}
					else
					{
						$USER->Authorize($arAuthResult);
						if ($USER->IsAuthorized())
						{
							if($arParams["SEND_NEW_USER_NOTIFY"] == "Y")
								CUser::SendUserInfo($USER->GetID(), SITE_ID, GetMessage("INFO_REQ"), true);
						}
						else
						{
							$arResult["ERROR"][] = GetMessage("STOF_ERROR_REG_CONFIRM");
						}
					}
				}
				else
					$arResult["ERROR"][] = GetMessage("STOF_ERROR_EMAIL");
			}

			if ($arUserResult["PAY_SYSTEM_ID"] <= 0 && $arUserResult["PAY_CURRENT_ACCOUNT"] != "Y")
				$arResult["ERROR"][] = GetMessage("STOF_ERROR_PAY_SYSTEM");

			if($USER->IsAuthorized() && empty($arResult["ERROR"]))
			{
				$arFields = array(
						"LID" => SITE_ID,
						"PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
						"PAYED" => "N",
						"CANCELED" => "N",
						"STATUS_ID" => "N",
						"PRICE" => $orderTotalSum,
						"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
						"USER_ID" => (int)$USER->GetID(),
						"PAY_SYSTEM_ID" => $arUserResult["PAY_SYSTEM_ID"],
						"PRICE_DELIVERY" => $arResult["DELIVERY_PRICE"],
						"DELIVERY_ID" => (strlen($arUserResult["DELIVERY_ID"]) > 0 ? $arUserResult["DELIVERY_ID"] : false),
						"DISCOUNT_VALUE" => $arResult["DISCOUNT_PRICE"],
						"TAX_VALUE" => $arResult["bUsingVat"] == "Y" ? $arResult["VAT_SUM"] : $arResult["TAX_PRICE"],
						"USER_DESCRIPTION" => $arUserResult["~ORDER_DESCRIPTION"]
				);

				$arOrderDat['USER_ID'] = $arFields['USER_ID'];

				if (IntVal($_POST["BUYER_STORE"]) > 0 && $arUserResult["DELIVERY_ID"] == $arUserResult["DELIVERY_STORE"])
					$arFields["STORE_ID"] = IntVal($_POST["BUYER_STORE"]);

				// add Guest ID
				if (Loader::includeModule("statistic"))
					$arFields["STAT_GID"] = CStatistic::GetEventParam();

				$affiliateID = CSaleAffiliate::GetAffiliate();
				if ($affiliateID > 0)
				{
					$dbAffiliat = CSaleAffiliate::GetList(array(), array("SITE_ID" => SITE_ID, "ID" => $affiliateID));
					$arAffiliates = $dbAffiliat->Fetch();
					if (count($arAffiliates) > 1)
						$arFields["AFFILIATE_ID"] = $affiliateID;
				}
				else
					$arFields["AFFILIATE_ID"] = false;

				$arResult["ORDER_ID"] = (int)CSaleOrder::DoSaveOrder($arOrderDat, $arFields, 0, $arResult["ERROR"]);

				$arOrder = array();
				if ($arResult["ORDER_ID"] > 0 && empty($arResult["ERROR"]))
				{
					$arOrder = CSaleOrder::GetByID($arResult["ORDER_ID"]);
					CSaleBasket::OrderBasket($arResult["ORDER_ID"], CSaleBasket::GetBasketUserID(), SITE_ID, false);

					$arResult["ACCOUNT_NUMBER"] = ($arResult["ORDER_ID"] <= 0) ? $arResult["ORDER_ID"] : $arOrder["ACCOUNT_NUMBER"];
				}

				$withdrawSum = 0.0;
				if (empty($arResult["ERROR"]))
				{
					if ($arResult["PAY_FROM_ACCOUNT"] == "Y" && $arUserResult["PAY_CURRENT_ACCOUNT"] == "Y"
						&& (($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && DoubleVal($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"]) >= DoubleVal($orderTotalSum)) || $arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] != "Y"))
					{
						$withdrawSum = CSaleUserAccount::Withdraw(
								$USER->GetID(),
								$orderTotalSum,
								$arResult["BASE_LANG_CURRENCY"],
								$arResult["ORDER_ID"]
							);

						if ($withdrawSum > 0)
						{
							$arFields = array(
									"SUM_PAID" => $withdrawSum,
									"USER_ID" => $USER->GetID()
								);
							CSaleOrder::Update($arResult["ORDER_ID"], $arFields);

							if ($withdrawSum == $orderTotalSum)
							{
								CSaleOrder::PayOrder($arResult["ORDER_ID"], "Y", False, False);
							}


						}
					}
					if($arResult["HAVE_PREPAYMENT"])
					{
						if($psPreAction && $psPreAction->IsAction())
						{
							$psPreAction->orderId = $arResult["ORDER_ID"];
							$psPreAction->orderAmount = $orderTotalSum;
							$psPreAction->deliveryAmount = $arResult["DELIVERY_PRICE"];
							$psPreAction->taxAmount = $arResult["TAX_PRICE"];
							$orderData = array();
							$dbBasketItems = CSaleBasket::GetList(
								array("ID" => "ASC"),
								array(
										"FUSER_ID" => CSaleBasket::GetBasketUserID(),
										"LID" => SITE_ID,
										"ORDER_ID" => $arResult["ORDER_ID"]
									),
								false,
								false,
								array("ID", "QUANTITY", "PRICE", "WEIGHT", "NAME", "CURRENCY", "PRODUCT_ID", "DETAIL_PAGE_URL")
							);
							while ($arItem = $dbBasketItems->Fetch())
								$orderData['BASKET_ITEMS'][] = $arItem;

							$psPreAction->payOrder($orderData);
						}
					}
				}

				if (empty($arResult["ERROR"]))
				{
					CSaleOrderUserProps::DoSaveUserProfile($USER->GetID(), $arUserResult["PROFILE_ID"], $arUserResult["PROFILE_NAME"], $arUserResult["PERSON_TYPE_ID"], $arUserResult["ORDER_PROP"], $arResult["ERROR"]);
				}

				// mail message
				if (empty($arResult["ERROR"]))
				{
					$strOrderList = "";
					$arBasketList = array();
					$dbBasketItems = CSaleBasket::GetList(
							array("ID" => "ASC"),
							array("ORDER_ID" => $arResult["ORDER_ID"]),
							false,
							false,
							array("ID", "PRODUCT_ID", "NAME", "QUANTITY", "PRICE", "CURRENCY", "TYPE", "SET_PARENT_ID")
						);
					while ($arItem = $dbBasketItems->Fetch())
					{
						if (CSaleBasketHelper::isSetItem($arItem))
							continue;

						$arBasketList[] = $arItem;
					}

					$arBasketList = getMeasures($arBasketList);

					if (!empty($arBasketList) && is_array($arBasketList))
					{
						foreach ($arBasketList as $arItem)
						{
							$measureText = (isset($arItem["MEASURE_TEXT"]) && strlen($arItem["MEASURE_TEXT"])) ? $arItem["MEASURE_TEXT"] : GetMessage("SOA_SHT");

							$strOrderList .= $arItem["NAME"]." - ".$arItem["QUANTITY"]." ".$measureText.": ".SaleFormatCurrency($arItem["PRICE"], $arItem["CURRENCY"]);
							$strOrderList .= "\n";
						}
					}

					$arFields = array(
						"ORDER_ID" => $arOrder["ACCOUNT_NUMBER"],
						"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", SITE_ID))),
						"ORDER_USER" => ( (strlen($arUserResult["PAYER_NAME"]) > 0) ? $arUserResult["PAYER_NAME"] : $USER->GetFormattedName(false)),
						"PRICE" => SaleFormatCurrency($orderTotalSum, $arResult["BASE_LANG_CURRENCY"]),
						"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
						"EMAIL" => (strlen($arUserResult["USER_EMAIL"])>0 ? $arUserResult["USER_EMAIL"] : $USER->GetEmail()),
						"ORDER_LIST" => $strOrderList,
						"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
						"DELIVERY_PRICE" => $arResult["DELIVERY_PRICE"],
					);

					$eventName = "SALE_NEW_ORDER";

					$bSend = true;
					foreach(GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
						if (ExecuteModuleEventEx($arEvent, array($arResult["ORDER_ID"], &$eventName, &$arFields))===false)
							$bSend = false;

					if($bSend)
					{
						$event = new CEvent;
						$event->Send($eventName, SITE_ID, $arFields, "N");
					}

					CSaleMobileOrderPush::send("ORDER_CREATED", array("ORDER_ID" => $arResult["ORDER_ID"]));
				}

				if (empty($arResult["ERROR"]))
				{
					if(Loader::includeModule("statistic"))
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

					foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepComplete", true) as $arEvent)
						ExecuteModuleEventEx($arEvent, Array($arResult["ORDER_ID"], $arOrder, $arParams));
				}

				if (empty($arResult["ERROR"]))
				{
					$arResult["REDIRECT_URL"] = $APPLICATION->GetCurPageParam("ORDER_ID=".urlencode(urlencode($arOrder["ACCOUNT_NUMBER"])), Array("ORDER_ID"));

					if(array_key_exists('json', $_REQUEST) && $_REQUEST['json'] == "Y" && ($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y"))
					{
						if($arUserResult["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
						{
							$APPLICATION->RestartBuffer();
							echo json_encode(array("success" => "Y", "redirect" => $arResult["REDIRECT_URL"]));
							die();
						}
					}
				}
				else
					$arUserResult["CONFIRM_ORDER"] = "N";
			}
			else
			{
				$arUserResult["CONFIRM_ORDER"] = "N";
			}
		}
		else
		{
			$arUserResult["CONFIRM_ORDER"] = "N";
		}

		$arResult["USER_VALS"] = $arUserResult;
	}
	else
	{
		$arOrder = false;
		$arResult["USER_VALS"]["CONFIRM_ORDER"] = "Y";
		$ID = urldecode(urldecode($_REQUEST["ORDER_ID"]));

		if ($bUseAccountNumber) // supporting ACCOUNT_NUMBER or ID in the request
		{
			$dbOrder = CSaleOrder::GetList(
				array("DATE_UPDATE" => "DESC"),
				array(
					"LID" => SITE_ID,
					"ACCOUNT_NUMBER" => $ID
				)
			);
			if ($arOrder = $dbOrder->GetNext())
			{
				$arResult["ORDER_ID"] = $arOrder["ID"];
				$arResult["ACCOUNT_NUMBER"] = $arOrder["ACCOUNT_NUMBER"];
			}
		}

		if (!$arOrder)
		{
			$dbOrder = CSaleOrder::GetList(
				array("DATE_UPDATE" => "DESC"),
				array(
					"LID" => SITE_ID,
					"ID" => $ID
				)
			);

			if($arOrder = $dbOrder->GetNext())
			{
				$arResult["ORDER_ID"] = $ID;
				$arResult["ACCOUNT_NUMBER"] = $arOrder["ACCOUNT_NUMBER"];
			}
		}
		if($arOrder)
		{
			foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepFinal", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, Array($arResult["ORDER_ID"], &$arOrder, &$arParams));
		}

		if ($arOrder && $arOrder["USER_ID"] == IntVal($USER->GetID()))
		{
			if (IntVal($arOrder["PAY_SYSTEM_ID"]) > 0 && $arOrder["PAYED"] != "Y")
			{
				$dbPaySysAction = CSalePaySystemAction::GetList(
						array(),
						array(
								"PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
								"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"]
							),
						false,
						false,
						array("NAME", "ACTION_FILE", "NEW_WINDOW", "PARAMS", "ENCODING", "LOGOTIP")
					);
				if ($arPaySysAction = $dbPaySysAction->Fetch())
				{
					$arPaySysAction["NAME"] = htmlspecialcharsEx($arPaySysAction["NAME"]);
					if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
					{
						if ($arPaySysAction["NEW_WINDOW"] != "Y")
						{
							CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], $arPaySysAction["PARAMS"]);

							$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];

							$pathToAction = str_replace("\\", "/", $pathToAction);
							while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
								$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

							if (file_exists($pathToAction))
							{
								if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
									$pathToAction .= "/payment.php";

								$arPaySysAction["PATH_TO_ACTION"] = $pathToAction;
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

					if ($arPaySysAction > 0)
						$arPaySysAction["LOGOTIP"] = CFile::GetFileArray($arPaySysAction["LOGOTIP"]);

					$arResult["PAY_SYSTEM"] = $arPaySysAction;
				}
			}
			$arResult["ORDER"] = $arOrder;
		}
	}
}

if(!$isAjaxRequest)
{
	CJSCore::Init(array('fx', 'popup', 'window', 'ajax'));
}

$this->IncludeComponentTemplate();

if ($isAjaxRequest)
{
	die();
}
?>