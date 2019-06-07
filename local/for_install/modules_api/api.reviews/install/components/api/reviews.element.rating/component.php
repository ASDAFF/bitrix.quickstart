<?php
use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var string           $parentComponentPath
 * @var string           $parentComponentName
 * @var string           $parentComponentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.reviews')) {
	ShowError(Loc::getMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

use \Api\Reviews\ReviewsTable;

//Inc template lang
$templateFile = CApiReviews::getTemplateFile($this);
Loc::loadMessages($templateFile);


//BASE
$arParams['INCLUDE_CSS']  = $arParams['INCLUDE_CSS'] == 'Y';
$arParams['REVIEWS_LINK'] = trim($arParams['~REVIEWS_LINK']);
$arParams['THEME']        = ($arParams['THEME'] ? $arParams['THEME'] : 'flat');
$arParams['COLOR']        = ($arParams['COLOR'] ? $arParams['COLOR'] : 'orange1');

$arParams['IBLOCK_ID']  = (int)$arParams['~IBLOCK_ID'];
$arParams['SECTION_ID'] = (int)$arParams['~SECTION_ID'];
$arParams['ELEMENT_ID'] = (int)$arParams['~ELEMENT_ID'];
$arParams['ORDER_ID']   = trim($arParams['~ORDER_ID']);
$arParams['URL']        = trim($arParams['~URL']);

$arParams['MESS_FULL_RATING']  = trim($arParams['~MESS_FULL_RATING']);
$arParams['MESS_EMPTY_RATING'] = trim($arParams['~MESS_EMPTY_RATING']);


//---------- $arFilter ----------//
$arSelect = array('ID', 'RATING');
$arFilter = array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID);

if($arParams['IBLOCK_ID'])
	$arFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];

if($arParams['SECTION_ID'])
	$arFilter['SECTION_ID'] = $arParams['SECTION_ID'];

if($arParams['ELEMENT_ID'])
	$arFilter['ELEMENT_ID'] = $arParams['ELEMENT_ID'];

if($arParams['ORDER_ID'])
	$arFilter['ORDER_ID'] = $arParams['ORDER_ID'];

if($arParams['URL'])
	$arFilter['URL'] = $arParams['URL'];


//RATING STATISTIC
$rating = 0;

//PREPARE $arResult
$arResult['ITEMS']          = array();
$arResult['COUNT_REVIEWS']  = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
$arResult['COUNT_PROGRESS'] = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);


//---------- Work with result  ----------//
$rsReviews = ReviewsTable::getList(array(
	 'select' => $arSelect,
	 'filter' => $arFilter,
));
while($arItem = $rsReviews->Fetch()) {
	$RATING = (int)$arItem['RATING'];
	$rating += $RATING;
	$arResult['COUNT_REVIEWS'][ $RATING ] += 1;

	$arResult['ITEMS'][] = $arItem;
}
unset($arItem, $RATING);


//GET RATING STATISTICS
$arResult['COUNT_ITEMS']    = count($arResult['ITEMS']);
$arResult['AVERAGE_RATING'] = ($arResult['COUNT_ITEMS'] > 0) ? round(($rating / $arResult['COUNT_ITEMS']), 1) : $arResult['COUNT_ITEMS'];
$arResult['FULL_RATING']    = ($arResult['COUNT_ITEMS'] > 0) ? round(($rating / $arResult['COUNT_ITEMS']) * 20, 1) : $arResult['COUNT_ITEMS'];

if($arResult['ITEMS']) {
	foreach($arResult['ITEMS'] as $key => &$arItem) {
		$RATING         = (int)$arItem['RATING'];
		$COUNT_PROGRESS = round(($arResult['COUNT_REVIEWS'][ $RATING ] / $arResult['COUNT_ITEMS']) * 100, 1);

		$arResult['COUNT_PROGRESS'][ $RATING ] = $COUNT_PROGRESS;
	}
}
//\\GET RATING STATISTIC

$arResult['MESS_FULL_RATING'] = str_replace(
	 array('#RATING#', '#COUNT#'),
	 array($arResult['AVERAGE_RATING'], $arResult['COUNT_ITEMS']),
	 $arParams['MESS_FULL_RATING']
);

if($arParams['MESS_EMPTY_RATING'] && $arResult['COUNT_ITEMS'] == 0)
	$arResult['MESS_FULL_RATING'] = $arParams['MESS_EMPTY_RATING'];


$this->includeComponentTemplate();