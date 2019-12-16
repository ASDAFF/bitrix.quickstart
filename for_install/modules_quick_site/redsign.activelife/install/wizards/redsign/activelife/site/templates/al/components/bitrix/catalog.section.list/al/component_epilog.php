<?php

use \Bitrix\Main\Page\Asset;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

$Asset = Asset::getInstance();
$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/jquery.menu-aim/jquery.menu-aim.js');

