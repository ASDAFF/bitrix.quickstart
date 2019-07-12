<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
/********************************
Get Prices from linked price list
********************************/
if(!function_exists("CurrencyFormat_SERGELAND"))
{
	function CurrencyFormat_SERGELAND($fSum, $strCurrency)
	{
		$result = "";
		$db_events = GetModuleEvents("currency", "CurrencyFormat");
		while ($arEvent = $db_events->Fetch())
			$result = ExecuteModuleEventEx($arEvent, Array($fSum, $strCurrency));

		if(strlen($result) > 0)
			return $result;

		if (!isset($fSum) || strlen($fSum)<=0)
			return "";

		$arCurFormat = CCurrencyLang::GetCurrencyFormat($strCurrency);

		if (!isset($arCurFormat["DECIMALS"]))
			$arCurFormat["DECIMALS"] = 2;
		$arCurFormat["DECIMALS"] = IntVal($arCurFormat["DECIMALS"]);

		if (!isset($arCurFormat["DEC_POINT"]))
			$arCurFormat["DEC_POINT"] = ".";
		if(!empty($arCurFormat["THOUSANDS_VARIANT"]))
		{
			if($arCurFormat["THOUSANDS_VARIANT"] == "N")
				$arCurFormat["THOUSANDS_SEP"] = "";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "D")
				$arCurFormat["THOUSANDS_SEP"] = ".";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "C")
				$arCurFormat["THOUSANDS_SEP"] = ",";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "S")
				$arCurFormat["THOUSANDS_SEP"] = chr(32);
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "B")
				$arCurFormat["THOUSANDS_SEP"] = chr(32);
		}

		if (!isset($arCurFormat["FORMAT_STRING"]))
			$arCurFormat["FORMAT_STRING"] = "#";

		$num = number_format($fSum, $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);
		
		if($arCurFormat["THOUSANDS_VARIANT"] == "B")
			$num = str_replace(" ", "&nbsp;", $num);
		
		return $num;
	}
}

if(!function_exists("sergeland_sort_sort_asc"))
{
	function sergeland_sort_sort_asc($a, $b)
	{
			return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
	}
}

CModule::IncludeModule("sale");

$arResult["~OFFERS"] = array();
$arResult["~OFFERS_ID"] = array();
$arResult["~LINKED_ELEMENTS"] = array();
			
$dbSite = CSite::GetByID(SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];
	
if(strlen($lang) <= 0)
	$lang = "ru";


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
		$arResult["~LINKED_ELEMENTS"][$arItem["ID"]] = $arItem;
		$arID[] = $arItem["ID"];
		$arMap[$arItem["ID"]] = $arItem["ID"];
	}
	$arResult["LINKED_ELEMENTS"] = $arResult["~LINKED_ELEMENTS"];
	
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

		/* SergeLand add */
		$arElement["PROPERTIES"] = $arProperties;		
		$arResult["~OFFERS"][$arElement["ID"]] = $arElement;
		$arResult["~OFFERS_ID"][$arResult["ID"]][] = $arElement["ID"];
				
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

/* SergeLand add*/
	
$arArray = array(
					"BUY_URL", 
					"ADD_URL", 
					"COMPARE_URL", 
					"~ID", 
					"~PRICES_ID", 
					"~PRICES", 
					"~PRICES_ALL", 
					"~PRICES_DISCOUNT", 
					"~PRICES_PRINT", 
					"~MAXIMUM_PRICE", 
					"~MINIMUM_PRICE", 
					"~CURRENCY_FORMAT",
					"~ARTNUMBER",
					"~QUANTITY",
					"~COUNTDOWN",
					"~SELECT", 
					"~DISCOUNT_PRICE_PERCENT", 
					"~DISCOUNT_PRICE_PERCENT_FORMATED"
				);

foreach($arArray as $index)
	$arResult[$index] = array();
	
