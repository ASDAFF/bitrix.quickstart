<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if($APPLICATION->GetGroupRight("imaginweb.sms") < "R") 
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	
$gate = (isset($_REQUEST['gate']) && strlen($_REQUEST['gate']) > 0)?htmlspecialchars($_REQUEST['gate']):false;
if(IsModuleInstalled("imaginweb.sms") && $gate) {
	require_once dirname(__FILE__).'/../classes/iweb/Sender.php';
	echo CIWebSMS::GetCreditBalance(array('GATE' => $gate));
	#die();
}
?>