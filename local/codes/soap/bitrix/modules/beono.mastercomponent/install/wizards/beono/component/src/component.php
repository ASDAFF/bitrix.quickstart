<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// + complex
$arDefaultUrlTemplates404 = array(
   "list" => "/",
   "detail" => "/#ELEMENT_ID#/"
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array("IBLOCK_ID", "ELEMENT_ID");

$SEF_FOLDER = "";
$arUrlTemplates = array();

if ($arParams["SEF_MODE"] == "Y")
{
	$arVariables = array();

	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

	$componentPage = CComponentEngine::ParseComponentPath($arParams["SEF_FOLDER"], $arUrlTemplates, $arVariables);

	if (StrLen($componentPage) <= 0) $componentPage = "list";

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

	$SEF_FOLDER = $arParams["SEF_FOLDER"];
   
} else {
	
	$arVariables = array();

	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

	$componentPage = "";
	if (IntVal($arVariables["ELEMENT_ID"]) > 0) {
		$componentPage = "detail";
	} else {
		$componentPage = "list";
	}
}

$arResult = array(
	"FOLDER" => $SEF_FOLDER,
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables,
	"ALIASES" => $arVariableAliases
);

$this->IncludeComponentTemplate($componentPage);
// - complex
// + cache
if(!isset($arParams["CACHE_TIME"])) {
	$arParams["CACHE_TIME"] = 3600;
}
// - cache
// + iblock
CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if($arParams["IBLOCK_ID"] < 1) {
	ShowError("IBLOCK_ID IS NOT DEFINED");
	return false;
}

if(!isset($arParams["ITEMS_LIMIT"])) {
	$arParams["ITEMS_LIMIT"] = 10;
}

$arNavParams = array();

if ($arParams["ITEMS_LIMIT"] > 0) {
	$arNavParams = array(
		"nPageSize" => $arParams["ITEMS_LIMIT"],
	);
}

$arNavigation = CDBResult::GetNavParams($arNavParams);
// - iblock
// + cache
if($this->StartResultCache(false, array($arNavigation)))
{
// - cache
	
	// + iblock
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError("IBLOCK_MODULE_NOT_INSTALLED");
		return false;
	}	
	
	$arSort= array("SORT" => "ASC", "DATE_ACTIVE_FROM" => "DESC", "ID" => "DESC");
	$arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "ACTIVE_DATE" => "Y");
	$arSelect = array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_TEXT", "PREVIEW_PICTURE");
	
	$rsElement = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
	
	if ($arParams["DETAIL_URL"]) {
		$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);
	}
	
	while($obElement = $rsElement->GetNextElement()) {
		
		$arElement = $obElement->GetFields();
		if ($arElement["PREVIEW_PICTURE"]) {
			$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
		} 
		//$arElement["PROPERTIES"] = $obElement->GetProperties();
		
		$arResult["ITEMS"][] = $arElement;
	}

	$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx($navComponentObject, "Страницы", "", "");
	// - iblock	
	// + cache
	$this->SetResultCacheKeys(array(
		"ID",
		"IBLOCK_ID",
		"NAV_CACHED_DATA",
		"NAME",
		"IBLOCK_SECTION_ID",
		"IBLOCK",
		"LIST_PAGE_URL", 
		"~LIST_PAGE_URL",
		"SECTION",
		"PROPERTIES",
	));
	// - cache	
	// + template
	$this->IncludeComponentTemplate();
	// - template		
	// + http_404
	if (empty($arResult["ITEM"])) {
		$this->AbortResultCache();
		ShowError("404 Not Found");
		@define("ERROR_404", "Y");
		CHTTP::SetStatus("404 Not Found");
	}	
	// - http_404
// + cache
}
// - cache
?>