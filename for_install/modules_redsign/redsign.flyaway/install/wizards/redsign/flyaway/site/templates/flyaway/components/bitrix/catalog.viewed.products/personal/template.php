<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){
    die();
}
$this->setFrameMode(true);

if ($arResult['TEMPLATE_DEFAULT']['TEMPLATE'] == 'showcase' || $arResult['TEMPLATE_DEFAULT']['TEMPLATE'] == 'showcase_mob') {
	include ($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/template_ext/catalog.section/flyaway/showcase.php");
} elseif ($arResult['TEMPLATE_DEFAULT']['TEMPLATE'] == 'list') {
	include ($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/template_ext/catalog.section/flyaway/showcase.php");
} elseif ($arResult['TEMPLATE_DEFAULT']['TEMPLATE'] == 'list_little') {
	include ($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/template_ext/catalog.section/flyaway/showcase.php");
}