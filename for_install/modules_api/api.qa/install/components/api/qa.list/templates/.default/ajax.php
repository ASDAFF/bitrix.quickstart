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

if($_SERVER['REQUEST_METHOD'] != 'POST' || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['SITE_ID']))
	die();

define('SITE_ID', htmlspecialchars($_POST['SITE_ID']));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION, $USER;

use Bitrix\Main\Loader,
	 Bitrix\Main\Type\DateTime,
	 Bitrix\Main\Application,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Text\Encoding;


Loc::loadMessages(dirname(__FILE__) . '/template.php');

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();

$post = $request->getPostList()->toArray();
if(!Application::isUtfMode())
	$post = Encoding::convertEncoding($post, 'UTF-8', $context->getCulture()->getCharset());

$form = &$post['FORM'];
//$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

if(!Loader::includeModule('api.qa'))
	die();

if(!Loader::includeModule('iblock'))
	die();

if(!$request->isPost() || !$post)
	die();

if($post['API_QA_LIST_AJAX'] != 'Y')
	die();


if(!$post['IBLOCK_ID'] && (!$post['ELEMENT_ID'] || !$post['XML_ID'] || !$post['CODE']))
	die();

use Api\QA\QuestionTable,
	 Api\QA\Converter,
	 Api\QA\Tools,
	 Api\QA\Event;


//Bitrix\Main\Localization\Loc::loadMessages(dirname(__FILE__).'/class.php');

//---------- Подготовим необходимые параметры ----------//
$isEditor = ($APPLICATION->GetGroupRight('api.qa') >= 'W');
$return   = array(
	 'status'   => 'error',
	 'message'  => '',
	 'errors'   => array(), //! required type in PHP 7
	 'type'     => '',
	 'parentId' => 0,
	 'id'       => 0,
	 'html'     => '',
);


$siteId     = trim($post['SITE_ID']);
$typeId     = trim($form['TYPE']);
$levelId    = intval($form['LEVEL']);
$guestName  = htmlspecialcharsbx(trim($form['GUEST_NAME']));
$guestEmail = htmlspecialcharsbx(trim($form['GUEST_EMAIL']));
$text       = Tools::formatText($form['TEXT'], true);
$notify     = ($form['NOTIFY'] == 'Y' ? 'Y' : 'N');
$parentId   = intval($form['PARENT_ID']);
$userId     = intval($USER->GetID());
$ip         = $request->getRemoteAddress();
$httpHost   = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();
$hash       = (substr($post['HASH'], 0, 4) != 'http' ? htmlspecialcharsbx($post['HASH']) : '');
$active     = ($isEditor ? 'Y' : $post['ACTIVE']);

if($active == 'A') {
	$active = ($userId ? 'Y' : 'N');
}

//Q - вопрос, A - официальный ответ, C - комментарий
$arAvailTypes = array('Q', 'A', 'C');
$typeId       = ($typeId && in_array($typeId, $arAvailTypes) ? $typeId : 'Q');

if(!$isEditor && $typeId == 'A')
	$typeId = 'C';


//---------- Проверим обязательные поля ----------//
$reqFields = array('TEXT');
if(!$USER->IsAuthorized()) {
	$reqFields = array('GUEST_NAME', 'GUEST_EMAIL', 'TEXT');
}



foreach($reqFields as $code) {
	if(!$form[ $code ]) {
		$return['errors'][ $code ] = Loc::getMessage('API_QALIST_AJAX_ERROR_' . $code);
	}

	if($code == 'GUEST_EMAIL') {
		if(!check_email($form[ $code ]))
			$return['errors'][ $code ] = Loc::getMessage('API_QALIST_AJAX_ERROR_EMAIL_VALID');
	}
}


/*
if(!$post['email']) {
	$return = array(
		 'status'  => 'error',
		 'message' => $arParams['MESS_ERROR_EMAIL'],
	);
}

if($post['email'] && !check_email($post['email'])) {
	$return = array(
		 'status'  => 'error',
		 'message' => $arParams['MESS_ERROR_CHECK_EMAIL'],
	);
}
*/

