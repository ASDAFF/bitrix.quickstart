<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $MESS;

$sLink = "<a href=\"http://secure.onpay.ru/\" target=\"_blank\">Onpay.ru</a>";

$MESS ['ONPAY.SALE_PAYMENT_ONPAY__TITLE'] = "Onpay.ru (Яндекс.Деньги, WebMoney, VISA, MasterCard)";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__DESCRIPTION'] = "<a href=\"http://www.onpay.ru\" target=\"_blank\">http://www.onpay.ru</a>";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__LOGIN'] = "Логин в системе Onpay.ru";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__LOGIN_DESCR'] = "Ваше Имя пользователя в системе ".$sLink;
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__API_INKEY'] = "Ключ API IN";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__API_INKEY_DESCR'] = "Секретный ключ API IN указанный в личном кабинете системы ".$sLink;
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__API_OUTKEY'] = "Ключ API OUT";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__API_OUTKEY_DESCR'] = "Секретный ключ API OUT указанный в личном кабинете системы ".$sLink;
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__SUCCESS_URL'] = "Адрес при успешной оплате";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__SUCCESS_URL_DESCR'] = "URL скрипта (на веб-сайте продавца) обрабатывающего оповещения о результате платежа";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__FAIL_URL'] = "Адрес при ошибке оплаты";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__FAIL_URL_DESCR'] = "URL (на веб-сайте продавца) для перенаправления плательщика при неуспешном платеже";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__EMAIL'] = "Электронная почта";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__EMAIL_DESCR'] = "";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__SHOULD_PAY'] = "Сумма к оплате";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__SHOULD_PAY_DESCR'] = "";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__CURRENCY'] = "Валюта оплаты";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__CURRENCY_DESCR'] = "";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__ORDER_ID'] = "Номер заказа";
$MESS ['ONPAY.SALE_PAYMENT_ONPAY__ORDER_ID_DESCR'] = "Номер заказа в вашем Интернет-магазине";

?>