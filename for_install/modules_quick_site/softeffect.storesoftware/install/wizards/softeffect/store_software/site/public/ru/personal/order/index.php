<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказы");
?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order",
	"list2",
	Array(
		"SEF_MODE" => "Y",
		"ORDERS_PER_PAGE" => "10",
		"PATH_TO_PAYMENT" => "#SITE_DIR#personal/order/payment/",
		"PATH_TO_BASKET" => "#SITE_DIR#basket/",
		"SET_TITLE" => "Y",
		"SAVE_IN_SESSION" => "N",
		"NAV_TEMPLATE" => "arrows",
		"PROP_1" => array("#fiz_town#"),
		"PROP_2" => array("#ur_location#"),
		"SEF_FOLDER" => "#SITE_DIR#personal/order/",
		"SEF_URL_TEMPLATES" => Array(
			"list" => "index.php",
			"detail" => "detail/#ID#/",
			"cancel" => "cancel/#ID#/"
		),
		"VARIABLE_ALIASES" => Array(
			"list" => Array(),
			"detail" => Array(),
			"cancel" => Array(),
		)
	)
);?> 
<div> </div>
 
<div></div>
 
<br />

<br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>