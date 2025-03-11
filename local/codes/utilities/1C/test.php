<?php 
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__));
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
ini_set('memory_limit', '512M');
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true); 
define("CHK_EVENT", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
 
@set_time_limit(0);


CModule::IncludeModule('catalog');
CCatalog::PreGenerateXML("yandex");