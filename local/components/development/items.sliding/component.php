<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["SECTION_ID"] = intval($arParams["~SECTION_ID"]);
if($arParams["SECTION_ID"] > 0 && $arParams["SECTION_ID"]."" != $arParams["~SECTION_ID"])
{
	ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
	return;
}


$arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N"? "Y": "N";
$arParams["SHOW_ALL_WO_SECTION"] = $arParams["SHOW_ALL_WO_SECTION"]==="Y";

if(strlen($arParams["ELEMENT_SORT_FIELD"])<=0)
	$arParams["ELEMENT_SORT_FIELD"]="sort";
if($arParams["ELEMENT_SORT_ORDER"]!="desc")
	 $arParams["ELEMENT_SORT_ORDER"]="asc";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	global $arrFilter;
	$arrFilter = $arParams["FILTER_NAME"];
	if(!is_array($arrFilter))
		$arrFilter = array();
}

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);

if(!is_array($arParams["PRODUCT_PROPERTIES"]))
	$arParams["PRODUCT_PROPERTIES"] = array();
foreach($arParams["PRODUCT_PROPERTIES"] as $k=>$v)
	if($v==="")
		unset($arParams["PRODUCT_PROPERTIES"][$k]);

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";


$arParams["CACHE_FILTER"]=$arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

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

	$arSelect = array(
		$arParams["META_KEYWORDS"],
		$arParams["META_DESCRIPTION"],
		$arParams["BROWSER_TITLE"],
	);
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

	// list of the element fields that will be used in selection
	$arSelect = array(
		"ID",
		"NAME",
		"CODE",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"DETAIL_PAGE_URL",
		"DETAIL_PICTURE",
		"PREVIEW_TEXT",
		"CATALOG_GROUP_1",
	);
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"INCLUDE_SUBSECTIONS" => "Y",
        "PROPERTY_DISPLAY_VALUE" => "Y",
	);
	$arDiscountFilter = array(
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"INCLUDE_SUBSECTIONS" => "Y",
        "PROPERTY_DISPLAY_VALUE" => "Y",
	);

	$arSort = array(
		$arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
		"ID" => "DESC",
	);
	
	//EXECUTE NEWS
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, Array("nTopCount"=>$arParams['SLIDER_NEWS_COUNT']), $arSelect);
	$rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);
	if($arParams["BY_LINK"]!=="Y" && !$arParams["SHOW_ALL_WO_SECTION"])
	$rsElements->SetSectionContext($arResult);
	$arResult["ITEMS"]["NEWS"] = array();
	while($arElement = $rsElements->GetNext())
	{
		$arItemPrice = CPrice::GetBasePrice($arElement["ID"]);
		$arItemPrice["PRICE"] = round($arItemPrice["PRICE"],0);
		$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arElement["ID"],$USER->GetUserGroupArray(),"N",1);
		if($arDiscounts[0]["VALUE_TYPE"] == "P"){
			$arItemDiscount = $arItemPrice["PRICE"]*(100-$arDiscounts[0]["VALUE"])/100;
		}else{
			$arItemDiscount = $arItemPrice["PRICE"] - $arDiscounts[0]["VALUE"];
		}
		
		$arItem["ID"] = $arElement["ID"];
		$arItem["NAME"] = $arElement["NAME"];
		$arItem["DETAIL_PAGE_URL"] = $arElement["DETAIL_PAGE_URL"];
		$arItem["DETAIL_PICTURE"] = CFile::GetPath($arElement["DETAIL_PICTURE"]);
		$arItem["PREVIEW_TEXT"] = $arElement["PREVIEW_TEXT"];
		$arItem["PRICE"] = $arItemPrice;
		$arItem["PRICE"]["DISCOUNT_PRICE"] = $arItemDiscount;
		$arResult["ITEMS"]["NEWS"][]=$arItem;
	}
	
	//EXECUTE DISCOUNT
	$rsDiscountProductsList = CCatalogDiscount::GetDiscountProductsList($arSort, array(), false,array(),array("PRODUCT_ID"));
	while($arDiscount = $rsDiscountProductsList->Fetch()){
		$discountID[]=$arDiscount["PRODUCT_ID"];
	}
	
	$rsElements = CIBlockElement::GetList($arSort, array_merge(array("ID"=>$discountID),$arDiscountFilter), false, false, $arSelect);
	$rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);
	if($arParams["BY_LINK"]!=="Y" && !$arParams["SHOW_ALL_WO_SECTION"])
	$rsElements->SetSectionContext($arResult);
	$arResult["ITEMS"]["DISCOUNT"] = array();
	while($arDiscount = $rsElements->GetNext())
	{
		$arItemPrice = CPrice::GetBasePrice($arDiscount["ID"]);
		$arItemPrice["PRICE"] = round($arItemPrice["PRICE"],0);
		$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arDiscount["ID"],$USER->GetUserGroupArray(),"N",1);
		if($arDiscounts[0]["VALUE_TYPE"] == "P"){
			$arItemDiscount = $arItemPrice["PRICE"]*(100-$arDiscounts[0]["VALUE"])/100;
		}else{
			$arItemDiscount = $arItemPrice["PRICE"] - $arDiscounts[0]["VALUE"];
		}
		
		$arItem["ID"] = $arDiscount["ID"];
		$arItem["NAME"] = $arDiscount["NAME"];
		$arItem["DETAIL_PAGE_URL"] = $arDiscount["DETAIL_PAGE_URL"];
		$arItem["DETAIL_PICTURE"] = CFile::GetPath($arDiscount["DETAIL_PICTURE"]);
		$arItem["PREVIEW_TEXT"] = $arDiscount["PREVIEW_TEXT"];
		$arItem["PRICE"] = $arItemPrice;
		$arItem["PRICE"]["DISCOUNT_PRICE"] = $arItemDiscount;
		$arResult["ITEMS"]["DISCOUNT"][]=$arItem;
	}


	//EXECUTE HITS
	CModule::IncludeModule("sale");
	$rsOrder = CSaleOrder::GetList(array(),array("LID"=>SITE_ID,"PAYED"=>"Y"),false,array("nTopCount"=>"50"),array("ID"));
	while($arOrder = $rsOrder->Fetch()){$payedOrders[]=$arOrder["ID"];}
	$rsBasket = CSaleBasket::GetList(array(),array("LID"=>SITE_ID,"ORDER_ID"=>$payedOrders),false,array("nTopCount"=>"1000"),array("PRODUCT_ID","QUANTITY"));
	while($arBasket = $rsBasket->Fetch()){
		if(CIBlockElement::GetByID($arBasket["PRODUCT_ID"])->Fetch()){
			$hitsID[$arBasket["PRODUCT_ID"]] += $arBasket["QUANTITY"];
		}
	}
	arsort($hitsID);
	foreach($hitsID as $key=>$HID){$i++;
		if($i>$arParams["SLIDER_HITS_COUNT"]){break;}
		$prodIDS[] = $key;
	}
	
	$rsElements = CIBlockElement::GetList($arSort, array_merge(array("ID"=>$prodIDS),$arDiscountFilter), false, false, $arSelect);
	$rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);
	if($arParams["BY_LINK"]!=="Y" && !$arParams["SHOW_ALL_WO_SECTION"])
	$rsElements->SetSectionContext($arResult);
	$arResult["ITEMS"]["HITS"] = array();
	while($arHits = $rsElements->GetNext())
	{
		$arItemPrice = CPrice::GetBasePrice($arHits["ID"]);
		$arItemPrice["PRICE"] = round($arItemPrice["PRICE"],0);
		$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arHits["ID"],$USER->GetUserGroupArray(),"N",1);
		if($arDiscounts[0]["VALUE_TYPE"] == "P"){
			$arItemDiscount = $arItemPrice["PRICE"]*(100-$arDiscounts[0]["VALUE"])/100;
		}else{
			$arItemDiscount = $arItemPrice["PRICE"] - $arDiscounts[0]["VALUE"];
		}
		
		$arItem["ID"] = $arHits["ID"];
		$arItem["NAME"] = $arHits["NAME"];
		$arItem["DETAIL_PAGE_URL"] = $arHits["DETAIL_PAGE_URL"];
		$arItem["DETAIL_PICTURE"] = CFile::GetPath($arHits["DETAIL_PICTURE"]);
		$arItem["PREVIEW_TEXT"] = $arHits["PREVIEW_TEXT"];
		$arItem["PRICE"] = $arItemPrice;
		$arItem["PRICE"]["DISCOUNT_PRICE"] = $arItemDiscount;
		$arResult["ITEMS"]["HITS"][]=$arItem;
	}

	$this->SetResultCacheKeys(array(
		"ID",
		"NAV_CACHED_DATA",
		$arParams["META_KEYWORDS"],
		$arParams["META_DESCRIPTION"],
		$arParams["BROWSER_TITLE"],
		"NAME",
		"PATH",
	));
	$this->IncludeComponentTemplate();
}

$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);
?>
