<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if($this->StartResultCache())
{
	
	$arResult = array();
	
	$this->IncludeComponentTemplate();
}
else
{
		$this->AbortResultCache();
}



?>