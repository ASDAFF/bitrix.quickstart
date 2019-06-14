<?php
/**
 * Bitrix vars
 *
 * @var CDatabase $DB
 * @var CUser     $USER
 * @var CMain     $APPLICATION
 *
 */

define('PUBLIC_AJAX_MODE', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

if($_SERVER['REQUEST_METHOD'] != 'POST' || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['siteId']))
	die();

define('SITE_ID', htmlspecialchars($_POST['siteId']));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
if(!check_bitrix_sessid())
	die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Text\Encoding;

Loader::includeModule('api.reviews');

use \Api\Reviews\SubscribeTable;

global $APPLICATION, $USER;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();
//$form   = $request->getPostList()->toArray();
//$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);


//if(!Bitrix\Main\Loader::includeModule('sale')) // || !$request->get('action')
//	return;

//Bitrix\Main\Localization\Loc::loadMessages(dirname(__FILE__).'/class.php');

//Получаем данные
$return   = array();
$siteId   = $request->getPost('siteId');
$arParams = $request->getPost('params');
$form     = $request->getPost('form');

//Конвертируем данные
if(!Application::isUtfMode())
	$form = Encoding::convertEncoding($form, 'UTF-8', $context->getCulture()->getCharset());


//Обрабатываем данные
if($form) {
	foreach($form as $key => $val)
		$form[ $key ] = htmlspecialcharsbx(trim($val));
}
else {
	$return = array(
		 'status'  => 'error',
		 'message' => $arParams['MESS_ERROR'],
	);
}


if(!$form['email']) {
	$return = array(
		 'status'  => 'error',
		 'message' => $arParams['MESS_ERROR_EMAIL'],
	);
}

if($form['email'] && !check_email($form['email'])) {
	$return = array(
		 'status'  => 'error',
		 'message' => $arParams['MESS_ERROR_CHECK_EMAIL'],
	);
}

if(!$return) {



	//Фильтр
	$arFilter = array(
		 'SITE_ID' => $siteId,
		 'EMAIL'   => $form['email'],
	);

	if($arParams['IBLOCK_ID'])
		$arFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];

	if($arParams['SECTION_ID'])
		$arFilter['SECTION_ID'] = $arParams['SECTION_ID'];

	if($arParams['ELEMENT_ID'])
		$arFilter['ELEMENT_ID'] = $arParams['ELEMENT_ID'];

	if($arParams['URL'])
		$arFilter['URL'] = $arParams['URL'];



	$rsSubscribe = SubscribeTable::getList(array(
		 'filter' => $arFilter,
	));

	if($row = $rsSubscribe->fetch()) {
		$result = SubscribeTable::delete($row['ID']);
		$return = array(
			 'status'  => 'ok',
			 'message' => ($result->isSuccess() ? $arParams['MESS_UNSUBSCRIBE'] : $arParams['MESS_ERROR']),
		);
	}
	else {
		if($USER->IsAuthorized())
			$arFilter['USER_ID'] = intval($USER->GetID());

		$result = SubscribeTable::add($arFilter);
		$return = array(
			 'status'  => 'ok',
			 'message' => ($result->isSuccess() ? $arParams['MESS_SUBSCRIBE'] : $arParams['MESS_ERROR']),
		);
	}
}

if(!Application::isUtfMode())
	$return = Encoding::convertEncoding($return, 'UTF-8', $context->getCulture()->getCharset());


$APPLICATION->RestartBuffer();
echo \Bitrix\Main\Web\Json::encode($return);
CMain::FinalActions();
die();