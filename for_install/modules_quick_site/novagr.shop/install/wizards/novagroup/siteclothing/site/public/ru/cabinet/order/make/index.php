<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказы");

$PROPS = array();

$basket = new Novagroup_Classes_General_Basket();
$address = $basket->getOrderPropertyByCode('ADDRESS');
$zip = $basket->getOrderPropertyByCode('ZIP');
//$location = $basket->getOrderPropertyByCode('LOCATION');
$checkDelivery = $basket->checkCurrentDeliveryByName("Самовывоз", $_POST);

if (is_array($address[0]) and $checkDelivery === true) {
    foreach ($address as $item) {
        $PROPS["PROP_" . $item['PERSON_TYPE_ID']][] =  $item['ID'];
    }
}
if (is_array($zip[0]) and $checkDelivery === true) {
    foreach ($zip as $item) {
        $PROPS["PROP_" . $item['PERSON_TYPE_ID']][] =  $item['ID'];
    }
}
if (is_array($location[0]) and $checkDelivery === true) {
    foreach ($location as $item) {
        $PROPS["PROP_" . $item['PERSON_TYPE_ID']][] =  $item['ID'];
    }
}

$PATH_TO_BASKET = (isset($_REQUEST['is_ajax_post']) and $_REQUEST['is_ajax_post']=='Y') ? "/cabinet/cart/?CAJAX=1" : "/cabinet/cart/";
?><?$APPLICATION->IncludeComponent("bitrix:sale.order.ajax", "demoshop", array(
	"PAY_FROM_ACCOUNT" => "Y",
	"COUNT_DELIVERY_TAX" => "N",
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
	"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
	"ALLOW_AUTO_REGISTER" => "Y",
	"SEND_NEW_USER_NOTIFY" => "Y",
	"DELIVERY_NO_AJAX" => "N",
	"DELIVERY_NO_SESSION" => "N",
	"TEMPLATE_LOCATION" => ".default",
	"DELIVERY_TO_PAYSYSTEM" => "d2p",
	"USE_PREPAYMENT" => "N",
	"PATH_TO_BASKET" => $PATH_TO_BASKET,
	"PATH_TO_PERSONAL" => "#SITE_DIR#cabinet/orders/",
	"PATH_TO_PAYMENT" => "#SITE_DIR#cabinet/order/payment/",
	"PATH_TO_AUTH" => "#SITE_DIR#auth/",
	"SET_TITLE" => "Y",
	"PATH_TO_ORDER" => "#SITE_DIR#cabinet/order/make/"
	) + $PROPS,
	false
);?>
	

<?
$APPLICATION->AddChainItem("Мои заказы", "#SITE_DIR#cabinet/orders/");
$APPLICATION->AddChainItem("Оформить заказ");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>