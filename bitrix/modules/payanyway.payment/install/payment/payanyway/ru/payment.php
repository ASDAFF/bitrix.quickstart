<?php
global $MESS;

$MESS['PAYANYWAY_TITLE']			= 'PayAnyWay.ru';
$MESS['PAYANYWAY_DESC']				= '<br>Оплата через платёжную систему <a href="http://payanyway.ru">PayAnyWay.ru</a>.<br>Для получения результата оплаты необходимо создать специальную страницу и разместить на ней компонент bitrix:sale.order.payment.receive с соответствующими параметрами.<br/>Указажите этот адрес в настройках Вашего счёта в системе PayAnyWay.ru («Pay URL»).';
$MESS['PAYANYWAY_SERVER']			= 'URL платежной системы<br/><small>demo.moneta.ru - для демо-аккаунта<br>www.payanyway.ru - для реального аккаунта в PayAnyWay</small>';
$MESS['PAYANYWAY_ID']				= 'Номер счета<br/><small>номер расширенного счета в платежной системе PayAnyWay (Moneta.ru)</small>';
$MESS['PAYANYWAY_AMOUNT']			= 'Сумма заказа';
$MESS['DATA_INTEGRITY_CODE']		= 'Код проверки целостности данных<br/><small>указан в настройках расширенного счета</small>';
$MESS['PAYANYWAY_TEST_MODE']		= 'Тестовый режим<br/><small>переход в режим тестирования, деньги не списываются со счета</small>';
$MESS['PAYANYWAY_TEST_MODE_TRUE']	= 'Да';
$MESS['PAYANYWAY_TEST_MODE_FALSE']	= 'Нет';
$MESS['PAYANYWAY_LOGIN']			= 'Логин в PayAnyWay';
$MESS['PAYANYWAY_PASSWORD']			= 'Пароль PayAnyWay';
$MESS['PAYANYWAY_PAY_URL']			= 'Pay URL';
$MESS['PAYANYWAY_PAY_URL_DESC']		= 'URL страницы подтверждения оплаты';
$MESS["PAYANYWAY_CHANGE_ORDER_STATUS"]		= "Автоматически менять статус заказа на 'Оплачен' при подтверждении оплаты.";
$MESS["PAYANYWAY_CHANGE_ORDER_STATUS_DESC"] = "Y - менять, N - не менять.";

$MESS['PAYMENT_PAYANYWAY_TITLE']	= 'Оплата через платёжную систему <b>PayAnyWay.ru</b>';
$MESS['PAYMENT_PAYANYWAY_ORDER']	= 'Заказ №';
$MESS['PAYMENT_PAYANYWAY_TO_PAY']	= 'Сумма к оплате:';
$MESS['PAYMENT_PAYANYWAY_BUTTON']	= 'Оплатить';
$MESS['PAYANYWAY_EXTRA_PARAMS_OK']	= 'Продолжить';
$MESS['PAYANYWAY_PAYMENT_CONFIRMED'] = 'Уведомление получено';


//$MESS['PAYANYWAY_PAYMENT_TYPE'] = 'Способ оплаты';
//$MESS['PAYANYWAY_DEPENDENCIES_ERROR'] = '<br/><small>Для использования всех функций модуля необходимо установить расширения SOAP и libxml</small>';
//$MESS['PAYANYWAY_BANKTRANSFER'] = 'Банковский перевод';
//$MESS['PAYANYWAY_CIBERPAY'] = 'Ciberpay';
//$MESS['PAYANYWAY_COMEPAY'] = 'Comepay';
//$MESS['PAYANYWAY_CONTACT'] = 'Contact';
//$MESS['PAYANYWAY_ELECSNET'] = 'Элекснет';
//$MESS['PAYANYWAY_EUROSET'] = 'Евросеть, Связной';
//$MESS['PAYANYWAY_FORWARD'] = 'Форвард Мобайл';
//$MESS['PAYANYWAY_GOROD'] = 'Федеральная Система ГОРОД';
//$MESS['PAYANYWAY_MCB'] = 'Московский Кредитный Банк';
//$MESS['PAYANYWAY_MONETA'] = 'Монета.ру';
//$MESS['PAYANYWAY_MONEYMAIL'] = 'MoneyMail';
//$MESS['PAYANYWAY_NOVOPLAT'] = 'NovoPlat';
//$MESS['PAYANYWAY_PLASTIC'] = 'VISA, MasterCard';
//$MESS['PAYANYWAY_PLATIKA'] = 'Платика';
//$MESS['PAYANYWAY_POST'] = 'Отделения "Почта России"';
//$MESS['PAYANYWAY_WEBMONEY'] = 'WebMoney';
//$MESS['PAYANYWAY_YANDEX'] = 'Яндекс.Деньги';


?>
