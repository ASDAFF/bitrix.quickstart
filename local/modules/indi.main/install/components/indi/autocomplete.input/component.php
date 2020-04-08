<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!CModule::IncludeModule("search")) {
	throw new Exception("Модуль 'Поиск' не установлен.");
}

$arResult["ID"] = GenerateUniqId($arParams["NAME"]);

$this->IncludeComponentTemplate();