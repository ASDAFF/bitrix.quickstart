<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
 
CModule::IncludeModule('yandexparser');
 
yandexSoap::startAgent();