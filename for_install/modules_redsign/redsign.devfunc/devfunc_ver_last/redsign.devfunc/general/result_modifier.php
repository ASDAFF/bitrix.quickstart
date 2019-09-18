<?
/************************************
*
* Extension for component"s result_modifier.php
* last update 27.06.2014
*
************************************/

IncludeModuleLangFile(__FILE__);

class RSDevFuncResultModifier
{
	function SaleBasketBasketSmall($arResult)
	{
		$arNewResult = $arResult;
		if(CModule::IncludeModule("catalog") && is_array($arNewResult["ITEMS"]) && count($arNewResult["ITEMS"])>0)
		{
			$arNewResult["FULL_PRICE"] = 0;
			$arNewResult["NUM_PRODUCTS"] = 0;
			foreach($arNewResult["ITEMS"] as $arItem)
			{
				$arNewResult["FULL_PRICE"] += $arItem["PRICE"] * $arItem["QUANTITY"];
				$arNewResult["NUM_PRODUCTS"]++;
			}
			$arNewResult["PRINT_FULL_PRICE"] = FormatCurrency($arNewResult["FULL_PRICE"], $arNewResult["ITEMS"][0]["CURRENCY"]);
		}
		return $arNewResult;
	}
	
	function SearchTitle($arResult)
	{
		$arNewResult = $arResult;
		if(CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog"))
		{
			$arCatalog = array();
			$arIBlocks = array();
			$arOthers = array();
			if(!empty($arNewResult["CATEGORIES"]))
			{
				foreach($arNewResult["CATEGORIES"] as $category_id => $arCategory)
				{
					foreach($arCategory["ITEMS"] as $i => $arItem)
					{
						if($arItem["MODULE_ID"]=="iblock")
						{
							if(empty($arIBlocks[$arItem["PARAM2"]]))
							{
								$res = CIBlock::GetByID( $arItem["PARAM2"] );
								if($arRes = $res->GetNext())
								{
									$arIBlocks["IBLOCKS"][$arItem["PARAM2"]] = $arRes;
								}
							}
							$arIBlocks["ITEMS"][$arItem["PARAM2"]][] = $arItem;
						} else {
							$arOthers["ITEMS"][] = $arItem;
						}
					}
				}
			}
			$arNewResult["EXT_SEARCH"] = array(
				"IBLOCK" => $arIBlocks,
				"OTHER" => $arOthers,
			);
		}
		return $arNewResult;
	}
	
	function CatalogSmartFilter($arResult)
	{
		$arNewResult = $arResult;
		
		// prices in first place
		if( isset($arNewResult['PRICES']) )
		{
			end($arNewResult['PRICES']);
			while(current($arNewResult['PRICES']))
			{
				$priceKey = key($arNewResult['PRICES']);
				$arPrice = $arResult['ITEMS'][$priceKey];
				unset($arNewResult['ITEMS'][$priceKey]);
				array_unshift($arNewResult['ITEMS'], $arPrice);
				prev($arNewResult['PRICES']);
			}
		}
		
		return $arNewResult;
	}
}