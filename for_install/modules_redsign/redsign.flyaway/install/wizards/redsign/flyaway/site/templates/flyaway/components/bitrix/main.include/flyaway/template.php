<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if($arResult["FILE"] <> '')
	include($arResult["FILE"]);
