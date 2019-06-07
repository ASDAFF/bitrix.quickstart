<?php
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

//ID компонента
//$cpId = $this->getEditAreaId($this->__currentCounter);

//Объект родительского компонента
//$parent = $this->getParent();
//$parentPath = $parent->getPath();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule("api.typo"))
{
	ShowError(GetMessage("API_TYPO_MODULE_ERROR"));
	return;
}

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();

//Inc template lang
if($this->initComponentTemplate())
{
	$template       = &$this->getTemplate()->GetFolder();
	$templateFolder = $server->getDocumentRoot() . $template . '/template.php';
	Loc::loadMessages($templateFolder);
}

//GROUP_BASE
$arParams['SITE_ID']    = SITE_ID;
$arParams['AJAX_URL']   = ($arParams['~AJAX_URL'] ? $arParams['~AJAX_URL'] : Loc::getMessage('API_TYPO_TPL_AJAX_URL'));
$arParams['MAX_LENGTH'] = ($arParams['MAX_LENGTH'] ? $arParams['MAX_LENGTH'] : 300);
$arParams['EMAIL_FROM'] = ($arParams['~EMAIL_FROM'] ? base64_encode(trim($arParams['~EMAIL_FROM'])) : '');
$arParams['EMAIL_TO']   = ($arParams['~EMAIL_TO'] ? base64_encode(trim($arParams['~EMAIL_TO'])) : '');
if($arParams['JQUERY_ON'] && $arParams['JQUERY_ON'] != 'N')
	CUtil::InitJSCore($arParams['JQUERY_ON']);


//GROUP_MESSAGE
$arParams['MESS_TPL_CONTENT']      = $arParams['~MESS_TPL_CONTENT'];
$arParams['MESS_ALERT_TEXT_MAX']   = $arParams['~MESS_ALERT_TEXT_MAX'];
$arParams['MESS_ALERT_TEXT_EMPTY'] = $arParams['~MESS_ALERT_TEXT_EMPTY'];
$arParams['MESS_ALERT_SEND_OK']    = $arParams['~MESS_ALERT_SEND_OK'];
$arParams['MESS_MODAL_TITLE']      = $arParams['~MESS_MODAL_TITLE'];
$arParams['MESS_MODAL_COMMENT']    = $arParams['~MESS_MODAL_COMMENT'];
$arParams['MESS_MODAL_SUBMIT']     = $arParams['~MESS_MODAL_SUBMIT'];
$arParams['MESS_MODAL_CLOSE']      = $arParams['~MESS_MODAL_CLOSE'];


$this->includeComponentTemplate();
