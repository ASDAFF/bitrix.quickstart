<?
$sModuleId = "step2use.redirects";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $sModuleId . "/include.php");
IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/config.php");
CModule::IncludeModule($sModuleId);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($sModuleId);

if ($MODULE_RIGHT < "R")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$APPLICATION->SetTitle(GetMessage("S2U_REDIRECT_INDEX_TITLE"));
if($_REQUEST["mode"] == "list")
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
else {
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
    echo S2uRedirects::getLicenseRenewalBanner();
}

//if(defined("S2U_REDIRECT_ACCESS_DENIED")) $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
//require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/check_activate.php");
//$adminPage->ShowSectionIndex("s2u_redirect_main", $sModuleId);

set_time_limit(0);

$deactivatedID = array();
$redirects = S2uRedirectsRulesDB::GetList(array("ACTIVE"=>"Y"), array("DATE_TIME_CREATE"=>"DESC"));
foreach($redirects as $redirect) {
    //var_dump($redirect);exit;
//    $redirect["SITE_ID"]

    if(in_array($redirect["ID"], $deactivatedID)) continue;

    while($reverse = S2uRedirectsRulesDB::FindRedirect($redirect["NEW_LINK"], $redirect["SITE_ID"])) {
    //if($reverse) {
        $reverse["ACTIVE"] = "N";
        $reverse["COMMENT"] .= "\nDeactivated by antireverse (".date("d.m.Y H:i:s").")";
        S2uRedirectsRulesDB::Update($reverse["ID"], $reverse);
        $deactivatedID[] = $reverse["ID"];
    }
}

echo "Исправление конфликтов завершено";

if($_REQUEST["mode"] == "list")
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
