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
		"PROPERTY_".$arParams["LINK_PROPERTY_SID"],
	);
	//WHERE
	$arID = array();
	$arMap = array();
	foreach($arResult["ITEMS"] as $key=>$arItem)
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
		"PROPERTY_".$arParams["LINK_PROPERTY_SID"] => $arID,
	);
	//ORDER BY
	$arSort = array(
		"ID" => "ASC",
	);
	//PRICES
	$arPriceTypeID = array();
	if (!$arParams["USE_PRICE_COUNT"])
	{
		foreach($arResult["PRICES"] as &$value)
		{
			$arSelect[] = $value["SELECT"];
			$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
		}
		if (isset($value))
			unset($value);
	}
	else
	{
		foreach($arResult["PRICES"] as &$value)
		{
			$arPriceTypeID[] = $value["ID"];
		}
		if (isset($value))
			unset($value);
	}

	$arCurrencyList = array();
	$arFound = array();
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
	while($arElement = $rsElements->GetNext())
	{
		$ID = $arElement["PROPERTY_".strtoupper($arParams["LINK_PROPERTY_SID"])."_VALUE"];
		if(!array_key_exists($ID, $arFound) || (strpos($arElement["XML_ID"], "#")===false))
		{
			$arFound[$ID] = true;
			$arItem = &$arResult["ITEMS"][$arMap[$ID]];
			/*You have to uncomment and modify lines below in order to display some prices*/
			/*
			if($arParams["USE_PRICE_COUNT"])
			{
				if(CModule::IncludeModule("catalog"))
				{
					$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arElement["ID"]);
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
				$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arParams["LINK_IBLOCK_ID"], $arResult["PRICES"], $arElement);
			}

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
			*/
			$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["LINK_IBLOCK_ID"], $arResult["PRICES"], $arElement);
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
$arResult["TD_WIDTH"] = round(100/$arParams["LINE_ELEMENT_COUNT"])."%";
$arResult["nRowsPerItem"] = 1; //Image, Name and Properties
$arResult["bDisplayPrices"] = false;
foreach($arResult["ITEMS"] as $arItem)
{
	if(count($arItem["PRICES"])>0 || is_array($arItem["PRICE_MATRIX"]))
		$arResult["bDisplayPrices"] = true;
	if($arResult["bDisplayPrices"])
		break;
}
if($arResult["bDisplayPrices"])
	$arResult["nRowsPerItem"]++; // Plus one row for prices
$arResult["bDisplayButtons"] = $arParams["DISPLAY_COMPARE"] || count($arResult["PRICES"])>0;
foreach($arResult["ITEMS"] as $arItem)
{
	if($arItem["CAN_BUY"])
		$arResult["bDisplayButtons"] = true;
	if($arResult["bDisplayButtons"])
		break;
}
if($arResult["bDisplayButtons"])
	$arResult["nRowsPerItem"]++; // Plus one row for buttons

//array_chunk
$arResult["ROWS"] = array();
while(count($arResult["ITEMS"])>0)
{

	$arRow = array_splice($arResult["ITEMS"], 0, $arParams["LINE_ELEMENT_COUNT"]);
	while(count($arRow) < $arParams["LINE_ELEMENT_COUNT"])
		$arRow[]=false;
	$arResult["ROWS"][]=$arRow;
}
?>