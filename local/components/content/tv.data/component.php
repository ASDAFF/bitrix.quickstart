<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ? intval($arParams['CACHE_TIME']) : 3600000;
if($this->StartResultCache(false, $USER->GetGroups()))
{
	$this->IncludeComponentTemplate();
}
?>