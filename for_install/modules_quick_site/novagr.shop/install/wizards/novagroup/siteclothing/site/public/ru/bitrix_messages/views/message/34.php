<?php
$arFields["ID"]= <<<EOD
34
EOD;
$arFields["EVENT_NAME"]= <<<EOD
VOTE_NEW
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
#SITE_NAME#: Новое голосование по опросу "[#VOTE_ID#] #VOTE_TITLE#"
EOD;
$arFields["MESSAGE"]= <<<EOD
Новое голосование по опросу

Опрос       - [#VOTE_ID#] #VOTE_TITLE#
Группа      - [#CHANNEL_ID#] #CHANNEL#

--------------------------------------------------------------

Посетитель  - [#VOTER_ID#] (#LOGIN#) #USER_NAME# [#STAT_GUEST_ID#]
Сессия      - #SESSION_ID#
IP адрес    - #IP#
Время       - #TIME#

Для просмотра данного голосования воспользуйтесь ссылкой:
http://#SERVER_NAME#/bitrix/admin/vote_user_results.php?EVENT_ID=#ID#&lang=ru


Для просмотра результирующей диаграммы опроса воспользуйтесь ссылкой:
http://#SERVER_NAME#/bitrix/admin/vote_results.php?lang=ru&VOTE_ID=#VOTE_ID#

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
27.12.2012 20:38:04
EOD;
$arFields["EVENT_TYPE"]= <<<EOD
[ VOTE_NEW ] Новое голосование
EOD;
