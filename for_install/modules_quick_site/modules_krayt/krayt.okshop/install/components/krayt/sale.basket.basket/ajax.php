<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
	return;

global $USER, $APPLICATION;

if (!check_bitrix_sessid() || $_SERVER["REQUEST_METHOD"] != "POST")
{
	return;
}

include(dirname(__FILE__)."/functions.php");

CUtil::JSPostUnescape();

$arRes = array();
$newProductId = false;
$newBasketId = false;
$action_var = (isset($_POST["action_var"]) && strlen(trim($_POST["action_var"])) > 0) ? trim($_POST["action_var"]) : "action";
$arErrors = array();

if (isset($_POST[$action_var]) && strlen($_POST[$action_var]) > 0)
{
	
	$arPropsValues = isset($_POST["props"]) ? $_POST["props"] : array();
	$strColumns = isset($_POST["select_props"]) ? $_POST["select_props"] : "";
	$arColumns = explode(",", $strColumns);
	$strOffersProps = isset($_POST["offers_props"]) ? $_POST["offers_props"] : "";
	$strOffersProps = explode(",", $strOffersProps);

	if ($_POST[$action_var] == "select_item")
	{
		$arSelect = array("ID", "IBLOCK_ID", "PROPERTY_CML2_LINK","XML_ID");
		foreach ($arColumns as &$columnName)
		{
			if ((strpos($columnName, "PROPERTY_", 0) === 0))
			{
				$columnName = str_replace("_VALUE", "", $columnName);
				$arSelect[] = $columnName;
			}
		}
		unset($columnName);

		$arItemSelect = array(
			"ID",
			"XML_ID",
			"PRODUCT_ID",
			"PRICE",
			"CURRENCY",
			"WEIGHT",
			"QUANTITY",
			"MODULE",
			"PRODUCT_PROVIDER_CLASS",
			"CALLBACK_FUNC",
		);

		$dbItemRes = CSaleBasket::GetList(array(),
			array("ID" => intval($_POST["basketItemId"])),
			false,
			false,
			$arItemSelect
		);

		if($arItem = $dbItemRes->Fetch())
		{
			$dbProp = CSaleBasket::GetPropsList(
				array("SORT" => "ASC", "ID" => "ASC"),
				array("BASKET_ID" => $arItem["ID"])
			);
			while ($arProp = $dbProp->GetNext())
			{
				$arItem["PROPS"][] = $arProp;
			}
		}


		$dbRes = CIBlockElement::GetList(
			array("SORT" => "ASC", "ID" => "ASC"),
			array("ID" => $arItem["PRODUCT_ID"]),
			false,
			false,
			$arSelect
		);

		if ($arElement = $dbRes->Fetch())
		{

			$bBasketUpdate = false;
			$arPropsValues["CML2_LINK"] = $arElement["PROPERTY_CML2_LINK_VALUE"];

			$newProductId = getProductByProps($arElement["IBLOCK_ID"], $arPropsValues);

			if ($newProductId > 0)
			{

				if ($productProvider = CSaleBasket::GetProductProvider($arItem))
				{
					$arFieldsTmp = $productProvider::GetProductData(array(
						"PRODUCT_ID" => $newProductId,
						"QUANTITY"   => $arItem['QUANTITY'],
						"RENEWAL"    => "N",
						"USER_ID"    => $USER->GetID(),
						"SITE_ID"    => SITE_ID,
						"CHECK_QUANTITY" => "Y",
						"CHECK_PRICE" => "N"
					));
				}
				elseif (isset($arItem["CALLBACK_FUNC"]) && strlen($arItem["CALLBACK_FUNC"]) > 0)
				{
					$arFieldsTmp = CSaleBasket::ExecuteCallbackFunction(
						$arItem["CALLBACK_FUNC"],
						$arItem["MODULE"],
						$newProductId,
						$arItem['QUANTITY'],
						"N",
						$USER->GetID(),
						SITE_ID
					);
				}

				if (isset($arFieldsTmp) && is_array($arFieldsTmp))
				{
					$arFields = array();
					$dbProduct = CIBlockElement::GetList(array(), array("ID" => $newProductId), false, false, array('IBLOCK_ID', 'IBLOCK_SECTION_ID','XML_ID'));
					if ($arProduct = $dbProduct->Fetch())
					{
						$arParentSku = CCatalogSku::GetProductInfo($newProductId, $arElement["IBLOCK_ID"]);
						if ($arParentSku && count($arParentSku) > 0)
						{
							$arProps = array();

							$dbParentProduct = CIBlockElement::GetList(array(), array("ID" => $arParentSku['ID']), false, false, array('IBLOCK_ID', 'IBLOCK_SECTION_ID','XML_ID'));
							if ($arParentProduct = $dbParentProduct->Fetch())
							{
								$arProduct["XML_ID"] = $arParentProduct['XML_ID'].'#'.$arProduct["XML_ID"];
							}

							$arFields = array(
								"PRODUCT_ID" => $newProductId,
								"QUANTITY" => $arFieldsTmp['QUANTITY'],
								"WEIGHT" => $arFieldsTmp['WEIGHT'],
							);


							$dbOfferProperties = CIBlock::GetProperties($arProduct["IBLOCK_ID"], array(), array("!XML_ID" => "CML2_LINK"));
							while($arOfferProperties = $dbOfferProperties->Fetch())
							{
								$arPropsSku[] = $arOfferProperties["CODE"];
							}

							$product_properties = CIBlockPriceTools::GetOfferProperties(
								$newProductId,
								$arParentSku["IBLOCK_ID"],
								$arPropsSku
							);

							foreach ($product_properties as $productSkuProp)
							{
								$bFieldExists = false;
								foreach ($strOffersProps as $existingSkuProp)
								{
									if ($existingSkuProp == $productSkuProp["CODE"])
									{
										$bFieldExists = true;
										break;
									}
								}

								if ($bFieldExists === true)
								{
									$arFields["PROPS"][] = array(
										"NAME" => $productSkuProp["NAME"],
										"CODE" => $productSkuProp["CODE"],
										"VALUE" => $productSkuProp["VALUE"],
										"SORT" => $productSkuProp["SORT"]
									);
								}
							}

							$arFields["PROPS"][] = array(
								"NAME" => "Product XML_ID",
								"CODE" => "PRODUCT.XML_ID",
								"VALUE" => $arProduct["XML_ID"]
							);

							// get price

							$arCallbackPrice = CSaleBasket::ReReadPrice("", "catalog", $newProductId, $arFieldsTmp['QUANTITY'], "N", "CCatalogProductProvider");
							if (!is_array($arCallbackPrice) || empty($arCallbackPrice))
							{
								$arErrors[] = GetMessage('SBB_PRODUCT_PRICE_NOT_FOUND');
							}else
							{
								$arFields['PRODUCT_PRICE_ID'] = $arCallbackPrice["PRODUCT_PRICE_ID"];
								$arFields['PRICE'] = $arCallbackPrice["PRICE"];
								$arFields['CURRENCY'] = $arCallbackPrice["CURRENCY"];
							}


						}

						if ((!is_array($arErrors) || empty($arErrors)) && is_array($arFields) && !empty($arFields) > 0)
						{
							$bBasketUpdate = CSaleBasket::Update($arItem['ID'], $arFields);
						}
					}
				}
			}

			if ($bBasketUpdate === true)
			{
				CBitrixComponent::includeComponentClass("bitrix:sale.basket.basket");

				$basket = new CBitrixBasketComponent();

				$basket->weightKoef = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));
				$basket->weightUnit = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
				$basket->columns = $arColumns;
				$basket->offersProps = $strOffersProps;

				$basket->quantityFloat = (isset($_POST["quantity_float"]) && $_POST["quantity_float"] == "Y") ? "Y" : "N";
				$basket->countDiscount4AllQuantity = (isset($_POST["count_discount_4_all_quantity"]) && $_POST["count_discount_4_all_quantity"] == "Y") ? "Y" : "N";
				$basket->priceVatShowValue = (isset($_POST["price_vat_show_value"]) && $_POST["price_vat_show_value"] == "Y") ? "Y" : "N";
				$basket->hideCoupon = (isset($_POST["hide_coupon"]) && $_POST["hide_coupon"] == "Y") ? "Y" : "N";
				$basket->usePrepayment = (isset($_POST["use_prepayment"]) && $_POST["use_prepayment"] == "Y") ? "Y" : "N";

				$columnsData = $basket->getCustomColumns();
				$basketData  = $basket->getBasketItems();

				$arRes["DELETE_ORIGINAL"] = "Y";
				$arRes["BASKET_DATA"] = $basketData;
				$arRes["BASKET_DATA"]["GRID"]["HEADERS"] = $columnsData;
				$arRes["COLUMNS"] = $strColumns;

				$arRes["BASKET_ID"] = $arItem['ID'];
			}

			$arRes["CODE"] = ($bBasketUpdate === true) ? "SUCCESS" : "ERROR";
			if ($bBasketUpdate === false && is_array($arErrors) && !empty($arErrors))
			{
				foreach ($arErrors as $error)
				{
					$arRes["MESSAGE"] .= (strlen($arRes["MESSAGE"]) > 0 ? "<br/>" : ""). $error;
				}
			}

		}
	}
	else if ($_POST[$action_var] == "recalculate")
	{
		// todo: extract duplicated code to function

		CBitrixComponent::includeComponentClass("bitrix:sale.basket.basket");

		$basket = new CBitrixBasketComponent();

		$basket->weightKoef = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));
		$basket->weightUnit = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
		$basket->columns = $arColumns;
		$basket->offersProps = explode(",", $strOffersProps);

		$basket->quantityFloat = (isset($_POST["quantity_float"]) && $_POST["quantity_float"] == "Y") ? "Y" : "N";
		$basket->countDiscount4AllQuantity = (isset($_POST["count_discount_4_all_quantity"]) && $_POST["count_discount_4_all_quantity"] == "Y") ? "Y" : "N";
		$basket->priceVatShowValue = (isset($_POST["price_vat_show_value"]) && $_POST["price_vat_show_value"] == "Y") ? "Y" : "N";
		$basket->hideCoupon = (isset($_POST["hide_coupon"]) && $_POST["hide_coupon"] == "Y") ? "Y" : "N";
		$basket->usePrepayment = (isset($_POST["use_prepayment"]) && $_POST["use_prepayment"] == "Y") ? "Y" : "N";

		$res = $basket->recalculateBasket($_POST);

		foreach ($res as $key => $value)
		{
			$arRes[$key] = $value;
		}

		$arRes["BASKET_DATA"] = $basket->getBasketItems();
		$arRes["BASKET_DATA"]["GRID"]["HEADERS"] = $basket->getCustomColumns();
		$arRes["COLUMNS"] = $strColumns;

		$arRes["CODE"] = "SUCCESS";
	}
}

$arRes["PARAMS"]["QUANTITY_FLOAT"] = (isset($_POST["quantity_float"]) && $_POST["quantity_float"] == "Y") ? "Y" : "N";

$APPLICATION->RestartBuffer();
header('Content-Type: application/json; charset='.LANG_CHARSET);
echo CUtil::PhpToJSObject($arRes);
die();
?>
