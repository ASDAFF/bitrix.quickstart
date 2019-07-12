<?php
$arFields["ID"]= <<<EOD
28
EOD;
$arFields["EVENT_NAME"]= <<<EOD
SALE_ORDER_REMIND_PAYMENT
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
#SITE_NAME#: Напоминание об оплате заказа N#ORDER_ID# 
EOD;
$arFields["MESSAGE"]= <<<EOD
Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Уважаемый #ORDER_USER#,

Вами был оформлен заказ N #ORDER_ID# от #ORDER_DATE# на сумму #PRICE#.

К сожалению, на сегодняшний день средства по этому заказу не поступили к нам. 

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
[ SALE_ORDER_REMIND_PAYMENT ] Напоминание об оплате заказа
EOD;
