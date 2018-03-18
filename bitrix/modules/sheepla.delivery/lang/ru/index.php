<?php
if(LANG_CHARSET){ header('Content-Type: text/html; charset='.LANG_CHARSET); }

global $MESS;
$MESS ['SHEEPLA_NAME'] = "Sheepla";
$MESS ['SHEEPLA_DESCRIPTION'] = "Модуль доставки несколькими службами";
$MESS ['SHEEPLA_DESCRIPTION_INNER'] = "Модуль доставки несколькими службами";
$MESS ['SHEEPLA_General'] = "Модуль доставки несколькими службами";
$MESS ['SHEEPLA_Carriers'] = "Службы доставки";
$MESS ['SHEEPLA_Carrier_info'] = "Информация о службе доставки";
$MESS ['SHEEPLA_API_KEY'] = "Ключ API";
$MESS ['SHEEPLA_CARRIER_NAME'] = "Название службы доставки";
$MESS ['SHEEPLA_CARRIER_COMMENT'] = "Комментарий";
$MESS ['SHEEPLA_SHEEPLA_TEMPLATE'] = "Шаблон доставки Sheepla";
$MESS ['SHEEPLA_ALLOWED_PAYMENT_TYPES'] = "Доступные способы оплаты";
$MESS ['SHEEPLA_DELIVERY_COST'] = "Стоимость доставки";
$MESS ['SHEEPLA_DELIVERY_SORT'] = "Сортировка";
$MESS ['SHEEPLA_SHEEPLA'] = "Sheepla";
$MESS ['SHEEPLA_EDIT'] = "Редактировать";
$MESS ['SHEEPLA_DELETE'] = "Удалить";
$MESS ['SHEEPLA_ADD_NEW_CARIER'] = "Добавить новую службу доставки";
$MESS ['SHEEPLA_E_API'] = "Введенные настройки неверны";
$MESS ['SHEEPLA_E_NAME'] = "Задайте название службы доставки";
$MESS ['SHEEPLA_E_TEMPLATE'] = "Выберите шаблон доставки";
$MESS ['SHEEPLA_I_COST'] = "Стоимость доставки по умолчанию задана равной 0";
$MESS ['SHEEPLA_E_PAYMENT'] = "Выберите способ оплаты";
$MESS ['SHEEPLA_I_CARRIERADDED'] = "Список служб доставки успешно изменен";
$MESS ['SHEEPLA_E_CARRIERADDED'] = "Не удалось изменить список служб доставки";
$MESS ['SHEEPLA_CONFIG'] = "Настройки";
$MESS ['SHEEPLA_DELIVERIS'] = "Доставки Sheepla";
$MESS ['SHEEPLA_E_ACTIVE'] = "Модуль управления доставкой не активен, пожалуйста, активируйте модуль в Магазин > Настройки магазина > Службы доставки > Автоматизированные.";
$MESS ['SHEEPLA_E_CONF'] = "Ошибка в прикрепленном файле, пожалуйста, проверьте файл";
$MESS ['SHEEPLA_E_CONF2'] = " or contact the software distributor Sheepla.";
$MESS ['SHEEPLA_DEFAULT_COUNTRY'] = 'Установка параметров страны';
$MESS ['SHEEPLA_ABOUT_COUNTRY_DEFUALT'] = 'Выбранная ниже страна по умолчанию будет использоваться в случае отсутствия поля выбора страны в форме заказа на сайте интернет-магазина. Для корректной работы модуля убедитесь, что страна, выбранная в выпадающем списке модуля Sheepla и английское наименование страны, введенное в Параметрах местоположения (Магазин > Настройки магазина > Местоположения > Список местоположений) совпадают.';
$MESS ['SHEEPLA_ADMIN_KEY'] = 'Ключ администратора';
$MESS ['SHEEPLA_PUBLIC_KEY'] = 'Публичный ключ';
$MESS ['SHEEPLA_CONTACT_MESSAGE_START'] = 'Перед началом конфигурации модуля настоятельно рекомендуем связаться с командой Sheepla для получения консультации.';
$MESS ['SHEEPLA_CONTACT_MESSAGE_PHONES'] = 'Тел.: +7(926)906-84-45, +7(495)775-87-28';
$MESS ['SHEEPLA_CONTACT_MESSAGE_EMAIL'] = 'E-mail: <a href="mailto:contact@sheepla.ru">contact@sheepla.ru</a>';
$MESS ['SHEEPLA_CONTACT_MESSAGE_END'] = 'Помните, что для работы модуля необходимо зарегистрироваться на сайте <a href="http://www.sheepla.ru">www.sheepla.ru</a>.';
$MESS ['SHEEPLA_CONFIG_URLS'] = 'Настройки путей';
$MESS ['SHEEPLA_CONFIG_URLS_FRONTEND'] = 'Путь загрузки виджетов';
$MESS ['SHEEPLA_CONFIG_URLS_COMMENT'] = 'По указанному ниже пути будут загружаться виджеты Sheepla. Если значение не установлено, то виджет будет загружаться на каждой странице сайта, что может повлиять на скорость загрузки страниц. Поэтому настоятельно рекомендуем указать путь к странице оформления заказа, например &laquo;/personal/order/make/&raquo;.';
$MESS ['SHEEPLA_PRICE'] = 'Оценочная стоимость - ';
?>