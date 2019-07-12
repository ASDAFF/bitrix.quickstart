<?php
$arFields["ID"]= <<<EOD
33
EOD;
$arFields["EVENT_NAME"]= <<<EOD
SUBSCRIBE_CONFIRM
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
#SITE_NAME#: Подтверждение подписки
EOD;
$arFields["MESSAGE"]= <<<EOD
Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Здравствуйте,

Вы получили это сообщение, так как ваш адрес был подписан
на список рассылки сервера #SERVER_NAME#.

Дополнительная информация о подписке:

Адрес подписки (email) ............ #EMAIL#
Дата добавления/редактирования .... #DATE_SUBSCR#

Ваш код для подтверждения подписки: #CONFIRM_CODE#

Для подтверждения подписки перейдите по следующей ссылке:
http://#SERVER_NAME##SUBSCR_SECTION#ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#

Вы также можете ввести код для подтверждения подписки на странице:
http://#SERVER_NAME##SUBSCR_SECTION#ID=#ID#

Внимание! Вы не будете получать сообщения рассылки, пока не подтвердите
свою подписку.

---------------------------------------------------------------------
Сохраните это письмо, так как оно содержит информацию для авторизации.
Используя код подтверждения подписки, вы cможете изменить параметры
подписки или отписаться от рассылки.

Изменить параметры:
http://#SERVER_NAME##SUBSCR_SECTION#ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#

Отписаться:
http://#SERVER_NAME##SUBSCR_SECTION#ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#&act=unsubscribe
---------------------------------------------------------------------

Сообщение сгенерировано автоматически.

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
27.12.2013 17:58:16
EOD;
$arFields["EVENT_TYPE"]= <<<EOD
[ SUBSCRIBE_CONFIRM ] Подтверждение подписки
EOD;
