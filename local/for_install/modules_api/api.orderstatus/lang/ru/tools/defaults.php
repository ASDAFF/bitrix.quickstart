<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main\Config\Option;

global $MESS;
include 'defaults_lang.php';

$server_name = trim(Option::get('main', 'server_name', $_SERVER['SERVER_NAME']));
$sale_name   = trim(Option::get('main', 'site_name', $server_name));
$sale_email  = trim(Option::get('main', 'email_from', 'info@' . $server_name));
$sale_url    = (CMain::IsHTTPS() ? "https://" : "http://") . $server_name;

$MESS['AOS_TD_INSTALL_DEFAULTS'] = array(
	'SALE_LOGO'                       => $MESS['AOS_DEFAULTS_SALE_LOGO'],
	'SALE_NAME'                       => $sale_name,
	'SALE_URL'                        => $sale_url,
	'SALE_EMAIL'                      => $sale_email,
	'SALE_PHONE'                      => $MESS['AOS_DEFAULTS_SALE_PHONE'],
	'SALE_ADDRESS'                    => $MESS['AOS_DEFAULTS_SALE_ADDRESS'],
	'MAIL_HEADER'                     => $MESS['AOS_DEFAULTS_MAIL_HEADER'],
	'MAIL_CONTENT'                    => $MESS['AOS_DEFAULTS_MAIL_CONTENT'],
	'MAIL_FOOTER'                     => $MESS['AOS_DEFAULTS_MAIL_FOOTER'],
	'EVENT_TYPE'                      => $MESS['AOS_DEFAULTS_EVENT_TYPE'],
	'MAIL_SALE_NEW_ORDER'             => $MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER'],
	'MAIL_SALE_NEW_ORDER_TYPE'        => $MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_TYPE'],
	'MAIL_SALE_NEW_ORDER_HEADER'      => $MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_HEADER'],
	'MAIL_SALE_NEW_ORDER_HEADER_TYPE' => $MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_HEADER_TYPE'],
	'MAIL_SALE_NEW_ORDER_FOOTER'      => $MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_FOOTER'],
	'MAIL_SALE_NEW_ORDER_FOOTER_TYPE' => $MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_FOOTER_TYPE'],
	'MAIL_SALE_NEW_ORDER_SUBJECT'     => $MESS['AOS_DEFAULTS_MAIL_SALE_NEW_ORDER_SUBJECT'],
);

$MESS['AOS_TD_INSTALL_DEFAULTS_GATEWAY'] = array(
	 "Devinotele" => "('Devinotele', 'N', 10, '', '2016-10-06 09:56:13', 1)",
	 "Redsms"     => "('Redsms', 'N', 20, '', '2016-10-06 09:33:50', 1)",
	 "Turbosms"   => "('Turbosms', 'N', 30, '', '2016-10-17 11:35:45', 1)",
	 "Smsclub"    => "('Smsclub', 'N', 40, '', '2017-07-14 09:26:25', 1)",
	 "Smsru"      => "('Smsru', 'N', 50, '', '2017-07-14 14:20:31', 1)",
	 "Smsint"     => "('Smsint', 'N', 60, '', '2018-07-06 00:00:00', 1)",
	 "Redsms3"    => "('Redsms3', 'N', 70, '', '2018-07-06 00:00:00', 1)",
);


//Alert
$MESS['AOS_TD_SESSION_EXPIRED']   = 'Ошибка! Ваша сессия истекла, попробуйте обновить страницу и заново сделать тоже самое.';
$MESS['AOS_TD_ACCESS_DENIED']     = 'Ошибка! Вам недостаточно прав для работы с модулем.';
$MESS['AOS_TD_API_MODULE_ERROR']  = 'Ошибка! Главный модуль не установлен.';
$MESS['AOS_TD_SALE_MODULE_ERROR'] = 'Ошибка! Модуль Интернет-магазина не установлен.';