<?php
use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
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
	ShowError(GetMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

//Inc template lang
$templateFile = CApiReviews::getTemplateFile($this);
Loc::loadMessages($templateFile);


$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();

$arParams['SITE_ID']     = SITE_ID;
$arParams['THEME']       = ($arParams['THEME'] ? trim($arParams['THEME']) : 'orange');
$arParams['USE_STAT']    = $arParams['USE_STAT'] == 'Y';
$arParams['INCLUDE_CSS'] = $arParams['INCLUDE_CSS'] == 'Y';
$arParams['THEME']       = ($arParams['THEME'] ? $arParams['THEME'] : 'flat');

$arParams['SORT_FIELDS'] = (array)$arParams['SORT_FIELDS'];
foreach($arParams['SORT_FIELDS'] as $key => $val) {
	if($val == '' || (!$USER->IsAdmin() && $val == 'ACTIVE'))
		unset($arParams['SORT_FIELDS'][ $key ]);

	if(!$arParams['USE_STAT'] && $val == 'RATING')
		unset($arParams['SORT_FIELDS'][ $key ]);
}


$isPost = ($request->isPost() && $request->get('API_REVIEWS_SORT_AJAX') == 'Y');

if($isPost) {
	$APPLICATION->RestartBuffer();
	$this->includeComponentTemplate();
	$APPLICATION->FinalActions();
	die();
}
else
	$this->includeComponentTemplate();