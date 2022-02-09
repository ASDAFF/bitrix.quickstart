<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("catalog"))
{
	ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALL"));
	return;
}
if (!CBXFeatures::IsFeatureEnabled('CatMultiStore'))
{
	ShowError(GetMessage("CAT_FEATURE_NOT_ALLOW"));
	return;
}
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 360;
$arResult["TITLE"] = GetMessage("SCS_DEFAULT_TITLE");
$arResult["MAP"] = $arParams["MAP_TYPE"];
if(!isset($arParams["PATH_TO_ELEMENT"]))
	$arParams["PATH_TO_ELEMENT"]="store/#store_id#";
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
	$dbStoreProps = CCatalogStore::GetList(array('TITLE' => 'ASC', 'ID' => 'ASC'),array("ACTIVE"=>"Y"),false,false,$arSelect);
	$arResult["PROFILES"] = Array();
	$viewMap = false;
	while ($arProp = $dbStoreProps->GetNext())
	{
		$url=CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_ELEMENT"], array("store_id" => $arProp["ID"]));

		if ($arProp["TITLE"]=='' && $arProp["ADDRESS"]!='')
			$storeName = $arProp["ADDRESS"];
		elseif ($arProp["ADDRESS"]=='' && $arProp["TITLE"]!='')
			$storeName = $arProp["TITLE"];
		else
			$storeName = $arProp["TITLE"]." (".$arProp["ADDRESS"].")";

		if ($arParams["PHONE"]=='Y' && $arProp["PHONE"]!='')
			$storePhone = $arProp["PHONE"];
		else
			$storePhone = null;
		if ($arParams["SCHEDULE"]=='Y' && $arProp["SCHEDULE"]!='')
			$storeSchedule = $arProp["SCHEDULE"];
		else
			$storeSchedule = null;
		if($arProp["GPS_N"] && $arProp["GPS_S"])
		{
			$viewMap=true;
			$this->AbortResultCache();
		}
		$arResult["STORES"][] = array(
			'ID' => $arProp["ID"],
			'TITLE' => $storeName,
			'PHONE' => $storePhone,
			'SCHEDULE' => $storeSchedule,
			'DETAIL_IMG' => $arParams["URL"],
			'GPS_N' => $arProp["GPS_N"],
			'GPS_S' => $arProp["GPS_S"],
			'ADDRESS' => $arProp["TITLE"],
			'URL' => $url,
		);
	}
	$arResult['VIEW_MAP'] = $viewMap;
	$this->IncludeComponentTemplate();
}
if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle($arParams["TITLE"]);
?>