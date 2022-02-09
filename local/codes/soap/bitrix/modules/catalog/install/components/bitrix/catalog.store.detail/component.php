<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("catalog"))
{
	ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALL"));
	return;
}
if(!CBXFeatures::IsFeatureEnabled('CatMultiStore'))
{
	ShowError(GetMessage("CAT_FEATURE_NOT_ALLOW"));
	return;
}
if(isset($arParams['STORE']))
{
	$arResult['STORE'] = intval($arParams['STORE']);
	if(!isset($arParams["CACHE_TIME"]))
		$arParams["CACHE_TIME"] = 3600;
	if($this->StartResultCache())
	{
		$arSelect = array(
			"ID",
			"TITLE",
			"ADDRESS",
			"DESCRIPTION",
			"GPS_N",
			"GPS_S",
			"IMAGE_ID",
			"PHONE",
			"SCHEDULE",
		);
		$dbProps = CCatalogStore::GetList(array('ID' => 'ASC'),array('ID' => $arResult['STORE'], 'ACTIVE' => 'Y'),false,false,$arSelect);
		$arResult = $dbProps->GetNext();
		if(!$arResult)
		{
			ShowError(GetMessage("STORE_NOT_EXIST"));
			$this->AbortResultCache();
		}
		if($arResult["GPS_N"] != '' && $arResult["GPS_S"] != '')
			$this->AbortResultCache();
		$arResult["MAP"] = $arParams["MAP_TYPE"];
		if(isset($arParams["PATH_TO_LISTSTORES"]))
		{
			$arResult["LIST_URL"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_LISTSTORES"]);
		}
		$this->IncludeComponentTemplate();
	}
}
else
	ShowError(GetMessage("STORE_NOT_EXIST"));

if($arParams["SET_TITLE"] == "Y")
{
	$title = ($arResult["TITLE"] != '')? $arResult["TITLE"]." (".$arResult["ADDRESS"].")" : $arResult["ADDRESS"];
	$APPLICATION->SetTitle($title);
}
?>