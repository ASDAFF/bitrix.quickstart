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
	 Bitrix\Main\Web\Json,
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

use Api\Auth\Tools;
use Api\Auth\SettingsTable as Settings;

//Настройки модуля
$arSettings = Settings::getAll();

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$result = array(
	 'TYPE'    => 'ERROR',
	 'MESSAGE' => '',
);



//Если включено шифрование
if(Option::get('main', 'use_encrypted_auth', 'N') == 'Y') { //$_REQUEST['__RSA_DATA']

	$sec = new CRsaSecurity();
	if(($arKeys = $sec->LoadKeys())) {

		$sec->SetKeys($arKeys);
		$err = $sec->AcceptFromForm(array('FIELDS'));

		if($err == CRsaSecurity::ERROR_SESS_CHECK) {
			$result['MESSAGE'] = Loc::getMessage('AARA_ERROR_SESS_CHECK');
		}

		if($err < 0) {
			$result['MESSAGE'] = Loc::getMessage('AARA_ERROR_RSA_DECODE', array('#CODE#' => $err));
		}
	}
}


//Парметры авторизации главного модуля
$bConfirmEmail = Option::get('main', 'new_user_registration_email_confirmation', 'Y') == 'Y';
$bCheckEmail   = Option::get('main', 'new_user_email_uniq_check', 'Y') == 'Y';


//Обязательные поля
$reqFields = (array)$_REQUEST['REQUIRED_FIELDS'];

//Данные формы регистрации
$formData = (array)$_REQUEST['FIELDS'];


$bEmail    = (strlen($formData['EMAIL']) > 0);
$bPassword = (strlen($formData['PASSWORD']) > 0);

//Валидация полей формы
foreach($formData as $key => &$val) {
	if(is_array($val)) {
		foreach($val as $k => $v) {
			$val[ $k ] = trim($v);
		}
	}
	else {
		$val = trim($val);
	}

	if(in_array($key, $reqFields) && !$val)
		$result['MESSAGE'] = Loc::getMessage('AARA_ERROR_FIELD', array('#FIELD#' => Loc::getMessage('AARA_' . $key)));
}

if($bCheckEmail && !$result['MESSAGE'] && !check_email($formData['EMAIL'])) {
	$result['MESSAGE'] = Loc::getMessage('AARA_ERROR_CHECK_EMAIL');
}


//Автогенерация пароля
$def_group = Option::get('main', 'new_user_registration_def_group', '');
if(!$bPassword) {
	if($def_group != '') {
		$groupID  = explode(',', $def_group);
		$arPolicy = $USER->GetGroupPolicy($groupID);
	}
	else {
		$arPolicy = $USER->GetGroupPolicy(array());
	}
	$password_min_length = ($arPolicy['PASSWORD_LENGTH'] ? $arPolicy['PASSWORD_LENGTH'] : 6);

	$password_chars = array(
		//'abcdefghijklnmopqrstuvwxyz',
		'ABCDEFGHIJKLNMOPQRSTUVWXYZ',
		'0123456789',
	);
	if($arPolicy['PASSWORD_PUNCTUATION'] == 'Y')
		$password_chars[] = ",.<>/?;:'\'[]{}\|`~!@#\$%^&*()-_+=";

	$formData['PASSWORD'] = $formData['CONFIRM_PASSWORD'] = randString($password_min_length, $password_chars);;
}


if($arSettings['USER_CONSENT_ID']){
	$userConsentError = array_diff(
		 (array)$arSettings['USER_CONSENT_ID'], (array)$_REQUEST['USER_CONSENT_ID']
	);
	if($userConsentError){
		$result['MESSAGE'] = $arSettings['MESS_PRIVACY_CONFIRM'];
	}
	unset($userConsentError);
}



