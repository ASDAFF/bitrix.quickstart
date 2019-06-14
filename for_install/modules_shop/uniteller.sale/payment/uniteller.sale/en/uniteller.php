<?php
/**
 * Массив языковых констант.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
global $MESS;

// Для админки.
$MESS['SALE_UNITELLER_DESCRIPTION'] = '<a href=\'http://www.uniteller.ru\' target=\'_blank\'>http://www.uniteller.ru</a>';
$MESS['SALE_UNITELLER_DESC'] = 'Description of the payment system Uniteller';
$MESS['SALE_UNITELLER_SHOP_IDP'] = 'Shop ID';
$MESS['SALE_UNITELLER_SHOP_IDP_DESC'] = 'Shop ID received from Uniteller';
$MESS['SALE_UNITELLER_SHOP_LOGIN'] = 'Login';
$MESS['SALE_UNITELLER_SHOP_LOGIN_DESC'] = 'Login, received from Uniteller ';
$MESS['SALE_UNITELLER_SHOP_PASSWORD'] = 'Password';
$MESS['SALE_UNITELLER_SHOP_PASSWORD_DESC'] = 'Password, received from Uniteller ';
$MESS['SALE_UNITELLER_SITE_NAME_LAT'] = 'Merchant name in Latin, assigned by Uniteller ';
$MESS['SALE_UNITELLER_LIFE_TIME'] = 'Payment page lifetime in seconds ';
$MESS['SALE_UNITELLER_LIFE_TIME_DESC'] = 'It should be in the form of integer positive number. If a Purchaser spends more time on the page than it is determined, the payment page will be considered stale and the payment will not be accepted. In this case, a Purchaser will be offered to return to a Merchant\'s site.';
$MESS['SALE_UNITELLER_TIME_PAID_CHANGE'] = 'The time, during which the "paid" status can be cancelled';
$MESS['SALE_UNITELLER_TIME_PAID_CHANGE_DESC'] = 'The time is calculated in days (14 days by default)';
$MESS['SALE_UNITELLER_TIME_ORDER_SYNC'] = 'The time within which will be synchronized with the payment status of orders';
$MESS['SALE_UNITELLER_TIME_ORDER_SYNC_DESC'] = 'The time is calculated in days (30 days by default)';
$MESS['SALE_UNITELLER_SUCCESS_URL'] = 'Page URL for successful transaction (URL_RETURN_OK)';
$MESS['SALE_UNITELLER_SUCCESS_URL_DESC'] = 'Page URL the purchaser should be returned to after a successful transaction in Uniteller system (maximum 128 symbols)';
$MESS['SALE_UNITELLER_FAIL_URL'] = 'Page URL for unsuccessful transaction (URL_RETURN_NO)';
$MESS['SALE_UNITELLER_FAIL_URL_DESC'] = 'URL of the page to return to in case of failed payment in Uniteller (maximum 128 characters)';
$MESS['SALE_UNITELLER_TESTMODE'] = 'Test mode';
$MESS['SALE_UNITELLER_TESTMODE_DESC'] = 'If an empty value - the store will operate as usual';
$MESS['SALE_UNITELLER_ORDER_ID'] = 'Order number';
$MESS['SALE_UNITELLER_ORDER_ID_DESC'] = 'Order number in your e-Shop';
$MESS['SALE_UNITELLER_EMAIL'] = 'E-mail';
$MESS['SALE_UNITELLER_EMAIL_DESC'] = 'Maximum 64 characters';
$MESS['SALE_UNITELLER_FIRST_NAME'] = 'First name';
$MESS['SALE_UNITELLER_FIRST_NAME_DESC'] = 'Maximum 64 characters';
$MESS['SALE_UNITELLER_MIDDLE_NAME'] = 'Middle buyer';
$MESS['SALE_UNITELLER_MIDDLE_NAME_DESC'] = 'Maximum 64 characters';
$MESS['SALE_UNITELLER_LAST_NAME'] = 'Last name';
$MESS['SALE_UNITELLER_LAST_NAME_DESC'] = 'Maximum 64 characters';
$MESS['SALE_UNITELLER_ADDRESS'] = 'Address';
$MESS['SALE_UNITELLER_ADDRESS_DESC'] = 'Maximum 128 characters';
$MESS['SALE_UNITELLER_PHONE'] = 'Phone';
$MESS['SALE_UNITELLER_PHONE_DESC'] = 'Maximum 64 characters';
$MESS['SALE_UNITELLER_CITY'] = 'City';
$MESS['SALE_UNITELLER_CITY_DESC'] = 'Maximum 64 characters';
$MESS['SALE_UNITELLER_ZIP'] = 'Zip code';
$MESS['SALE_UNITELLER_ZIP_DESC'] = 'Maximum 64 characters';
$MESS['SALE_UNITELLER_LANGUAGE'] = 'Interface language code of the payment page';
$MESS['SALE_UNITELLER_LANGUAGE_DESC'] = 'Maximum 2 characters (\'en \' or \'ru \')';
$MESS['SALE_UNITELLER_COMMENT'] = 'Comment to the transaction';
$MESS['SALE_UNITELLER_COMMENT_DESC'] = 'Maximum 255 characters';
$MESS['SALE_UNITELLER_COUNTRY'] = 'Country code (ISO 3166)';
$MESS['SALE_UNITELLER_COUNTRY_DESC'] = 'Maximum 3 characters';
$MESS['SALE_UNITELLER_STATE'] = 'Code State Region';
$MESS['SALE_UNITELLER_STATE_DESC'] = 'Maximum 3 characters';

// Для чека и обновления статусов.
$MESS['SASP_AS000'] = 'SUCCESSFUL AUTHORIZATION';
$MESS['SASP_AS100'] = 'AUTHORIZATION DENIAL';
$MESS['SASP_AS101'] = 'AUTHORIZATION DENIAL. Invalid card number ';
$MESS['SASP_AS102'] = 'AUTHORIZATION DENIAL. Not enough funds ';
$MESS['SASP_AS104'] = 'AUTHORIZATION DENIAL. Wrong expiration date ';
$MESS['SASP_AS105'] = 'AUTHORIZATION DENIAL. Limit is exceeded ';
$MESS['SASP_AS107'] = 'AUTHORIZATION DENIAL. Data receive error';
$MESS['SASP_AS108'] = 'AUTHORIZATION DENIAL. Fraud suspicion ';
$MESS['SASP_AS109'] = 'AUTHORIZATION DENIAL. The limit for Uniteller operations is exceeded';
$MESS['SASP_AS200'] = 'REPEAT AUTHORIZATION ';
$MESS['SASP_AS998'] = 'SYSTEM ERROR. Please, contact Uniteller';

// Для чека.
$MESS['SALE_UNITELLER_MERCH_NAME'] = 'Merchant name: ';
$MESS['SALE_UNITELLER_MERCH_NAME_LAT'] = 'Merchant e-Shop name in Latin, assigned by Uniteller: ';
$MESS['SALE_UNITELLER_MERCH_UNIQ_URL'] = 'Merchant URL: ';
$MESS['SALE_UNITELLER_MERCH_TEL'] = 'Company contact phone: ';
$MESS['SALE_UNITELLER_MERCH_EMAIL'] = 'Company contact e-mail: ';
$MESS['SALE_UNITELLER_PS_SUM'] = 'Transaction amount (currency): ';
$MESS['SALE_UNITELLER_PS_DATE'] = 'Transaction date: ';
$MESS['SALE_UNITELLER_BILLNUMBER'] = 'Bill number: ';
$MESS['SALE_UNITELLER_USER_FIO'] = 'Cardholder name: ';
$MESS['SALE_UNITELLER_APPROVEL_CODE'] = 'Verification code: ';
$MESS['SALE_UNITELLER_TRANSACTION_TYPE'] = 'Transaction type: ';
$MESS['SALE_UNITELLER_ERROR'] = 'This order does not any corresponding payment in the Uniteller system.';
$MESS['SALE_UNITELLER_PRINT_CHECK'] = 'Print check';
$MESS['SALE_UNITELLER_CLOSE_WINDOW'] = 'Close window';

// Для страницы оплаты.
$MESS['SUSP_DESC_TITLE'] = 'Description of the payment system: ';
$MESS['SUSP_ORDER_SUM'] = 'Total: ';
$MESS['SUSP_ACCOUNT_NO'] = 'Order no.';
$MESS['SUSP_ORDER_FROM'] = ' from ';
$MESS['SUSP_UNITELLER_PAY_BUTTON'] = 'Pay';