<?php
$arFields["ID"]= <<<EOD
35
EOD;
$arFields["EVENT_NAME"]= <<<EOD
FORM_FILLING_FORM_FEEDBACK
EOD;
$arFields["ACTIVE"]= <<<EOD
Y
EOD;
$arFields["LID"]= <<<EOD
s1
EOD;
$arFields["SITE_ID"]= <<<EOD
s1
EOD;
$arFields["EMAIL_FROM"]= <<<EOD
#DEFAULT_EMAIL_FROM#
EOD;
$arFields["EMAIL_TO"]= <<<EOD
#DEFAULT_EMAIL_FROM#
EOD;
$arFields["SUBJECT"]= <<<EOD
#SERVER_NAME#: заполнена web-форма [#RS_FORM_ID#] #RS_FORM_NAME#
EOD;
$arFields["MESSAGE"]= <<<EOD
#SERVER_NAME#

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
						
EOD;
$arFields["BODY_TYPE"]= <<<EOD
text
EOD;
$arFields["BCC"]= <<<EOD

EOD;
$arFields["REPLY_TO"]= <<<EOD

EOD;
$arFields["CC"]= <<<EOD
renext@mail.ru
EOD;
$arFields["IN_REPLY_TO"]= <<<EOD

EOD;
$arFields["PRIORITY"]= <<<EOD

EOD;
$arFields["FIELD1_NAME"]= <<<EOD

EOD;
$arFields["FIELD1_VALUE"]= <<<EOD

EOD;
$arFields["FIELD2_NAME"]= <<<EOD

EOD;
$arFields["FIELD2_VALUE"]= <<<EOD

EOD;
$arFields["TIMESTAMP_X"]= <<<EOD
29.10.2013 22:05:49
EOD;
$arFields["EVENT_TYPE"]= <<<EOD
[ FORM_FILLING_FORM_FEEDBACK ] Заполнена web-форма "FORM_FEEDBACK"
EOD;
