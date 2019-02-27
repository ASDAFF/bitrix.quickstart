<?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/.description.php"))
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/payment/payanyway/.description.php");

$psTitle = GetMessage("PAYANYWAY_PLASTIC_TITLE");
$psDescription = GetMessage("PAYANYWAY_DESC");
?>
