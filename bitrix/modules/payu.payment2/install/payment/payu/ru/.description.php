<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $MESS;
$MESS['PEYU_PSTITLE'] = 'PayU';
$MESS["PEYU_MERCHANT"] = "Идентификатор мерчанта";
$MESS["PEYU_SECURE_KEY"] = "Секретный ключ";
$MESS["PEYU_LU_URL"] = "Ссылка перехода на страницу платежной системы";
$MESS["PEYU_DESC_LU_URL"] = "По умолчанию : https://secure.payu.ru/order/lu.php";
$MESS["PEYU_PRICE_CURRENCY"] = "Валюта платежа";
$MESS["PEYU_DESC_PRICE_CURRENCY"] = "<b style='color=#aeaeae;'>Внимание!</b> Это значение должно соответствовать валюте вашего мерчанта";
$MESS["PEYU_DEBUG_MODE"] = "Включить режим отладки";
$MESS["PEYU_DESC_DEBUG_MODE"] = "(1 - отладка включена, 0 - отладка выключена)";
$MESS["PEYU_BACK_REF"] = "Ссылка возврата клиента";
$MESS["PEYU_DESC_BACK_REF"] = "Ссылка, на которую вернется клиент после завершения платежа.<br>Если оставить поле пустым, данный параметр не будет учитываться";
$MESS["PEYU_LANGUAGE"] = "Язык страницы платежной системы";
$MESS["PEYU_DESC_LANGUAGE"] = "Например : RU";

$MESS["PEYU_USE_VAT"] = "Считать НДС при платеже";
$MESS["PEYU_USE_VAT_DESC"] = "Укажите НЕТ если НДС включен в стоимость товара или не используется.";
$MESS["PEYU_USE_VAT_TRUE"] = "Да";
$MESS["PEYU_USE_VAT_FALSE"] = "Нет";

$MESS["PEYU_VAT_RATE"] = "Процент НДС";
$MESS["PEYU_VAT_RATE_DESC"] = "Укажите процент который используется при подсчёте НДС. Настройка имеет смысл только если настройка 'Считать НДС при платеже' указана как 'НЕТ'";
$MESS["PEYU_VAT_19"] = "18%";
$MESS["PEYU_VAT_0"] = "0%";

$MESS["PEYU_IPN_LINK"] = "Сылка для IPN протокола";
$MESS["PEYU_IPN_LINK_DESC"] = "<b>Поле только для чтения. Полный путь до скрипта IPN.</b> Необходимо указать эту ссылку в Cpanel мерчанта.";

$MESS['PEYU_AUTOMODE'] = 'Пропуск первой страницы';
$MESS['PEYU_DESC_AUTOMODE'] = 'При передаче имени, фамилии, почты и телефона первый шаг платежной страницы будет пропущен. Это поможет увеличить конверсию';
$MESS['PEYU_AUTOMODE_YES'] = 'Да';
$MESS['PEYU_AUTOMODE_NO'] = 'Нет';

$MESS['PAYU_BILL_FNAME'] = 'Ваше имя:';
$MESS['PAYU_BILL_LNAME'] = 'Ваша фамилия:';
$MESS['PAYU_BILL_EMAIL'] = 'Ваш E-mail:';
$MESS['PAYU_BILL_PHONE'] = 'Ваш Телефон:';
$MESS['PAYU_BILL_COUNTRYCODE'] = '<span style="display:inline-block;max-width: 100px;vertical-align: middle;">Код страны в международном формате(RU,UK,KZ):</span>';
?>