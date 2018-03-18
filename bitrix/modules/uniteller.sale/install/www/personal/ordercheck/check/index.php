<?require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');?>
<?
// если это версия для печати, то стандартный header не нужен.
if ($_GET['print'] == 'Y') {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo LANG_CHARSET?>">
<META NAME="ROBOTS" content="ALL">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#FFFFFF" link="#2974B2" alink="#3A8DD1" vlink="#7E0143"><?
} else {
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_after.php');
}

// от системы Uniteller запрос приходит с параметром Order_ID, а от интернет магазина с параметром ID, и дальше все обращения идут по $ID.
if (isset($_GET['Order_ID'])) {
	$ID = (int)$_GET['Order_ID'];
} elseif (isset($_GET['ID'])) {
	$ID = (int)$_GET['ID'];
}

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
	'bitrix:sale.personal.ordercheck.check',
	'',
	$arDetParams,
	$component
);

// если это версия для печати, то стандартный footer не нужен.
if ($_GET['print'] == 'Y') {
?></body></html><?php
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
} else {
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
}
?>