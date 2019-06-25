<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
$APPLICATION->IncludeComponent(
	"intec:startshop.basket",
	".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"CURRENCY" => "",
		"REQUEST_VARIABLE_ACTION" => "action",
		"REQUEST_VARIABLE_ITEM" => "item",
		"REQUEST_VARIABLE_QUANTITY" => "quantity",
		"REQUEST_VARIABLE_PAGE" => "page",
		"URL_BASKET_EMPTY" => "",
		"USE_ITEMS_PICTURES" => "Y",
		"USE_BUTTON_CLEAR" => "Y",
		"USE_BUTTON_BASKET" => "Y",
		"USE_SUM_FIELD" => "Y",
		"TITLE_BASKET" => "Корзина",
		"TITLE_ORDER" => "Оформление заказа",
		"TITLE_PAYMENT" => "Оплата",
		"URL_ORDER_CREATED" => "#PERSONAL_PATH#orders/?ORDER_ID=#ID#",
		"USE_ADAPTABILITY" => "Y",
		"REQUEST_VARIABLE_PAYMENT" => "payment",
		"REQUEST_VARIABLE_VALUE_RESULT" => "result",
		"REQUEST_VARIABLE_VALUE_SUCCESS" => "success",
		"REQUEST_VARIABLE_VALUE_FAIL" => "fail",
		"URL_ORDER_CREATED_TO_USER" => "#PERSONAL_PATH#"
	),
	false
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
