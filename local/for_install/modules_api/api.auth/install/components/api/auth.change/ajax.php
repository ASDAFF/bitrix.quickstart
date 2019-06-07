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
	 Bitrix\Main\Text,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Application;

if($_SERVER['REQUEST_METHOD'] != 'POST' || !$_POST['API_AUTH_CHANGE_AJAX'] || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['siteId']))
	die();

define('SITE_ID', htmlspecialchars($_POST['siteId']));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION, $USER;

if(!Loader::includeModule('api.auth'))
	die();


$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$result = array(
	 'TYPE'    => 'ERROR',
	 'MESSAGE' => '',
);

if($request->isPost() && $request->getPost('API_AUTH_CHANGE_AJAX')) {

	//Если включено шифрование
	if(Option::get('main', 'use_encrypted_auth', 'N') == 'Y') {

		$sec = new CRsaSecurity();
		if(($arKeys = $sec->LoadKeys())) {

			$sec->SetKeys($arKeys);
			$err = $sec->AcceptFromForm(array('USER_PASSWORD', 'USER_CONFIRM_PASSWORD'));

			if($err == CRsaSecurity::ERROR_SESS_CHECK) {
				$result['MESSAGE'] = Loc::getMessage('AACA_ERROR_SESS_CHECK');
			}

			if($err < 0) {
				$result['MESSAGE'] = Loc::getMessage('AACA_ERROR_RSA_DECODE', array("#CODE#" => $err));
			}
		}
	}

	//Данные формы авторизации
	$formData = (array)$_REQUEST;

	if(!Application::isUtfMode())
		$formData = Text\Encoding::convertEncoding($formData, 'UTF-8', $context->getCulture()->getCharset());

	foreach($formData as &$data) {
		$data = is_array($data) ? $data : trim($data);
	}


	$params = array(
		 'LOGIN'            => $formData['USER_LOGIN'],
		 'CHECKWORD'        => $formData['USER_CHECKWORD'],
		 'PASSWORD'         => $formData['USER_PASSWORD'],
		 'CONFIRM_PASSWORD' => $formData['USER_CONFIRM_PASSWORD'],
		 'SITE_ID'          => SITE_ID,
		 'captcha_word'     => $formData['captcha_word'],
		 'captcha_sid'      => $formData['captcha_sid'],
	);

	$result = Api\Auth\User::change($params);

	/*$result = $USER->ChangePassword(
		 $formData['USER_LOGIN'],
		 $formData['USER_CHECKWORD'],
		 $formData['USER_PASSWORD'],
		 $formData['USER_CONFIRM_PASSWORD'],
		 SITE_ID,
		 $formData['captcha_word'],
		 $formData['captcha_sid']
	);*/
}


$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo Json::encode($result);
die();