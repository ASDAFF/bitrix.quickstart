<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if(!is_array($arParams["~SERVICES"]))
	$arParams["~SERVICES"] = array();

if(!is_array($arParams["~POST"]))
	$arParams["~POST"] = array();
	
if($arParams["POPUP"] === "Y" || $arParams["POPUP"] === true)
	$arParams["POPUP"] = true;
else
	$arParams["POPUP"] = false;

if(!isset($arParams["~SHOW_TITLES"]))
	$arParams["~SHOW_TITLES"] = 'Y';

if(!isset($arParams["~FOR_SPLIT"]))
	$arParams["~FOR_SPLIT"] = 'N';

if(!isset($arParams["~AUTH_LINE"]))
	$arParams["~AUTH_LINE"] = 'Y';

	$this->IncludeComponentTemplate();
?>