//---------- Обработка данных ----------//
if(!check_bitrix_sessid()) {
	$return = array(
		 'status'  => 'error',
		 'message' => Loc::getMessage('API_QALIST_AJAX_ERROR_SESSION_EXPIRED'),
	);
}
elseif(!$form) {
	$return = array(
		 'status'  => 'error',
		 'message' => Loc::getMessage('API_QALIST_AJAX_ERROR_EMPTY_FORM'),
	);
}
elseif(!$return['errors']) {

	//---------- Ищем товар в базе ----------//
	$arElFilter = array(
		 '=IBLOCK_ID' => intval($post['IBLOCK_ID']),
	);

	if($post['ELEMENT_ID'])
		$arElFilter['=ID'] = intval($post['ELEMENT_ID']);

	if($post['XML_ID'])
		$arElFilter['=XML_ID'] = htmlspecialcharsbx($post['XML_ID']);

	if($post['CODE'])
		$arElFilter['=CODE'] = htmlspecialcharsbx($post['CODE']);


	if(count($arElFilter) < 2)
		die();


	$rsElement = \CIBlockElement::GetList(
		 false,
		 $arElFilter,
		 false,
		 array('nTopCount' => 1),
		 array('ID', 'NAME', 'DETAIL_PAGE_URL')
	);

	if($arElement = $rsElement->GetNext(false, false)) {

		$arItem = array(
			 'DATE_CREATE' => new DateTime(),
			 'ACTIVE'      => $active,
			 'TYPE'        => $typeId,
			 'LEVEL'       => $levelId,
			 'PARENT_ID'   => $parentId,
			 'USER_ID'     => $userId,
			 'GUEST_NAME'  => $guestName,
			 'GUEST_EMAIL' => $guestEmail,
			 'TEXT'        => $text,
			 'NOTIFY'      => $notify,
			 'IP'          => $ip,
			 'SITE_ID'     => $siteId,
		);

		if($post['PAGE_URL'] && $post['PAGE_URL'] != $arElement['DETAIL_PAGE_URL'])
			$arItem['PAGE_URL'] = $post['PAGE_URL'];

		if($post['PAGE_TITLE'] && $post['PAGE_TITLE'] != $arElement['NAME'])
			$arItem['PAGE_TITLE'] = $post['PAGE_TITLE'];


		$arItem['IBLOCK_ID'] = $arElement['IBLOCK_ID'];

		if($post['ELEMENT_ID'])
			$arItem['ELEMENT_ID'] = $arElement['ID'];

		if($post['XML_ID'])
			$arItem['XML_ID'] = $arElement['XML_ID'];

		if($post['CODE'])
			$arItem['CODE'] = $arElement['CODE'];

		$result = QuestionTable::add($arItem);

		$html   = '';
		$status = 'error';
		if($result->isSuccess()) {

			$arItem['HTTP_HOST']  = $httpHost;
			$arItem['PAGE_URL']   = $httpHost . ($arItem['PAGE_URL'] ? $arItem['PAGE_URL'] : $arElement['DETAIL_PAGE_URL']);
			$arItem['PAGE_TITLE'] = $arItem['PAGE_TITLE'] ? $arItem['PAGE_TITLE'] : $arElement['NAME'];
			$arItem['HASH']       = (substr($post['HASH'], 0, 4) != 'http' ? htmlspecialcharsbx($post['HASH']) : '');

			$arItem['ID']   = $result->getId();
			$arItem['URL']  = str_replace('#ID#', $arItem['ID'], $arItem['PAGE_URL'] . $arItem['HASH']);
			$arItem['TEXT'] = Converter::replace($arItem['TEXT']);

			$user = array();
			if($USER->IsAuthorized()) {
				$user['NAME']  = $USER->GetFormattedName();
				$user['EMAIL'] = $USER->GetEmail();
			}

			if(!$arItem['GUEST_NAME'] && $user['NAME'])
				$arItem['GUEST_NAME'] = $user['NAME'];

			if(!$arItem['GUEST_EMAIL'] && $user['EMAIL'])
				$arItem['GUEST_EMAIL'] = $user['EMAIL'];

			$arItem['PICTURE'] = Tools::getGravatar($arItem['GUEST_EMAIL']);
			$arItem['USER']    = $user;

			$arItem['ADMIN_EMAIL'] = $post['ADMIN_EMAIL'];

			$siteDateFormat = \CSite::GetDateFormat();
			$arItem['DATE_CREATE'] = Tools::formatDate($post['DATE_FORMAT'], MakeTimeStamp($arItem['DATE_CREATE'], $siteDateFormat));

			$arParams              = $post;
			$arParams['IS_EDITOR'] = $isEditor;


			if($active == 'Y') {

				$status = 'ok';

				ob_start();
				include 'ajax_tpl.php';
				$html = ob_get_contents();
				ob_end_clean();

				//Delete components cache
				BXClearCache(true, "/{$siteId}/api/qa.recent");
			}
			else {
				$status = 'moderation';
				$html   = $post['MESS_ACTIVE'];
			}

			//Mail
			Event::sendAdd($arItem);
		}

		$return = array(
			 'status'   => $status,
			 'message'  => ($result->isSuccess() ? $post['MESS_SUCCESS_ADD'] : $post['MESS_ERROR_ADD']),
			 'type'     => $typeId,
			 'parentId' => $parentId,
			 'id'       => $arItem['ID'],
			 'html'     => $html,
		);
	}
	else {
		$return = array(
			 'status'  => 'error',
			 'message' => $post['MESS_ELEMENT_NOT_FOUND'],
		);
	}
}


$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo Json::encode($return);
//CMain::FinalActions();
die();