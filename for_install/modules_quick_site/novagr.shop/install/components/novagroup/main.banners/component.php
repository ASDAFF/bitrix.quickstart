<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();


if( CModule::IncludeModule("iblock") ) {
} else {
	die("IBLOCK MODULE NOT INSTALLED");
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

// Если нет валидного кеша (то есть нужно запросить данные и сделать валидный кеш)
if ($this->StartResultCache(false))
{
    $novaGroupBanners = new Novagroup_Classes_General_Banners(NOVAGROUP_MODULE_ID);
    $arResult = $novaGroupBanners->getActive();
	$this -> IncludeComponentTemplate();
}


?>