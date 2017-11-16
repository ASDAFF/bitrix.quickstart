<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*DEMO CODE for component inheritance
CBitrixComponent::includeComponentClass("bitrix::news.base");
class CBitrixCatalogSmartFilter extends CBitrixNewsBase
*/
class CBitrixCatalogSmartFilter extends CBitrixComponent
{
	var $IBLOCK_ID = 0;
	var $PROPERTY_COUNT = 0;
	var $SKU_IBLOCK_ID = 0;
	var $SKU_PROPERTY_COUNT = 0;
	var $SKU_PROPERTY_ID = 0;
	var $SECTION_ID = 0;
	var $FILTER_NAME = "";

	public function onPrepareComponentParams($arParams)
	{
		$result = array(
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
			"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
			"SECTION_ID" => intval($arParams["SECTION_ID"]),
			"PRICE_CODE" => is_array($arParams["PRICE_CODE"])? $arParams["PRICE_CODE"]: array(),
			"SAVE_IN_SESSION" => $arParams["SAVE_IN_SESSION"] == "Y",
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"] !== "N",
			"INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"] === "Y",
		);

		if(strlen($arParams["FILTER_NAME"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
			$result["FILTER_NAME"] = "arrFilter";
		else
			$result["FILTER_NAME"] = $arParams["FILTER_NAME"];

		return $result;
	}

	public function executeComponent()
	{
		$this->IBLOCK_ID = $this->arParams["IBLOCK_ID"];
		$this->SECTION_ID = $this->arParams["SECTION_ID"];
		$this->FILTER_NAME = $this->arParams["FILTER_NAME"];

		if(CModule::IncludeModule("catalog"))
		{
			$arCatalog = CCatalog::GetSkuInfoByProductID($this->IBLOCK_ID);
			if (is_array($arCatalog))
			{
				$this->SKU_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
				$this->SKU_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
			}
		}

		/*DEMO CODE for "pure" class.php component
		$this->arResult["FFF"] = "ggg";
		$this->includeComponentTemplate();
		return $this->ELEMENT_ID;
		*/

		return parent::executeComponent();
	}

	public function getIBlockItems($IBLOCK_ID)
	{
		$items = array();
		foreach(CIBlockSectionPropertyLink::GetArray($IBLOCK_ID, $this->SECTION_ID) as $PID => $arLink)
		{
			if($arLink["SMART_FILTER"] !== "Y")
				continue;

			$rsProperty = CIBlockProperty::GetByID($PID);
			$arProperty = $rsProperty->Fetch();
			if($arProperty)
			{
				$items[$arProperty["ID"]] = array(
					"ID" => $arProperty["ID"],
					"IBLOCK_ID" => $arProperty["IBLOCK_ID"],
					"CODE" => $arProperty["CODE"],
					"NAME" => $arProperty["NAME"],
					"PROPERTY_TYPE" => $arProperty["PROPERTY_TYPE"],
					"VALUES" => array(),
				);

				if($arProperty["PROPERTY_TYPE"] == "N")
				{
					$items[$arProperty["ID"]]["VALUES"] = array(
						"MIN" => array(
							"CONTROL_ID" => htmlspecialcharsbx($this->FILTER_NAME."_".$arProperty["ID"]."_MIN"),
							"CONTROL_NAME" => htmlspecialcharsbx($this->FILTER_NAME."_".$arProperty["ID"]."_MIN"),
						),
						"MAX" => array(
							"CONTROL_ID" => htmlspecialcharsbx($this->FILTER_NAME."_".$arProperty["ID"]."_MAX"),
							"CONTROL_NAME" => htmlspecialcharsbx($this->FILTER_NAME."_".$arProperty["ID"]."_MAX"),
						),
					);
				}
			}
		}
		return $items;
	}

	public function getPriceItems()
	{
		$items = array();
		if(CModule::IncludeModule("catalog"))
		{
			$rsPrice = CCatalogGroup::GetList($v1, $v2);
			while($arPrice = $rsPrice->Fetch())
			{
				if(
					($arPrice["CAN_ACCESS"] == "Y" || $arPrice["CAN_BUY"] == "Y")
					&& in_array($arPrice["NAME"], $this->arParams["PRICE_CODE"])
				)
				{
					$items[$arPrice["NAME"]] = array(
						"ID" => $arPrice["ID"],
						"CODE" => $arPrice["NAME"],
						"NAME" => $arPrice["NAME_LANG"],
						"PRICE" => true,
						"VALUES" => array(
							"MIN" => array(
								"CONTROL_ID" => htmlspecialcharsbx($this->FILTER_NAME."_P".$arPrice["ID"]."_MIN"),
								"CONTROL_NAME" => htmlspecialcharsbx($this->FILTER_NAME."_P".$arPrice["ID"]."_MIN"),
							),
							"MAX" => array(
								"CONTROL_ID" => htmlspecialcharsbx($this->FILTER_NAME."_P".$arPrice["ID"]."_MAX"),
								"CONTROL_NAME" => htmlspecialcharsbx($this->FILTER_NAME."_P".$arPrice["ID"]."_MAX"),
							),
						),
					);
				}
			}
		}
		return $items;
	}

	public function getResultItems()
	{
		$items = $this->getIBlockItems($this->IBLOCK_ID);
		$this->PROPERTY_COUNT = count($items);

		if($this->SKU_IBLOCK_ID)
		{
			foreach($this->getIBlockItems($this->SKU_IBLOCK_ID) as $PID => $arItem)
			{
				$items[$PID] = $arItem;
				$this->SKU_PROPERTY_COUNT++;
			}
		}

		if (!empty($this->arParams["PRICE_CODE"]))
		{
			foreach($this->getPriceItems() as $PID => $arItem)
			{
				$items[$PID] = $arItem;
			}
		}

		return $items;
	}

	public function fillItemPrices(&$resultItem, $arElement)
	{
		$price = $arElement["CATALOG_PRICE_".$resultItem["ID"]];
		if(strlen($price))
		{
			if(!isset($resultItem["VALUES"]["MIN"]) || !array_key_exists("VALUE", $resultItem["VALUES"]["MIN"]) || doubleval($resultItem["VALUES"]["MIN"]["VALUE"]) > doubleval($price))
				$resultItem["VALUES"]["MIN"]["VALUE"] = $price;

			if(!isset($resultItem["VALUES"]["MAX"]) || !array_key_exists("VALUE", $resultItem["VALUES"]["MAX"]) || doubleval($resultItem["VALUES"]["MAX"]["VALUE"]) < doubleval($price))
				$resultItem["VALUES"]["MAX"]["VALUE"] = $price;
		}
	}

	public function fillItemValues(&$resultItem, $arProperty)
	{
		static $cacheL = array();
		static $cacheE = array();
		static $cacheG = array();

		if(is_array($arProperty))
		{
			if(isset($arProperty["PRICE"]))
			{
				return;
			}
			$key = $arProperty["VALUE"];
			$PROPERTY_TYPE = $arProperty["PROPERTY_TYPE"];
			$PROPERTY_ID = $arProperty["ID"];
		}
		else
		{
			$key = $arProperty;
			$PROPERTY_TYPE = $resultItem["PROPERTY_TYPE"];
			$PROPERTY_ID = $resultItem["ID"];
		}

		if($PROPERTY_TYPE == "F")
		{
			return;
		}
		elseif($PROPERTY_TYPE == "N")
		{
			if(!isset($resultItem["VALUES"]["MIN"]) || !array_key_exists("VALUE", $resultItem["VALUES"]["MIN"]) || doubleval($resultItem["VALUES"]["MIN"]["VALUE"]) > doubleval($key))
				$resultItem["VALUES"]["MIN"]["VALUE"] = $key;

			if(!isset($resultItem["VALUES"]["MAX"]) || !array_key_exists("VALUE", $resultItem["VALUES"]["MAX"]) || doubleval($resultItem["VALUES"]["MAX"]["VALUE"]) < doubleval($key))
				$resultItem["VALUES"]["MAX"]["VALUE"] = $key;

			return;
		}
		elseif($PROPERTY_TYPE == "E" && $key <= 0)
		{
			return;
		}
		elseif($PROPERTY_TYPE == "G" && $key <= 0)
		{
			return;
		}
		elseif(strlen($key) <= 0)
		{
			return;
		}

		switch($PROPERTY_TYPE)
		{
		case "L":
			if(!isset($cacheL[$PROPERTY_ID]))
			{
				$cacheL[$PROPERTY_ID] = array();
				$rsEnum = CIBlockPropertyEnum::GetList(array("SORT"=>"ASC", "VALUE"=>"ASC"), array("PROPERTY_ID" => $PROPERTY_ID));
				while ($enum = $rsEnum->Fetch())
					$cacheL[$PROPERTY_ID][$enum["ID"]] = $enum;
			}
			$sort = $cacheL[$PROPERTY_ID][$key]["SORT"];
			$value = $cacheL[$PROPERTY_ID][$key]["VALUE"];
			break;
		case "E":
			if(!isset($cacheE[$key]))
			{
				$arLinkFilter = array (
					"ID" => $key,
					"ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
				);
				$rsLink = CIBlockElement::GetList(array(), $arLinkFilter, false, false, array("ID","IBLOCK_ID","NAME","SORT"));
				$cacheE[$key] = $rsLink->Fetch();
			}
			$value = $cacheE[$key]["NAME"];
			$sort = $cacheE[$key]["SORT"];
			break;
		case "G":
			if(!isset($cacheG[$key]))
			{
				$arLinkFilter = array (
					"ID" => $key,
					"GLOBAL_ACTIVE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
				);
				$rsLink = CIBlockSection::GetList(array(), $arLinkFilter, false, array("ID","IBLOCK_ID","NAME","LEFT_MARGIN","DEPTH_LEVEL"));
				$cacheG[$key] = $rsLink->Fetch();
			}
			$value = str_repeat(".", $cacheG["DEPTH_LEVEL"]).$cacheG[$key]["NAME"];
			$sort = $cacheG[$key]["LEFT_MARGIN"];
			break;
		default:
			$value = $key;
			$sort = 0;
			break;
		}

		$key = htmlspecialcharsbx($key);
		$value = htmlspecialcharsex($value);
		$sort = intval($sort);

		$resultItem["VALUES"][$key] = array(
			"CONTROL_ID" => htmlspecialcharsbx($this->FILTER_NAME."_".$PROPERTY_ID."_".abs(crc32($key))),
			"CONTROL_NAME" => htmlspecialcharsbx($this->FILTER_NAME."_".$PROPERTY_ID."_".abs(crc32($key))),
			"HTML_VALUE" => "Y",
			"VALUE" => $value,
			"SORT" => $sort,
			"UPPER" => ToUpper($value),
		);

		return $key;
	}

	function combineCombinations(&$arCombinations)
	{
		$result = array();
		foreach($arCombinations as $key => $arCombination)
		{
			foreach($arCombination as $PID => $value)
			{
				if(!isset($result[$PID]))
					$result[$PID] = array();
				if(strlen($value))
					$result[$PID][] = $value;
			}
		}
		return $result;
	}

	function filterCombinations(&$arCombinations, $arItems, $currentPID)
	{
		foreach($arCombinations as $key => $arCombination)
		{
			//echo "filterCombinations: ",$key,"<br>";
			if(!$this->combinationMatch($arCombination, $arItems, $currentPID))
				unset($arCombinations[$key]);
			//echo "<hr>";
		}
	}

	function combinationMatch($combination, $arItems, $currentPID)
	{
		foreach($arItems as $PID => $arItem)
		{
			/////echo "combinationMatch: ",$combination[$PID],"<br>";
			if($arItem["PROPERTY_TYPE"] != "N" && !isset($arItem["PRICE"]) && $PID != $currentPID)
			{
				if(!$this->matchProperty($combination[$PID], $arItem["VALUES"]))
					return false;
			}
			else
			{
				//TODO
			}
		}
		return true;
	}

	function matchProperty($value, $arValues)
	{
		$match = true;
		foreach($arValues as $formControl)
		{	//echo "<pre>",print_r($formControl,1),"</pre>";
			if($formControl["CHECKED"])
			{
				if($formControl["VALUE"] == $value)
					return true;
				else
					$match = false;
			}
		}
		return $match;
	}

	public function _sort($v1, $v2)
	{
		if ($v1["SORT"] > $v2["SORT"])
			return 1;
		elseif ($v1["SORT"] < $v2["SORT"])
			return -1;
		elseif ($v1["UPPER"] > $v2["UPPER"])
			return 1;
		elseif ($v1["UPPER"] < $v2["UPPER"])
			return -1;
	}

	/*
	This function takes an array (arTuple) which is mix of scalar values and arrays
	and return "rectangular" array of arrays.
	For example:
	array(1, array(1, 2), 3, arrays(4, 5))
	will be transformed as
	array(
		array(1, 1, 3, 4),
		array(1, 1, 3, 5),
		array(1, 2, 3, 4),
		array(1, 2, 3, 5),
	)
	*/
	function ArrayMultiply(&$arResult, $arTuple, $arTemp = array())
	{
		if(count($arTuple) == 0)
			$arResult[] = $arTemp;
		else
		{
			$key = reset($arTuple);
			list($key, $head) = each($arTuple);
			unset($arTuple[$key]);
			$arTemp[$key] = false;
			if(is_array($head))
			{
				if(empty($head))
				{
					$this->ArrayMultiply($arResult, $arTuple, $arTemp);
				}
				else
				{
					foreach($head as $value)
					{
						$arTemp[$key] = $value;
						$this->ArrayMultiply($arResult, $arTuple, $arTemp);
					}
				}
			}
			else
			{
				$arTemp[$key] = $head;
				$this->ArrayMultiply($arResult, $arTuple, $arTemp);
			}
		}
	}

	function makeFilter($FILTER_NAME)
	{
		$gFilter = $GLOBALS[$FILTER_NAME];

		$arFilter = array(
			"IBLOCK_ID" => $this->IBLOCK_ID,
			"IBLOCK_LID" => SITE_ID,
			"IBLOCK_ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
			"MIN_PERMISSION" => "R",
			"INCLUDE_SUBSECTIONS" => "Y", //($arParams["INCLUDE_SUBSECTIONS"] != 'N' ? 'Y' : 'N'),
			"SECTION_ID" => $this->SECTION_ID,
		);

		if(is_array($gFilter["OFFERS"]))
		{
			if(!empty($gFilter["OFFERS"]))
			{
				$arSubFilter = $gFilter["OFFERS"];
				$arSubFilter["IBLOCK_ID"] = $this->SKU_IBLOCK_ID;
				$arSubFilter["ACTIVE_DATE"] = "Y";
				$arSubFilter["ACTIVE"] = "Y";
				$arFilter["=ID"] = CIBlockElement::SubQuery("PROPERTY_".$this->SKU_PROPERTY_ID, $arSubFilter);
			}

			$arPriceFilter = array();
			foreach($gFilter as $key => $value)
			{
				if(preg_match('/^(>=|<=)CATALOG_PRICE_/', $key))
				{
					$arPriceFilter[$key] = $value;
					unset($gFilter[$key]);
				}
			}

			if(!empty($arPriceFilter))
			{
				$arSubFilter = $arPriceFilter;
				$arSubFilter["IBLOCK_ID"] = $this->SKU_IBLOCK_ID;
				$arSubFilter["ACTIVE_DATE"] = "Y";
				$arSubFilter["ACTIVE"] = "Y";
				$arFilter[] = array(
					"LOGIC" => "OR",
					array($arPriceFilter),
					"=ID" => CIBlockElement::SubQuery("PROPERTY_".$this->SKU_PROPERTY_ID, $arSubFilter),
				);
			}

			unset($gFilter["OFFERS"]);
		}

		return array_merge($gFilter, $arFilter);
	}
}
?>