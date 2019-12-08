<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?=$arResult["NAVIGATION"];?>

<?$APPLICATION->IncludeComponent(
	"bitrix:main.profile",
	"",
	Array(
		"USER_PROPERTY_NAME" => $arParams["USER_PROPERTY_NAME"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"AJAX_MODE" => $arParams["AJAX_MODE"],
		"USER_PROPERTY" => $arParams["USER_PROPERTY"],
		"SEND_INFO" => $arParams["SEND_INFO"],
		"CHECK_RIGHTS" => $arParams["CHECK_RIGHTS"],
		"AJAX_OPTION_JUMP" => $arParams["AJAX_OPTION_JUMP"],
		"AJAX_OPTION_STYLE" => $arParams["AJAX_OPTION_STYLE"],
		"AJAX_OPTION_HISTORY" => $arParams["AJAX_OPTION_HISTORY"],
	)
);?>