if(is_array($arResult["~OFFERS_ID"][$arResult["ID"]]))		
	foreach($arResult["~OFFERS_ID"][$arResult["ID"]] as $OFFERS_ID)
	{
		$arFields = $arResult["~OFFERS"][$OFFERS_ID];
		
		// about update script
		$arPrice = $arResult["LINKED_ELEMENTS"][$OFFERS_ID]["PRICES"];				
		
		$arQuantity = CCatalogProduct::GetByID($arFields["ID"]);	
		if(!is_array($arQuantity)) $arQuantity = array();		
		
		foreach($arParams["PRICE_CODE"] as $PRICE_CODE)
		{
			if(!empty($arPrice[$PRICE_CODE]) && $arPrice[$PRICE_CODE]["DISCOUNT_VALUE"] > 0)
			{
				//if($arFields["CATALOG_CAN_BUY_ZERO"] == "N"  && $arFields["CATALOG_QUANTITY"] < 1) continue;
								
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["ID"]   = $arFields["ID"];						
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["COLOR"] = $arFields["PROPERTIES"]["COLOR"]["VALUE"];

				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["SELECT_COLOR_VALUE"] 	    = $arFields["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["SELECT_SIZE_VALUE"]  	    = $arFields["PROPERTIES"]["SIZE"]["VALUE_ENUM_ID"];
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["SELECT_ARTNUMBER_VALUE"]   = $arFields["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["SELECT_QUANTITY_VALUE"]    	  = $arQuantity["QUANTITY"];
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["SELECT_QUANTITY_RESERVED_VALUE"] = $arQuantity["QUANTITY_RESERVED"];
				$arResult["QUANTITY_ALL"] += $arQuantity["QUANTITY"];
				
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["PRICES_PRINT"][$PRICE_CODE] 	= CurrencyFormat_SERGELAND($arPrice[$PRICE_CODE]["DISCOUNT_VALUE"], $arPrice[$PRICE_CODE]["CURRENCY"]);
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["PRICES_OLD_PRINT"][$PRICE_CODE] = CurrencyFormat_SERGELAND($arPrice[$PRICE_CODE]["VALUE"], $arPrice[$PRICE_CODE]["CURRENCY"]);

				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["BUY_URL"]  = htmlspecialchars( $APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arFields["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["ADD_URL"]  = htmlspecialchars( $APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arFields["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );
				
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["NAME"] = $arFields["PROPERTIES"]["SIZE"]["VALUE"];
				$arResult["~SELECT"]["SIZE"][$arFields["ID"]]["SORT"] = $arFields["PROPERTIES"]["SIZE"]["VALUE_SORT"];				
				
				$arResult["~SELECT"]["COLOR"][$arFields["PROPERTIES"]["COLOR"]["VALUE"]]["NAME"] = $arFields["PROPERTIES"]["COLOR"]["VALUE"];
				$arResult["~SELECT"]["COLOR"][$arFields["PROPERTIES"]["COLOR"]["VALUE"]]["SORT"] = $arFields["PROPERTIES"]["COLOR"]["VALUE_SORT"];
				$arResult["~SELECT"]["COLOR"][$arFields["PROPERTIES"]["COLOR"]["VALUE"]]["SELECT_COLOR_VALUE"] = $arFields["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
				
				$index = $arPrice[$PRICE_CODE]["DISCOUNT_VALUE"];

				$arResult["~ARTNUMBER"][$PRICE_CODE]["$index"] = $arFields["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];				
				$arResult["~QUANTITY"][$PRICE_CODE]["$index"] = $arQuantity["QUANTITY"];				
				
				$arResult["~PRICES_PRINT"][$PRICE_CODE]["$index"] = CurrencyFormat_SERGELAND($arPrice[$PRICE_CODE]["DISCOUNT_VALUE"], $arPrice[$PRICE_CODE]["CURRENCY"]);				
				$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][] = trim($arPrice[$PRICE_CODE]["DISCOUNT_VALUE"]);				
				
				$arResult["~DISCOUNT_PRICE_PERCENT"][$PRICE_CODE]["$index"] = 100*($arPrice[$PRICE_CODE]["VALUE"] - $arPrice[$PRICE_CODE]["DISCOUNT_VALUE"])/$arPrice[$PRICE_CODE]["VALUE"];
				$arResult["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE]["$index"] = "-".roundEx($arResult["~DISCOUNT_PRICE_PERCENT"][$PRICE_CODE]["$index"], SALE_VALUE_PRECISION)."%";
				
				$arResult["~PRICES"][$PRICE_CODE]["$index"] = $arPrice[$PRICE_CODE]["VALUE"];
				$arResult["~PRICES_OLD_PRINT"][$PRICE_CODE]["$index"] = CurrencyFormat_SERGELAND($arPrice[$PRICE_CODE]["VALUE"], $arPrice[$PRICE_CODE]["CURRENCY"]);
				
				$arResult["~PRICES_ID"][$PRICE_CODE]["$index"] = $arFields["ID"];
				$arResult["~PRICES_CURRENCY"][$PRICE_CODE]["$index"] = $arPrice[$PRICE_CODE]["CURRENCY"];

				$arResult["~PRICES_ALL"][$PRICE_CODE]["$index"] = $arPrice[$PRICE_CODE];
				$arResult["~CURRENCY_FORMAT"][$PRICE_CODE] = CCurrencyLang::GetCurrencyFormat($arPrice[$PRICE_CODE]["CURRENCY"], $lang);			
				$arResult["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"] = trim(str_replace("#", "", $arResult["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_STRING"]));				
			}			
		}
	}		

foreach($arParams["PRICE_CODE"] as $PRICE_CODE)
{
	$arResult["~SKU_PRICES_COUNT"][$PRICE_CODE] = count($arResult["~PRICES_DISCOUNT"][$PRICE_CODE]);				
	$arQuantity = CCatalogProduct::GetByID($arResult["ID"]);	
	if(!is_array($arQuantity)) $arQuantity = array();
					
	if(!empty($arResult["PRICES"]))
	{		
		if($arResult["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"] > 0 && count($arResult["~PRICES_DISCOUNT"][$PRICE_CODE]) < 1)
		{
			foreach($arResult["PROPERTIES"]["COLOR"]["VALUE"] as $SORT1=>$COLOR)
			{				
				foreach($arResult["PROPERTIES"]["SIZE"]["VALUE"] as $SORT2=>$SIZE)
				{					
					//if($arResult["CATALOG_CAN_BUY_ZERO"] == "N"  && $arResult["CATALOG_QUANTITY"] < 1) continue;
				
					$arSize = array();
					
					$arSize["ID"]     = $arResult["ID"];
					$arSize["COLOR"]  = $COLOR;						
					$arSize["NAME"]   = $SIZE;
					$arSize["SORT"]   = $SORT2;		

					$arSize["SELECT_SIZE_VALUE"]  = $SORT2;
					$arSize["SELECT_COLOR_VALUE"] = $SORT1;												
					$arSize["SELECT_ARTNUMBER_VALUE"] = $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];
					$arSize["SELECT_QUANTITY_VALUE"]  = $arQuantity["QUANTITY"];
					$arSize["SELECT_QUANTITY_RESERVED_VALUE"] = $arQuantity["QUANTITY_RESERVED"];
					$arResult["QUANTITY_ALL"] = $arQuantity["QUANTITY"];
					
					$buy_url = $arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arResult["ID"];
					$add_url = $arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arResult["ID"];						
					
					 foreach($arParams["PRODUCT_PROPERTIES"] as $PROPERTY_CODE)
					 {
						if($PROPERTY_CODE == "COLOR")
						{
							$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[COLOR]=".urlencode($COLOR);
							$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[COLOR]=".urlencode($COLOR);					
							continue;
						}

						if($PROPERTY_CODE == "SIZE")
						{
							$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[SIZE]=".urlencode($SIZE);
							$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[SIZE]=".urlencode($SIZE);					
							continue;
						}
						
						if(is_array($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"]))
						{
							$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"][0]);
							$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"][0]);
						}
						else
						{
							$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"]);
							$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"]);
						}
					 }
					
					$arSize["BUY_URL"]  = htmlspecialchars( $APPLICATION->GetCurPageParam($buy_url, array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );		
					$arSize["ADD_URL"]  = htmlspecialchars( $APPLICATION->GetCurPageParam($add_url, array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );
					
					$arSize["PRICES_PRINT"][$PRICE_CODE] 	= CurrencyFormat_SERGELAND($arResult["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"], $arResult["PRICES"][$PRICE_CODE]["CURRENCY"]);
					$arSize["PRICES_OLD_PRINT"][$PRICE_CODE] = CurrencyFormat_SERGELAND($arResult["PRICES"][$PRICE_CODE]["VALUE"], $arResult["PRICES"][$PRICE_CODE]["CURRENCY"]);
					
					$arResult["~SELECT"]["SIZE"][] = $arSize;
				}

				$arResult["~SELECT"]["COLOR"][$SORT1]["NAME"] = $COLOR;
				$arResult["~SELECT"]["COLOR"][$SORT1]["SORT"] = $SORT1;
				$arResult["~SELECT"]["COLOR"][$SORT1]["SELECT_COLOR_VALUE"] = $SORT1;
			}
						
			$index = $arResult["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"];

			$arResult["~PRICES_PRINT"][$PRICE_CODE]["$index"] = CurrencyFormat_SERGELAND($arResult["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"], $arResult["PRICES"][$PRICE_CODE]["CURRENCY"]);				
			$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][] = trim($arResult["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"]);

			$arResult["~DISCOUNT_PRICE_PERCENT"][$PRICE_CODE]["$index"] = 100*($arResult["PRICES"][$PRICE_CODE]["VALUE"] - $arResult["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"])/$arResult["PRICES"][$PRICE_CODE]["VALUE"];
			$arResult["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE]["$index"] = "-".roundEx($arResult["~DISCOUNT_PRICE_PERCENT"][$PRICE_CODE]["$index"], SALE_VALUE_PRECISION)."%";				
			
			$arResult["~PRICES"][$PRICE_CODE]["$index"] = $arResult["PRICES"][$PRICE_CODE]["VALUE"];	
			$arResult["~PRICES_OLD_PRINT"][$PRICE_CODE]["$index"] =  CurrencyFormat_SERGELAND($arResult["PRICES"][$PRICE_CODE]["VALUE"], $arResult["PRICES"][$PRICE_CODE]["CURRENCY"]);
							
			$arResult["~PRICES_ID"][$PRICE_CODE]["$index"] = $arResult["ID"];
			$arResult["~PRICES_CURRENCY"][$PRICE_CODE]["$index"] = $arResult["PRICES"][$PRICE_CODE]["CURRENCY"];			

			$arResult["~PRICES_ALL"][$PRICE_CODE]["$index"] = $arResult["PRICES"][$PRICE_CODE];
			$arResult["~CURRENCY_FORMAT"][$PRICE_CODE] = CCurrencyLang::GetCurrencyFormat($arResult["PRICES"][$PRICE_CODE]["CURRENCY"], $lang);			
			$arResult["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"] = trim(str_replace("#", "", $arResult["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_STRING"]));				
		}
	}

	if(is_array($arResult["~PRICES_DISCOUNT"][$PRICE_CODE]))
		sort($arResult["~PRICES_DISCOUNT"][$PRICE_CODE], SORT_NUMERIC);		
	
	switch(count($arResult["~PRICES_DISCOUNT"][$PRICE_CODE]))
	{
		case 0:
					$arResult["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~MAXIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arResult["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = 0;						
					$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arResult["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE][0];
					break;
				
		case 1:
					$arResult["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0];
					$arResult["~MAXIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arResult["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arResult["~PRICES_PRINT"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
					$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arResult["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
					break;
				
		case 2:
					$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0];
					$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arResult["~PRICES_PRINT"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
					$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arResult["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
					
					$arResult["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES_DISCOUNT"][$PRICE_CODE][1];
					$arResult["~MAXIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arResult["~PRICES_PRINT"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][1]];
					break;
				
		default:
					$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0];
					$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arResult["~PRICES_PRINT"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
					$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arResult["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
					
					$arResult["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES_DISCOUNT"][$PRICE_CODE][$arResult["~SKU_PRICES_COUNT"][$PRICE_CODE] - 1];
					$arResult["~MAXIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arResult["~PRICES_PRINT"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][$arResult["~SKU_PRICES_COUNT"][$PRICE_CODE] - 1]];
	}

	$arResult["~ID"][$PRICE_CODE] = $arResult["~PRICES_ID"][$PRICE_CODE][$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]];
	
	$buy_url = $arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arResult["~ID"][$PRICE_CODE];
	$add_url = $arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arResult["~ID"][$PRICE_CODE];
	
	if($arResult["~SKU_PRICES_COUNT"][$PRICE_CODE] < 1)
	{
		 foreach($arParams["PRODUCT_PROPERTIES"] as $PROPERTY_CODE)
		 {
			if(empty($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"]))
			{
				$buy_url = "";
				$add_url = "";					
				break;
			}
			
			if(is_array($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"]))
			{
				$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"][0]);
				$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"][0]);
			}
			else
			{
				$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"]);
				$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arResult["PROPERTIES"][$PROPERTY_CODE]["VALUE"]);
			}
		 }

		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["ARTNUMBER"] = $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];			 
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["QUANTITY"]  = $arQuantity["QUANTITY"];			

		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SIZE"]  	= $arResult["PROPERTIES"]["SIZE"]["VALUE"][0];
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["COLOR"] 	= $arResult["PROPERTIES"]["COLOR"]["VALUE"][0];
		
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_SIZE_VALUE"]	 = 0;
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_COLOR_VALUE"] = 0;
	}
	else 
	{
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["ARTNUMBER"] = $arResult["~ARTNUMBER"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0]];		
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["QUANTITY"]  = $arResult["~QUANTITY"][$PRICE_CODE][$arResult["~PRICES_DISCOUNT"][$PRICE_CODE][0]];							

		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SIZE"]  	= $arResult["~OFFERS"][$arResult["~ID"][$PRICE_CODE]]["PROPERTIES"]["SIZE"]["VALUE"];
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["COLOR"] 	= $arResult["~OFFERS"][$arResult["~ID"][$PRICE_CODE]]["PROPERTIES"]["COLOR"]["VALUE"];
		
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_SIZE_VALUE"]	 	= $arResult["~OFFERS"][$arResult["~ID"][$PRICE_CODE]]["PROPERTIES"]["SIZE"]["VALUE_ENUM_ID"];
		$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_COLOR_VALUE"] 	= $arResult["~OFFERS"][$arResult["~ID"][$PRICE_CODE]]["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
	}
	
	$arResult["BUY_URL"][$PRICE_CODE] 	= htmlspecialchars( $APPLICATION->GetCurPageParam($buy_url, array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );		
	$arResult["ADD_URL"][$PRICE_CODE] 	= htmlspecialchars( $APPLICATION->GetCurPageParam($add_url, array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );
	
	$arResult["COMPARE_URL"][$PRICE_CODE] = htmlspecialchars($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arResult["~ID"][$PRICE_CODE], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));	
}		

if(is_array($arResult["~SELECT"]["COLOR"]))
	usort($arResult["~SELECT"]["COLOR"], "sergeland_sort_sort_asc");
	
if(is_array($arResult["~SELECT"]["SIZE"]))	
	usort($arResult["~SELECT"]["SIZE"], "sergeland_sort_sort_asc");

$arResult["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["LINK_IBLOCK_ID"], $arResult["PRICES"], $arResult);

if(!empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"]))
{
	foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as &$arPicFile)
	{
		$id_photo = $arPicFile;
		$arPicFile = array();
		$arPicFile["ID"] = $id_photo;
		$arPicFile["DETAIL_PICTURE"] = CFile::GetFileArray($id_photo);
		$arPicFile["PREVIEW_PICTURE"] = CFile::ResizeImageGet($id_photo, array("width" => 170, "height" => 250, BX_RESIZE_IMAGE_PROPORTIONAL_ALT));
		$arPicFile["PREVIEW_PICTURE"]["SRC"] = $arPicFile["PREVIEW_PICTURE"]["src"];
	}
}

$arResult["~COUNTDOWN"][$arResult["ID"]]["ID"] = $arResult["ID"];
$arResult["~COUNTDOWN"][$arResult["ID"]]["PROPERTIES"]["COUNTDOWN_SALE_FROM"]["VALUE"] = $arResult["PROPERTIES"]["COUNTDOWN_SALE_FROM"]["VALUE"];
$arResult["~COUNTDOWN"][$arResult["ID"]]["PROPERTIES"]["COUNTDOWN_SALE_TO"]["VALUE"] = $arResult["PROPERTIES"]["COUNTDOWN_SALE_TO"]["VALUE"];

$arResult["~RECOMMEND"]["ID"] = $arResult["PROPERTIES"]["RECOMMEND"]["VALUE"];
$arResult["~RECOMMEND"]["TITLE"] = $arResult["PROPERTIES"]["RECOMMEND"]["NAME"];

$cp = $this->__component; 
if(is_object($cp)) 
$cp->SetResultCacheKeys(array("~COUNTDOWN", "~RECOMMEND", "SECTION"));	
?>