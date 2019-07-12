<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CCacheManager $CACHE_MANAGER
 * @global CDatabase $DB
 * @param CBitrixComponent $this
 * @param array $this->arParams
 * @param array $this->arResult
 */

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

global $APPLICATION;

$items = array();
$rsSalepoint = \Bitrix\Catalog\StoreTable::getList();
while ($salepoint = $rsSalepoint->fetch())
{
	if (!empty($salepoint['GPS_N']) && !empty($salepoint['GPS_S'])) {
		$salepoint['loc'] = array($salepoint['GPS_N'], $salepoint['GPS_S']);
	}
	$items[] = $salepoint; 
}

if (!empty($items))
{
	$APPLICATION->AddHeadScript('//api-maps.yandex.ru/2.1/?lang=ru_RU');
	echo '<script>window.mm_map_salepoints = ' . CUtil::PhpToJSObject($items) . ';</script>';
	$this->IncludeComponentTemplate();
}