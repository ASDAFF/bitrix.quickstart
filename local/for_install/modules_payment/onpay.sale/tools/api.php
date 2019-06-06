<?
error_reporting(E_ERROR | E_PARSE);

define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

if($_GET["admin_section"]=="Y")
	define("ADMIN_SECTION", true);
else
	define("BX_PUBLIC_TOOLS", true);

if(!require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php")) die('prolog_before.php not found!');

if(CModule::IncludeModule("onpay.sale")) {
	$rqst = COnpayPaymentV2::GetData();
	if($rqst['type'] == 'check') {
		COnpayPaymentV2::CheckAction($rqst);
	} elseif($rqst['type'] == 'pay') {
		COnpayPaymentV2::PayAction($rqst);
	} elseif($_REQUEST['type'] == 'check') {
		COnpayPayment::CheckAction($_REQUEST);
	} elseif($_REQUEST['type'] == 'pay') {
		COnpayPayment::PayAction($_POST);
	} else {
		COnpayPayment::SaveLog(array_merge($_REQUEST, array('INPUT.REQUEST' => $rqst)));
	}
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>