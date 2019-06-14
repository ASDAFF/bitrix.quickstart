<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");

$moduleID = 'step2use.redirects';

if(!CModule::IncludeModule($moduleID)) die('no module '.$moduleID);

// lang
IncludeModuleLangFile(__FILE__);

// check access
/*if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));*/

// is admin
$isAdmin = S2uRedirects::canAdminThisModule() || $USER->CanDoOperation('edit_php');
if(!$isAdmin) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

//--------PREPARE THE FORM DATA.
// browser's title
$APPLICATION->SetTitle(GetMessage("ATL_TO_BITRIX_TITLE"));

// indlude admin core
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("ATL_TO_BITRIX_TITLE"), "ICON" => ""),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>

<? echo BeginNote(); ?>
<? echo GetMessage("ATL_TO_BITRIX_HELP"); ?>
<? echo EndNote(); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
