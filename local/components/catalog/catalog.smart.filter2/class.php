<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*DEMO CODE for component inheritance
CBitrixComponent::includeComponentClass("bitrix::news.base");
class CBitrixCatalogSmartFilter extends CBitrixNewsBase
*/
class CBitrixCatalogSmartFilter extends CBitrixComponent
{
	var $IBLOCK_ID = 0;
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

		/*DEMO CODE for "pure" class.php component
		$this->arResult["FFF"] = "ggg";
		$this->includeComponentTemplate();
		return $this->ELEMENT_ID;
		*/

		return parent::executeComponent();
	}

	public function getResultItems()
	{
		$items = array();
		foreach(CIBlockSectionPropertyLink::GetArray($this->IBLOCK_ID, $this->SECTION_ID) as $PID => $arLink)
		{
			if($arLink["SMART_FILTER"] !== "Y")
				continue;

			$rsProperty = CIBlockProperty::GetByID($PID);
			$arProperty = $rsProperty->Fetch();
			if($arProperty)
			{
				$items[$arProperty["ID"]] = array(
					"ID" => $arProperty["ID"],
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
		static $cacheE = array();
		static $cacheG = array();
		$key = $arProperty["VALUE"];

		if(isset($arProperty["PRICE"]))
		{
			return;
		}
		elseif($arProperty["PROPERTY_TYPE"] == "F")
		{
			return;
		}
		elseif($arProperty["PROPERTY_TYPE"] == "N")
		{
			if(!isset($resultItem["VALUES"]["MIN"]) || !array_key_exists("VALUE", $resultItem["VALUES"]["MIN"]) || doubleval($resultItem["VALUES"]["MIN"]["VALUE"]) > doubleval($key))
				$resultItem["VALUES"]["MIN"]["VALUE"] = $key;

			if(!isset($resultItem["VALUES"]["MAX"]) || !array_key_exists("VALUE", $resultItem["VALUES"]["MAX"]) || doubleval($resultItem["VALUES"]["MAX"]["VALUE"]) < doubleval($key))
				$resultItem["VALUES"]["MAX"]["VALUE"] = $key;

			return;
		}

		switch($arProperty["PROPERTY_TYPE"])
		{
		case "L":
            $value = $arProperty["VALUE"];
			$value_list = $arProperty["VALUE_ENUM"];
			$sort = $arProperty["VALUE_SORT"];
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
			$value = $cacheE[$key]["ID"];
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
			$value = $arProperty["VALUE"];
			$sort = 0;
			break;
		}

		$key = htmlspecialcharsbx($key);
		$value = htmlspecialcharsex($value);
		$sort = intval($arProperty);

		$resultItem["VALUES"][$key] = array(
			"CONTROL_ID" => $arProperty["ID"],
            "CONTROL_NAME" => $arProperty["NAME"],
			"CONTROL_CODE" => $arProperty["CODE"],
			"HTML_VALUE" => "Y",
            "VALUE" => $value,
			"VALUE_LIST" => $value_list,
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
}
?>