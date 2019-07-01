<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if (!CModule::IncludeModule('more.acomponents')) return;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["SECTION_ID"] = intval($arParams["~SECTION_ID"]);
if($arParams["SECTION_ID"] > 0 && $arParams["SECTION_ID"]."" != $arParams["~SECTION_ID"]) {
	ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
	return;
}


$arParams["SECTION_URL"]=trim($arParams["SECTION_URL"]);

$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]==="Y"; //Turn off by default
$arParams["GET_PATH"] = $arParams["GET_PATH"]==="Y"; //Turn off by default
$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["CP_PPSD_SHOW_ONLY_ACTIVE"] = $arParams["CP_PPSD_SHOW_ONLY_ACTIVE"]==="Y";





$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"]) {
	$arNavParams = array(
		"nPageSize" => 1,
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
}
else {
	$arNavigation = false;
}


	
//Format these UF_ after select
if(!is_array($arParams["SECTION_S_PROPERTIES"]))
	$arParams["SECTION_S_PROPERTIES"] = array();	
foreach($arParams["SECTION_S_PROPERTIES"] as $k=>$v)
	if($v==="" || !preg_match("/^UF_/", $v))
		unset($arParams["SECTION_S_PROPERTIES"][$k]);

$arCheckParamsArray = Array(
	"SECTION_S_FIELDS",	//Select these fields from database
	"SECTION_F_FIELDS",	//Format these fields after select
	"SECTION_F_PROPERTIES", //Select these UF_ from database
);


