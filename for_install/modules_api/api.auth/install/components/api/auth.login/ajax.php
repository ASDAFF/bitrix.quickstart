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
	 Bitrix\Main\Text,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Application;

if($_SERVER['REQUEST_METHOD'] != 'POST' || !$_POST['API_AUTH_LOGIN_AJAX'] || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['siteId']))
	die();

define('SITE_ID', htmlspecialchars($_POST['siteId']));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

Loc::loadMessages(__FILE__);

global $APPLICATION, $USER;

if(!Loader::includeModule('api.auth'))
	die();

use Api\Auth\Tools;
use Api\Auth\SettingsTable as Settings;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$result = array(
	 'TYPE'    => 'ERROR',
	 'MESSAGE' => '',
	 'CAPTCHA' => false,
);


if($action = $request->getPost('api_action')) {

	if($action == 'getCaptcha') {
		$captchaCode       = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
		$result['CAPTCHA'] = array(
			 'SID' => $captchaCode,
			 'SRC' => '/bitrix/tools/captcha.php?captcha_sid=' . $captchaCode,
		);
	}

	$APPLICATION->RestartBuffer();
	header('Content-Type: application/json');
	echo Json::encode($result);
	die();
}


//Если включено шифрование
if(Option::get('main', 'use_encrypted_auth', 'N') == 'Y') {

	$sec = new CRsaSecurity();
	if(($arKeys = $sec->LoadKeys())) {

		$sec->SetKeys($arKeys);
		$err = $sec->AcceptFromForm(array('PASSWORD'));

		if($err == CRsaSecurity::ERROR_SESS_CHECK) {
			$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_SESS_CHECK');
		}

		if($err < 0) {
			$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_RSA_DECODE', array("#CODE#" => $err));
		}
	}
}


//Данные формы авторизации
$formData = (array)$_REQUEST;
foreach($formData as &$data) {
	$data = is_array($data) ? $data : trim($data);
}

if(!Application::isUtfMode())
	$formData = Text\Encoding::convertEncoding($formData, 'UTF-8', $context->getCulture()->getCharset());


//Настройки модуля
$arSettings = Settings::getAll();
$arAuthFields = $arSettings['AUTH_FIELDS'];


if(!check_bitrix_sessid())
	$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_SESS_CHECK');
elseif(!$arAuthFields)
	$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_SETTINGS');
elseif(!$formData['LOGIN'])
	$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_LOGIN', array('#FIELD#' => $formData['messLogin']));
elseif(!$formData['PASSWORD'])
	$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_PASSWORD');

if(!$result['MESSAGE']) {
	foreach($arAuthFields as $field) {

		$select = array('ID', 'ACTIVE', 'LOGIN', 'EMAIL', 'PERSONAL_PHONE');
		$filter = array(
			 '=EXTERNAL_AUTH_ID' => '',
			 '=' . $field => $formData['LOGIN']
		);

		$arUser = UserTable::getRow(array(
			 'select' => $select,
			 'filter' => $filter,
		));

		if($arUser['ID']) {
			if($arUser['ACTIVE'] == 'Y'){
				$authResult = $USER->Login($arUser['LOGIN'], $formData['PASSWORD'], "Y");
				if($authResult['TYPE'] == 'ERROR') {

					$result['MESSAGE'] = $authResult['MESSAGE'];

					if($APPLICATION->NeedCAPTHAForLogin($formData['LOGIN'])) {
						$captchaCode       = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
						$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_CAPTCHA');
						$result['CAPTCHA'] = array(
							 'SID' => $captchaCode,
							 'SRC' => '/bitrix/tools/captcha.php?captcha_sid=' . $captchaCode,
						);
					}
				}
				else {

					//$ttfile=dirname(__FILE__).'/1_txt.php';
					//file_put_contents($ttfile, "<pre>".print_r($_SESSION['SPREAD_COOKIE'],1)."</pre>\n");

					//special login form in the control panel
					if($authResult === true)
					{
						//store cookies for next hit (see CMain::GetSpreadCookieHTML())
						//bitrix/modules/main/include.php:455
						$APPLICATION->StoreCookies();
						//$APPLICATION->SetAuthResult($authResult);
					}

					//$ttfile=dirname(__FILE__).'/1_txt.php';
					//file_put_contents($ttfile, "<pre>".print_r($_SESSION['SPREAD_COOKIE'],1)."</pre>\n", FILE_APPEND);

					$result = array(
						 'TYPE'    => 'SUCCESS',
						 'MESSAGE' => $formData['messSuccess'],
					);
				}
			}
			else{
				$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_ACTIVE');
			}

			//Если пользователь найден, выходим из цикла
			break;
		}
		else {
			$result['MESSAGE'] = Loc::getMessage('AALA_ERROR_SEARCH');
		}
	}
}



$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo Json::encode($result);
die();