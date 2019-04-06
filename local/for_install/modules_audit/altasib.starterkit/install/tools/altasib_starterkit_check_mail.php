<?php
if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.starterkit/tools/altasib_starterkit_check_mail.php")){
    include_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/altasib.starterkit/tools/altasib_starterkit_check_mail.php");
}else{
    include_once ($_SERVER["DOCUMENT_ROOT"] . "/local/modules/altasib.starterkit/tools/altasib_starterkit_check_mail.php");
}
