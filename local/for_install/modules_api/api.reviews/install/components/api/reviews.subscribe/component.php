<?php
use \Bitrix\Main\Loader,
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
	ShowError(GetMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

$arParams['SITE_ID']                = SITE_ID;
$arParams['AJAX_URL']               = trim($arParams['~AJAX_URL']);
$arParams['MESS_LINK']              = trim($arParams['~MESS_LINK']);
$arParams['MESS_SUBSCRIBE']         = trim($arParams['~MESS_SUBSCRIBE']);
$arParams['MESS_UNSUBSCRIBE']       = trim($arParams['~MESS_UNSUBSCRIBE']);
$arParams['MESS_FIELD_PLACEHOLDER'] = trim($arParams['~MESS_FIELD_PLACEHOLDER']);
$arParams['MESS_BUTTON_TEXT']       = trim($arParams['~MESS_BUTTON_TEXT']);
$arParams['MESS_ERROR']             = trim($arParams['~MESS_ERROR']);
$arParams['MESS_ERROR_EMAIL']       = trim($arParams['~MESS_ERROR_EMAIL']);
$arParams['MESS_ERROR_CHECK_EMAIL'] = trim($arParams['~MESS_ERROR_CHECK_EMAIL']);


$this->IncludeComponentTemplate();