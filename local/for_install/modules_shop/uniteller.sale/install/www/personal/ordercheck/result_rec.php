<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');?>
<?
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/payment/uniteller.sale/result_rec.php')) {
	include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/payment/uniteller.sale/result_rec.php');
}
?>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
?>