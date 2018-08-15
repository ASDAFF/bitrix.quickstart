<?php
/**
 * Массив языковых констант.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
global $MESS;

// Для админки.
$MESS['SALE_UNITELLER_DESCRIPTION'] = '<a href=\'http://www.uniteller.ru\' target=\'_blank\'>http://www.uniteller.ru</a>';
$MESS['SALE_UNITELLER_DESC'] = 'Описание платежной системы Uniteller';
$MESS['SALE_UNITELLER_SHOP_IDP'] = 'Код магазина';
$MESS['SALE_UNITELLER_SHOP_IDP_DESC'] = 'Код магазина, полученный от Uniteller';
$MESS['SALE_UNITELLER_SHOP_LOGIN'] = 'Логин';
$MESS['SALE_UNITELLER_SHOP_LOGIN_DESC'] = 'Логин, полученный от Uniteller';
$MESS['SALE_UNITELLER_SHOP_PASSWORD'] = 'Пароль';
$MESS['SALE_UNITELLER_SHOP_PASSWORD_DESC'] = 'Пароль, полученный от Uniteller';
$MESS['SALE_UNITELLER_SITE_NAME_LAT'] = 'Латинское наименование точки приема, присвоенное Uniteller';
$MESS['SALE_UNITELLER_LIFE_TIME'] = 'Время жизни формы оплаты в секундах';
$MESS['SALE_UNITELLER_LIFE_TIME_DESC'] = 'Должно быть целым положительным числом. Если покупатель проведет на форме дольше, чем указанное время, то форма оплаты будет считаться устаревшей, и платеж не будет принят. Покупателю в таком случае будет предложено вернуться на сайт.';
$MESS['SALE_UNITELLER_TIME_PAID_CHANGE'] = 'Время, в течение которого статус "paid" может быть отменён';
$MESS['SALE_UNITELLER_TIME_PAID_CHANGE_DESC'] = 'Время считается в днях (по умолчанию 14 дней)';
$MESS['SALE_UNITELLER_TIME_ORDER_SYNC'] = 'Время, в течение которого будет выполняться синхронизация статусов платежей с заказами';
$MESS['SALE_UNITELLER_TIME_ORDER_SYNC_DESC'] = 'Время считается в днях (по умолчанию 30 дней)';
$MESS['SALE_UNITELLER_SUCCESS_URL'] = 'Адрес при успешной оплате (URL_RETURN_OK)';
$MESS['SALE_UNITELLER_SUCCESS_URL_DESC'] = 'URL страницы, на которую должен вернуться покупатель после успешного осуществления платежа в системе Uniteller (максимум 128 символов)';
$MESS['SALE_UNITELLER_FAIL_URL'] = 'Адрес при ошибке оплаты (URL_RETURN_NO)';
$MESS['SALE_UNITELLER_FAIL_URL_DESC'] = 'URL страницы, на которую должен вернуться Покупатель после неуспешного осуществления платежа в системе Uniteller (максимум 128 символов)';
$MESS['SALE_UNITELLER_TESTMODE'] = 'Тестовый режим';
$MESS['SALE_UNITELLER_TESTMODE_DESC'] = 'Если пустое значение - магазин будет работать в обычном режиме';
$MESS['SALE_UNITELLER_ORDER_ID'] = 'Номер заказа';
$MESS['SALE_UNITELLER_ORDER_ID_DESC'] = 'Номер заказа в Вашем Интернет-магазине';
$MESS['SALE_UNITELLER_EMAIL'] = 'E-mail покупателя';
$MESS['SALE_UNITELLER_EMAIL_DESC'] = 'Максимум 64 символа';
$MESS['SALE_UNITELLER_FIRST_NAME'] = 'Имя покупателя';
$MESS['SALE_UNITELLER_FIRST_NAME_DESC'] = 'Максимум 64 символа';
$MESS['SALE_UNITELLER_MIDDLE_NAME'] = 'Отчество покупателя';
$MESS['SALE_UNITELLER_MIDDLE_NAME_DESC'] = 'Максимум 64 символа';
$MESS['SALE_UNITELLER_LAST_NAME'] = 'Фамилия покупателя';
$MESS['SALE_UNITELLER_LAST_NAME_DESC'] = 'Максимум 64 символа';
$MESS['SALE_UNITELLER_ADDRESS'] = 'Адрес покупателя';
$MESS['SALE_UNITELLER_ADDRESS_DESC'] = 'Максимум 128 символов';
$MESS['SALE_UNITELLER_PHONE'] = 'Телефон покупателя';
$MESS['SALE_UNITELLER_PHONE_DESC'] = 'Максимум 64 символа';
$MESS['SALE_UNITELLER_CITY'] = 'Город покупателя';
$MESS['SALE_UNITELLER_CITY_DESC'] = 'Максимум 64 символа';
$MESS['SALE_UNITELLER_ZIP'] = 'Почтовый индекс';
$MESS['SALE_UNITELLER_ZIP_DESC'] = 'Максимум 64 символа';
$MESS['SALE_UNITELLER_LANGUAGE'] = 'Код языка интерфейса платёжной страницы';
$MESS['SALE_UNITELLER_LANGUAGE_DESC'] = 'Максимум 2 символа. Может быть \'en\' или \'ru\'';
$MESS['SALE_UNITELLER_COMMENT'] = 'Комментарий к платежу';
$MESS['SALE_UNITELLER_COMMENT_DESC'] = 'Максимум 255 символов';
$MESS['SALE_UNITELLER_COUNTRY'] = 'Код страны покупателя (ISO 3166)';
$MESS['SALE_UNITELLER_COUNTRY_DESC'] = 'Максимум 3 символа';
$MESS['SALE_UNITELLER_STATE'] = 'Код штата региона';
$MESS['SALE_UNITELLER_STATE_DESC'] = 'Максимум 3 символа';

// Для чека и обновления статусов.
$MESS['SASP_AS000'] = 'АВТОРИЗАЦИЯ УСПЕШНО ЗАВЕРШЕНА';
$MESS['SASP_AS100'] = 'ОТКАЗ В АВТОРИЗАЦИИ';
$MESS['SASP_AS101'] = 'ОТКАЗ В АВТОРИЗАЦИИ. Ошибочный номер карты';
$MESS['SASP_AS102'] = 'ОТКАЗ В АВТОРИЗАЦИИ. Недостаточно средств';
$MESS['SASP_AS104'] = 'ОТКАЗ В АВТОРИЗАЦИИ. Неверный срок действия карты';
$MESS['SASP_AS105'] = 'ОТКАЗ В АВТОРИЗАЦИИ. Превышен лимит';
$MESS['SASP_AS107'] = 'ОТКАЗ В АВТОРИЗАЦИИ. Ошибка приема данных';
$MESS['SASP_AS108'] = 'ОТКАЗ В АВТОРИЗАЦИИ. Подозрение на мошенничество';
$MESS['SASP_AS109'] = 'ОТКАЗ В АВТОРИЗАЦИИ. Превышен лимит операций Uniteller';
$MESS['SASP_AS200'] = 'ПОВТОРИТЕ АВТОРИЗАЦИЮ';
$MESS['SASP_AS998'] = 'ОШИБКА СИСТЕМЫ. Свяжитесь с Uniteller';

// Для чека.
$MESS['SALE_UNITELLER_MERCH_NAME'] = 'Торговое наименование мёрчанта: ';
$MESS['SALE_UNITELLER_MERCH_NAME_LAT'] = 'Латинское наименование точки приема, присвоенное Uniteller: ';
$MESS['SALE_UNITELLER_MERCH_UNIQ_URL'] = 'Электронный адрес магазина: ';
$MESS['SALE_UNITELLER_MERCH_TEL'] = 'Контактный телефон предприятия: ';
$MESS['SALE_UNITELLER_MERCH_EMAIL'] = 'Контактная электронная почта предприятия: ';
$MESS['SALE_UNITELLER_PS_SUM'] = 'Сумма операции в валюте: ';
$MESS['SALE_UNITELLER_PS_DATE'] = 'Дата операции: ';
$MESS['SALE_UNITELLER_BILLNUMBER'] = 'Уникальный идентификатор транзакции: ';
$MESS['SALE_UNITELLER_USER_FIO'] = 'Ф.И.О: ';
$MESS['SALE_UNITELLER_APPROVEL_CODE'] = 'Код подтверждения: ';
$MESS['SALE_UNITELLER_TRANSACTION_TYPE'] = 'Тип операции: ';
$MESS['SALE_UNITELLER_ERROR'] = 'Для данного заказа в системе Uniteller не существует соответствующего платежа.';
$MESS['SALE_UNITELLER_PRINT_CHECK'] = 'Печать чека';
$MESS['SALE_UNITELLER_CLOSE_WINDOW'] = 'Закрыть окно';

// Для страницы оплаты.
$MESS['SUSP_DESC_TITLE'] = 'Описание платежной системы: ';
$MESS['SUSP_ORDER_SUM'] = 'Сумма к оплате: ';
$MESS['SUSP_ACCOUNT_NO'] = 'Заказ №: ';
$MESS['SUSP_ORDER_FROM'] = ' от ';
$MESS['SUSP_UNITELLER_PAY_BUTTON'] = 'Оплатить';