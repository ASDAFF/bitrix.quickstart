<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//---------- Base ----------//
$MESS['AFD_INSTALL_MODULE_NAME']  = 'TS Умный конструктор форм на инфоблоках';
$MESS['AFD_INSTALL_MODULE_DESC']  = 'Очень функциональный и многозадачный конструктор форм на инфоблоках';
$MESS['AFD_INSTALL_PARTNER_NAME'] = 'Тюнинг-Софт';
$MESS['AFD_INSTALL_PARTNER_URI']  = 'https://tuning-soft.ru';
$MESS['AFD_INSTALL_BUTTON_BACK']  = 'Вернуться в список решений';


//---------- Event log ----------//
$MESS['AFD_LOG_CHECK_DEPENDENCY'] = 'Ваша система не соответствует минимальным требованиям';
$MESS['AFD_LOG_RIGHTS']           = 'Недостаточно прав для установки модуля';


//---------- Events ----------//
$MESS['AFD_INSTALL_ET_EVENT_NAME']     = 'API_FORMDESIGNER';
$MESS['AFD_INSTALL_RU_ET_NAME']        = 'TS Умный конструктор форм на инфоблоках';
$MESS['AFD_INSTALL_RU_ET_DESCRIPTION'] = '#WORK_AREA# - Рабочая область (все поля формы)
#EMAIL_FROM# - E-mail отправителя
#EMAIL_TO# - E-mail получателя
#BCC# - E-mail для скрытой копии письма
#TICKET_ID# - Номер тикета (если включена запись)
#ELEMENT_ID# - ID элемента инфоблока (если включена запись)
#FORM_TITLE# - Заголовок формы из настроек компонента
#FORM_ID# - ID формы из настроек компонента
#PAGE_TITLE# - Заголовок страницы
#PAGE_URL# - Адрес страницы
#DIR_URL# - Адрес директории
#HTTP_HOST# - Адрес сайта с протоколом
#DATETIME# - Дата и время
#IP# - IP-адрес посетителя

=== Системные макросы ===
';

$MESS['AFD_INSTALL_EM_EMAIL_FROM'] = '#EMAIL_FROM#';
$MESS['AFD_INSTALL_EM_EMAIL_TO']   = '#EMAIL_TO#';
$MESS['AFD_INSTALL_EM_SUBJECT']    = '#SUBJECT#';
$MESS['AFD_INSTALL_EM_MESSAGE']    = '#WORK_AREA#';


//---------- Iblock ----------//
$MESS['AFD_INSTALL_IBLOCK_TYPE_ID']   = 'api_formdesigner';
$MESS['AFD_INSTALL_IBLOCK_TYPE_LANG'] = Array(
	'ru' => Array(
		'NAME'         => 'Конструктор форм',
		'SECTION_NAME' => 'Разделы',
		'ELEMENT_NAME' => 'Элементы',
	),
	'en' => Array(
		'NAME'         => 'Form Designer',
		'SECTION_NAME' => 'Sections',
		'ELEMENT_NAME' => 'Elements',
	),
);

//Simple form
$MESS['AFD_INSTALL_IBLOCK_NAME_simple']  = 'Обратная связь';
$MESS['AFD_INSTALL_IBLOCK_PROPS_simple'] = array(
	array(
		'NAME'          => '№ обращения',
		'CODE'          => 'TICKET_ID',
		'PROPERTY_TYPE' => 'N',
		'IS_REQUIRED'   => 'N',
	),
	array(
		'NAME' => 'Ваше имя',
		'CODE' => 'NAME',
	),
	array(
		'NAME' => 'Ваш емайл',
		'CODE' => 'EMAIL',
	),
	array(
		'NAME'        => 'Ваш телефон',
		'CODE'        => 'PHONE',
		'IS_REQUIRED' => 'N',
	),
	array(
		'NAME'      => 'Сообщение',
		'CODE'      => 'MESSAGE',
		'USER_TYPE' => 'HTML',
	),
);