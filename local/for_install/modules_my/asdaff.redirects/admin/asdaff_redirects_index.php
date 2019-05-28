<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

$sModuleId = "asdaff.redirects";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $sModuleId . "/include.php");
IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/config.php");
CModule::IncludeModule($sModuleId);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($sModuleId);

if ($MODULE_RIGHT < "R")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$APPLICATION->SetTitle(GetMessage("SEO2_REDIRECT_INDEX_TITLE"));
if($_REQUEST["mode"] == "list")
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
else
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if(defined("SEO2_REDIRECT_ACCESS_DENIED")) $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/check_activate.php");
$adminPage->ShowSectionIndex("seo2_redirect_main", $sModuleId);

if($_REQUEST["mode"] == "list")
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>