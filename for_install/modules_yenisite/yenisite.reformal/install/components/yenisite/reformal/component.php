<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ? intval($arParams['CACHE_TIME']) : 3600000;
if($this->StartResultCache(false, $USER->GetGroups()))
{   
	$this->IncludeComponentTemplate();
}
?>	

