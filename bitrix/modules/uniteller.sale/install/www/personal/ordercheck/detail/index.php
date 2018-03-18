<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
?>
<?
/**
 * Подключает компонент bitrix:sale.personal.ordercheck.detail сделанный по аналогии со стандартным компонентом bitrix:sale.personal.order.detail
 * Доступен по URL: http://домен_магазина/personal/ordercheck/detail/
 * @author r.smoliarenko
 * @author r.sarazhyn
 */

if (!class_exists('ps_uniteller') && file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/payment/uniteller.sale/tools.php')) {
	include($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/payment/uniteller.sale/tools.php');
}

$arDetParams = array(
		'PATH_TO_LIST' => '/personal/ordercheck/',
		'PATH_TO_CANCEL' => '/personal/ordercheck/cancel/index.php?ID=' . $ID,
		'PATH_TO_PAYMENT' => ps_uniteller::UNITELLER_SALE_PATH . '/payment.php',
		'SET_TITLE' => 'Y',
		'ID' => $ID,
	);
foreach ($arParams as $key => $val) {
	if (strpos($key, 'PROP_') !== false) {
		$arDetParams[$key] = $val;
	}
}
$APPLICATION->IncludeComponent(
	'bitrix:sale.personal.ordercheck.detail',
	'',
	$arDetParams,
	$component
);
?>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>