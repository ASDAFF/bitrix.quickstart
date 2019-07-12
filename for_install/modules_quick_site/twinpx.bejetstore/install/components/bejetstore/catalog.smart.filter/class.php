<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*DEMO CODE for component inheritance
CBitrixComponent::includeComponentClass("bitrix::news.base");
class CBitrixCatalogSmartFilter extends CBitrixNewsBase
*/
class CBitrixCatalogSmartFilter extends CBitrixComponent
{
	var $IBLOCK_ID = 0;
	var $SKU_IBLOCK_ID = 0;
	var $SKU_PROPERTY_ID = 0;
	var $SECTION_ID = 0;
	var $FILTER_NAME = "";
	protected $currencyCache = array();

	public function onPrepareComponentParams($arParams)
	{
		$arParams["CACHE_TIME"] = isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000;
		$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
		if(is_array($arParams["SECTION_ID"])){
			$secCounter = 0;
			$arSecitionParams = array();
			foreach ($arParams["SECTION_ID"] as $key => $sectionID) {
				if(intval($sectionID)){
					$arSecitionParams[$secCounter] = intval($sectionID);
					$secCounter++;
				}
			}
			if(!empty($arSecitionParams)){
				$arParams["SECTION_ID"] = $arSecitionParams;
			}else{
				$arParams["SECTION_ID"] = 0;
			}
		}else{
			$arParams["SECTION_ID"] = intval($arParams["SECTION_ID"]);
		}
		$arParams["PRICE_CODE"] = is_array($arParams["PRICE_CODE"])? $arParams["PRICE_CODE"]: array();
		foreach ($arParams["PRICE_CODE"] as $k=>$v)
		{
			if ($v==="")
				unset($arParams["PRICE_CODE"][$k]);
		}
		$arParams["SAVE_IN_SESSION"] = $arParams["SAVE_IN_SESSION"] == "Y";
		$arParams["CACHE_GROUPS"] = $arParams["CACHE_GROUPS"] !== "N";
		$arParams["INSTANT_RELOAD"] = $arParams["INSTANT_RELOAD"] === "Y";
		$arParams["SECTION_TITLE"] = trim($arParams["SECTION_TITLE"]);
		$arParams["SECTION_DESCRIPTION"] = trim($arParams["SECTION_DESCRIPTION"]);

		if(
			strlen($arParams["FILTER_NAME"]) <= 0
			|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])
		)
		{
			$arParams["FILTER_NAME"] = "arrFilter";
		}

		return $arParams;
	}

	public function executeComponent()
	{
		$this->IBLOCK_ID = $this->arParams["IBLOCK_ID"];
		$this->SECTION_ID = $this->arParams["SECTION_ID"];
		$this->FILTER_NAME = $this->arParams["FILTER_NAME"];

		if(CModule::IncludeModule("catalog"))
		{
			$arCatalog = CCatalogSKU::GetInfoByProductIBlock($this->IBLOCK_ID);
			if (!empty($arCatalog) && is_array($arCatalog))
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
					"USER_TYPE" => $arProperty["USER_TYPE"],
					"USER_TYPE_SETTINGS" => $arProperty["USER_TYPE_SETTINGS"],
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
		if (!empty($this->arParams["PRICE_CODE"]))
		{
			if(CModule::IncludeModule("catalog"))
			{
				$rsPrice = CCatalogGroup::GetList(
					array('SORT' => 'ASC', 'ID' => 'ASC'),
					array('=NAME' => $this->arParams["PRICE_CODE"]),
					false,
					false,
					array('ID', 'NAME', 'NAME_LANG', 'CAN_ACCESS', 'CAN_BUY')
				);
				while($arPrice = $rsPrice->Fetch())
				{
					if($arPrice["CAN_ACCESS"] == "Y" || $arPrice["CAN_BUY"] == "Y")
					{
						$items[$arPrice["NAME"]] = array(
							"ID" => $arPrice["ID"],
							"CODE" => $arPrice["NAME"],
							"NAME" => strlen($arPrice["NAME_LANG"])? $arPrice["NAME_LANG"]: $arPrice["NAME"],
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
		}
		return $items;
	}

	public function getResultItems()
	{
		$items = $this->getIBlockItems($this->IBLOCK_ID);
		$this->arResult["PROPERTY_COUNT"] = count($items);

		if($this->SKU_IBLOCK_ID)
		{
			foreach($this->getIBlockItems($this->SKU_IBLOCK_ID) as $PID => $arItem)
			{
				$items[$PID] = $arItem;
				$this->arResult["SKU_PROPERTY_COUNT"]++;
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
		$currency = $arElement["CATALOG_CURRENCY_".$resultItem["ID"]];
		if(strlen($price))
		{
			if(
				!isset($resultItem["VALUES"]["MIN"])
				|| !array_key_exists("VALUE", $resultItem["VALUES"]["MIN"])
				|| doubleval($resultItem["VALUES"]["MIN"]["VALUE"]) > doubleval($price)
			)
			{
				$resultItem["VALUES"]["MIN"]["VALUE"] = $price;
				if (strlen($currency))
					$resultItem["VALUES"]["MIN"]["CURRENCY"] = $currency;
			}

			if(
				!isset($resultItem["VALUES"]["MAX"])
				|| !array_key_exists("VALUE", $resultItem["VALUES"]["MAX"])
				|| doubleval($resultItem["VALUES"]["MAX"]["VALUE"]) < doubleval($price)
			)
			{
				$resultItem["VALUES"]["MAX"]["VALUE"] = $price;
				if (strlen($currency))
					$resultItem["VALUES"]["MAX"]["CURRENCY"] = $currency;
			}
		}
		if(strlen($currency))
		{
			$resultItem["CURRENCIES"][$currency] = $this->getCurrencyFullName($currency);
		}
	}

	public function fillItemValues(&$resultItem, $arProperty)
	{
		static $cacheL = array();
		static $cacheE = array();
		static $cacheG = array();
		static $cacheU = array();

		if(is_array($arProperty))
		{
			if(isset($arProperty["PRICE"]))
			{
				return null;
			}
			$key = $arProperty["VALUE"];
			$PROPERTY_TYPE = $arProperty["PROPERTY_TYPE"];
			$PROPERTY_USER_TYPE = $arProperty["USER_TYPE"];
			$PROPERTY_ID = $arProperty["ID"];
		}
		else
		{
			$key = $arProperty;
			$PROPERTY_TYPE = $resultItem["PROPERTY_TYPE"];
			$PROPERTY_USER_TYPE = $resultItem["USER_TYPE"];
			$PROPERTY_ID = $resultItem["ID"];
			$arProperty = $resultItem;
		}

		if($PROPERTY_TYPE == "F")
		{
			return null;
		}
		elseif($PROPERTY_TYPE == "N")
		{
			if (strlen($key) <= 0)
			{
				return null;
			}
			if(!isset($resultItem["VALUES"]["MIN"]) || !array_key_exists("VALUE", $resultItem["VALUES"]["MIN"]) || doubleval($resultItem["VALUES"]["MIN"]["VALUE"]) > doubleval($key))
				$resultItem["VALUES"]["MIN"]["VALUE"] = preg_replace("/\\.0+\$/", "", $key);

			if(!isset($resultItem["VALUES"]["MAX"]) || !array_key_exists("VALUE", $resultItem["VALUES"]["MAX"]) || doubleval($resultItem["VALUES"]["MAX"]["VALUE"]) < doubleval($key))
				$resultItem["VALUES"]["MAX"]["VALUE"] = preg_replace("/\\.0+\$/", "", $key);

			return null;
		}
		elseif($PROPERTY_TYPE == "E" && $key <= 0)
		{
			return null;
		}
		elseif($PROPERTY_TYPE == "G" && $key <= 0)
		{
			return null;
		}
		elseif(strlen($key) <= 0)
		{
			return null;
		}

		$htmlKey = htmlspecialcharsbx($key);
		if (isset($resultItem["VALUES"][$htmlKey]))
		{
			return $htmlKey;
		}

		$arUserType = array();
		if($PROPERTY_USER_TYPE != "")
		{
			$arUserType = CIBlockProperty::GetUserType($PROPERTY_USER_TYPE);
			if(array_key_exists("GetPublicViewHTML", $arUserType))
				$PROPERTY_TYPE = "U";
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

			if (!array_key_exists($key,  $cacheL[$PROPERTY_ID]))
				return null;
			$value = $cacheL[$PROPERTY_ID][$key]["VALUE"];
			$sort = $cacheL[$PROPERTY_ID][$key]["SORT"];
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
		case "U":
			if(!isset($cacheU[$PROPERTY_ID]))
				$cacheU[$PROPERTY_ID] = array();

			if(!isset($cacheU[$PROPERTY_ID][$key]))
			{
				$cacheU[$PROPERTY_ID][$key] = call_user_func_array(
					$arUserType["GetPublicViewHTML"],
					array(
						$arProperty,
						array("VALUE" => $key),
						array("MODE" => "SIMPLE_TEXT"),
					)
				);
			}

			$value = $cacheU[$PROPERTY_ID][$key];
			$sort = 0;
			break;
		default:
			$value = $key;
			$sort = 0;
			break;
		}

		$keyCrc = abs(crc32($htmlKey));
		$value = htmlspecialcharsex($value);
		$sort = intval($sort);

		$resultItem["VALUES"][$htmlKey] = array(
			"CONTROL_ID" => htmlspecialcharsbx($this->FILTER_NAME."_".$PROPERTY_ID."_".$keyCrc),
			"CONTROL_NAME" => htmlspecialcharsbx($this->FILTER_NAME."_".$PROPERTY_ID."_".$keyCrc),
			"CONTROL_NAME_ALT" => htmlspecialcharsbx($this->FILTER_NAME."_".$PROPERTY_ID),
			"HTML_VALUE_ALT" => $keyCrc,
			"HTML_VALUE" => "Y",
			"VALUE" => $value,
			"SORT" => $sort,
			"UPPER" => ToUpper($value),
		);

		return $htmlKey;
	}

	function combineCombinations(&$arCombinations)
	{
		$result = array();
		foreach($arCombinations as $arCombination)
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
			if(!$this->combinationMatch($arCombination, $arItems, $currentPID))
				unset($arCombinations[$key]);
		}
	}

	function combinationMatch($combination, $arItems, $currentPID)
	{
		foreach($arItems as $PID => $arItem)
		{
			if ($PID != $currentPID)
			{
				if($arItem["PROPERTY_TYPE"] == "N" || isset($arItem["PRICE"]))
				{
					//TODO
				}
				else
				{
					if(!$this->matchProperty($combination[$PID], $arItem["VALUES"]))
						return false;
				}
			}
		}
		return true;
	}

	function matchProperty($value, $arValues)
	{
		$match = true;
		foreach($arValues as $formControl)
		{
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
		else
			return 0;
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
		if($arTuple)
		{
			reset($arTuple);
			list($key, $head) = each($arTuple);
			unset($arTuple[$key]);
			$arTemp[$key] = false;
			if(is_array($head))
			{
				if(empty($head))
				{
					if(empty($arTuple))
						$arResult[] = $arTemp;
					else
						$this->ArrayMultiply($arResult, $arTuple, $arTemp);
				}
				else
				{
					foreach($head as $value)
					{
						$arTemp[$key] = $value;
						if(empty($arTuple))
							$arResult[] = $arTemp;
						else
							$this->ArrayMultiply($arResult, $arTuple, $arTemp);
					}
				}
			}
			else
			{
				$arTemp[$key] = $head;
				if(empty($arTuple))
					$arResult[] = $arTemp;
				else
					$this->ArrayMultiply($arResult, $arTuple, $arTemp);
			}
		}
		else
		{
			$arResult[] = $arTemp;
		}
	}

	function makeFilter($FILTER_NAME)
	{
		$bOffersIBlockExist = false;
		$arCatalog = false;
		$bCatalog = \Bitrix\Main\Loader::includeModule('catalog');
		if ($bCatalog)
		{
			$arCatalog = CCatalogSKU::GetInfoByIBlock($this->IBLOCK_ID);
			if (!empty($arCatalog) && is_array($arCatalog))
			{
				$bOffersIBlockExist = (
					$arCatalog['CATALOG_TYPE'] == CCatalogSKU::TYPE_PRODUCT
					|| $arCatalog['CATALOG_TYPE'] == CCatalogSKU::TYPE_FULL
				);
			}
		}

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

		if ('Y' == $this->arParams['HIDE_NOT_AVAILABLE'])
			$arFilter['CATALOG_AVAILABLE'] = 'Y';

		if($bCatalog && $bOffersIBlockExist)
		{
			$arPriceFilter = array();
			foreach($gFilter as $key => $value)
			{
				if(preg_match('/^(>=|<=|><)CATALOG_PRICE_/', $key))
				{
					$arPriceFilter[$key] = $value;
					unset($gFilter[$key]);
				}
			}

			if(!empty($gFilter["OFFERS"]))
			{
				if (empty($arPriceFilter))
					$arSubFilter = $gFilter["OFFERS"];
				else
					$arSubFilter = array_merge($gFilter["OFFERS"], $arPriceFilter);

				$arSubFilter["IBLOCK_ID"] = $this->SKU_IBLOCK_ID;
				$arSubFilter["ACTIVE_DATE"] = "Y";
				$arSubFilter["ACTIVE"] = "Y";
				if ('Y' == $this->arParams['HIDE_NOT_AVAILABLE'])
					$arSubFilter['CATALOG_AVAILABLE'] = 'Y';
				$arFilter["=ID"] = CIBlockElement::SubQuery("PROPERTY_".$this->SKU_PROPERTY_ID, $arSubFilter);
			}
			elseif(!empty($arPriceFilter))
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

	public function getCurrencyFullName($currencyId)
	{
		if (!isset($this->currencyCache[$currencyId]))
		{
			$currencyInfo = CCurrencyLang::GetById($currencyId, LANGUAGE_ID);
			if ($currencyInfo["FULL_NAME"] != "")
				$this->currencyCache[$currencyId] = $currencyInfo["FULL_NAME"];
			else
				$this->currencyCache[$currencyId] = $currencyId;
		}
		return $this->currencyCache[$currencyId];
	}
}
?>
