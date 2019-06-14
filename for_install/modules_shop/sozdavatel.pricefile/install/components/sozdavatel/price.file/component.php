<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
	Processing of received parameters
*************************************************************************/
$arParams["CACHE_TIME"] = 0;
$arParams["CACHE_TYPE"] = "N";
$arParams["CACHE_FILTER"] = false;


$arParams["PAGE_ELEMENT_COUNT"] = 1000000;
$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["SECTION_ID"] = false;

$arParams["INCLUDE_SUBSECTIONS"] = "Y";
$arParams["SHOW_ALL_WO_SECTION"] = "Y";

if(strlen($arParams["ELEMENT_SORT_FIELD"])<=0)
	$arParams["ELEMENT_SORT_FIELD"]="sort";

if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["ELEMENT_SORT_ORDER"]))
	 $arParams["ELEMENT_SORT_ORDER"]="asc";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	global $$arParams["FILTER_NAME"];
	$arrFilter = ${$arParams["FILTER_NAME"]};
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arParams["LINE_ELEMENT_COUNT"]=1;

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);

if(!is_array($arParams["PRICE_CODE"]))
	$arParams["PRICE_CODE"] = array();
$arParams["USE_PRICE_COUNT"] = $arParams["USE_PRICE_COUNT"]=="Y";
$arParams["SHOW_PRICE_COUNT"] = intval($arParams["SHOW_PRICE_COUNT"]);
if($arParams["SHOW_PRICE_COUNT"]<=0)
	$arParams["SHOW_PRICE_COUNT"]=1;
$arParams["USE_PRODUCT_QUANTITY"] = $arParams["USE_PRODUCT_QUANTITY"]==="Y";

if(!is_array($arParams["PRODUCT_PROPERTIES"]))
	$arParams["PRODUCT_PROPERTIES"] = array();
foreach($arParams["PRODUCT_PROPERTIES"] as $k=>$v)
	if($v==="")
		unset($arParams["PRODUCT_PROPERTIES"][$k]);

$arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";

