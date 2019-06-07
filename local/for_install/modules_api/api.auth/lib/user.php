<?php

namespace Api\Auth;

use \Bitrix\Main\Config\Option,
	 \Bitrix\Main\Application,
	 \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class User
{

	//Аналог: $USER->SendPassword($arUser['LOGIN'], $arUser['EMAIL'], SITE_ID);
	/**
	 * @param        LOGIN
	 * @param        EMAIL
	 * @param        bool   SITE_ID
	 * @param        string captcha_word
	 * @param        int    captcha_sid
	 *
	 * @return array
	 */
	public static function restore($params)
	{
		/** @global \CMain $APPLICATION */
		global $DB, $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		$arParams = array(
			 'LOGIN'        => '',
			 'EMAIL'        => '',
			 'SITE_ID'      => false,
			 'captcha_word' => '',
			 'captcha_sid'  => 0,
		);

		$arParams = array_merge($arParams, $params);

		$result_message = array('MESSAGE' => Loc::getMessage('ACCOUNT_INFO_SENT') . '<br>', 'TYPE' => 'OK');
		$APPLICATION->ResetException();
		$bOk = true;
		foreach(GetModuleEvents('main', 'OnBeforeUserSendPassword', true) as $arEvent) {
			if(ExecuteModuleEventEx($arEvent, array(&$arParams)) === false) {
				if($err = $APPLICATION->GetException())
					$result_message = array('MESSAGE' => $err->GetString() . '<br>', 'TYPE' => 'ERROR');

				$bOk = false;
				break;
			}
		}

		if($bOk && Option::get('main', 'captcha_restoring_password', 'N') == 'Y') {
			if(!($APPLICATION->CaptchaCheckCode($arParams['captcha_word'], $arParams['captcha_sid']))) {
				$result_message = array('MESSAGE' => Loc::getMessage('main_user_captcha_error') . '<br>', 'TYPE' => 'ERROR');
				$bOk            = false;
			}
		}

		if($bOk) {
			$f = false;
			if($arParams['LOGIN'] <> '' || $arParams['EMAIL'] <> '') {
				$confirmation = (Option::get('main', 'new_user_registration_email_confirmation', 'N') == 'Y');

				$strSql = '';
				if($arParams['LOGIN'] <> '') {
					$strSql =
						 "SELECT ID, LID, ACTIVE, CONFIRM_CODE, LOGIN, EMAIL, NAME, LAST_NAME, LANGUAGE_ID " .
						 "FROM b_user u " .
						 "WHERE LOGIN='" . $DB->ForSQL($arParams["LOGIN"]) . "' " .
						 "	AND (ACTIVE='Y' OR NOT(CONFIRM_CODE IS NULL OR CONFIRM_CODE='')) " .
						 "	AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') ";
				}
				if($arParams['EMAIL'] <> '') {
					if($strSql <> '') {
						$strSql .= "\nUNION\n";
					}
					$strSql .=
						 "SELECT ID, LID, ACTIVE, CONFIRM_CODE, LOGIN, EMAIL, NAME, LAST_NAME, LANGUAGE_ID " .
						 "FROM b_user u " .
						 "WHERE EMAIL='" . $DB->ForSQL($arParams["EMAIL"]) . "' " .
						 "	AND (ACTIVE='Y' OR NOT(CONFIRM_CODE IS NULL OR CONFIRM_CODE='')) " .
						 "	AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') ";
				}
				$res = $DB->Query($strSql);

				while($arUser = $res->Fetch()) {
					if($arParams['SITE_ID'] === false) {
						if(defined('ADMIN_SECTION') && ADMIN_SECTION === true)
							$arParams['SITE_ID'] = \CSite::GetDefSite($arUser['LID']);
						else
							$arParams['SITE_ID'] = SITE_ID;
					}

					if($arUser['ACTIVE'] == 'Y') {
						static::SendUserInfo($arUser['ID'], $arParams, Loc::getMessage('AALU_RESTORE_INFO_REQ'), true, 'API_AUTH_RESTORE');
						$f = true;
					}
					elseif($confirmation) {
						//unconfirmed registration - resend confirmation email
						$arFields = array(
							 'USER_ID'      => $arUser['ID'],
							 'LOGIN'        => $arUser['LOGIN'],
							 'EMAIL'        => $arUser['EMAIL'],
							 'NAME'         => $arUser['NAME'],
							 'LAST_NAME'    => $arUser['LAST_NAME'],
							 'CONFIRM_CODE' => $arUser['CONFIRM_CODE'],
							 'USER_IP'      => $_SERVER['REMOTE_ADDR'],
							 'USER_HOST'    => @gethostbyaddr($_SERVER['REMOTE_ADDR']),

							 //NEW
							 'SERVER_URL'   => ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost(),
						);

						$event = new \CEvent;
						$event->SendImmediate('API_AUTH_CONFIRM', $arParams['SITE_ID'], $arFields, 'Y', "", array(), $arUser['LANGUAGE_ID']);

						$result_message = array('MESSAGE' => Loc::getMessage('MAIN_SEND_PASS_CONFIRM') . '<br>', 'TYPE' => 'OK');

						$f = true;
					}

					if(Option::get('main', 'event_log_password_request', 'N') === 'Y') {
						\CEventLog::Log('SECURITY', 'USER_INFO', 'main', $arUser['ID']);
					}
				}
			}
			if(!$f) {
				return array('MESSAGE' => Loc::getMessage('DATA_NOT_FOUND') . '<br>', 'TYPE' => 'ERROR');
			}
		}
		return $result_message;
	}


	//Аналог: $USER->ChangePassword()

	/**
	 * @param        $LOGIN
	 * @param        $CHECKWORD
	 * @param        $PASSWORD
	 * @param        $CONFIRM_PASSWORD
	 * @param bool   $SITE_ID
	 * @param string $captcha_word
	 * @param int    $captcha_sid
	 *
	 * @return array
	 */
	public static function change($params)
	{
		/** @global \CMain $APPLICATION */
		global $DB, $APPLICATION;

		$result_message = array('MESSAGE' => Loc::getMessage('PASSWORD_CHANGE_OK') . '<br>', 'TYPE' => 'OK');

		$arParams = array(
			 'LOGIN'            => '',
			 'CHECKWORD'        => '',
			 'PASSWORD'         => '',
			 'CONFIRM_PASSWORD' => '',
			 'SITE_ID'          => false,
			 'captcha_word'     => '',
			 'captcha_sid'      => 0,
		);

		$arParams = array_merge($arParams, $params);


		$APPLICATION->ResetException();
		$bOk = true;
		foreach(GetModuleEvents('main', 'OnBeforeUserChangePassword', true) as $arEvent) {
			if(ExecuteModuleEventEx($arEvent, array(&$arParams)) === false) {
				if($err = $APPLICATION->GetException())
					$result_message = array('MESSAGE' => $err->GetString() . '<br>', 'TYPE' => 'ERROR');

				$bOk = false;
				break;
			}
		}

		if($bOk && Option::get('main', 'captcha_restoring_password', 'N') == 'Y') {
			if(!($APPLICATION->CaptchaCheckCode($arParams['captcha_word'], $arParams['captcha_sid']))) {
				$result_message = array('MESSAGE' => Loc::getMessage('main_user_captcha_error') . '<br>', 'TYPE' => 'ERROR');
				$bOk            = false;
			}
		}

		if($bOk) {
			$strAuthError = "";
			if(strlen($arParams['LOGIN']) < 3)
				$strAuthError .= Loc::getMessage('MIN_LOGIN') . '<br>';
			if($arParams['PASSWORD'] <> $arParams['CONFIRM_PASSWORD'])
				$strAuthError .= Loc::getMessage('WRONG_CONFIRMATION') . '<br>';

			if($strAuthError <> '')
				return array('MESSAGE' => $strAuthError, 'TYPE' => 'ERROR');

			\CTimeZone::Disable();
			$db_check = $DB->Query(
				 "SELECT ID, LID, CHECKWORD, " . $DB->DateToCharFunction("CHECKWORD_TIME", "FULL") . " as CHECKWORD_TIME " .
				 "FROM b_user " .
				 "WHERE LOGIN='" . $DB->ForSql($arParams["LOGIN"], 0) . "' AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='')");
			\CTimeZone::Enable();

			if(!($res = $db_check->Fetch()))
				return array(
					 'MESSAGE' => preg_replace('/#LOGIN#/i', htmlspecialcharsbx($arParams['LOGIN']), Loc::getMessage('LOGIN_NOT_FOUND')),
					 'TYPE'    => 'ERROR',
					 'FIELD'   => 'LOGIN',
				);

			$salt = substr($res['CHECKWORD'], 0, 8);
			if($res['CHECKWORD'] == '' || $res['CHECKWORD'] != $salt . md5($salt . $arParams['CHECKWORD']))
				return array(
					 'MESSAGE' => preg_replace('/#LOGIN#/i', htmlspecialcharsbx($arParams['LOGIN']), Loc::getMessage('CHECKWORD_INCORRECT')) . '<br>',
					 'TYPE'    => 'ERROR',
					 'FIELD'   => 'CHECKWORD',
				);

			$arPolicy = \CUser::GetGroupPolicy($res['ID']);

			$passwordErrors = static::CheckPasswordAgainstPolicy($arParams['PASSWORD'], $arPolicy);
			if(!empty($passwordErrors)) {
				return array(
					 'MESSAGE' => implode('<br>', $passwordErrors) . '<br>',
					 'TYPE'    => 'ERROR',
				);
			}

			$site_format = \CSite::GetDateFormat();
			if(time() - $arPolicy['CHECKWORD_TIMEOUT'] * 60 > MakeTimeStamp($res['CHECKWORD_TIME'], $site_format))
				return array(
					 'MESSAGE' => preg_replace('/#LOGIN#/i', htmlspecialcharsbx($arParams['LOGIN']), Loc::getMessage('CHECKWORD_EXPIRE')) . '<br>',
					 'TYPE'    => 'ERROR',
					 'FIELD'   => 'CHECKWORD_EXPIRE',
				);

			if($arParams['SITE_ID'] === false) {
				if(defined('ADMIN_SECTION') && ADMIN_SECTION === true)
					$arParams['SITE_ID'] = \CSite::GetDefSite($res['LID']);
				else
					$arParams['SITE_ID'] = SITE_ID;
			}

			// change the password
			$ID     = $res['ID'];
			$obUser = new \CUser;
			$res    = $obUser->Update($ID, array('PASSWORD' => $arParams['PASSWORD']));
			if(!$res && $obUser->LAST_ERROR <> '')
				return array(
					 'MESSAGE' => $obUser->LAST_ERROR . '<br>',
					 'TYPE'    => 'ERROR',
				);


			//Отправит пароль на почту после смены
			static::SendUserInfo(
				 $ID,
				 $arParams,
				 Loc::getMessage('AALU_CHANGE_PASS_OK', array(
						'#LOGIN#'    => $arParams['LOGIN'],
						'#PASSWORD#' => $arParams['PASSWORD'],
				 )),
				 true,
				 'API_AUTH_CHANGE'
			);
		}

		return $result_message;
	}


	//Base Bitrix

	/**
	 * Sends a profile information to email
	 *
	 * @param        $ID
	 * @param        $params
	 * @param        $MSG
	 * @param bool   $bImmediate
	 * @param string $eventName
	 *
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function sendUserInfo($ID, $params, $MSG, $bImmediate = false, $eventName = "USER_INFO")
	{
		global $DB;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		$SITE_ID = $params['SITE_ID'];

		// change CHECKWORD
		$ID        = intval($ID);
		$salt      = randString(8);
		$checkword = md5(\CMain::GetServerUniqID() . uniqid());
		$strSql    = "UPDATE b_user SET " .
			 "	CHECKWORD = '" . $salt . md5($salt . $checkword) . "', " .
			 "	CHECKWORD_TIME = " . $DB->CurrentTimeFunction() . ", " .
			 "	LID = '" . $DB->ForSql($SITE_ID, 2) . "', " .
			 "   TIMESTAMP_X = TIMESTAMP_X " .
			 "WHERE ID = '" . $ID . "'" .
			 "	AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') ";

		$DB->Query($strSql, false, "FILE: " . __FILE__ . "<br> LINE: " . __LINE__);

		$res = $DB->Query(
			 "SELECT u.* " .
			 "FROM b_user u " .
			 "WHERE ID='" . $ID . "'" .
			 "	AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') "
		);

		if($res_array = $res->Fetch()) {
			$event    = new \CEvent;
			$arFields = array(
				 'USER_ID'    => $res_array['ID'],
				 'STATUS'     => ($res_array['ACTIVE'] == 'Y' ? GetMessage('STATUS_ACTIVE') : GetMessage('STATUS_BLOCKED')),
				 'MESSAGE'    => $MSG,
				 'LOGIN'      => $res_array['LOGIN'],
				 'PASSWORD'   => $params['PASSWORD'],
				 'URL_LOGIN'  => urlencode($res_array['LOGIN']),
				 'CHECKWORD'  => $checkword,
				 'NAME'       => $res_array['NAME'],
				 'LAST_NAME'  => $res_array['LAST_NAME'],
				 'EMAIL'      => $res_array['EMAIL'],

				 //NEW
				 'SERVER_URL' => ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost(),
			);

			$arParams = array(
				 'FIELDS'      => &$arFields,
				 'USER_FIELDS' => $res_array,
				 'SITE_ID'     => &$SITE_ID,
				 'EVENT_NAME'  => &$eventName,
			);

			foreach(GetModuleEvents('main', 'OnSendUserInfo', true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array(&$arParams));

			if(!$bImmediate)
				$event->Send($eventName, $SITE_ID, $arFields, 'Y', "", array(), $res_array['LANGUAGE_ID']);
			else
				$event->SendImmediate($eventName, $SITE_ID, $arFields, 'Y', "", array(), $res_array['LANGUAGE_ID']);
		}
	}


	protected static function CheckPasswordAgainstPolicy($password, $arPolicy)
	{
		$errors = array();

		$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);

		if($password_min_length <= 0)
			$password_min_length = 6;

		if(strlen($password) < $password_min_length)
			$errors[] = Loc::getMessage("MAIN_FUNCTION_REGISTER_PASSWORD_LENGTH", array("#LENGTH#" => $arPolicy["PASSWORD_LENGTH"]));

		if(($arPolicy["PASSWORD_UPPERCASE"] === "Y") && !preg_match("/[A-Z]/", $password))
			$errors[] = Loc::getMessage("MAIN_FUNCTION_REGISTER_PASSWORD_UPPERCASE");

		if(($arPolicy["PASSWORD_LOWERCASE"] === "Y") && !preg_match("/[a-z]/", $password))
			$errors[] = Loc::getMessage("MAIN_FUNCTION_REGISTER_PASSWORD_LOWERCASE");

		if(($arPolicy["PASSWORD_DIGITS"] === "Y") && !preg_match("/[0-9]/", $password))
			$errors[] = Loc::getMessage("MAIN_FUNCTION_REGISTER_PASSWORD_DIGITS");

		if(($arPolicy["PASSWORD_PUNCTUATION"] === "Y") && !preg_match("/[,.<>\\/?;:'\"[\\]\\{\\}\\\\|`~!@#\$%^&*()_+=-]/", $password))
			$errors[] = Loc::getMessage("MAIN_FUNCTION_REGISTER_PASSWORD_PUNCTUATION");

		return $errors;
	}

}