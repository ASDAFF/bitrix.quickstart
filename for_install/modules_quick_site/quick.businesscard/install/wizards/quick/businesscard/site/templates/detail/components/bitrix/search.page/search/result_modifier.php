<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["SEARCH"] as &$arItem)
	$arItem["DATE_CHANGE"] = ConvertDateTime($arItem["DATE_CHANGE"], "DD/MM/YYYY");

if(strlen($arResult["REQUEST"]["~QUERY"]) && is_object($arResult["NAV_RESULT"]))
{
	$arResult["FILTER_MD5"] = $arResult["NAV_RESULT"]->GetFilterMD5();
	$obSearchSuggest = new CSearchSuggest($arResult["FILTER_MD5"], $arResult["REQUEST"]["~QUERY"]);
	$obSearchSuggest->SetResultCount($arResult["NAV_RESULT"]->NavRecordCount);
}
?>