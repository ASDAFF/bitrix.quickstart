<?php
$_SERVER["DOCUMENT_ROOT"] = '/var/www';
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');
 
CModule::IncludeModule('remains');

$remainUpdater = new remainUpdater();

$remainUpdater->scanDir();