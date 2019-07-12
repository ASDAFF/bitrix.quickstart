<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 180;

if(strlen($arParams["DISPLAY_IMG_WIDTH"])<=0)
	$arParams["DISPLAY_IMG_WIDTH"] = "75";
if(strlen($arParams["DISPLAY_IMG_WIDTH"])<=0)
	$arParams["DISPLAY_IMG_WIDTH"] = "225";
if(strlen($arParams["SHARPEN"])<=0)
	$arParams["SHARPEN"] = "30";
	
if(is_array($arParams['IBLOCK_ID']))
{
	foreach($arParams['IBLOCK_ID'] as $k => $v)
	{
		$v = intval($v);
		if ($v <= 0)
			unset($arParams['IBLOCK_ID'][$k]);
		else
			$arParams['IBLOCK_ID'][$k] = $v;
	}
	if(!count($arParams['IBLOCK_ID']))
		$arParams['IBLOCK_ID'] = 0;
}
else
{
	$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
}

$arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);

if($this->StartResultCache(false, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))
{
	if(!CModule::IncludeModule("catalog"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALLED"));
		return;
	}
	//SELECT
	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"CODE",
		"IBLOCK_SECTION_ID",
		"NAME",
		'PREVIEW_TEXT',
		'PREVIEW_TEXT_TYPE',
		'DETAIL_TEXT',
		'DETAIL_TEXT_TYPE',
		"PREVIEW_PICTURE",
		"DETAIL_PICTURE",
		"DETAIL_PAGE_URL",
		"PROPERTY_PRICE",
		"PROPERTY_PRICECURRENCY"
	);
	//WHERE
	$arFilter = array(
		"ACTIVE_DATE" => "Y",
		"ACTIVE"=>"Y",
		"IBLOCK_ACTIVE"=>"Y",
		"CHECK_PERMISSIONS"=>"Y",

		"!PROPERTY_SPECIALOFFER_VALUE" => false,
		"IBLOCK_LID" => SITE_ID
	);

	if(is_array($arParams['IBLOCK_ID']))
	{
		$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
		if(defined("BX_COMP_MANAGED_CACHE"))
		{
			foreach($arParams["IBLOCK_ID"] as $iblock_id)
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$iblock_id);
		}
	}
	elseif($arParams['IBLOCK_ID'] > 0)
	{
		$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
	}
	else
	{
		$arFilter["IBLOCK_TYPE"] = $arParams["IBLOCK_TYPE_ID"];
		if(defined("BX_COMP_MANAGED_CACHE"))
		{
			$rsIBlock = CIBlock::GetList(
				array('SORT' => 'ASC', 'ID' => 'DESC'),
				array("SITE_ID" => SITE_ID, "TYPE"=>$arParams["IBLOCK_TYPE_ID"])
			);
			while($arIBlock = $rsIBlock->GetNext())
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arIBlock["ID"]);

			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_new");
		}
	}

	if($arParams["PARENT_SECTION"]>0)
	{
		$arFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
		$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	}
	//ORDER BY
	$arSort = array(
		"RAND"=>"ASC",
	);
	
	$baseCurrency = CCurrency::GetBaseCurrency();
	
	//EXECUTE
	$rsIBlockElement = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
	$rsIBlockElement->SetUrlTemplates($arParams["DETAIL_URL"]);
	$i = 1;
	while($obElement = $rsIBlockElement->GetNextElement())
	{
		$arItem = $obElement->GetFields();
		
		$i++;
		$arItem['PICTURE'] = null;
		if ($arItem["DETAIL_PICTURE"])
			$arItem["PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

		$arItem['PRICE'] = CCatalogProduct::GetOptimalPrice($arItem['ID'], 1, $USER->GetUserGroupArray());
		if(count($arItem['PRICE']) > 0)
			$arItem["PRICE"]['DISCOUNT_PRICE'] = CCurrencyRates::ConvertCurrency($arItem["PRICE"]['DISCOUNT_PRICE'], $baseCurrency, $arItem["PRICE"]['PRICE']['CURRENCY']);
		
		if(count($arParams["PROPERTY_CODE"]))
			$arItem["PROPERTIES"] = $obElement->GetProperties();
		elseif(count($arParams["PRODUCT_PROPERTIES"]))
			$arItem["PROPERTIES"] = $obElement->GetProperties();
			
		$arItem["DISPLAY_PROPERTIES"] = array();
		foreach($arParams["PROPERTY_CODE"] as $pid)
		{
			$prop = &$arItem["PROPERTIES"][$pid];
			if((is_array($prop["VALUE"]) && count($prop["VALUE"])>0) ||
			   (!is_array($prop["VALUE"]) && strlen($prop["VALUE"])>0))
			{
				$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "catalog_out");
			}
		}

		if(empty($arParams["RAND_COUNT"])){ 
			$arResult = $arItem;break;
		}
		else
		{
			$arResult["ITEMS"][] = $arItem;
			if($arParams["RAND_COUNT"] < $i) break;
		}

	}
	if($i > 0)
	{
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->EndResultCache();
	}
}
?>
