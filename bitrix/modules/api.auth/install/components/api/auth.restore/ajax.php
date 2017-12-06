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
	 Bitrix\Main\Text,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\UserTable,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Application;

if($_SERVER['REQUEST_METHOD'] != 'POST' || !$_POST['API_AUTH_RESTORE_AJAX'] || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['siteId']))
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
);

$formData = $_REQUEST;
foreach($formData as &$data) {
	$data = is_array($data) ? $data : trim($data);
}

if(!Application::isUtfMode())
	$formData = Text\Encoding::convertEncoding($formData, 'UTF-8', $context->getCulture()->getCharset());


//Настройки модуля
$arSettings   = Settings::getAll();
$arAuthFields = $arSettings['AUTH_FIELDS'];


if(!check_bitrix_sessid())
	$result['MESSAGE'] = Loc::getMessage('AARA_ERROR_SESS_CHECK');
elseif(!$formData['LOGIN'])
	$result['MESSAGE'] = Loc::getMessage('AARA_ERROR_EMPTY_LOGIN');


if($arSettings['USER_CONSENT_ID']) {
	$userConsentError = array_diff(
		 (array)$arSettings['USER_CONSENT_ID'], (array)$formData['USER_CONSENT_ID']
	);
	if($userConsentError) {
		$result['MESSAGE'] = $arSettings['MESS_PRIVACY_CONFIRM'];
	}
	unset($userConsentError);
}


if(!$result['MESSAGE']) {

	$arLogin = array();
	foreach($arAuthFields as $field) {

		$select = array('ID', 'LOGIN', 'EMAIL', 'PERSONAL_PHONE', 'LID');
		$filter = array('=' . $field => $formData['LOGIN']);

		$arUser = UserTable::getRow(array(
			 'select' => $select,
			 'filter' => $filter,
		));

		if($arUser['ID']) {

			//$result = $USER->SendPassword($arUser['LOGIN'], $arUser['EMAIL'], SITE_ID);

			$params = array(
				 "LOGIN"   => $arUser['LOGIN'],
				 "EMAIL"   => $arUser['EMAIL'],
				 "SITE_ID" => $arUser['LID'] ? $arUser['LID'] : SITE_ID,
			);

			$result = Api\Auth\User::restore($params);

			//logUserConsent
			if($arSettings['USER_CONSENT_ID']){
				Tools::logUserConsent($arSettings['USER_CONSENT_ID'], 'api:auth.restore');
			}

			break;
		}
		else {
			$result['MESSAGE'] = Loc::getMessage('AARA_ERROR_FOUND_LOGIN');
		}
	}
}


$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo Json::encode($result);
die();