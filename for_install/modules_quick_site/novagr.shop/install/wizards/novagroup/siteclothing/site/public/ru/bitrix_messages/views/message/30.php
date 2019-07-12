<?php
$arFields["ID"]= <<<EOD
30
EOD;
$arFields["EVENT_NAME"]= <<<EOD
SALE_STATUS_CHANGED_F
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
#SALE_EMAIL#
EOD;
$arFields["EMAIL_TO"]= <<<EOD
#EMAIL#
EOD;
$arFields["SUBJECT"]= <<<EOD
#SERVER_NAME#: Изменение статуса заказа N#ORDER_ID#
EOD;
$arFields["MESSAGE"]= <<<EOD
Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Статус заказа номер #ORDER_ID# от #ORDER_DATE# изменен.

Новый статус заказа:
#ORDER_STATUS#
#ORDER_DESCRIPTION#
#TEXT#

Для получения подробной информации по заказу пройдите на сайт http://#SERVER_NAME#/cabinet/orders/

Спасибо за ваш выбор!
#SITE_NAME#
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
21.06.2013 11:20:36
EOD;
$arFields["EVENT_TYPE"]= <<<EOD
[ SALE_STATUS_CHANGED_F ] Изменение статуса заказа на  "Выполнен"
EOD;
