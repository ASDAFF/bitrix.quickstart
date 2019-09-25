<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

error_reporting(0);
header('Content-Type: text/html; charset='.SITE_CHARSET);

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	
	$APPLICATION->IncludeComponent(
		"krayt:sale.basket.basket.small",
		"",
		Array(
			"PATH_TO_BASKET" => SITE_DIR."personal/basket/",
			"PATH_TO_ORDER" => SITE_DIR."personal/order/",
			"SHOW_DELAY" => "N",
			"SHOW_NOTAVAIL" => "N",
			"SHOW_SUBSCRIBE" => "N"
		),
	false
	);
}
?>
