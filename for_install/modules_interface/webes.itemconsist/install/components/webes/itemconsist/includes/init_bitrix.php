<?php
GLOBAL $PATH,$connection,$USER,$DB;

//define('STOP_STATISTICS', true);
ob_start();
ob_clean();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog.php");
//$GLOBALS['APPLICATION']->RestartBuffer();
ob_end_clean();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

Loader::includeModule("iblock");

$DBL=CDatabase::GetModuleConnection("main");
$connection=new mysqli($DBL->DBHost, $DBL->DBLogin, $DBL->DBPassword, $DBL->DBName);
$connection->set_charset((mb_strtolower(LANG_CHARSET)!='utf-8'?'cp1251':'utf8'));

if ($USER->IsAdmin())$AUTH=1;
else $AUTH=0;


?>