/*************************************************************************
			Work with cache
*************************************************************************/
if($this->StartResultCache(false, array($arrFilter, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $arNavigation)))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	$arSelect = array();
	if(isset($arParams["SECTION_USER_FIELDS"]) && is_array($arParams["SECTION_USER_FIELDS"]))
	{
		foreach($arParams["SECTION_USER_FIELDS"] as $field)
			if(is_string($field) && preg_match("/^UF_/", $field))
				$arSelect[] = $field;
	}

	$arFilter = array(
		"ACTIVE"=>"Y",
		"GLOBAL_ACTIVE"=>"Y",
		"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		"IBLOCK_ACTIVE"=>"Y",
	);

	$bSectionFound = false;
	//Hidden triky parameter USED to display linked
	//by default it is not set
	if($arParams["BY_LINK"]==="Y")
	{
		$arResult = array(
			"ID" => 0,
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		);
		$bSectionFound = true;
	}
	elseif(strlen($arParams["SECTION_CODE"]) > 0)
	{
		$arFilter["CODE"]=$arParams["SECTION_CODE"];
		$rsSection = CIBlockSection::GetList(Array(), $arFilter, false, $arSelect);
		$rsSection->SetUrlTemplates("", $arParams["SECTION_URL"]);
		$arResult = $rsSection->GetNext();
		if($arResult)
			$bSectionFound = true;
	}
	elseif($arParams["SECTION_ID"])
	{
		$arFilter["ID"]=$arParams["SECTION_ID"];
		$rsSection = CIBlockSection::GetList(Array(), $arFilter, false, $arSelect);
		$rsSection->SetUrlTemplates("", $arParams["SECTION_URL"]);
		$arResult = $rsSection->GetNext();
		if($arResult)
			$bSectionFound = true;
	}
	else
	{
		//Root section (no section filter)
		$arResult = array(
			"ID" => 0,
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		);
		$bSectionFound = true;
	}

	if(!$bSectionFound)
	{
		$this->AbortResultCache();
		ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
		return;
	}
	elseif($arResult["ID"] > 0 && $arParams["ADD_SECTIONS_CHAIN"])
	{
		$arResult["PATH"] = array();
		$rsPath = GetIBlockSectionPath($arResult["IBLOCK_ID"], $arResult["ID"]);
		$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
		while($arPath = $rsPath->GetNext())
		{
			$arResult["PATH"][]=$arPath;
		}
	}

	//This function returns array with prices description and access rights
	//in case catalog module n/a prices get values from element properties
	$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);

	$arResult["PICTURE"] = CFile::GetFileArray($arResult["PICTURE"]);
	$arResult["DETAIL_PICTURE"] = CFile::GetFileArray($arResult["DETAIL_PICTURE"]);

	// list of the element fields that will be used in selection
	$arSelect = array(
		"ID",
		"NAME",
		"CODE",
		"DATE_CREATE",
		"ACTIVE_FROM",
		"CREATED_BY",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"DETAIL_PAGE_URL",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"DETAIL_PICTURE",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"PREVIEW_PICTURE",
		"PROPERTY_*",
	);
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
	);

	if($arParams["BY_LINK"]!=="Y")
	{
		if($arResult["ID"])
			$arFilter["SECTION_ID"] = $arResult["ID"];
		elseif(!$arParams["SHOW_ALL_WO_SECTION"])
			$arFilter["SECTION_ID"] = 0;
	}

	if(is_array($arrFilter["OFFERS"]))
	{
		$arOffersIBlock = CIBlockPriceTools::GetOffersIBlock($arParams["IBLOCK_ID"]);
		if(is_array($arOffersIBlock))
		{
			if(!empty($arrFilter["OFFERS"]))
			{
				$arSubFilter = $arrFilter["OFFERS"];
				$arSubFilter["IBLOCK_ID"] = $arOffersIBlock["OFFERS_IBLOCK_ID"];
				$arSubFilter["ACTIVE_DATE"] = "Y";
				$arSubFilter["ACTIVE"] = "Y";
				$arFilter["=ID"] = CIBlockElement::SubQuery("PROPERTY_".$arOffersIBlock["OFFERS_PROPERTY_ID"], $arSubFilter);
			}

			$arPriceFilter = array();
			foreach($arrFilter as $key => $value)
			{
				if(preg_match('/^(>=|<=)CATALOG_PRICE_/', $key))
				{
					$arPriceFilter[$key] = $value;
					unset($arrFilter[$key]);
				}
			}

			if(!empty($arPriceFilter))
			{
				$arSubFilter = $arPriceFilter;
				$arSubFilter["IBLOCK_ID"] = $arOffersIBlock["OFFERS_IBLOCK_ID"];
				$arSubFilter["ACTIVE_DATE"] = "Y";
				$arSubFilter["ACTIVE"] = "Y";
				$arFilter[] = array(
					"LOGIC" => "OR",
					array($arPriceFilter),
					"=ID" => CIBlockElement::SubQuery("PROPERTY_".$arOffersIBlock["OFFERS_PROPERTY_ID"], $arSubFilter),
				);
			}
		}
	}

	//PRICES
	if(!$arParams["USE_PRICE_COUNT"])
	{
		foreach($arResult["PRICES"] as $key => $value)
		{
			$arSelect[] = $value["SELECT"];
			$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
		}
	}

	$arSort = array(
		$arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
		"ID" => "DESC",
	);
	//EXECUTE
	$rsElements = CIBlockElement::GetList($arSort, array_merge($arrFilter, $arFilter), false, $arNavParams, $arSelect);
	$rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);
	if($arParams["BY_LINK"]!=="Y" && !$arParams["SHOW_ALL_WO_SECTION"])
		$rsElements->SetSectionContext($arResult);
	$arResult["ITEMS"] = array();
	$arSections = array();
	$arResult["DISPLAY_PROPERTIES"] = array();
	while($obElement = $rsElements->GetNextElement())
	{
		$arItem = $obElement->GetFields();

		if($arResult["ID"])
			$arItem["IBLOCK_SECTION_ID"] = $arResult["ID"];

		$arButtons = CIBlock::GetPanelButtons(
			$arItem["IBLOCK_ID"],
			$arItem["ID"],
			$arResult["ID"],
			array("SECTION_BUTTONS"=>false, "SESSID"=>false)
		);
		$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

		$arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
		$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

		if(count($arParams["PROPERTY_CODE"]))
			$arItem["PROPERTIES"] = $obElement->GetProperties();
		elseif(count($arParams["PRODUCT_PROPERTIES"]))
			$arItem["PROPERTIES"] = $obElement->GetProperties();

		$arItem["DISPLAY_PROPERTIES"] = array();
		foreach($arParams["PROPERTY_CODE"] as $pid)
		{
			$prop = &$arItem["PROPERTIES"][$pid];
			if(
				(is_array($prop["VALUE"]) && count($prop["VALUE"]) > 0)
				|| (!is_array($prop["VALUE"]) && strlen($prop["VALUE"]) > 0)
			)
			{
				$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "catalog_out");
			}
		}

		$arItem["PRODUCT_PROPERTIES"] = CIBlockPriceTools::GetProductProperties(
			$arParams["IBLOCK_ID"],
			$arItem["ID"],
			$arParams["PRODUCT_PROPERTIES"],
			$arItem["PROPERTIES"]
		);

		if($arParams["USE_PRICE_COUNT"])
		{
			if(CModule::IncludeModule("catalog"))
			{
				$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arItem["ID"]);
				foreach($arItem["PRICE_MATRIX"]["COLS"] as $keyColumn=>$arColumn)
					$arItem["PRICE_MATRIX"]["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialcharsex($arColumn["NAME_LANG"]);
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
			$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem, $arParams['PRICE_VAT_INCLUDE']);
		}
		$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem);

		$arItem["BUY_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
		$arItem["ADD_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
		$arItem["COMPARE_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arItem["ID"], array("action", "id")));

		$arItem["SECTION"]["PATH"] = array();
		if($arParams["BY_LINK"]==="Y")
		{
			$rsPath = GetIBlockSectionPath($arItem["IBLOCK_ID"], $arItem["IBLOCK_SECTION_ID"]);
			$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
			while($arPath = $rsPath->GetNext())
			{
				$arItem["SECTION"]["PATH"][]=$arPath;
			}
		}

		$arResult["ITEMS"][]=$arItem;
		$arResult["ELEMENTS"][] = $arItem["ID"];
		$arSections[$arItem["IBLOCK_SECTION_ID"]] = Array();
		foreach ($arItem["DISPLAY_PROPERTIES"] as $arProp)
		{
			$arResult["DISPLAY_PROPERTIES"][$arProp["CODE"]]["NAME"] = $arProp["NAME"];
		}
	}

	$arResult["NAV_STRING"] = $rsElements->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
	$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
	$arResult["NAV_RESULT"] = $rsElements;

	if(!isset($arParams["OFFERS_FIELD_CODE"]))
		$arParams["OFFERS_FIELD_CODE"] = array();
	foreach($arParams["OFFERS_FIELD_CODE"] as $key => $value)
		if($value === "")
			unset($arParams["OFFERS_FIELD_CODE"][$key]);

	if(!isset($arParams["OFFERS_PROPERTY_CODE"]))
		$arParams["OFFERS_PROPERTY_CODE"] = array();
	foreach($arParams["OFFERS_PROPERTY_CODE"] as $key => $value)
		if($value === "")
			unset($arParams["OFFERS_PROPERTY_CODE"][$key]);

	if(
		!empty($arResult["ELEMENTS"])
		&& (
			!empty($arParams["OFFERS_FIELD_CODE"])
			|| !empty($arParams["OFFERS_PROPERTY_CODE"])
		)
	)
	{
		$arOffers = CIBlockPriceTools::GetOffersArray(
			$arParams["IBLOCK_ID"]
			,$arResult["ELEMENTS"]
			,array(
				$arParams["OFFERS_SORT_FIELD"] => $arParams["OFFERS_SORT_ORDER"],
				"ID" => "DESC",
			)
			,$arParams["OFFERS_FIELD_CODE"]
			,$arParams["OFFERS_PROPERTY_CODE"]
			,$arParams["OFFERS_LIMIT"]
			,$arResult["PRICES"]
			,$arParams['PRICE_VAT_INCLUDE']
		);
		if(!empty($arOffers))
		{
			$arElementOffer = array();
			foreach($arResult["ELEMENTS"] as $i => $id)
			{
				$arResult["ITEMS"][$i]["OFFERS"] = array();
				$arElementOffer[$id] = &$arResult["ITEMS"][$i]["OFFERS"];
			}

			foreach($arOffers as $arOffer)
			{
				if(array_key_exists($arOffer["LINK_ELEMENT_ID"], $arElementOffer))
				{
					$arOffer["BUY_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arOffer["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
					$arOffer["ADD_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arOffer["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
					$arOffer["COMPARE_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arOffer["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));

					$arElementOffer[$arOffer["LINK_ELEMENT_ID"]][] = $arOffer;
				}
			}
		}
	}
	
	// get sections
	$arSections = array_unique($arSections);
	$arSecFilter = array(
		'IBLOCK_ID'=>$arParams["IBLOCK_ID"], 
		'GLOBAL_ACTIVE'=>'Y'
	);
	$arSecSort = array(
		$arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
		"ID" => "DESC",
	);
	$res_sections = CIBlockSection::GetList($arSecSort, $arSecFilter, true);
	while($ar_fields = $res_sections->GetNext())
	{
		$arSections[$ar_fields["ID"]]["NAME"] = $ar_fields['NAME'];
		$arSections[$ar_fields["ID"]]["ELEMENT_CNT"] = $ar_fields['ELEMENT_CNT'];
	}
	
	foreach ($arResult["ITEMS"] as $arItem)
	{
		$arSections[$arItem["IBLOCK_SECTION_ID"]]["ITEMS"][] = $arItem;
	}
	unset($arResult["ITEMS"]);
	$arResult["SECTIONS"] = $arSections;
	
	$this->SetResultCacheKeys(array(
		"ID",
		"NAV_CACHED_DATA",
		$arParams["META_KEYWORDS"],
		$arParams["META_DESCRIPTION"],
		$arParams["BROWSER_TITLE"],
		"NAME",
		"PATH",
		"IBLOCK_SECTION_ID",
	));

	$this->IncludeComponentTemplate();
}

$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);

?>
