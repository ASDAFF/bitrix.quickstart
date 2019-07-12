<?php
$arFields["ID"]= <<<EOD
24
EOD;
$arFields["EVENT_NAME"]= <<<EOD
SALE_ORDER_CANCEL
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
#SITE_NAME#: Отмена заказа N#ORDER_ID#
EOD;
$arFields["MESSAGE"]= <<<EOD
Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Заказ номер #ORDER_ID# от #ORDER_DATE# отменен.

#ORDER_CANCEL_DESCRIPTION#

Для получения подробной информации по заказу пройдите на сайт http://#SERVER_NAME#/personal/order/#ORDER_ID#/

#SITE_NAME#

EOD;
$arFields["BODY_TYPE"]= <<<EOD
text
EOD;
$arFields["BCC"]= <<<EOD
#BCC#
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
27.12.2012 20:37:54
EOD;
$arFields["EVENT_TYPE"]= <<<EOD
[ SALE_ORDER_CANCEL ] Отмена заказа
EOD;
