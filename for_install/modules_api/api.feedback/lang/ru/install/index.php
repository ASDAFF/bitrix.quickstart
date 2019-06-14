<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//==============================================================================
// GENERAL
//==============================================================================
$MESS['api.feedback_MODULE_NAME']        = 'TS Умная форма обратной связи';
$MESS['api.feedback_MODULE_DESC']        = 'Умная форма обратной связи на ajax с конструктором форм для отправки сообщений с сайта с вложением';
$MESS['api.feedback_PARTNER_NAME']       = 'Тюнинг-Софт';
$MESS['api.feedback_PARTNER_URI']        = 'https://tuning-soft.ru/';


//==============================================================================
// EVENTS
//==============================================================================
//EVENT_TYPE RU
$MESS['ET_EVENT_NAME']     = 'API_FEEDBACK';
$MESS['RU_ET_NAME']        = 'TS Умная форма обратной связи';
$MESS['RU_ET_DESCRIPTION'] = '#WORK_AREA# - Главная динамическая область со всеми полями формы

=== Служебные макросы ===
#TICKET_ID# - Номер тикета (элемент инфоблока)
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

=== Макросы полей конструктора форм ===
Значение атрибута "@name=" любого поля, например:
#email#

=== Макросы фикс. полей формы ===
#AUTHOR_FIO# - ФИО
#AUTHOR_NAME# - Ваше Имя
#AUTHOR_LAST_NAME# - Фамилия
#AUTHOR_SECOND_NAME# - Отчество
#AUTHOR_EMAIL# - E-mail
#AUTHOR_PERSONAL_MOBILE# - Контактный телефон
#AUTHOR_WORK_COMPANY# - Компания
#AUTHOR_POSITION# - Должность
#AUTHOR_PROFESSION# - Профессия
#AUTHOR_STATE# - Область, район
#AUTHOR_CITY# - Город
#AUTHOR_WORK_CITY# - Город работы
#AUTHOR_STREET# - Улица
#AUTHOR_ADRESS# - Адрес
#AUTHOR_PERSONAL_PHONE# - Домашний телефон
#AUTHOR_WORK_PHONE# - Рабочий телефон
#AUTHOR_FAX# - Факс
#AUTHOR_MAILBOX# - Почтовый ящик
#AUTHOR_WORK_MAILBOX# - Рабочий почтовый ящик
#AUTHOR_SKYPE# - Скайп
#AUTHOR_ICQ# - Номер ICQ
#AUTHOR_WWW# - Персональный сайт
#AUTHOR_WORK_WWW#  - Рабочий сайт
#AUTHOR_MESSAGE_THEME# - Тема сообщения
#AUTHOR_MESSAGE# - Сообщение
#AUTHOR_NOTES# - Заметки
#BRANCH_NAME# - Офис(филиал)
#FILES# - Файлы (этот макрос должен быть в самом конце почтового шаблона, если файлы отправляются вложениями)

=== Системные макросы ===
';


//EVENT_MESSAGE ADMIN
$MESS['EM_EMAIL_FROM']    = '#DEFAULT_EMAIL_FROM#';
$MESS['EM_EMAIL_TO']      = '#EMAIL_TO#';
$MESS['EM_SUBJECT_ADMIN'] = '#SITE_NAME#: Сообщение из формы обратной связи';
$MESS['EM_MESSAGE']       = 'Информационное сообщение сайта #SITE_NAME#<br>
------------------------------------------<br>
#WORK_AREA#<br>
';

//EVENT_MESSAGE USER
$MESS['EM_SUBJECT_USER'] = '#SITE_NAME#: Копия сообщения из формы обратной связи';