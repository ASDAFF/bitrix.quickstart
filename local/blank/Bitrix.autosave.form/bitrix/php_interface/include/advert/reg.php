<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

$arrResult = array('result' => 'fail', 'error_message' => '', 'error_field' => array());

$strSessid = htmlspecialcharsEx(trim($_REQUEST['sessid']));
$strEmail = htmlspecialcharsEx(trim($_REQUEST['email']));
$strCaptchaWord = htmlspecialcharsEx(trim($_REQUEST['captcha_word']));
$strCaptchaSid = htmlspecialcharsEx(trim($_REQUEST['captcha_sid']));

if (check_bitrix_sessid() || !strlen($strCaptchaSid) || !strlen($strSessid)) {
	$arrErrorField = array();
	if (!strlen($strEmail)) $arrErrorField['REG_EMAIL'] = 'Поле E-mail обязательное, заполните его.';
	if (!strlen($strCaptchaWord)) $arrErrorField['REG_CAPTCHA_WORD'] = 'Поле цифры обязательное, заполните его.';
	if (!count($arrErrorField)) {
		if (strlen($strEmail) < 6) $arrErrorField['REG_EMAIL'] = 'Минимальное количество символов 6 символов.';
		if (strlen($strCaptchaWord) < 5) $arrErrorField['REG_CAPTCHA_WORD'] = 'Минимальное количество символов 5 символов.';
		if (!count($arrErrorField)) {
			if (!check_email($strEmail)) $arrErrorField['REG_EMAIL'] = 'Вы ввели не правильный E-Mail.';
			if (!$APPLICATION->CaptchaCheckCode($strCaptchaWord, $strCaptchaSid)) $arrErrorField['REG_CAPTCHA_WORD'] = 'Вы ввели не правильные цифры с картинки.';
			if (!count($arrErrorField)) {
				// Проверим уникальность E-Mail
				$rsUser = CUser::GetByLogin($strEmail);
				if ($arUser = $rsUser->Fetch()) {
					// Такой пользователь есть
					$arrResult['error_message'] = 'Указанный E-Mail уже зарегистрирован в базе сайта. Данные авторизации были отправлены Вам на электронный адрес. Если Вы утеряли их, то всегда можете восстановить перейдя по ссылке забыли пароль в форме авторизации.';
					$arrErrorField['REG_EMAIL'] = ' ';
				} else {
					// Это новый пользователь
					// Регим его
					$strPassword = randString();
					$arResult = $USER->Register($strEmail, '', '', $strPassword, $strPassword, $strEmail);
					if ($arResult['ID']) {
						$arrResult['result'] = 'ok';
						$arrResult['login'] = $strEmail;
						$arrResult['password'] = $strPassword;
					} else $arrResult['error_message'] = 'Регистрация не удалась.';
				}//\\ if
			}//\\ if
		}//\\ if
	}//\\ if
	$arrResult['error_field'] = $arrErrorField;
} else {
	$arrResult['error_message'] = 'Ваша сессия устарела';
}//\\ if

$arrResult['captcha_sid'] = $APPLICATION->CaptchaGetCode();

echo json_encode($arrResult);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");