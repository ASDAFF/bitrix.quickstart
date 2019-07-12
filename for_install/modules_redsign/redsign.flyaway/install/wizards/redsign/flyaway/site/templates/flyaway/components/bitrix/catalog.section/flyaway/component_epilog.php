<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

use \Bitrix\Main\Page\Asset;

$Asset = Asset::getInstance();
$Asset->addJs(SITE_TEMPLATE_PATH.'/template_ext/catalog.section/flyaway/script.js');


$sIncludeFilePath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/flyaway/component_epilog.php';

if (file_exists($sIncludeFilePath)) {
    include($sIncludeFilePath);
}