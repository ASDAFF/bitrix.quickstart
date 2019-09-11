<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams["FORM_NAME"] = is_array($arParams["FORM_NAME"])? $arParams["FORM_NAME"]: array();
$arParams["USE_GLOBAL"] = ($arParams["USE_GLOBAL"] == "Y")? true : false;
$arParams["IMAGE_DIALOG"] = (file_exists($_SERVER["DOCUMENT_ROOT"].$arParams["IMAGE_DIALOG"]))?$arParams["IMAGE_DIALOG"]:$this->GetPath()."/templates/.default/images/reload.png";

$this->IncludeComponentTemplate();
?>
