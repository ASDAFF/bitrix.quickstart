<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//==============================================================================
//                             component.php
//==============================================================================
$MESS['API_SEARCH_MODULE_ERROR'] = 'Модуль «Умный поиск элементов» не установлен';
$MESS['IBLOCK_MODULE_ERROR']     = 'Модуль «Инфоблоки» не установлен';

$MESS['API_SEARCH_PAGE_FIELD_TAGS'] = 'Теги';

// Multilanguage phrases replace
$MESS['API_SEARCH_PAGE_INPUT_PLACEHOLDER'] = '';
$MESS['API_SEARCH_PAGE_BUTTON_TEXT']       = '';
$MESS['API_SEARCH_PAGE_RESULT_NOT_FOUND']  = '';

$MESS['API_SEARCH_PAGE_RESULT_FOUND_MESS']    = array('Найден', 'Найдено', 'Найдено');
$MESS['API_SEARCH_PAGE_RESULT_ITEMS_MESS']    = array('результат', 'результата', 'результатов');
$MESS['API_SEARCH_PAGE_RESULT_SECTIONS_MESS'] = array('категории', 'категориях', 'категориях');
$MESS['API_SEARCH_PAGE_RESULT_ITEMS_TEXT']    = '#FOUND# <strong>#COUNT_ITEMS#</strong> #RESULT#';
$MESS['API_SEARCH_PAGE_RESULT_SECTIONS_TEXT'] = '#FOUND# <strong>#COUNT_ITEMS#</strong> #RESULT# в <strong>#COUNT_SECTIONS#</strong> #CATEGORY#';

//==============================================================================
//                             template.php
//==============================================================================
$MESS['API_SEARCH_PAGE_PRICE_EXT_MODE']   = 'от #PRICE# #MEASURE#';
$MESS['API_SEARCH_PAGE_MEASURE_EXT_MODE'] = 'за #VALUE# #UNIT#';

$MESS['API_SEARCH_PAGE_PRICE_SIMPLE_MODE']   = 'от #PRICE##MEASURE#';
$MESS['API_SEARCH_PAGE_MEASURE_SIMPLE_MODE'] = '';