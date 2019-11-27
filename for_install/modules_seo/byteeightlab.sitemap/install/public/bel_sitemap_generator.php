<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("byteeightlab.sitemap");
$Sitemap = new Sitemap;
$Sitemap->Generate();
?>