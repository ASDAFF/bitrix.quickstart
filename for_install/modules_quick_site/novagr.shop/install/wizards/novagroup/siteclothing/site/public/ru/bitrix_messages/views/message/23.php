<?php
$arFields["ID"]= <<<EOD
23
EOD;
$arFields["EVENT_NAME"]= <<<EOD
SALE_NEW_ORDER_RECURRING
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
#SITE_NAME#: Новый заказ N#ORDER_ID# на продление подписки
EOD;
$arFields["MESSAGE"]= <<<EOD
Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Уважаемый #ORDER_USER#,

Ваш заказ номер #ORDER_ID# от #ORDER_DATE# на продление подписки принят.

Стоимость заказа: #PRICE#.

Состав заказа:
#ORDER_LIST#

Вы можете следить за выполнением своего заказа (на какой
стадии выполнения он находится), войдя в Ваш персональный
раздел сайта #SITE_NAME#. Обратите внимание, что для входа
в этот раздел Вам необходимо будет ввести логин и пароль
пользователя сайта #SITE_NAME#.

Для того, чтобы аннулировать заказ, воспользуйтесь функцией
отмены заказа, которая доступна в Вашем персональном
разделе сайта #SITE_NAME#.

Пожалуйста, при обращении к администрации сайта #SITE_NAME#
ОБЯЗАТЕЛЬНО указывайте номер Вашего заказа - #ORDER_ID#.

Спасибо за покупку!

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
[ SALE_NEW_ORDER_RECURRING ] Новый заказ на продление подписки
EOD;
