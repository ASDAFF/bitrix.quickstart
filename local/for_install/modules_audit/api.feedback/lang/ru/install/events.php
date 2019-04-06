<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//EVENT_TYPE RU
$MESS['ET_EVENT_NAME']     = 'FEEDBACK_FORM';
$MESS['RU_ET_NAME']        = 'Отправка сообщения через форму обратной связи';
$MESS['RU_ET_DESCRIPTION'] = '=== Макросы "стандартной формы обартной связи" ===
#AUTHOR# - Автор сообщения
#AUTHOR_EMAIL# - Email автора сообщения
#TEXT# - Текст сообщения
#EMAIL_FROM# - Email отправителя письма
#EMAIL_TO# - Email получателя письма

=== Макросы "расширенной формы обратной связи" ===
#WORK_AREA#   Автоматически заменяемая область
#AUTHOR_FIO# 			ФИО
#AUTHOR_NAME# 			Ваше Имя
#AUTHOR_LAST_NAME# 		Фамилия
#AUTHOR_SECOND_NAME# 	Отчество
#AUTHOR_EMAIL# 			E-mail
#AUTHOR_PERSONAL_MOBILE# Контактный телефон
#AUTHOR_WORK_COMPANY# 	Компания
#AUTHOR_POSITION# 		Должность
#AUTHOR_PROFESSION# 	Профессия
#AUTHOR_STATE# 			Область, район
#AUTHOR_CITY# 			Город
#AUTHOR_STREET# 		Улица
#AUTHOR_ADRESS# 		Адрес
#AUTHOR_PERSONAL_PHONE# Домашний телефон
#AUTHOR_WORK_PHONE# 	Рабочий телефон
#AUTHOR_FAX# 			Факс
#AUTHOR_MAILBOX# 		Почтовый ящик
#AUTHOR_WORK_MAILBOX# 	Рабочий почтовый ящик
#AUTHOR_SKYPE# 			Скайп
#AUTHOR_ICQ# 			Номер ICQ
#AUTHOR_WWW# 			Персональный сайт
#AUTHOR_WORK#_WWW 		Рабочий сайт
#AUTHOR_MESSAGE# 		Сообщение
#AUTHOR_NOTES# 			Заметки
#CUSTOM_FIELD_0#        Поле конструктора № 0
#CUSTOM_FIELD_1#        Поле конструктора № 1 и т.д.
#BRANCH_NAME#           Офис(филиал)
#FILES#                 Файлы
#PAGE_URL#              URL страницы
#PAGE_TITLE#            Заголовок страницы
#FORM_TITLE#            Заголовок формы
#HTTP_HOST#             Имя хоста/домена
#IP#                    IP отправителя
#HTTP_USER_AGENT#       Браузер отправителя
#DATETIME#              Дата и время
#EMAIL_TO#              E-mail получателя письма
#EMAIL_FROM#            E-mail администратора из настроек главного модуля (не изменяется)
#DEFAULT_EMAIL_FROM#    E-mail адрес администратора(задается в настройках главного модуля) или посетителя(замена включается в настройках компонента)';


//EVENT_MESSAGE ADMIN
$MESS['EM_EMAIL_FROM']       = '#DEFAULT_EMAIL_FROM#';
$MESS['EM_EMAIL_TO']         = '#EMAIL_TO#';
$MESS['EM_SUBJECT_ADMIN']    = '#SITE_NAME#: Сообщение из расширенной формы обратной связи';
$MESS['EM_MESSAGE']          = 'Информационное сообщение сайта #SITE_NAME#
------------------------------------------

#WORK_AREA#

------
С уважением, администрация #SITE_NAME#';

//EVENT_MESSAGE USER
$MESS['EM_SUBJECT_USER']    = '#SITE_NAME#: Копия сообщения из расширенной формы обратной связи';