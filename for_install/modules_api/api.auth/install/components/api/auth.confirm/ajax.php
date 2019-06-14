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

use Bitrix\Main\Loader,
	 Bitrix\Main\Config\Option,
	 Bitrix\Main\UserTable,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Text,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Application;

if($_SERVER['REQUEST_METHOD'] != 'POST' || !$_POST['API_AUTH_REGISTER_AJAX'] || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['siteId']))
	die();

define('SITE_ID', htmlspecialchars($_POST['siteId']));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

Loc::loadMessages(__FILE__);

global $APPLICATION, $USER;

if(!Loader::includeModule('api.auth'))
	die();

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$result = array();

$formData = (array)$_REQUEST;

if(!Application::isUtfMode())
	$formData = Text\Encoding::convertEncoding($formData, 'UTF-8', $context->getCulture()->getCharset());

foreach($formData as &$data) {
	$data = trim($data);
}

$bConfirmEmail = Option::get('main', 'new_user_registration_email_confirmation', 'Y') == 'Y';
$bCheckEmail   = Option::get('main', 'new_user_email_uniq_check', 'Y') == 'Y';
$def_group     = Option::get('main', 'new_user_registration_def_group', '');

$bEmail    = (strlen($formData['EMAIL']) > 0);
$bPassword = (strlen($formData['PASSWORD']) > 0);


if(!$bEmail) {
	$result = array(
		 'TYPE'    => 'ERROR',
		 'MESSAGE' => Loc::getMessage('AACA_ERROR_EMPTY_EMAIL'),
	);
}

if($bCheckEmail && !check_email($formData['EMAIL'])) {
	$result = array(
		 'TYPE'    => 'ERROR',
		 'MESSAGE' => Loc::getMessage('AACA_ERROR_CHECK_EMAIL'),
	);
}

if(!$result) {

	/*
	$arSettings   = SettingsTable::getRow(array(
		 'filter' => array('=NAME' => 'AUTH_FIELDS'),
	));
	$arAuthFields = unserialize($arSettings['VALUE']);
	*/


	$select = array('ID', 'LOGIN', 'EMAIL', 'PERSONAL_PHONE');
	$filter = array('=EMAIL' => $formData['EMAIL']);

	$arUser = UserTable::getRow(array(
		 'select' => $select,
		 'filter' => $filter,
	));

	if(!$arUser['ID']) {

		$user = new CUser;

		if(!$bPassword) {
			//ƒлину парол€ просим у системы, например, если включен повышенный уровень безопасности и пароли длиньше 6
			if($def_group != "") {
				$groupID  = explode(",", $def_group);
				$arPolicy = $USER->GetGroupPolicy($groupID);
			}
			else {
				$arPolicy = $USER->GetGroupPolicy(array());
			}
			$password_min_length = ($arPolicy["PASSWORD_LENGTH"] ? $arPolicy["PASSWORD_LENGTH"] : 6);

			$password_chars = array(
				//"abcdefghijklnmopqrstuvwxyz",
				"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
				"0123456789",
			);
			if($arPolicy["PASSWORD_PUNCTUATION"] == "Y")
				$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";

			$formData['PASSWORD'] = $formData['PASSWORD_CONFIRM'] = randString($password_min_length, $password_chars);;
		}


		$userData = array(
			 'LOGIN'            => uniqid(),
			 'NAME'             => '',
			 'LAST_NAME'        => '',
			 'PASSWORD'         => $formData['PASSWORD'],
			 'PASSWORD_CONFIRM' => $formData['PASSWORD_CONFIRM'],
			 'EMAIL'            => $formData['EMAIL'],
			 'GROUP_ID'         => $groupID,
			 "ACTIVE"           => $bConfirmEmail ? 'N' : 'Y',
			 'CHECKWORD'        => md5(CMain::GetServerUniqID() . uniqid()),
			 '~CHECKWORD_TIME'  => $DB->CurrentTimeFunction(),
			 'CONFIRM_CODE'     => $bConfirmEmail ? randString(8) : 'Y',
			 "LID"              => $context->getSite(),
			 "LANGUAGE_ID"      => LANGUAGE_ID,
			 "USER_IP"          => $request->getRemoteAddress(),
			 "USER_HOST"        => @gethostbyaddr($request->getRemoteAddress()),
		);

		$bOk = true;

		$events = GetModuleEvents("main", "OnBeforeUserRegister", true);
		foreach($events as $arEvent) {
			if(ExecuteModuleEventEx($arEvent, array(&$userData)) === false) {
				if($err = $APPLICATION->GetException())
					$arResult['ERRORS'][] = $err->GetString();

				$bOk = false;
				break;
			}
		}

		//---------- —оздаем пользовател€ ----------//
		$userId = 0;
		if($bOk) {
			$userId = (int)$user->Add($userData);
		}

		//$arResult = $USER->SimpleRegister("admin@mysite.ru");


		if(intval($userId) > 0) {

			$userData['USER_ID']   = $userId;
			$userData['HTTP_HOST'] = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();
			$userData['CODE']      = md5($userData['USER_ID'] . $userData['CONFIRM_CODE']);

			/*
			$arPrint = array(
				 '$formData' => $formData,
				 '$userData' => $userData,
				 '$userId'   => $userId,
			);
			$tttfile = dirname(__FILE__) . '/1_txt.php';
			file_put_contents($tttfile, "<pre>" . print_r($arPrint, 1) . "</pre>\n");
			*/

			$event = new CEvent;
			if($bConfirmEmail) {
				$result = array(
					 'TYPE'    => 'SUCCESS',
					 'MESSAGE' => Loc::getMessage('AACA_REGISTER_CONFIRM'),
				);
				$event->SendImmediate("API_AUTH_NEW_USER_CONFIRM", SITE_ID, $userData);
			}
			else {

				$USER->Authorize($userId);
				if($USER->IsAuthorized()) {
					//CUser::SendUserInfo($USER->GetID(), $context->getSite(), '¬ы успешно зарегистрированы', true);
					$result = array(
						 'TYPE'    => 'SUCCESS',
						 'MESSAGE' => Loc::getMessage('AACA_REGISTER_SUCCESS'), //$USER->GetFormattedName()
					);
				}
				$event->SendImmediate("API_AUTH_NEW_USER", SITE_ID, $userData);
			}
		}
		else {
			$arResult["ERRORS"][] = $user->LAST_ERROR;
		}

		/*if(count($arResult["ERRORS"]) <= 0)
		{
			if(COption::GetOptionString("main", "event_log_register", "N") === "Y")
				CEventLog::Log("SECURITY", "USER_REGISTER", "main", $userId);
		}
		else
		{
			if(COption::GetOptionString("main", "event_log_register_fail", "N") === "Y")
				CEventLog::Log("SECURITY", "USER_REGISTER_FAIL", "main", $userId, implode("<br>", $arResult["ERRORS"]));
		}*/

		$events = GetModuleEvents("main", "OnAfterUserRegister", true);
		foreach($events as $arEvent) {
			ExecuteModuleEventEx($arEvent, array(&$userData));
		}
	}
	else {
		$result = array(
			 'TYPE'    => 'ERROR',
			 'MESSAGE' => Loc::getMessage('AACA_ERROR_ISSET_EMAIL'),
		);
	}
}

$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo Json::encode($result);
die();