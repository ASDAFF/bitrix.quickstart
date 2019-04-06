<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/rarusspb.onlinedengi/payment/onlinedengi_payment/payment.php')) {
?><?
	include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/rarusspb.onlinedengi/payment/onlinedengi_payment/payment.php');

}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>