<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
/********************************
Get Prices from linked price list
********************************/
if($arParams["LINK_IBLOCK_ID"] && $arParams["LINK_PROPERTY_SID"] && count($arResult["LINKED_ELEMENTS"]))
{
	global $CACHE_MANAGER;
	//SELECT
	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"XML_ID",
	);
	if(is_array($arParams["OFFERS_FIELDS"]))
		foreach($arParams["OFFERS_FIELDS"] as $key => $FIELD_CODE)
		{
			if($FIELD_CODE)
			{
				$FIELD_CODE = ToUpper($FIELD_CODE);
				$arParams["OFFERS_FIELDS"][$key] = $FIELD_CODE;
				$arSelect[] = $FIELD_CODE;
			}
		}
	$bProperty = false;
	if(is_array($arParams["OFFERS_PROPERTIES"]))
		foreach($arParams["OFFERS_PROPERTIES"] as $PROPERTY_CODE)
			if($PROPERTY_CODE)
			{
				$bProperty = true;
				break;
			}
	if($bProperty)
		$arSelect[] = "PROPERTY_*";
	//WHERE
	$arID = array();
	$arMap = array();
	foreach($arResult["LINKED_ELEMENTS"] as $key=>$arItem)
	{
		$arID[] = $arItem["ID"];
		$arMap[$arItem["ID"]] = $key;
	}

	$arFilter = array(
		"ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"ID" => $arID,
	);
	//ORDER BY
	$arSort = array(
		"ID" => "ASC",
	);
	//PRICES
	$arPriceTypeID = array();
	if (!$arParams["USE_PRICE_COUNT"])
	{
		foreach($arResult["CAT_PRICES"] as &$value)
		{
			$arSelect[] = $value["SELECT"];
			$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
		}
		if (isset($value))
			unset($value);
	}
	else
	{
		foreach($arResult["CAT_PRICES"] as &$value)
		{
			$arPriceTypeID[] = $value["ID"];
		}
		if (isset($value))
			unset($value);
	}

	$arCurrencyList = array();
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
	while($obElement = $rsElements->GetNextElement())
	{
		$arElement = $obElement->GetFields();
		if($bProperty)
			$arProperties = $obElement->GetProperties();
		$ID = $arElement["ID"];
		$arItem = &$arResult["LINKED_ELEMENTS"][$arMap[$ID]];

		if(is_array($arParams["OFFERS_FIELDS"]))
			foreach($arParams["OFFERS_FIELDS"] as $FIELD_CODE)
				if($FIELD_CODE)
				{
					$arItem[$FIELD_CODE] = $arElement[$FIELD_CODE];
					$arItem["~".$FIELD_CODE] = $arElement["~".$FIELD_CODE];
				}

		$arItem["DISPLAY_PROPERTIES"] = array();
		if(is_array($arParams["OFFERS_PROPERTIES"]))
			foreach($arParams["OFFERS_PROPERTIES"] as $PROPERTY_CODE)
				if($PROPERTY_CODE)
				{
					$arItem["DISPLAY_PROPERTIES"][$PROPERTY_CODE] = CIBlockFormatProperties::GetDisplayValue($arElement, $arProperties[$PROPERTY_CODE], "catalog_out");
				}

		if($arParams["USE_PRICE_COUNT"])
		{
			if(CModule::IncludeModule("catalog"))
			{
				$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arElement["ID"], 0, $arPriceTypeID, 'Y', $arResult['CONVERT_CURRENCY']);
				foreach($arItem["PRICE_MATRIX"]["COLS"] as $keyColumn=>$arColumn)
					$arItem["PRICE_MATRIX"]["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialcharsbx($arColumn["NAME_LANG"]);
			}
			else
			{
				$arItem["PRICE_MATRIX"] = false;
			}
			$arItem["PRICES"] = array();
		}
		else
		{
			$arItem["PRICE_MATRIX"] = false;
			$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arParams["LINK_IBLOCK_ID"], $arResult["CAT_PRICES"], $arElement, $arParams['PRICE_VAT_INCLUDE'], $arResult['CONVERT_CURRENCY']);
		}
		$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["LINK_IBLOCK_ID"], $arResult["CAT_PRICES"], $arElement);

		$arItem["BUY_URL"] = htmlspecialcharsbx($GLOBALS["APPLICATION"]->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
		$arItem["ADD_URL"] = htmlspecialcharsbx($GLOBALS["APPLICATION"]->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));

		if ('Y' == $arParams['CONVERT_CURRENCY'])
		{
			if($arParams["USE_PRICE_COUNT"])
			{
				if (is_array($arItem["PRICE_MATRIX"]) && !empty($arItem["PRICE_MATRIX"]))
				{
					if (isset($arItem["PRICE_MATRIX"]['CURRENCY_LIST']) && is_array($arItem["PRICE_MATRIX"]['CURRENCY_LIST']))
						$arCurrencyList = array_merge($arCurrencyList, $arItem["PRICE_MATRIX"]['CURRENCY_LIST']);
				}
			}
			else
			{
				if (!empty($arItem["PRICES"]))
				{
					foreach ($arItem["PRICES"] as &$arOnePrices)
					{
						if (isset($arOnePrices['ORIG_CURRENCY']))
							$arCurrencyList[] = $arOnePrices['ORIG_CURRENCY'];
					}
					if (isset($arOnePrices))
						unset($arOnePrices);
				}
			}
		}
	}
	if ('Y' == $arParams['CONVERT_CURRENCY'])
	{
		if (!empty($arCurrencyList))
		{
			if (defined("BX_COMP_MANAGED_CACHE"))
			{
				$arCurrencyList[] = $arConvertParams['CURRENCY_ID'];
				$arCurrencyList = array_unique($arCurrencyList);
				$CACHE_MANAGER->StartTagCache($this->__component->GetCachePath());
				foreach ($arCurrencyList as &$strOneCurrency)
				{
					$CACHE_MANAGER->RegisterTag("currency_id_".$strOneCurrency);
				}
				if (isset($strOneCurrency))
					unset($strOneCurrency);
				$CACHE_MANAGER->EndTagCache();
			}
		}
	}
}
?>