if(!$result['MESSAGE']) {

	$userData = array(
		 'LAST_NAME'        => $formData['LAST_NAME'],
		 'NAME'             => $formData['NAME'],
		 'LOGIN'            => $formData['LOGIN'],
		 'EMAIL'            => $formData['EMAIL'],
		 'PASSWORD'         => $formData['PASSWORD'],
		 'CONFIRM_PASSWORD' => $formData['CONFIRM_PASSWORD'],
		 'ACTIVE'           => $bConfirmEmail ? 'N' : 'Y',
		 'CHECKWORD'        => md5(CMain::GetServerUniqID() . uniqid()),
		 '~CHECKWORD_TIME'  => $DB->CurrentTimeFunction(),
		 'CONFIRM_CODE'     => $bConfirmEmail ? randString(8) : 'Y',
		 'LID'              => SITE_ID,
		 'LANGUAGE_ID'      => LANGUAGE_ID,
		 'USER_IP'          => $request->getRemoteAddress(),
		 'USER_HOST'        => @gethostbyaddr($request->getRemoteAddress()),
	);

	if($formData['GROUP_ID']) {
		if(in_array(1, $formData['GROUP_ID']))
			unset($formData['GROUP_ID']);
	}

	if($def_group != '' && !$formData['GROUP_ID'])
		$userData['GROUP_ID'] = explode(",", $def_group);

	if(!$userData['LOGIN'])
		$userData['LOGIN'] = md5(bitrix_sessid() . uniqid(rand(), true));

	$userData = array_merge($formData, $userData);


	$bOk    = true;
	$events = GetModuleEvents('main', 'OnBeforeUserRegister', true);
	foreach($events as $arEvent) {
		if(ExecuteModuleEventEx($arEvent, array(&$userData)) === false) {
			if($err = $APPLICATION->GetException())
				$result['MESSAGE'] .= $err->GetString();

			$bOk = false;
			break;
		}
	}

	//---------- Создаем пользователя ----------//
	$user = new CUser;

	$userId = 0;
	if($bOk) {
		$userId = $user->Add($userData);
	}

	if(intval($userId) > 0) {
		$register_done = true;

		//Вернет ошибку: Логин должен быть не менее 3 символов.
		//$user->Update($userId, array('LOGIN' => $userId));

		$userData['USER_ID']    = $userId;
		$userData['URL_LOGIN']  = urlencode($userData['LOGIN']);
		$userData['SERVER_URL'] = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();

		$event = new CEvent;
		if($bConfirmEmail) {
			$result = array(
				 'TYPE'    => 'SUCCESS',
				 'MESSAGE' => Loc::getMessage('AARA_REGISTER_CONFIRM'),
			);
			$event->SendImmediate('API_AUTH_NEW_USER_CONFIRM', SITE_ID, $userData);
		}
		else {

			//Пробуем авторизовать пользователя
			//$USER->Authorize($userId, true);
			if(!$authResult = $USER->Login($userData['LOGIN'], $userData['PASSWORD'], 'Y', 'Y')) {
				$result = $authResult;
			}
			else {

				//CUser::SendUserInfo($USER->GetID(), $context->getSite(), 'Вы успешно зарегистрированы', true);
				$result = array(
					 'TYPE'    => 'SUCCESS',
					 'MESSAGE' => Loc::getMessage('AARA_REGISTER_SUCCESS'),
				);
			}
			$event->SendImmediate('API_AUTH_NEW_USER', SITE_ID, $userData);
		}

		//logUserConsent
		if($arSettings['USER_CONSENT_ID']){
			Tools::logUserConsent($arSettings['USER_CONSENT_ID'], 'api:auth.register');
		}
	}
	else {
		$result['MESSAGE'] = $user->LAST_ERROR;
	}

	/*if(count($arResult['ERRORS']) <= 0)
	{
		if(COption::GetOptionString('main', 'event_log_register', 'N') === 'Y')
			CEventLog::Log('SECURITY', 'USER_REGISTER', 'main', $userId);
	}
	else
	{
		if(COption::GetOptionString('main', 'event_log_register_fail', 'N') === 'Y')
			CEventLog::Log('SECURITY', 'USER_REGISTER_FAIL', 'main', $userId, implode('<br>', $arResult['ERRORS']));
	}*/

	$events = GetModuleEvents('main', 'OnAfterUserRegister', true);
	foreach($events as $arEvent) {
		ExecuteModuleEventEx($arEvent, array(&$userData));
	}
}



$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo Json::encode($result);
die();