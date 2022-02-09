<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$TYPE_ID = $_REQUEST['id'];
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tc/include.php");
CModule::IncludeModule('iblock');
$APPLICATION->SetTitle('Модерация комментариев');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); 
?><iframe src="/api/forum_moderate.php?PAGE_NAME=list&FID=1" style="width: 100%; height: 700px;"><?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");