foreach ($arCheckParamsArray as $paramkey) {
	if(!is_array($arParams[$paramkey])) $arParams[$paramkey] = array();
		
	if ($paramkey == "SECTION_S_FIELDS") {
		if (in_array("DESCRIPTION",$arParams[$paramkey]) && !in_array("DESCRIPTION_TYPE",$arParams[$paramkey])) {
			$arParams[$paramkey][] = "DESCRIPTION_TYPE";
		}
		
		$arRequired = Array("ID","IBLOCK_ID","SECTION_PAGE_URL");
		$arParams[$paramkey] = array_merge($arParams[$paramkey],$arRequired);
	}
	
	foreach($arParams[$paramkey] as $k=>$v) {
		if($v==="") {
			unset($arParams[$paramkey][$k]);
		} else {
			if ($paramkey == "SECTION_F_FIELDS") {
				if (!in_array($v,$arParams['SECTION_S_FIELDS'])) {
					unset($arParams[$paramkey][$k]);
				}
			}
			if ($paramkey == "SECTION_F_PROPERTIES") {
				if (!in_array($v,$arParams['SECTION_S_PROPERTIES'])) {
					unset($arParams[$paramkey][$k]);
				}
			}
		}
	}
}
/*************************************************************************
			Work with cache
*************************************************************************/
if($this->StartResultCache(false, array($arNavigation)))
{
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	
	$arSelect = array();
	foreach($arParams["SECTION_S_FIELDS"] as $field) {
		$arSelect[] = $field;
	}
	if(isset($arParams["SECTION_S_PROPERTIES"]) && is_array($arParams["SECTION_S_PROPERTIES"])) {
		foreach($arParams["SECTION_S_PROPERTIES"] as $field) {
			$arSelect[] = $field;
		}
	}
	if(preg_match("/^UF_/", $arParams["META_KEYWORDS"])) $arSelect[] = $arParams["META_KEYWORDS"];
	if(preg_match("/^UF_/", $arParams["META_DESCRIPTION"])) $arSelect[] = $arParams["META_DESCRIPTION"];
	if(preg_match("/^UF_/", $arParams["BROWSER_TITLE"])) $arSelect[] = $arParams["BROWSER_TITLE"];

	$arFilter = array(
		"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		"IBLOCK_ACTIVE"=>"Y",
	);
	if ($arParams["CP_PPSD_SHOW_ONLY_ACTIVE"] === true) {
		$arFilter['ACTIVE'] = "Y";
		$arFilter['GLOBAL_ACTIVE'] = "Y";		
	}
	
	
	$bneedfound = false;
	if(strlen($arParams["SECTION_CODE"]) > 0) {
		$arFilter["CODE"] = $arParams["SECTION_CODE"];
		$bneedfound = true;
	} elseif($arParams["SECTION_ID"]) {
		$arFilter["ID"] = $arParams["SECTION_ID"];
		$bneedfound = true;
	}
	
	if ($bneedfound === true) {
		$rsSection = CIBlockSection::GetList(Array(), $arFilter, false, $arSelect);
		$rsSection->SetUrlTemplates("", $arParams["SECTION_URL"]);
		$arResult = $rsSection->GetNext();
		
		
		//format fields
		$obpxformatsf = new CPPFormatSF;
		if (count($arParams["SECTION_F_FIELDS"])) {
			$obpxformatsf->SetFormatted($arParams["SECTION_F_FIELDS"]);
			
			foreach (Array("PICTURE","DETAIL_PICTURE") as $fkey) {
				if ($arParams['PPR_'.$fkey.'_C'] = "Y") {
					$bInitSizes = $arParams['PPR_'.$fkey.'_RT'] === "Y";
					
					$obpxformatsf->paramformatclass->AddParam($fkey,Array(
						"RESIZE" => Array(
							"arSize" => Array("width"=>$arParams['PPR_'.$fkey.'_W'],"height"=>$arParams['PPR_'.$fkey.'_H']),
							"resizeType" => constant($arParams['PPR_'.$fkey.'_RT']),
							"bInitSizes" => $bInitSizes,
							"arFilters" => 	Array((Array("name" => "sharpen", "precision" => 100)))
						)
					));
				}			
			}
			
			
		}
		$obpxformatsf->GetDispayFields($arResult);
		
		
		if ($arResult['IBLOCK_ID'] > 0) {
			$obpxformatuf = new CPPFormatUF;
			$obpxformatuf->Init("IBLOCK_".$arResult["IBLOCK_ID"]."_SECTION");	
			if (count($arParams["SECTION_F_PROPERTIES"])) {
				$obpxformatuf->SetFormatted($arParams["SECTION_F_PROPERTIES"]);
				$arResult['USER_FIELDS'] = $obpxformatuf->GetEntityMeta();
			}
			$obpxformatuf->GetDispayFields($arResult);
		}
		
	}

	if (intval($arResult["ID"]) <= 0) {
		$this->AbortResultCache();
		ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
		return;
	} 
	
	if($arParams["ADD_SECTIONS_CHAIN"] || $arParams["GET_PATH"]) {
		$arResult["PATH"] = array();
		$rsPath = GetIBlockSectionPath($arResult["IBLOCK_ID"], $arResult["ID"]);
		$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
		while($arPath = $rsPath->GetNext()) {
			$arResult["PATH"][]=$arPath;
		}
	}
	
	$arResult["NAV_RESULT"] = new CDBResult;
	if(($arResult["DESCRIPTION_TYPE"]=="html") && (strstr($arResult["DESCRIPTION"], "<BREAK />")!==false))
		$arPages=explode("<BREAK />", $arResult["DESCRIPTION"]);
	elseif(($arResult["DETAIL_TEXT_TYPE"]!="html") && (strstr($arResult["DESCRIPTION"], "&lt;BREAK /&gt;")!==false))
		$arPages=explode("&lt;BREAK /&gt;", $arResult["DESCRIPTION"]);
	else
		$arPages=array();
	$arResult["NAV_RESULT"]->InitFromArray($arPages);
	$arResult["NAV_RESULT"]->NavStart($arNavParams);
	if(count($arPages)==0) {
		$arResult["NAV_RESULT"] = false;
	} else {
		$arResult["NAV_STRING"] = $arResult["NAV_RESULT"]->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
		$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();

		$arResult["NAV_TEXT"] = "";
		while($ar = $arResult["NAV_RESULT"]->Fetch())
			$arResult["NAV_TEXT"].=$ar;
	}

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

$arTitleOptions = null;
if($USER->IsAuthorized())
{
	if(
		$APPLICATION->GetShowIncludeAreas()
		|| $arParams["SET_TITLE"]
		|| isset($arResult[$arParams["BROWSER_TITLE"]])
	)
	{
		if(CModule::IncludeModule("iblock"))
		{
			$UrlDeleteSectionButton = "";
			if($arResult["IBLOCK_SECTION_ID"] > 0)
			{
				$rsSection = CIBlockSection::GetList(
					array(),
					array("=ID" => $arResult["IBLOCK_SECTION_ID"]),
					false,
					array("SECTION_PAGE_URL")
				);
				$rsSection->SetUrlTemplates("", $arParams["SECTION_URL"]);
				$arSection = $rsSection->GetNext();
				$UrlDeleteSectionButton = $arSection["SECTION_PAGE_URL"];
			}

			if(empty($UrlDeleteSectionButton))
			{
				$url_template = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "LIST_PAGE_URL");
				$arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"]);
				$arIBlock["IBLOCK_CODE"] = $arIBlock["CODE"];
				$UrlDeleteSectionButton = CIBlock::ReplaceDetailURL($url_template, $arIBlock, true, false);
			}

			$arReturnUrl = array(
				"add_section" => (
					strlen($arParams["SECTION_URL"])?
					$arParams["SECTION_URL"]:
					CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_PAGE_URL")
				),
				"delete_section" => $UrlDeleteSectionButton,
			);
			$arButtons = CIBlock::GetPanelButtons(
				$arParams["IBLOCK_ID"],
				0,
				$arResult["ID"],
				array("RETURN_URL" =>  $arReturnUrl)
			);

			if($APPLICATION->GetShowIncludeAreas())
				$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

			if($arParams["SET_TITLE"] || isset($arResult[$arParams["BROWSER_TITLE"]]))
			{
				$arTitleOptions = array(
					'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_section"]["ACTION"],
					'PUBLIC_EDIT_LINK' => $arButtons["edit"]["edit_section"]["ACTION"],
					'COMPONENT_NAME' => $this->GetName(),
				);
			}
		}
	}
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
