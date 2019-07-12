<?
$MESS["MESSAGE_NEW_USER_SUBJECT"] = "#SITE_NAME#: Зарегистрировался новый пользователь";

$MESS["MESSAGE_NEW_USER_MESSAGE"] = "Информационное сообщение сайта #SITE_NAME#
------------------------------------------

На сайте #SERVER_NAME# успешно зарегистрирован новый пользователь.

Данные пользователя:

ID пользователя: #USER_ID#
ФИО: #NAME#
E-Mail: #EMAIL#
Login: #LOGIN#

Письмо сгенерировано автоматически.
";

$MESS["MESSAGE_USER_PASS_REQUEST_SUBJECT"] = "#SITE_NAME#: Запрос на смену пароля";

$MESS["MESSAGE_USER_PASS_REQUEST_MESSAGE"] = "Информационное сообщение сайта #SITE_NAME#
------------------------------------------
#NAME# #LAST_NAME#,

#MESSAGE#

Для смены пароля перейдите по следующей ссылке:
http://#SERVER_NAME#/auth/index.php?change_password=yes&lang=ru&USER_CHECKWORD=#CHECKWORD#&USER_LOGIN=#LOGIN#

Ваша регистрационная информация:

ID пользователя: #USER_ID#
Статус профиля: #STATUS#
Login: #LOGIN#

Сообщение сгенерировано автоматически.
";

$MESS["MESSAGE_USER_PASS_CHANGED_SUBJECT"] = "#SITE_NAME#: Подтверждение смены пароля";

$MESS["MESSAGE_USER_PASS_CHANGED_MESSAGE"] = "Информационное сообщение сайта #SITE_NAME#
------------------------------------------
#NAME# #LAST_NAME#,

#MESSAGE#

Ваша регистрационная информация:

ID пользователя: #USER_ID#
Статус профиля: #STATUS#
Login: #LOGIN#

Сообщение сгенерировано автоматически.
";


$MESS["MESSAGE_SALE_SUBSCRIBE_PRODUCT_SUBJECT"] = "#SITE_NAME#: Уведомление о поступлении товара";

$MESS["MESSAGE_SALE_SUBSCRIBE_PRODUCT_MESSAGE"] = "Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Уважаемый, #USER_NAME#!

