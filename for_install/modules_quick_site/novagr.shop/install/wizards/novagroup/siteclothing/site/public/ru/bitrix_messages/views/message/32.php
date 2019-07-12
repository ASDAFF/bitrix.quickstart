<?php
$arFields["ID"]= <<<EOD
32
EOD;
$arFields["EVENT_NAME"]= <<<EOD
VIRUS_DETECTED
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
#SITE_NAME#: Обнаружен вирус
EOD;
$arFields["MESSAGE"]= <<<EOD
Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Здравствуйте!

Вы получили это сообщение, так как модуль проактивной защиты сервера #SERVER_NAME# обнаружил код, похожий на вирус.

1. Подозрительный код был вырезан из html.
2. Проверьте журнал вторжений и убедитесь, что код действительно вредоносный, а не является кодом какого-либо счетчика или фреймворка.
 (ссылка: http://#SERVER_NAME#/bitrix/admin/event_log.php?lang=ru&set_filter=Y&find_type=audit_type_id&find_audit_type[]=SECURITY_VIRUS )
3. В случае, если код не является опасным, добавьте его в исключения на странице настройки антивируса.
 (ссылка: http://#SERVER_NAME#/bitrix/admin/security_antivirus.php?lang=ru&tabControl_active_tab=exceptions )
4. Если код является вирусным, то необходимо выполнить следующие действия:

 а) Смените пароли доступа к сайту у администраторов и ответственных сотрудников.
 б) Смените пароли доступа по ssh и ftp.
 в) Проверьте и вылечите компьютеры администраторов, имевших доступ к сайту по ssh или ftp.
 г) В программах доступа к сайту по ssh и ftp отключите сохранение паролей.
 д) Удалите вредоносный код из зараженных файлов. Например, восстановите поврежденные файлы из самой свежей резервной копии.

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
27.12.2012 20:37:58
EOD;
$arFields["EVENT_TYPE"]= <<<EOD
[ VIRUS_DETECTED ] Обнаружен вирус
EOD;
