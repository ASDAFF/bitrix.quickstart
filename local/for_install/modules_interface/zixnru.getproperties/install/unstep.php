<?php

/*
 * Code is distributed as-is
 * the Developer may change the code at its discretion without prior notice
 * Developers: Djo 
 * Website: http://zixn.ru
 * Twitter: https://twitter.com/Zixnru
 * Email: izm@zixn.ru
 */

if (!check_bitrix_sessid())
    return;
?>
<?
IncludeModuleLangFile(__FILE__);
echo CAdminMessage::ShowNote(GetMessage('ZIXNRU_GETPROP_MODULE_UNSTALL_NOTI',array('MODULE_ID'=>'zixnru.getproperties')));
?>