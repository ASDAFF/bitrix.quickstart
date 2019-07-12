<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (isset($arParams["USE_ARCHIVE"]) && $arParams["USE_ARCHIVE"] == "Y") {
	$arParams["FILTER_NAME"] = trim($arParams["FILTER_NAME"]);
	if ($arParams["FILTER_NAME"] === '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
		$arParams["FILTER_NAME"] = "arrFilter";
	}
} else {
	$arParams["FILTER_NAME"] = "";
}
