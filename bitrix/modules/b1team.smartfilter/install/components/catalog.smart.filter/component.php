<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CC_BCF_MODULE_NOT_INSTALLED"));
	return;
}

$FILTER_NAME = $arParams["FILTER_NAME"];

global ${$FILTER_NAME};
if(!is_array(${$FILTER_NAME}))
        ${$FILTER_NAME} = array();
$arrFilter = &${$FILTER_NAME};

if($this->StartResultCache(false, array($arrFilter,($arParams["CACHE_GROUPS"]? $USER->GetGroups(): false))))
{
        
	$arResult["COMBO"] = array();
	$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
	$arResult["ITEMS"] = $this->getResultItems();

	$propertyEmptyValuesCombination = array();
	foreach($arResult["ITEMS"] as $PID => $arItem)
		$propertyEmptyValuesCombination[$arItem["ID"]] = array();

	if(!empty($arResult["ITEMS"]))
	{
		$arElementFilter = array(
			"IBLOCK_ID" => $this->IBLOCK_ID,
			"ACTIVE_DATE" => "Y",
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		);
                if($this->SECTION_ID > 0) {
                    $arElementFilter["SUBSECTION"] = $this->SECTION_ID;
                    $arElementFilter["SECTION_SCOPE"] = "IBLOCK";
                }
                
                if ('Y' == $this->arParams['HIDE_NOT_AVAILABLE']) {
                    
                    if($this->SKU_IBLOCK_ID >0) {
                       
                        //либо товар имеет СКУ с остатком,
                        //либо товар вообще не иммет СКУ, но имеет свой остаток
                        $arElementFilter[] = array(
                            "LOGIC" =>"OR",
                            array("=ID"=> CIBlockElement::SubQuery("PROPERTY_".$this->SKU_PROPERTY_ID, array(
                                            "IBLOCK_ID" => $this->SKU_IBLOCK_ID,
                                            "ACTIVE_DATE" => "Y",
                                            "ACTIVE" => "Y",
                                            "CHECK_PERMISSIONS" => "Y",
                                            "CATALOG_AVAILABLE" => "Y"
                                         ))
                            ),
                            array("CATALOG_AVAILABLE" => "Y")
                        );
                    } else {
                        $arElementFilter['CATALOG_AVAILABLE'] = 'Y';
                    }
                    
                }
                
                $arElements = array();
                
		if (!empty($this->arResult["PROPERTY_ID_LIST"]))
		{
			$rsElements = CIBlockElement::GetPropertyValues($this->IBLOCK_ID, $arElementFilter, false, array('ID' => $this->arResult["PROPERTY_ID_LIST"]));
			while($arElement = $rsElements->Fetch())
				$arElements[$arElement["IBLOCK_ELEMENT_ID"]] = $arElement;
		}
		else
		{
			$rsElements = CIBlockElement::GetList(array('ID' => 'ASC'), $arElementFilter, false, false, array('ID', 'IBLOCK_ID'));
			while($arElement = $rsElements->Fetch())
				$arElements[$arElement["ID"]] = array();
		}
                
                if (!empty($arElements) && $this->SKU_IBLOCK_ID && $arResult["SKU_PROPERTY_COUNT"] > 0)
		{
			$arSkuFilter = array(
				"IBLOCK_ID" => $this->SKU_IBLOCK_ID,
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
				"=PROPERTY_".$this->SKU_PROPERTY_ID => array_keys($arElements),
			);
                        if ('Y' == $this->arParams['HIDE_NOT_AVAILABLE'])
                            $arSkuFilter['CATALOG_AVAILABLE'] = 'Y';
                        
			$rsElements = CIBlockElement::GetPropertyValues($this->SKU_IBLOCK_ID, $arSkuFilter);
			while($arSku = $rsElements->Fetch())
			{
				foreach($arResult["ITEMS"] as $PID => $arItem)
				{
					if (isset($arSku[$PID]) && $arSku[$this->SKU_PROPERTY_ID] > 0)
					{
						if (is_array($arSku[$PID]))
						{
							foreach($arSku[$PID] as $value)
								$arElements[$arSku[$this->SKU_PROPERTY_ID]][$PID][] = $value;
						}
						else
						{
							$arElements[$arSku[$this->SKU_PROPERTY_ID]][$PID][] = $arSku[$PID];
						}
					}	
				}
			}
		}
                
                foreach($arElements as $arElement)
		{
			$propertyValues = $propertyEmptyValuesCombination;
			foreach($arResult["ITEMS"] as $PID => $arItem)
			{
				if (is_array($arElement[$PID]))
				{
					foreach($arElement[$PID] as $value)
					{
						$key = $this->fillItemValues($arResult["ITEMS"][$PID], $value);
						$propertyValues[$PID][$key] = $arResult["ITEMS"][$PID]["VALUES"][$key]["VALUE"];
					}
				}
				elseif ($arElement[$PID] !== false)
				{
					$key = $this->fillItemValues($arResult["ITEMS"][$PID], $arElement[$PID]);
					$propertyValues[$PID][$key] = $arResult["ITEMS"][$PID]["VALUES"][$key]["VALUE"];
				}
			}

			$propertyValuesCombination = array();
			$this->ArrayMultiply($propertyValuesCombination, $propertyValues);
			foreach($propertyValuesCombination as $propertyValues)
				$arResult["COMBO"][md5(serialize($propertyValues))] = $propertyValues;
		}
                
                $arSelect = array("ID", "IBLOCK_ID");
		foreach($arResult["PRICES"] as &$value)
		{
                        if (!$value['CAN_VIEW'] && !$value['CAN_BUY'])
				continue;
			$arSelect[] = $value["SELECT"];
			$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = 1;
		}

		$rsElements = CIBlockElement::GetList(array(), array_merge($arrFilter,$arElementFilter), false, false, $arSelect);
		while($arElement = $rsElements->Fetch())
		{
			foreach($arResult["PRICES"] as $NAME => $arPrice)
				if(isset($arResult["ITEMS"][$NAME]))
					$this->fillItemPrices($arResult["ITEMS"][$NAME], $arElement);
		}

		if (isset($arSkuFilter))
		{
			$rsElements = CIBlockElement::GetList(array(), $arSkuFilter, false, false, $arSelect);
			while($arSku = $rsElements->Fetch())
			{
				foreach($arResult["PRICES"] as $NAME => $arPrice)
					if(isset($arResult["ITEMS"][$NAME]))
						$this->fillItemPrices($arResult["ITEMS"][$NAME], $arSku);
			}
		}

		foreach($arResult["ITEMS"] as $PID => $arItem)
			uasort($arResult["ITEMS"][$PID]["VALUES"], array($this, "_sort"));
                
	}

	$this->EndResultCache();
}
/*Handle checked for checkboxes and html control value for numbers*/
if(isset($_REQUEST["ajax"]) && $_REQUEST["ajax"] === "y")
	$_CHECK = &$_REQUEST;
