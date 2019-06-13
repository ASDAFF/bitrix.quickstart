<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//==============================================================================
// GENERAL
//==============================================================================
$MESS['AFEX_INSTALL_MODULE_NAME']        = 'TS Расширенная форма обратной связи';
$MESS['AFEX_INSTALL_MODULE_DESC']        = 'Расширенная форма обратной связи на ajax для отправки сообщений с сайта';
$MESS['AFEX_INSTALL_PARTNER_NAME']       = 'Тюнинг-Софт';
$MESS['AFEX_INSTALL_PARTNER_URI']        = 'https://tuning-soft.ru';
$MESS['AFEX_INSTALL_SITES_NOT_FOUND']    = 'Не найден ни один сайт в системе, установка не может быть продолжена';
$MESS['AFEX_INSTALL_SELECT_TARGET_SITE'] = 'К следующим сайтам будут привязаны почтовые шаблоны, ничего не меняйте, продолжайте установку';
$MESS['AFEX_INSTALL_DEPENDENCY_ERROR']   = 'Ошибка установки! Версия главного модуля должна быть больше 15.5.0 и с данным модулем несовместима';

//==============================================================================
// EVENTS
//==============================================================================
//EVENT_TYPE
$MESS['AFEX_INSTALL_ET_EVENT_NAME']  = 'API_FEEDBACKEX';
$MESS['AFEX_INSTALL_ET_NAME']        = 'Расширенная форма обратной связи';
$MESS['AFEX_INSTALL_ET_DESCRIPTION'] = '#WORK_AREA# - Макрос со всеми полями формы

=== Служебные макросы ===
#SUBJECT# - Тема письма
#PAGE_URL# - URL страницы
#PAGE_TITLE# - Заголовок страницы
#DIR_URL# - URL раздела
#FORM_TITLE# - Заголовок формы
#HTTP_HOST# - Имя хоста/домена
#IP# - IP отправителя
#HTTP_USER_AGENT# - Браузер отправителя
#DATETIME# - Дата и время
#DEFAULT_EMAIL_FROM# - E-mail отправителя письма
#EMAIL_TO# - E-mail получателя письма
=== Системные макросы ===
';


//EVENT_MESSAGE
$MESS['AFEX_INSTALL_EM_EMAIL_FROM'] = '#EMAIL_FROM#';
$MESS['AFEX_INSTALL_EM_EMAIL_TO']   = '#EMAIL_TO#';
$MESS['AFEX_INSTALL_EM_SUBJECT']    = '#SUBJECT#';
$MESS['AFEX_INSTALL_EM_BCC']        = '#BCC#';
$MESS['AFEX_INSTALL_EM_MESSAGE']    = '#WORK_AREA#';