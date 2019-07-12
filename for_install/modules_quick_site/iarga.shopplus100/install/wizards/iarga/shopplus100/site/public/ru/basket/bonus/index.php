<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказ оформлен и оплачен бонусами");
if($_REQUEST['ORDER_ID']<1 && !$USER->IsAdmin()) LocalRedirect('/basket/');
?> 
<h1>Заказ оформлен и оплачен со счёта покупателя</h1>

<div>Номер вашего заказа <?=$_REQUEST['ORDER_ID']?>.</div>

<div>Состав заказа отправлен вам на E-mail.</div>

<div>Наши менеджеры свяжутся с вами в ближайшее время.</div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>