elseif(isset($_REQUEST["del_filter"]))
	$_CHECK = array();
elseif(isset($_GET["set_filter"]))
	$_CHECK = &$_GET;
elseif($arParams["SAVE_IN_SESSION"] && isset($_SESSION[$FILTER_NAME][$this->SECTION_ID]))
	$_CHECK = $_SESSION[$FILTER_NAME][$this->SECTION_ID];
else
	$_CHECK = array();

/*Set state of the html controls depending on filter values*/
$allCHECKED = array();
foreach($arResult["ITEMS"] as $PID => $arItem)
{
	foreach($arItem["VALUES"] as $key => $ar)
	{
		if(
			isset($_CHECK[$ar["CONTROL_NAME"]])
			|| (
				isset($_CHECK[$ar["CONTROL_NAME_ALT"]])
				&& $_CHECK[$ar["CONTROL_NAME_ALT"]] == $ar["HTML_VALUE_ALT"]
			)
		)
		{
			if($arItem["PROPERTY_TYPE"] == "N" || isset($arItem["PRICE"]))
			{
				$arResult["ITEMS"][$PID]["VALUES"][$key]["HTML_VALUE"] = htmlspecialcharsbx($_CHECK[$ar["CONTROL_NAME"]]);
			}
			elseif($_CHECK[$ar["CONTROL_NAME"]] == $ar["HTML_VALUE"])
			{
				$arResult["ITEMS"][$PID]["VALUES"][$key]["CHECKED"] = true;
				$allCHECKED[$PID][$ar["VALUE"]] = true;
			}
			elseif($_CHECK[$ar["CONTROL_NAME_ALT"]] == $ar["HTML_VALUE_ALT"])
			{
				$arResult["ITEMS"][$PID]["VALUES"][$key]["CHECKED"] = true;
				$allCHECKED[$PID][$ar["VALUE"]] = true;
			}
		}
	}
}

/*Handle disabled for checkboxes (TODO: handle number type)*/
foreach($arResult["ITEMS"] as $PID => $arItem)
{
	$combo = $arResult["COMBO"];
	/*filter all possible property values combinations*/
	$this->filterCombinations($combo, $arResult["ITEMS"], $PID);
	$combo = $this->combineCombinations($combo);

	foreach($arItem["VALUES"] as $key => $ar)
	{
		if($arItem["PROPERTY_TYPE"] != "N" && !isset($arItem["PRICE"]))
		{
			if(!is_array($combo[$PID]) || !in_array($ar["VALUE"], $combo[$PID]))
				$arResult["ITEMS"][$PID]["VALUES"][$key]["DISABLED"] = true;
		}
	}
}
/*Make iblock filter*/
global ${$FILTER_NAME};
if(!is_array(${$FILTER_NAME}))
	${$FILTER_NAME} = array();
        
