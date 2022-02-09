<?   
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
   
 
    $APPLICATION->SetTitle("Цифровой мир. Главная страница");
?><?

$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket",
	"basket_cm_order",
	Array(
	)
);?> 
<div><?$APPLICATION->IncludeComponent("cm:sale.order.full", "", array(
	"ALLOW_PAY_FROM_ACCOUNT" => "Y",
	"SHOW_MENU" => "Y", 
	"CITY_OUT_LOCATION" => "N",
	"COUNT_DELIVERY_TAX" => "N",
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
	"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
	"SEND_NEW_USER_NOTIFY" => "Y",
	"DELIVERY_NO_SESSION" => "N",
	"PROP_1" => array(
		0 => "2",
	),
	"PROP_2" => array(
	),
	"PATH_TO_BASKET" => "/basket/", 
	"PATH_TO_PERSONAL" => "index.php",
	"PATH_TO_AUTH" => "/auth.php",
	"PATH_TO_PAYMENT" => "payment.php",
	"USE_AJAX_LOCATIONS" => "N",
	"SHOW_AJAX_DELIVERY_LINK" => "Y",
	"SET_TITLE" => "Y",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRICE_VAT_SHOW_VALUE" => "Y"
	),
	false
);?>  </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>