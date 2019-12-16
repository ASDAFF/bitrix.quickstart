<?php

use \Bitrix\Main\Page\Asset;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

$Asset = Asset::getInstance();
$Asset->addJs(SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/script.js');

$sTemplateExtPath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/component_epilog.php';
if (file_exists($sTemplateExtPath)) {
    include($sTemplateExtPath);    
}