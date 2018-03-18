<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams["CACHE_TIME"])) {
	$arParams["CACHE_TIME"] = 3600;
}

if(!isset($arParams["POSITION"])) {
	$arParams["POSITION"] = "in_place";
}

if(!isset($arParams["THEME"])) {
	$arParams["THEME"] = "01";
}

if(!isset($arParams["SIZE"])) {
	$arParams["SIZE"] = "medium";
}

if(!isset($arParams["FORM"])) {
	$arParams["FORM"] = "square";
}

if(!isset($arParams["LINE"])) {
	$arParams["LINE"] = "line";
}

if(!isset($arParams["PLACEMENT"])) {
	$arParams["PLACEMENT"] = "horizontal";
}

if(!isset($arParams["COUNTER"])) {
	$arParams["COUNTER"] = "counter";
}

if($this->StartResultCache(false, array($arNavigation)))
{

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

	$this->IncludeComponentTemplate();

}

?>