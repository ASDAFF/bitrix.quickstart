<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/include.php");

IncludeModuleLangFile(__FILE__);

//getting user rights for module
$module_id = "rarus.sms4b";

$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);

if($SMS_RIGHT < "R") 
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

global $SMS4B;

$APPLICATION->SetTitle(GetMessage("sms4b_index_title"));

if($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

//if something wrong, show message
if (!$SMS4B->LastError == '' && !$SMS4B->GetSOAP("AccountParams",array("SessionID" => $SMS4B->GetSID())) === true)
{	
	echo '<tr><td colspan="2">'.CAdminMessage::ShowMessage($SMS4B->LastError.GetMessage("M_OPTIONS")).'</td></tr>';
	return;
}

$adminPage->ShowSectionIndex("menu_sms4b", "rarus.sms4b"); 

if($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

?>
