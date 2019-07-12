<?php
$arFields["ID"]= <<<EOD
6
EOD;
$arFields["EVENT_NAME"]= <<<EOD
USER_INVITE
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
#EMAIL#
EOD;
$arFields["SUBJECT"]= <<<EOD
#SITE_NAME#: Приглашение на сайт
EOD;
$arFields["MESSAGE"]= <<<EOD
Информационное сообщение сайта #SITE_NAME#
------------------------------------------
Здравствуйте, #NAME# #LAST_NAME#!

Администратором сайта вы добавлены в число зарегистрированных пользователей.

Приглашаем Вас на наш сайт.

Ваша регистрационная информация:

ID пользователя: #ID#
Login: #LOGIN#

Рекомендуем вам сменить установленный автоматически пароль.

Для смены пароля перейдите по следующей ссылке:
http://#SERVER_NAME#/auth.php?change_password=yes&USER_LOGIN=#URL_LOGIN#&USER_CHECKWORD=#CHECKWORD#

EOD;
$arFields["BODY_TYPE"]= <<<EOD
text
EOD;
$arFields["BCC"]= <<<EOD

EOD;
$arFields["REPLY_TO"]= <<<EOD

EOD;
$arFields["CC"]= <<<EOD

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
27.12.2012 20:37:27
EOD;
$arFields["EVENT_TYPE"]= <<<EOD
[ USER_INVITE ] Приглашение на сайт нового пользователя
EOD;
