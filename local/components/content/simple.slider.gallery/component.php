<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
//echo "<pre>";print_r($arParams);echo "</pre>";

if (!CModule::IncludeModule("iblock")){
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALED"));
	return 0;
}

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);

$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

$arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";

if(strlen($arParams["SSG_SLIDESHOW_MODE"])<=0)
{
	$arParams["SSG_SLIDESHOW_MODE"] = "N";
}

if(strlen($arParams["SSG_PRELOAD_IMG"]<=0))
{
	$arParams["SSG_PRELOAD_IMG"] = "Y";
}

if(strlen($arParams["SSG_ONLOAD_START"]<=0))
{
	$arParams["SSG_ONLOAD_START"] = "N";
}

	if(is_numeric($arParams["IBLOCK_ID"]))
		{
			$rsIBlock = CIBlock::GetList(array(), array(
				"ACTIVE" => "Y",
				"ID" => $arParams["IBLOCK_ID"],
			));
		}
		else
		{
			$rsIBlock = CIBlock::GetList(array(), array(
				"ACTIVE" => "Y",
				"CODE" => $arParams["IBLOCK_ID"],
				"SITE_ID" => SITE_ID,
			));
		}
	
	if($arResult = $rsIBlock->GetNext())
	{
		$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
		//SELECT
		$arSelect = array(
			"ID",
			"NAME",
			"ACTIVE_FROM",
			"PREVIEW_PICTURE",
			"DETAIL_PICTURE",
		);
		//WHERE
		$arFilter = array (
			"IBLOCK_ID" => $arResult["ID"],
			"IBLOCK_LID" => SITE_ID,
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		);
		
		if($arParams["CHECK_DATES"])
			$arFilter["ACTIVE_DATE"] = "Y";
		//ORDER BY
		$arSort = array(
			"ID" => "DESC",
		);
		
		$arResult["ITEMS"] = array();
		$arResult["ELEMENTS"] = array();
		$rsElement = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
	
	
		while($obElement = $rsElement->GetNextElement())
		{
			$arItem = $obElement->GetFields();

			if(array_key_exists("PREVIEW_PICTURE", $arItem))
				$arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
			if(array_key_exists("DETAIL_PICTURE", $arItem))
				$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

			$arResult["ITEMS"][] = $arItem;
			$arResult["ELEMENTS"][] = $arItem["ID"];
		}
		
	//echo "<pre>";print_r($arResult);echo "</pre>";
	$this->IncludeComponentTemplate();
	}	
	else
	{
		ShowError(GetMessage("SSG_ELEMENTS_NA"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}


?>