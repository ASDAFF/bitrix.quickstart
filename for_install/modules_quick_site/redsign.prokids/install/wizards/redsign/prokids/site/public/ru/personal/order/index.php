<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Заказы");
$APPLICATION->SetTitle("Заказы");
?>

<div class="pmenu">
<?$APPLICATION->IncludeComponent("bitrix:menu", "personal", array(
	"ROOT_MENU_TYPE" => "personal",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N",
	"SEPARATORS_PLACE" => array(
		0 => "2",
		1 => "5",
		2 => "",
	)
	),
	false
);?>
</div>

<div class="pcontent">
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order", 
	"gopro", 
	array(
		"PROP_1" => array(
		),
		"PROP_2" => array(
		),
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SEF_MODE" => "N",
		"SEF_FOLDER" => "#SITE_DIR#personal/order/",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"ORDERS_PER_PAGE" => "20",
		"PATH_TO_PAYMENT" => "payment.php",
		"PATH_TO_BASKET" => "basket.php",
		"SET_TITLE" => "Y",
		"SAVE_IN_SESSION" => "Y",
		"NAV_TEMPLATE" => "",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"HISTORIC_STATUSES" => array(
		),
		"STATUS_COLOR_N" => "green",
		"STATUS_COLOR_F" => "gray",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red"
	),
	false
);?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>