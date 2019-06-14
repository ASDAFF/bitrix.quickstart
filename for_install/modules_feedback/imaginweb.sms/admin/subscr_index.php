<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("imaginweb.sms");
if($POST_RIGHT <= "D"){
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
$APPLICATION->SetTitle(GetMessage("subscr_index_title"));
if($_REQUEST["mode"] == "list"){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
}
else{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
}
$adminPage->ShowSectionIndex("menu_imaginweb.sms", "imaginweb.sms");
if($_REQUEST["mode"] == "list"){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
}
else{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}
?>
