<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!isset($arParams["CACHE_TIME"])) {
	$arParams["CACHE_TIME"] = 3600;
}
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
GLOBAL $USER;
$user_id = $USER->GetId();
if($this->StartResultCache(false, array($arNavigation, $user_id)))
{
	if(!CModule::IncludeModule("iblock")) {
		$this->AbortResultCache();
		ShowError("IBLOCK_MODULE_NOT_INSTALLED");
		return false;
	}
	if($user_id){
		$arSort= array("SORT" => "ASC", "DATE_ACTIVE_FROM" => "DESC", "ID" => "DESC");
		$arFilter =  array('IBLOCK_ID'=>2, 'GLOBAL_ACTIVE'=>'Y', "CREATED_BY" => $user_id);
		$arSelect = array("ID", "NAME", "SECTION_PAGE_URL");
		$rsElement = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, $arSelect);
		if ($arParams["DETAIL_URL"]) {
			$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);
		}
		while($obElement = $rsElement->GetNextElement()) {
			$arElement = $obElement->GetFields();
			if ($arElement["PREVIEW_PICTURE"]) {
				$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
			} 
			unset(
				$arElement["~ID"],
				$arElement["~NAME"],
				$arElement["~SECTION_PAGE_URL"],
				$arElement["~CODE"],
				$arElement["~EXTERNAL_ID"],
				$arElement["~IBLOCK_TYPE_ID"],
				$arElement["~IBLOCK_ID"],
				$arElement["~IBLOCK_EXTERNAL_ID"],
				$arElement["~IBLOCK_CODE"],
				$arElement["~GLOBAL_ACTIVE"],
				$arElement["~ELEMENT_CNT"],
				$arElement["EXTERNAL_ID"],
				$arElement["IBLOCK_TYPE_ID"],
				$arElement["IBLOCK_ID"],
				$arElement["IBLOCK_CODE"],
				$arElement["IBLOCK_EXTERNAL_ID"],
				$arElement["GLOBAL_ACTIVE"],
				$arElement["CODE"]
				);
			$arResult[] = $arElement;
		}
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
	}
	$this->IncludeComponentTemplate();
}
?>