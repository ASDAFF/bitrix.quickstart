<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["MODELS_IBLOCK_ID"] = intval($arParams["MODELS_IBLOCK_ID"]);
$arParams["PROPERTY_BRAND"] = empty($arParams["PROPERTY_BRAND"]) ? "fil_models_brand" : $arParams["PROPERTY_BRAND"];

$arParams["ONLY_WITH_IMAGE"] = ($arParams["ONLY_WITH_IMAGE"]==="Y");

$arParams["PAGE_ELEMENT_COUNT"] = intval($arParams["PAGE_ELEMENT_COUNT"]);
if($arParams["PAGE_ELEMENT_COUNT"]<=0)
	$arParams["PAGE_ELEMENT_COUNT"]=20;
$arParams["LINE_ELEMENT_COUNT"] = intval($arParams["LINE_ELEMENT_COUNT"]);
if($arParams["LINE_ELEMENT_COUNT"]<=0)
	$arParams["LINE_ELEMENT_COUNT"]=3;

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

$arNavParams = array(
	"nPageSize" => $arParams["PAGE_ELEMENT_COUNT"],
	"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	"bShowAll" => $arParams["PAGER_SHOW_ALL"],
);
$arNavigation = CDBResult::GetNavParams($arNavParams);
if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
	$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];

$arParams["CACHE_FILTER"]=$arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

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

    $property = CIBlockProperty::GetPropertyEnum($arParams["PROPERTY_BRAND"], array("sort"=>"asc", "name"=>"asc"), array("IBLOCK_ID" => $arParams["MODELS_IBLOCK_ID"]));
    while($prop = $property->GetNext())
    {
        $symbolCode = $prop["XML_ID"];
        $brand = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arParams["BRANDS_IBLOCK_ID"], "ACTIVE" => "Y", "PROPERTY_xmlcode" => $symbolCode), false, false);
        if($brand = $brand->GetNext())
        {
            $brandArr = array();
            $brandArr["NAME"] = $brand["NAME"];
            $brandArr["DETAIL_PAGE_URL"] = $brand["DETAIL_PAGE_URL"];
            $brandArr["DETAIL_PICTURE"] = $brand["DETAIL_PICTURE"];
            $arResult["ITEMS"][] = $brandArr;
        }
        elseif(!$arParams["ONLY_WITH_IMAGE"])
        {
            $brandArr = array();
            $brandArr["NAME"] = $prop["VALUE"];
            $brandArr["DETAIL_PAGE_URL"] = SITE_DIR."brands/".$prop["ID"]."/";
            $arResult["ITEMS"][] = $brandArr;

        }
    }

//	$arResult["NAV_STRING"] = $rsElements->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
//	$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
//	$arResult["NAV_RESULT"] = $rsElements;

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

if(isset($arResult[$arParams["META_KEYWORDS"]]))
{
	$val = $arResult[$arParams["META_KEYWORDS"]];
	if(is_array($val))
		$val = implode(" ", $val);
	$APPLICATION->SetPageProperty("keywords", $val);
}

if(isset($arResult[$arParams["META_DESCRIPTION"]]))
{
	$val = $arResult[$arParams["META_DESCRIPTION"]];
	if(is_array($val))
		$val = implode(" ", $val);
	$APPLICATION->SetPageProperty("description", $val);
}

if($arParams["SET_TITLE"])
	$APPLICATION->SetTitle($arResult["NAME"], $arTitleOptions);

if(isset($arResult[$arParams["BROWSER_TITLE"]]))
{
	$val = $arResult[$arParams["BROWSER_TITLE"]];
	if(is_array($val))
		$val = implode(" ", $val);
	$APPLICATION->SetPageProperty("title", $val, $arTitleOptions);
}

if($arParams["ADD_SECTIONS_CHAIN"] && isset($arResult["PATH"]) && is_array($arResult["PATH"]))
{
	foreach($arResult["PATH"] as $arPath)
	{
		$APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
	}
}

?>
