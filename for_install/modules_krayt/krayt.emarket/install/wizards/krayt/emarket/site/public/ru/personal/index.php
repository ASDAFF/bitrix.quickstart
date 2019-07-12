<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
<div><?$APPLICATION->IncludeComponent(
	"krayt:personal.area", 
	".default", 
	array(
		"ORDERS" => "Y",
		"USER_ADDRESES" => "Y",
		"LOGOUT" => "Y",
		"USER_PROPERTY" => array(
		),
		"SEND_INFO" => "N",
		"CHECK_RIGHTS" => "N",
		"PATH_TO_COPY" => "",
		"PATH_TO_CANCEL" => "",
		"PATH_TO_BASKET" => "",
		"ORDERS_PER_PAGE" => "20",
		"SAVE_IN_SESSION" => "Y",
		"NAV_TEMPLATE" => "",
		"PATH_TO_PAYMENT" => "payment.php",
		"PER_PAGE_ADR" => "20",
		"USE_AJAX_LOCATIONS" => "N",
		"SET_TITLE" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"VIEWED" => "Y"
	),
	false
);?></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>