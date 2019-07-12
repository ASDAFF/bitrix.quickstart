<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

use \Bitrix\Main\Localization\Loc;

$arParams['BLOCK_TITLE'] = Loc::getMessage('RS.MONOPOLY.CWP_TITLE');

$frame = $this->createFrame()->begin();

$sIncludeFilePath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/mini/template.php';

if (file_exists($sIncludeFilePath)) {
    include($sIncludeFilePath);
}
$frame->beginStub();
$frame->end();