if($arResult["SKU_PROPERTY_COUNT"] > 0)
{
	//This will force to use catalog.section offers price filter
	if(!isset(${$FILTER_NAME}["OFFERS"]))
		${$FILTER_NAME}["OFFERS"] = array();
}

foreach($arResult["ITEMS"] as $PID => $arItem)
{
	if(isset($arItem["PRICE"]))
	{
		if(strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]) && strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]))
			${$FILTER_NAME}["><CATALOG_PRICE_".$arItem["ID"]] = array($arItem["VALUES"]["MIN"]["HTML_VALUE"], $arItem["VALUES"]["MAX"]["HTML_VALUE"]);
		elseif(strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]))
			${$FILTER_NAME}[">=CATALOG_PRICE_".$arItem["ID"]] = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
		elseif(strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]))
			${$FILTER_NAME}["<=CATALOG_PRICE_".$arItem["ID"]] = $arItem["VALUES"]["MAX"]["HTML_VALUE"];
	}
	elseif($arItem["PROPERTY_TYPE"] == "N")
	{
		$existMinValue = (strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]) > 0);
		$existMaxValue = (strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]) > 0);
		if ($existMinValue || $existMaxValue)
		{
			$filterKey = '';
			$filterValue = '';
			if ($existMinValue && $existMaxValue)
			{
				$filterKey = "><PROPERTY_".$PID;
				$filterValue = array($arItem["VALUES"]["MIN"]["HTML_VALUE"], $arItem["VALUES"]["MAX"]["HTML_VALUE"]);
			}
			elseif($existMinValue)
			{
				$filterKey = ">=PROPERTY_".$PID;
				$filterValue = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
			}
			elseif($existMaxValue)
			{
				$filterKey = "<=PROPERTY_".$PID;
				$filterValue = $arItem["VALUES"]["MAX"]["HTML_VALUE"];
			}

			if ($arItem["IBLOCK_ID"] == $this->SKU_IBLOCK_ID)
			{
				if (!isset(${$FILTER_NAME}["OFFERS"]))
				{
					${$FILTER_NAME}["OFFERS"] = array();
				}
				${$FILTER_NAME}["OFFERS"][$filterKey] = $filterValue;
			}
			else
			{
				${$FILTER_NAME}[$filterKey] = $filterValue;
			}
		}
	}
	else
	{
		foreach($arItem["VALUES"] as $key => $ar)
		{
			if($ar["CHECKED"])
			{
				$filterKey = "=PROPERTY_".$PID;
				if ($arItem["IBLOCK_ID"] == $this->SKU_IBLOCK_ID)
				{
					if (!isset(${$FILTER_NAME}["OFFERS"]))
					{
						${$FILTER_NAME}["OFFERS"] = array();
					}
					if (!isset(${$FILTER_NAME}["OFFERS"][$filterKey]))
						${$FILTER_NAME}["OFFERS"][$filterKey] = array(htmlspecialcharsback($key));
					elseif (!is_array(${$FILTER_NAME}["OFFERS"][$filterKey]))
						${$FILTER_NAME}["OFFERS"][$filterKey] = array($filter[$filterKey], htmlspecialcharsback($key));
					elseif (!in_array(htmlspecialcharsback($key), ${$FILTER_NAME}["OFFERS"][$filterKey]))
						${$FILTER_NAME}["OFFERS"][$filterKey][] = htmlspecialcharsback($key);
				}
				else
				{
					if (!isset(${$FILTER_NAME}[$filterKey]))
						${$FILTER_NAME}[$filterKey] = array(htmlspecialcharsback($key));
					elseif (!is_array(${$FILTER_NAME}[$filterKey]))
						${$FILTER_NAME}[$filterKey] = array($filter[$filterKey], htmlspecialcharsback($key));
					elseif (!in_array(htmlspecialcharsback($key), ${$FILTER_NAME}[$filterKey]))
						${$FILTER_NAME}[$filterKey][] = htmlspecialcharsback($key);
				}
			}
		}
	}
}

