<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ($this->StartResultCache($arParams['CACHE_TIME']))
{
	$this->IncludeComponentTemplate();
}

?>
