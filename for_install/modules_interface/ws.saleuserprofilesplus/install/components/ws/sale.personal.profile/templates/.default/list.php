<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent(
	"ws:sale.personal.profile.list",
	"",
	array(
		"PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
		"PER_PAGE" => $arParams["PER_PAGE"],
		"SET_TITLE" =>$arParams["SET_TITLE"],
	),
	$component
);