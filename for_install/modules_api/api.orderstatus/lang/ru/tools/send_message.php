<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['AOS_TSM_MODULE_ERROR']             = 'Модуль «Расширенные статусы и письма заказов» не установлен';
$MESS['AOS_TSM_SALE_MODULE_ERROR']        = 'Модуль «Интернет-магазин» не установлен';
$MESS['AOS_TSM_ACCESS_DENIED']            = 'Доступ запрещен';
$MESS['AOS_TSM_SESSION_EXPIRED']          = 'Ошибка! Ваша сессия истекла, обновите страницу и отправьте сообщение повторно';
$MESS['AOS_TSM_EVENT_TYPE_ADD_ERROR']     = 'Ошибка создания типа почтового события';
$MESS['AOS_TSM_EVENT_MESS_ADD_ERROR']     = 'Ошибка создания почтового шаблона';
$MESS['AOS_TSM_EVENT_MESSAGE_SEND']       = 'Сообщение успешно отправлено!';
$MESS['AOS_TSM_EVENT_MESSAGE_SEND_ERROR'] = 'Ошибка! Сообщение не было отправлено';


$MESS['AOS_TSM_EVENT_TYPE'] = array(
	0 => array(
		'LID'         => 'ru',
		'EVENT_NAME'  => 'API_ORDERSTATUS',
		'NAME'        => 'Расширенные статусы и письма заказов',
		'DESCRIPTION' => '',
	),
	1 => array(
		'LID'         => 'en',
		'EVENT_NAME'  => 'API_ORDERSTATUS',
		'NAME'        => 'Advanced status of orders and letters',
		'DESCRIPTION' => '',
	),
);

$MESS['AOS_TSM_EVENT_MESSAGE'] = array(
	'ACTIVE'     => 'Y',
	'EVENT_NAME' => 'API_ORDERSTATUS',
	'LID'        => '',
	'EMAIL_FROM' => '#SALE_EMAIL#',
	'EMAIL_TO'   => '#EMAIL#',
	'SUBJECT'    => '#SITE_NAME#: Сообщение по заказу №#ORDER_ID#',
	'BODY_TYPE'  => 'html',
	'MESSAGE'    => '#ORDER_DESCRIPTION#',
);