/*Save to session if needed*/
if($arParams["SAVE_IN_SESSION"])
{
	$_SESSION[$FILTER_NAME][$this->SECTION_ID] = array();
	foreach($arResult["ITEMS"] as $PID => $arItem)
	{
		foreach($arItem["VALUES"] as $key => $ar)
		{
			if(isset($_CHECK[$ar["CONTROL_NAME"]]))
			{
				if($arItem["PROPERTY_TYPE"] == "N" || isset($arItem["PRICE"]))
					$_SESSION[$FILTER_NAME][$this->SECTION_ID][$ar["CONTROL_NAME"]] = $_CHECK[$ar["CONTROL_NAME"]];
				elseif($_CHECK[$ar["CONTROL_NAME"]] == $ar["HTML_VALUE"])
					$_SESSION[$FILTER_NAME][$this->SECTION_ID][$ar["CONTROL_NAME"]] = $_CHECK[$ar["CONTROL_NAME"]];
			}
		}
	}
}

$pageURL = $APPLICATION->GetCurPageParam();
$paramsToDelete = array("set_filter", "del_filter", "ajax", "bxajaxid", "AJAX_CALL", "mode");
foreach($arResult["ITEMS"] as $PID => $arItem)
{
	foreach($arItem["VALUES"] as $key => $ar)
	{
		$paramsToDelete[] = $ar["CONTROL_NAME"];
		$paramsToDelete[] = $ar["CONTROL_NAME_ALT"];
	}
}
$clearURL = CHTTP::urlDeleteParams($pageURL, $paramsToDelete, array("delete_system_params" => true));

if(isset($_REQUEST["ajax"]) && $_REQUEST["ajax"] === "y")
{
        $arFilter = $this->makeFilter($FILTER_NAME);
	$arResult["ELEMENT_COUNT"] = CIBlockElement::GetList(array(), $arFilter, array(), false);
        
	$paramsToAdd = array(
		"set_filter" => "y",
	);
	foreach($arResult["ITEMS"] as $PID => $arItem)
	{
		foreach($arItem["VALUES"] as $key => $ar)
		{
			if(isset($_CHECK[$ar["CONTROL_NAME"]]))
			{
				if($arItem["PROPERTY_TYPE"] == "N" || isset($arItem["PRICE"]))
					$paramsToAdd[$ar["CONTROL_NAME"]] = $_CHECK[$ar["CONTROL_NAME"]];
				elseif($_CHECK[$ar["CONTROL_NAME"]] == $ar["HTML_VALUE"])
					$paramsToAdd[$ar["CONTROL_NAME"]] = $_CHECK[$ar["CONTROL_NAME"]];
			}
		}
	}
	$arResult["FILTER_URL"] = htmlspecialcharsbx(CHTTP::urlAddParams($clearURL, $paramsToAdd, array(
		"skip_empty" => true,
		"encode" => true,
	)));
	
        if (isset($_GET["bxajaxid"]))
	{
		$arResult["COMPONENT_CONTAINER_ID"] = htmlspecialcharsbx("comp_".$_GET["bxajaxid"]);
		if ($arParams["INSTANT_RELOAD"])
			$arResult["INSTANT_RELOAD"] = true;
	}
        
	$arResult["FILTER_AJAX_URL"] = htmlspecialcharsbx(CHTTP::urlAddParams($clearURL, $paramsToAdd + array(
		"AJAX_CALL" => "Y",
		"bxajaxid" => $_GET["bxajaxid"],
	), array(
		"skip_empty" => true,
		"encode" => true,
	)));
}

$arInputNames = array();
foreach($arResult["ITEMS"] as $PID => $arItem)
{
	foreach($arItem["VALUES"] as $key => $ar)
		$arInputNames[$ar["CONTROL_NAME"]] = true;
}
$arInputNames["set_filter"]=true;
$arInputNames["del_filter"]=true;

$arSkip = array(
	"AUTH_FORM" => true,
	"TYPE" => true,
	"USER_LOGIN" => true,
	"USER_CHECKWORD" => true,
	"USER_PASSWORD" => true,
	"USER_CONFIRM_PASSWORD" => true,
	"USER_EMAIL" => true,
	"captcha_word" => true,
	"captcha_sid" => true,
	"login" => true,
	"Login" => true,
	"backurl" => true,
	"ajax" => true,
        "mode" => true,
);

$arResult["FORM_ACTION"] = $clearURL;
$arResult["HIDDEN"] = array();
foreach(array_merge($_GET, $_POST) as $key => $value)
{
	if(
		!array_key_exists($key, $arInputNames)
		&& !array_key_exists($key, $arSkip)
		&& !is_array($value)
	)
	{
		$arResult["HIDDEN"][] = array(
			"CONTROL_ID" => htmlspecialcharsbx($key),
			"CONTROL_NAME" => htmlspecialcharsbx($key),
			"HTML_VALUE" => htmlspecialcharsbx($value),
		);
	}
}

if(isset($_REQUEST["ajax"]) && $_REQUEST["ajax"] === "y")
{
	$this->IncludeComponentTemplate("ajax");
        require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
	die();
}
else
	$this->IncludeComponentTemplate();
?>
