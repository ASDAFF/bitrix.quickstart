<?php
use \Bitrix\Main\Loader,
		\Bitrix\Main\Application,
		\Bitrix\Main\Localization\Loc;

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

//Inc template lang
$templateFile = CApiReviews::getTemplateFile($this);
Loc::loadMessages($templateFile);


use \Api\Reviews\ReviewsTable;

$cache        = Application::getInstance()->getCache();
$taggetCache  = Application::getInstance()->getTaggedCache();
$managedCache = Application::getInstance()->getManagedCache();

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();


//==============================================================================
//                     MULTILANGUAGE PHRASES REPLACE
//==============================================================================
$arParams['MESS_TOTAL_RATING']    = $arParams['~MESS_TOTAL_RATING'] ? $arParams['~MESS_TOTAL_RATING'] : Loc::getMessage('API_REVIEWS_STAT_MESS_TOTAL_RATING');
$arParams['MESS_CUSTOMER_RATING'] = $arParams['~MESS_CUSTOMER_RATING'] ? $arParams['~MESS_CUSTOMER_RATING'] : Loc::getMessage('API_REVIEWS_STAT_MESS_CUSTOMER_RATING');


//BASE
$arParams['THEME']              = ($arParams['THEME'] ? trim($arParams['THEME']) : 'orange');
$arParams['IBLOCK_ID']          = (int)$arParams['IBLOCK_ID'];
$arParams['SECTION_ID']         = (int)$arParams['SECTION_ID'];
$arParams['ELEMENT_ID']         = (int)$arParams['ELEMENT_ID'];
$arParams['ORDER_ID']           = trim($arParams['ORDER_ID']);
$arParams['URL']                = trim($arParams['URL']);
$arResult['MIN_AVERAGE_RATING'] = ($arParams['MIN_AVERAGE_RATING'] ? floatval($arParams['MIN_AVERAGE_RATING']) : 5);
$arParams['INCLUDE_CSS']        = $arParams['INCLUDE_CSS'] == 'Y';
$arParams['THEME']              = ($arParams['THEME'] ? $arParams['THEME'] : 'flat');


//CACHE
$arParams['CACHE_TYPE'] = trim($arParams['CACHE_TYPE']);
$arParams['CACHE_TIME'] = ($arParams['CACHE_TYPE'] != 'N') ? $arParams['CACHE_TIME'] : 0;



//---------- $arFilter ----------//
$arSelect = array('ID', 'RATING');
$arFilter = array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID);

if($arParams['IBLOCK_ID'])
	$arFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];

if($arParams['SECTION_ID'])
	$arFilter['SECTION_ID'] = $arParams['SECTION_ID'];

if($arParams['ELEMENT_ID'])
	$arFilter['ELEMENT_ID'] = $arParams['ELEMENT_ID'];

//if($arParams['ORDER_ID'])
//	$arFilter['ORDER_ID'] = $arParams['ORDER_ID'];

if($arParams['URL'])
	$arFilter['URL'] = $arParams['URL'];



//==============================================================================
//                             WORK WITH POST
//==============================================================================
$isPost = ($request->isPost() && $request->getPost('API_REVIEWS_STAT_AJAX') == 'Y' && check_bitrix_sessid());


//==============================================================================
//                      WORK WITH CACHE
//==============================================================================

//---------- Init cache ----------//
/*$cache_time = $arParams['CACHE_TIME'];
$cache_id   = $this->getCacheID();
$cache_path = $managedCache->getCompCachePath($this->__relativePath);

if($isPost) {
	$cache_time = 0;
}

if($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
	$arResult = $cache->getVars();
}
else {*/

	//Обновление кэша при аякс-изменениях
	/*if($cache_time == 0) {
		//$cache->clean($cache_id, $cache_path);
		$cache->cleanDir($cache_path);

		$cache_time = $arParams['CACHE_TIME'];
	}*/

	//$cache->abortDataCache();

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
	$arResult['COUNT_ITEMS']    = (count($arResult['ITEMS']) > 0) ? count($arResult['ITEMS']) : 1;
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


	if($arResult['AVERAGE_RATING'])
		$arResult['MIN_AVERAGE_RATING'] = $arResult['AVERAGE_RATING'];

	/*if($cache_time) {
		//начинаем буферизирование вывода
		$cache->startDataCache($cache_time, $cache_id, $cache_path);

		//Кэшируем переменные
		$cache->endDataCache($arResult);
	}
}*/

if($isPost) {
	$APPLICATION->RestartBuffer();
	$this->includeComponentTemplate('ajax');
	die();
}
else
	$this->includeComponentTemplate();