Товар \"#NAME#\" (#PAGE_URL#) поступил на склад.
Вы можете Оформить заказ (http://#SERVER_NAME#/cabinet/cart/).

Не забудьте авторизоваться!

Вы получили это сообщение по Вашей просьбе оповестить при появлении товара.
Не отвечайте на него - письмо сформировано автоматически.

Спасибо за покупку!
";

$MESS["MESSAGE_QUICK_ORDER1_SUBJECT"] = "Был оформлен \"Быстрый Заказ\"";

$MESS["MESSAGE_QUICK_ORDER1_MESSAGE"] = "#SERVER_NAME#<br>
<br>
Клиент оформил окно \"Быстрого заказа\"<br>
________________________________________________<br>
<br>
Дата отправки - #DATE_ENTER#<br>
Позвонить не позже чем через 30 минут!<br>
<br>
E-Mail клиента: #USER_EMAIL#<br>
Телефон клиента: <b>#USER_PHONE#</b><br>
<br>
<b> Заказ: #ORDER_LIST# </b><br>
________________________________________________
";


$MESS["MESSAGE_QUICK_ORDER2_SUBJECT"] = "Вы оформили \"Быстрый заказ\"";

$MESS["MESSAGE_QUICK_ORDER2_MESSAGE"] = "Вы оформили \"Быстрый заказ\" на сайте #SERVER_NAME#<br>
________________________________________________<br>
<br>
Дата заказа - #DATE_ENTER#<br>
<br>
E-Mail: #USER_EMAIL#<br>
Телефон: <b>#USER_PHONE#</b><br>
<br>
Заказ отправлен в обработку, время ожидания результата не более 30 минут.<br>
<br>
<b>Состав заказа: #ORDER_LIST# </b><br>
________________________________________________
";


$MESS["MESSAGE_NEW_VALUE_ADDED_SUBJECT"] = "#SITE_NAME#: предложено новое значение справочника";

$MESS["MESSAGE_NEW_VALUE_ADDED_MESSAGE"] = "В кабинете администратора-каталога было предложено новое значение для справочника
---------------------------------------------------------------------------

#TEXT#
---------------------------------------------------------------------------
дата:  #DATE_ENTER#;
---------------------------------------------------------------------------


Письмо сгенерировано автоматически.
";

$MESS["UP_TYPE_NEW_USER_SUBJECT"] = "Зарегистрировался новый пользователь";
$MESS["UP_TYPE_NEW_USER_DESC"] = "
#USER_ID# - ID пользователя
#LOGIN# - Логин
#EMAIL# - EMail
#NAME# - Имя
#LAST_NAME# - Фамилия
#USER_IP# - IP пользователя
#USER_HOST# - Хост пользователя
";

$MESS["UP_TYPE_USER_PASS_REQUEST_SUBJECT"] = "Запрос на смену пароля";
$MESS["UP_TYPE_USER_PASS_REQUEST_DESC"] = "
#USER_ID# - ID пользователя
#STATUS# - Статус логина
#MESSAGE# - Сообщение пользователю
#LOGIN# - Логин
#CHECKWORD# - Контрольная строка для смены пароля
#NAME# - Имя
#LAST_NAME# - Фамилия
#EMAIL# - E-Mail пользователя
";

$MESS["UP_TYPE_USER_PASS_CHANGED_SUBJECT"] = "Подтверждение смены пароля";
$MESS["UP_TYPE_USER_PASS_CHANGED_DESC"] = "
#USER_ID# - ID пользователя
#STATUS# - Статус логина
#MESSAGE# - Сообщение пользователю
#LOGIN# - Логин
#CHECKWORD# - Контрольная строка для смены пароля
#NAME# - Имя
#LAST_NAME# - Фамилия
#EMAIL# - E-Mail пользователя
";

$MESS["UP_TYPE_SALE_SUBSCRIBE_PRODUCT_SUBJECT"] = "Уведомление о поступлении товара";
$MESS["UP_TYPE_SALE_SUBSCRIBE_PRODUCT_DESC"] = "#USER_NAME# - имя пользователя
#EMAIL# - email пользвоателя
#NAME# - название товара
#PAGE_URL# - детальная страница товара";

$MESS["UP_TYPE_NEW_VALUE_ADDED_SUBJECT"] = "Предложено новое значение справочника";
$MESS["UP_TYPE_NEW_VALUE_ADDED_DESC"] = "#TEXT# - Текст
#DATE_ENTER# - дата создания ";

$MESS["UP_TYPE_QUICK_ORDER_SUBJECT"] = "Быстрый заказ";
$MESS["UP_TYPE_QUICK_ORDER_DESC"] = "#ID# - ID
#USER_EMAIL# - email пользователя
#USER_PHONE# - телефон пользователя
#PRODUCT_ARTICUL# - артикул товара
#PRODUCT_ID# - ID товара
#DATE_ENTER# - дата создания документа
#ORDER_LIST# - заказ";

$MESS['MESSAGE_FORM_FILLING_FORM_FEEDBACK_SUBJECT'] = "#SERVER_NAME#: заполнена web-форма [#RS_FORM_ID#] #RS_FORM_NAME#";
$MESS['MESSAGE_FORM_FILLING_FORM_FEEDBACK_MESSAGE'] = "#SERVER_NAME#

Заполнена web-форма: [#RS_FORM_ID#] #RS_FORM_NAME#
-------------------------------------------------------

Дата - #RS_DATE_CREATE#
Результат - #RS_RESULT_ID#
Пользователь - [#RS_USER_ID#] #RS_USER_NAME# #RS_USER_AUTH#
Посетитель - #RS_STAT_GUEST_ID#
Сессия - #RS_STAT_SESSION_ID#


Имя
*******************************
#feedback_name#

Сообщение
*******************************
#feedback_message#

Email
*******************************
#feedback_email#


Для просмотра воспользуйтесь ссылкой:
http://#SERVER_NAME#/bitrix/admin/form_result_view.php?lang=ru&WEB_FORM_ID=#RS_FORM_ID#&RESULT_ID=#RS_RESULT_ID#

-------------------------------------------------------
Письмо сгенерировано автоматически.
";
$MESS['UP_TYPE_FORM_FILLING_FORM_FEEDBACK_SUBJECT'] = 'Заполнена web-форма "FORM_FEEDBACK"';
$MESS['UP_TYPE_FORM_FILLING_FORM_FEEDBACK_DESC'] = "#RS_FORM_ID# - ID формы
#RS_FORM_NAME# - Имя формы
#RS_FORM_SID# - SID формы
#RS_RESULT_ID# - ID результата
#RS_DATE_CREATE# - Дата заполнения формы
#RS_USER_ID# - ID пользователя
#RS_USER_EMAIL# - EMail пользователя
#RS_USER_NAME# - Фамилия, имя пользователя
#RS_USER_AUTH# - Пользователь был авторизован?
#RS_STAT_GUEST_ID# - ID посетителя
#RS_STAT_SESSION_ID# - ID сессии
#feedback_name# - Имя
#feedback_name_RAW# - Имя (оригинальное значение)
#feedback_message# - Сообщение
#feedback_message_RAW# - Сообщение (оригинальное значение)
#feedback_email# - Email
#feedback_email_RAW# - Email (оригинальное значение)
";
?>