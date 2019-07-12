<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказ оформлен");
$urls = explode("/",$_SERVER['REQUEST_URI']);
$_REQUEST['ORDER_ID'] = $urls[(sizeof($urls)-2)];
if($_REQUEST['ORDER_ID']<1 && !$USER->IsAdmin()) LocalRedirect('/auth/orders/');
?>
<h1>Спасибо за заказ на нашем сайте</h1>

<div>Номер вашего заказа <a href="/auth/orders/"><?=$_REQUEST['ORDER_ID']?></a>.</div>

<div>Состав заказа отправлен вам на E-mail.</div>

<div>Наши менеджеры свяжутся с вами в ближайшее время.</div>
<div>При необходимости произведите оплату заказа выбранным способом.</div>

<?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.payment",
	"",
Array(),
false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>