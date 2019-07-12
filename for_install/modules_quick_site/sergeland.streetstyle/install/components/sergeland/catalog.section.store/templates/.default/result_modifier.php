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
			
$dbSite = CSite::GetByID(SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];
	
if(strlen($lang) <= 0)
	$lang = "ru";	
	
if($arParams["LINK_IBLOCK_ID"] > 1 && strlen($arParams["LINK_PROPERTY_SID"]) > 1)
{	
	$arFilterLink = Array("IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"], "ACTIVE" => "Y");	
	$arSelectLink = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
	foreach( $arResult["PRICES"] as $arPriceID)
		$arSelectLink[] =  $arPriceID["SELECT"];

	$res = CIBlockElement::GetList(Array(), $arFilterLink, false, false, $arSelectLink);

	while($obRes = $res->GetNextElement())
	{
		$arFields = $obRes->GetFields();		
		$arFields["PROPERTIES"] = $obRes->GetProperties();
		
		$arResult["~OFFERS"][$arFields["ID"]] = $arFields;
		$arResult["~OFFERS_ID"][$arFields["PROPERTIES"][$arParams["LINK_PROPERTY_SID"]]["VALUE"]][] = $arFields["ID"];
	}
}

foreach($arResult["ITEMS"] as &$arItem)
{		
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
						"~SELECT", 
						"~DISCOUNT_PRICE_PERCENT", 
						"~DISCOUNT_PRICE_PERCENT_FORMATED"
					);
	
	foreach($arArray as $index)
		$arItem[$index] = array();

	if(is_array($arResult["~OFFERS_ID"][$arItem["ID"]]))		
		foreach($arResult["~OFFERS_ID"][$arItem["ID"]] as $OFFERS_ID)
		{
			$arFields = $arResult["~OFFERS"][$OFFERS_ID];
			$arPrice = CIBlockPriceTools::GetItemPrices($arParams["LINK_IBLOCK_ID"], $arResult["PRICES"], $arFields, $arParams["PRICE_VAT_INCLUDE"]);				
					
			foreach($arParams["PRICE_CODE"] as $PRICE_CODE)
			{
				if(!empty($arPrice[$PRICE_CODE]) && $arPrice[$PRICE_CODE]["DISCOUNT_VALUE"] > 0)
				{
					//if($arFields["CATALOG_CAN_BUY_ZERO"] == "N"  && $arFields["CATALOG_QUANTITY"] < 1) continue;
									
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["ID"]   = $arFields["ID"];						
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["COLOR"] = $arFields["PROPERTIES"]["COLOR"]["VALUE"];

					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["SELECT_COLOR_VALUE"] 	    = $arFields["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["SELECT_SIZE_VALUE"]  	    = $arFields["PROPERTIES"]["SIZE"]["VALUE_ENUM_ID"];
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["SELECT_ARTNUMBER_VALUE"]  = $arFields["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];
					
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["PRICES_PRINT"][$PRICE_CODE] 	= CurrencyFormat_SERGELAND($arPrice[$PRICE_CODE]["DISCOUNT_VALUE"], $arPrice[$PRICE_CODE]["CURRENCY"]);
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["PRICES_OLD_PRINT"][$PRICE_CODE] = CurrencyFormat_SERGELAND($arPrice[$PRICE_CODE]["VALUE"], $arPrice[$PRICE_CODE]["CURRENCY"]);

					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["BUY_URL"]  = htmlspecialchars( $APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arFields["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["ADD_URL"]  = htmlspecialchars( $APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arFields["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );
					
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["NAME"] = $arFields["PROPERTIES"]["SIZE"]["VALUE"];
					$arItem["~SELECT"]["SIZE"][$arFields["ID"]]["SORT"] = $arFields["PROPERTIES"]["SIZE"]["VALUE_SORT"];				
					
					$arItem["~SELECT"]["COLOR"][$arFields["PROPERTIES"]["COLOR"]["VALUE"]]["NAME"] = $arFields["PROPERTIES"]["COLOR"]["VALUE"];
					$arItem["~SELECT"]["COLOR"][$arFields["PROPERTIES"]["COLOR"]["VALUE"]]["SORT"] = $arFields["PROPERTIES"]["COLOR"]["VALUE_SORT"];
					$arItem["~SELECT"]["COLOR"][$arFields["PROPERTIES"]["COLOR"]["VALUE"]]["SELECT_COLOR_VALUE"] = $arFields["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
					
					$index = $arPrice[$PRICE_CODE]["DISCOUNT_VALUE"];

					$arItem["~ARTNUMBER"][$PRICE_CODE]["$index"] = $arFields["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];				
					
					$arItem["~PRICES_PRINT"][$PRICE_CODE]["$index"] = CurrencyFormat_SERGELAND($arPrice[$PRICE_CODE]["DISCOUNT_VALUE"], $arPrice[$PRICE_CODE]["CURRENCY"]);				
					$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][] = trim($arPrice[$PRICE_CODE]["DISCOUNT_VALUE"]);				
					
					$arItems["~DISCOUNT_PRICE_PERCENT"][$PRICE_CODE]["$index"] = 100*($arPrice[$PRICE_CODE]["VALUE"] - $arPrice[$PRICE_CODE]["DISCOUNT_VALUE"])/$arPrice[$PRICE_CODE]["VALUE"];
					$arItems["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE]["$index"] = "-".roundEx($arItems["~DISCOUNT_PRICE_PERCENT"][$PRICE_CODE]["$index"], SALE_VALUE_PRECISION)."%";
					
					$arItem["~PRICES"][$PRICE_CODE]["$index"] = $arPrice[$PRICE_CODE]["VALUE"];
					$arItem["~PRICES_OLD_PRINT"][$PRICE_CODE]["$index"] = CurrencyFormat_SERGELAND($arPrice[$PRICE_CODE]["VALUE"], $arPrice[$PRICE_CODE]["CURRENCY"]);
					
					$arItem["~PRICES_ID"][$PRICE_CODE]["$index"] = $arFields["ID"];
					$arItem["~PRICES_CURRENCY"][$PRICE_CODE]["$index"] = $arPrice[$PRICE_CODE]["CURRENCY"];

					$arItem["~PRICES_ALL"][$PRICE_CODE]["$index"] = $arPrice[$PRICE_CODE];
					$arItem["~CURRENCY_FORMAT"][$PRICE_CODE] = CCurrencyLang::GetCurrencyFormat($arPrice[$PRICE_CODE]["CURRENCY"], $lang);			
					$arItem["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"] = trim(str_replace("#", "", $arItem["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_STRING"]));				
				}			
			}
		}		
	
	foreach($arParams["PRICE_CODE"] as $PRICE_CODE)
	{
		$arItem["~SKU_PRICES_COUNT"][$PRICE_CODE] = count($arItem["~PRICES_DISCOUNT"][$PRICE_CODE]);				
		
		if(!empty($arItem["PRICES"]))
		{		
			if($arItem["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"] > 0 && count($arItem["~PRICES_DISCOUNT"][$PRICE_CODE]) < 1)
			{
				foreach($arItem["PROPERTIES"]["COLOR"]["VALUE"] as $SORT1=>$COLOR)
				{				
					foreach($arItem["PROPERTIES"]["SIZE"]["VALUE"] as $SORT2=>$SIZE)
					{					
						//if($arItem["CATALOG_CAN_BUY_ZERO"] == "N"  && $arItem["CATALOG_QUANTITY"] < 1) continue;
					
						$arSize = array();
						
						$arSize["ID"]     = $arItem["ID"];
						$arSize["COLOR"]  = $COLOR;
						
						$arSize["NAME"] = $SIZE;
						$arSize["SORT"] = $SORT2;		

						$arSize["SELECT_SIZE_VALUE"]  = $SORT2;
						$arSize["SELECT_COLOR_VALUE"] = $SORT1;						
						$arSize["SELECT_ARTNUMBER_VALUE"] = $arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];
						
						$buy_url = $arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"];
						$add_url = $arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"];						
						
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
							
							if(is_array($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]))
							{
								$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"][0]);
								$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"][0]);
							}
							else
							{
								$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]);
								$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]);
							}
						 }
						
						$arSize["BUY_URL"]  = htmlspecialchars( $APPLICATION->GetCurPageParam($buy_url, array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );		
						$arSize["ADD_URL"]  = htmlspecialchars( $APPLICATION->GetCurPageParam($add_url, array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );
						
						$arSize["PRICES_PRINT"][$PRICE_CODE] 	= CurrencyFormat_SERGELAND($arItem["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"], $arItem["PRICES"][$PRICE_CODE]["CURRENCY"]);
						$arSize["PRICES_OLD_PRINT"][$PRICE_CODE] = CurrencyFormat_SERGELAND($arItem["PRICES"][$PRICE_CODE]["VALUE"], $arItem["PRICES"][$PRICE_CODE]["CURRENCY"]);
						
						$arItem["~SELECT"]["SIZE"][] = $arSize;
					}

					$arItem["~SELECT"]["COLOR"][$SORT1]["NAME"] = $COLOR;
					$arItem["~SELECT"]["COLOR"][$SORT1]["SORT"] = $SORT1;
					$arItem["~SELECT"]["COLOR"][$SORT1]["SELECT_COLOR_VALUE"] = $SORT1;
				}
							
				$index = $arItem["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"];

				$arItem["~PRICES_PRINT"][$PRICE_CODE]["$index"] = CurrencyFormat_SERGELAND($arItem["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"], $arItem["PRICES"][$PRICE_CODE]["CURRENCY"]);				
				$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][] = trim($arItem["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"]);

				$arItems["~DISCOUNT_PRICE_PERCENT"][$PRICE_CODE]["$index"] = 100*($arItem["PRICES"][$PRICE_CODE]["VALUE"] - $arItem["PRICES"][$PRICE_CODE]["DISCOUNT_VALUE"])/$arItem["PRICES"][$PRICE_CODE]["VALUE"];
				$arItems["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE]["$index"] = "-".roundEx($arItems["~DISCOUNT_PRICE_PERCENT"][$PRICE_CODE]["$index"], SALE_VALUE_PRECISION)."%";				
				
				$arItem["~PRICES"][$PRICE_CODE]["$index"] = $arItem["PRICES"][$PRICE_CODE]["VALUE"];	
				$arItem["~PRICES_OLD_PRINT"][$PRICE_CODE]["$index"] =  CurrencyFormat_SERGELAND($arItem["PRICES"][$PRICE_CODE]["VALUE"], $arItem["PRICES"][$PRICE_CODE]["CURRENCY"]);
								
				$arItem["~PRICES_ID"][$PRICE_CODE]["$index"] = $arItem["ID"];
				$arItem["~PRICES_CURRENCY"][$PRICE_CODE]["$index"] = $arItem["PRICES"][$PRICE_CODE]["CURRENCY"];			

				$arItem["~PRICES_ALL"][$PRICE_CODE]["$index"] = $arItem["PRICES"][$PRICE_CODE];
				$arItem["~CURRENCY_FORMAT"][$PRICE_CODE] = CCurrencyLang::GetCurrencyFormat($arItem["PRICES"][$PRICE_CODE]["CURRENCY"], $lang);			
				$arItem["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"] = trim(str_replace("#", "", $arItem["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_STRING"]));
				$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["ARTNUMBER"] = $arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];
			}
		}

		if(is_array($arItem["~PRICES_DISCOUNT"][$PRICE_CODE]))
			sort($arItem["~PRICES_DISCOUNT"][$PRICE_CODE], SORT_NUMERIC);		

		if($arItem["~SKU_PRICES_COUNT"][$PRICE_CODE] > 0)
			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["ARTNUMBER"] = $arItem["~ARTNUMBER"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
		
		switch(count($arItem["~PRICES_DISCOUNT"][$PRICE_CODE]))
		{
			case 0:
						$arItem["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arItem["~MAXIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arItem["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arItem["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = 0;						
						$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arItems["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE][0];
						break;
					
			case 1:
						$arItem["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arItem["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0];
						$arItem["~MAXIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arItem["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arItem["~PRICES_PRINT"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
						$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arItems["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
						break;
					
			case 2:
						$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0];
						$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arItem["~PRICES_PRINT"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
						$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arItems["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
						
						$arItem["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arItem["~PRICES_DISCOUNT"][$PRICE_CODE][1];
						$arItem["~MAXIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arItem["~PRICES_PRINT"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][1]];
						break;
					
			default:
						$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0];
						$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arItem["~PRICES_PRINT"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
						$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"] = $arItems["~DISCOUNT_PRICE_PERCENT_FORMATED"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][0]];
						
						$arItem["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arItem["~PRICES_DISCOUNT"][$PRICE_CODE][$arItem["~SKU_PRICES_COUNT"][$PRICE_CODE] - 1];
						$arItem["~MAXIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"] = $arItem["~PRICES_PRINT"][$PRICE_CODE][$arItem["~PRICES_DISCOUNT"][$PRICE_CODE][$arItem["~SKU_PRICES_COUNT"][$PRICE_CODE] - 1]];
		}
	
		$arItem["~ID"][$PRICE_CODE] = $arItem["~PRICES_ID"][$PRICE_CODE][$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]];
		
		$buy_url = $arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["~ID"][$PRICE_CODE];
		$add_url = $arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["~ID"][$PRICE_CODE];
		
		if($arItem["~SKU_PRICES_COUNT"][$PRICE_CODE] < 1)
		{
			 foreach($arParams["PRODUCT_PROPERTIES"] as $PROPERTY_CODE)
			 {
				if(empty($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]))
				{
					$buy_url = "";
					$add_url = "";					
					break;
				}
				
				if(is_array($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]))
				{
					$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"][0]);
					$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"][0]);
				}
				else
				{
					$buy_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]);
					$add_url .= "&".$arParams["PRODUCT_PROPS_VARIABLE"]."[".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]);
				}
			 }

			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["SIZE"]  = $arItem["PROPERTIES"]["SIZE"]["VALUE"][0];
			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["COLOR"] = $arItem["PROPERTIES"]["COLOR"]["VALUE"][0];
			
			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_SIZE_VALUE"]	 = 0;
			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_COLOR_VALUE"] = 0;
		}
		else 
		{
			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["SIZE"]  = $arResult["~OFFERS"][$arItem["~ID"][$PRICE_CODE]]["PROPERTIES"]["SIZE"]["VALUE"];
			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["COLOR"] = $arResult["~OFFERS"][$arItem["~ID"][$PRICE_CODE]]["PROPERTIES"]["COLOR"]["VALUE"];
			
			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_SIZE_VALUE"]	 = $arResult["~OFFERS"][$arItem["~ID"][$PRICE_CODE]]["PROPERTIES"]["SIZE"]["VALUE_ENUM_ID"];
			$arItem["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_COLOR_VALUE"] = $arResult["~OFFERS"][$arItem["~ID"][$PRICE_CODE]]["PROPERTIES"]["COLOR"]["VALUE_ENUM_ID"];
		}
		
		$arItem["BUY_URL"][$PRICE_CODE] 	= htmlspecialchars( $APPLICATION->GetCurPageParam($buy_url, array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );		
		$arItem["ADD_URL"][$PRICE_CODE] 	= htmlspecialchars( $APPLICATION->GetCurPageParam($add_url, array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"], $arParams["PRODUCT_QUANTITY_VARIABLE"], $arParams["PRODUCT_PROPS_VARIABLE"])) );
		
		$arItem["COMPARE_URL"][$PRICE_CODE] = htmlspecialchars($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arItem["~ID"][$PRICE_CODE], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));	
	}		

	if(is_array($arItem["~SELECT"]["COLOR"]))
		usort($arItem["~SELECT"]["COLOR"], "sergeland_sort_sort_asc");
		
	if(is_array($arItem["~SELECT"]["SIZE"]))	
		usort($arItem["~SELECT"]["SIZE"], "sergeland_sort_sort_asc");
	
	$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["LINK_IBLOCK_ID"], $arResult["PRICES"], $arItem);
}	
?>