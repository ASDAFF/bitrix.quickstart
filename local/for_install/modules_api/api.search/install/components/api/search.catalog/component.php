<?
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
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 * @var CCacheManager    $CACHE_MANAGER
 */

$arResultModules = array(
	'api.search' => CModule::IncludeModule('api.search'),
	'iblock'     => CModule::IncludeModule('iblock'),
);

if(!$arResultModules['api.search'])
{
	ShowError(GetMessage('API_SEARCH_MODULE_ERROR'));
	return;
}

if(!$arResultModules['iblock'])
{
	ShowError(GetMessage('API_SEARCH_IBLOCK_ERROR'));
	return;
}

global $MESS;
CApiSearch::incComponentLang($this);

$query         = preg_replace('/\s+/', ' ', trim(strip_tags($_REQUEST['q'])));
$arResult['q'] = $query;


$arResult['THEME_COMPONENT'] = $this->getParent();
if(!is_object($arResult['THEME_COMPONENT']))
	$arResult['THEME_COMPONENT'] = $this;

if(!isset($arParams['ELEMENT_SORT_FIELD2']))
	$arParams['ELEMENT_SORT_FIELD2'] = '';
if(!isset($arParams['ELEMENT_SORT_ORDER2']))
	$arParams['ELEMENT_SORT_ORDER2'] = '';
if(!isset($arParams['HIDE_NOT_AVAILABLE']))
	$arParams['HIDE_NOT_AVAILABLE'] = '';
if(!isset($arParams['OFFERS_SORT_FIELD2']))
	$arParams['OFFERS_SORT_FIELD2'] = '';
if(!isset($arParams['OFFERS_SORT_ORDER2']))
	$arParams['OFFERS_SORT_ORDER2'] = '';


$arResult['COMPONENT_ID'] = $this->GetEditAreaId($this->__currentCounter);

$this->IncludeComponentTemplate();