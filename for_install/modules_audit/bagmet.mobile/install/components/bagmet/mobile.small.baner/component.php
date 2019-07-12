<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}


/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

if (empty($arParams['IBLOCK_ID']))
{
	$rsIBlock = CIBlock::GetList(Array("element_cnt" => "1"), Array("TYPE" => $arParams["IBLOCK_TYPE_ID"], "ACTIVE"=>"Y", "IBLOCK_LID"=>SITE_ID), true);
	if($arr=$rsIBlock->Fetch())
		$arParams['IBLOCK_ID'] = $arr["ID"];
}

$arParams["ELEMENT_SORT_FIELD"]="sort";
if($arParams["ELEMENT_SORT_ORDER"]!="desc")
	$arParams["ELEMENT_SORT_ORDER"]="asc";

$arParams["ELEMENT_COUNT"] = intval($arParams["ELEMENT_COUNT"]);
if($arParams["ELEMENT_COUNT"]<=0)
	$arParams["ELEMENT_COUNT"]=9;

/*if(strlen($arParams["FILTER_NAME"])>0)
{
	global ${$arParams["FILTER_NAME"]};
	$arrFilter = ${$arParams["FILTER_NAME"]};
}
if(!is_array($arrFilter))*/
$arrFilter=array();

$arParams["CACHE_FILTER"]=$arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

if($arParams['IBLOCK_ID'] > 0)
{
	$arrFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
	//if(defined("BX_COMP_MANAGED_CACHE"))
	//	$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

	$arFilter = Array("ID" => $arParams['IBLOCK_ID'],"TYPE"=>"banner", "SITE_ID"=>SITE_ID);
	$obCache = new CPHPCache;
	if($obCache->InitCache(36000, serialize($arFilter), "/iblock/banner"))
	{
		$arIblock = $obCache->GetVars();
	}
	else
	{
		$arIBlock = array();
		$dbRes = CIBlock::GetList(Array(), $arFilter);
		$dbRes = new CIBlockResult($dbRes);

		if(defined("BX_COMP_MANAGED_CACHE"))
		{
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache("/iblock/banner");

			if ($arIBlock = $dbRes->GetNext())
			{
				$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
			}
			$CACHE_MANAGER->EndTagCache();
		}
		else
		{
			if(!$arIBlock = $dbRes->GetNext())
				$arIBlock = array();
		}

		$obCache->EndDataCache($arIBlock);
	}
}
else
{
	ShowError("IBLOCK_ERROR");
	return;
}
/*************************************************************************
			Work with cache
*************************************************************************/
if($this->StartResultCache(false, array($arrFilter, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()))))
{
	/************************************
			Elements
	************************************/
	//SELECT
	$arSelect = array(
		"ID",
		"NAME",
		"CODE",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		//"DETAIL_PAGE_URL",
		//"PREVIEW_PICTURE",
		"DETAIL_PICTURE",
		//"PREVIEW_TEXT",
		//"PREVIEW_TEXT_TYPE",
		//"PROPERTY_*",
		"PROPERTY_BANNER_LINK"
	);
	//WHERE
	$arrFilter["ACTIVE"] = "Y";

	$arrFilter["IBLOCK_LID"] = SITE_ID;
	$arrFilter["IBLOCK_ACTIVE"] = "Y";
	$arrFilter["ACTIVE_DATE"] = "Y";
	$arrFilter["ACTIVE"] = "Y";
	$arrFilter["CHECK_PERMISSIONS"] = "Y";

	//ORDER BY
	$arSort = array(
		$arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
		"ID" => "DESC",
	);

	$arResult["ITEMS"] = array();
	$rsElements = CIBlockElement::GetList($arSort, $arrFilter, false, array("nTopCount" => $arParams["ELEMENT_COUNT"]), $arSelect);
	//$rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);

	while($obElement = $rsElements->GetNextElement())
	{
		$arItem = $obElement->GetFields();

		$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

		/*$arItem["PROPERTIES"] = $obElement->GetProperties();

		$arItem["DISPLAY_PROPERTIES"] = array();

		$arItem["PRODUCT_PROPERTIES"] = CIBlockPriceTools::GetProductProperties(
			$arParams["IBLOCK_ID"],
			$arItem["ID"],
			$arParams["PRODUCT_PROPERTIES"],
			$arItem["PROPERTIES"]
		);

		*/
		$arResult["ITEMS"][]=$arItem;
	}
	$this->IncludeComponentTemplate();
}
?>