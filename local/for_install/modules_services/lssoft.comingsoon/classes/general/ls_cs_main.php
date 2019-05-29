<?php

/**
 * Основной класс модуля
 */
class CLsCsMain {
	/**
	 * Обработка события "OnBeforeProlog"
	 */
	public static function OnBeforeProlog() {
		if (self::CheckNeedShowComing()) {
			self::ShowComing();
		}
	}
	/**
	 * Проверяет необходимость показа страницы "Coming soon" (заглушки)
	 *
	 * @return bool
	 */
	public static function CheckNeedShowComing() {
		global $USER,$APPLICATION;
		if (!CSite::GetByID(SITE_ID)->Fetch()) {
			return false;
		}
		/**
		 * Для гостей показываем заглушку
		 */
		if (!$USER->IsAuthorized()) {
			return true;
		}
		/**
		 * Для админов не показываем
		 */
		if ($USER->IsAdmin()) {
			return false;
		}
		/**
		 * Не показываем тем, у кого есть права на пропуск заглушки
		 */
		if ($APPLICATION->GetUserRight('lssoft.comingsoon')>='R') {
			return false;
		}
		return true;
	}
	/**
	 *	Формирует данные и подключает компонент для отображения заглушки
	 */
	public static function ShowComing() {
		global $APPLICATION;
				
		if (self::GetOptionSite('LS_CS_ENABLED')!='Y') {
			return;
		}
		/**
		 * Получаем текущий сайт
		 */
		$aSiteItem=CSite::GetByID(SITE_ID)->Fetch();
		/**
		 * Определяем тему оформления
		 */
		$sTheme=self::GetOptionSite('LS_CS_THEME_CUSTOM');
		if (!$sTheme) {
			$sTheme=self::GetOptionSite('LS_CS_THEME');
		}
		/**
		 * Подключаем компонент
		 */
		$APPLICATION->IncludeComponent("lssoft:cs",'.default',array(
				'SHOW_TIMER' => true,
				'THEME' => $sTheme,
				'TITLE' => self::GetOptionSite('LS_CS_TITLE'),
				'DESCRIPTION' => self::GetOptionSite('LS_CS_DESCRIPTION'),
				'MAIL' => self::GetOptionSite('LS_CS_MAIL'),
				'LOGO' => self::GetOptionSite('LS_CS_LOGO'),
				'TIMER' => self::GetOptionSite('LS_CS_TIMER'),
				'TIMER_DATE' => self::GetOptionSite('LS_CS_TIMER_DATE'),
				'LIKE' => self::GetOptionSite('LS_CS_LIKE'),
				'SHARE' => array(
					'FB'=>self::GetOptionSite('LS_CS_SHARE_FB'),
					'TW'=>self::GetOptionSite('LS_CS_SHARE_TW'),
					'VK'=>self::GetOptionSite('LS_CS_SHARE_VK'),
					'ODN'=>self::GetOptionSite('LS_CS_SHARE_ODN'),
					'GP'=>self::GetOptionSite('LS_CS_SHARE_GP'),
				),
				'INVITE_ENABLED' => self::GetOptionSite('LS_CS_INVITE_ENABLED'),
				'INVITE_NEED_LOGIN' => self::GetOptionSite('LS_CS_INVITE_NEED_LOGIN'),

				'_SITE_DIR' => $aSiteItem['DIR'],
			),
			false
		);
		die();
	}
	/**
	 * Враппер для удобного получения настроек модуля для текущего сайта
	 *
	 * @param string $sName
	 * @param string $sDefault
	 *
	 * @return mixed
	 */
	public static function GetOptionSite($sName,$sDefault='') {
		return COption::GetOptionString('lssoft.comingsoon',$sName.'_'.SITE_ID,$sDefault);
	}
	/**
	 * Производит регистрацию пользователя
	 *
	 * @param string $sLogin
	 * @param string $sMail
	 * @param string $sSiteId
	 *
	 * @return array|bool
	 */
	public static function RegisterUser($sLogin,$sMail,$sSiteId) {
		global $APPLICATION, $DB, $REMOTE_ADDR;

		/**
		 * Проверяем незанятость логина и емайла
		 */
		if (COption::GetOptionString('main','new_user_email_uniq_check','N')=='Y') {
    		if (CUser::GetList($by='id',$order='desc',array('=EMAIL'=>$sMail))->Fetch()) {
    			return false;
    		}
    	}
		/**
		 * Проверяем корректность логин
		 */
		if (CUser::GetByLogin($sLogin)->Fetch()) {
    		return false;
    	}
		/**
		 * Запускаем процесс регистрации
		 */
		$APPLICATION->ResetException();

		$sCheckWord = md5(CMain::GetServerUniqID().uniqid());
		$aFields = array(
			"CHECKWORD" => $sCheckWord,
			"~CHECKWORD_TIME" => $DB->CurrentTimeFunction(),
			"EMAIL" => $sMail,
			"LOGIN" => $sLogin,
			"ACTIVE" => "Y",
			"NAME"=>"",
			"LAST_NAME"=>"",
			"USER_IP"=>$REMOTE_ADDR,
			"USER_HOST"=>@gethostbyaddr($REMOTE_ADDR),
			"SITE_ID" => $sSiteId
		);

		$sDefGroup=COption::GetOptionString("main","new_user_registration_def_group",'');
		if($sDefGroup!='') {
			$aFields["GROUP_ID"]=explode(',',$sDefGroup);
			$aPolicy=CUser::GetGroupPolicy($aFields["GROUP_ID"]);
		} else {
			$aPolicy=CUser::GetGroupPolicy(array());
		}
		$iPasswordMinLength = intval($aPolicy["PASSWORD_LENGTH"]);
		if($iPasswordMinLength<=0) {
			$iPasswordMinLength=6;
		}
		$aPasswordChars = array(
			"abcdefghijklnmopqrstuvwxyz",
			"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
			"0123456789",
		);
		if($aPolicy["PASSWORD_PUNCTUATION"] === "Y") {
			$aPasswordChars[] = ",.<>/?;:'\"[]{}\\|`~!@#\$%^&*()-_+=";
		}
		$aFields["PASSWORD"]=$aFields["CONFIRM_PASSWORD"]=randString($iPasswordMinLength, $aPasswordChars);

		$bOk=true;
		$aResultMessage=null;
		foreach(GetModuleEvents("main", "OnBeforeUserSimpleRegister", true) as $arEvent) {
			if(ExecuteModuleEventEx($arEvent, array(&$aFields)) === false) {
				if($err = $APPLICATION->GetException()) {
					$aResultMessage = array("MESSAGE"=>$err->GetString()."<br>", "TYPE"=>"ERROR");
				} else {
					$APPLICATION->ThrowException("Unknown error");
					$aResultMessage = array("MESSAGE"=>"Unknown error"."<br>", "TYPE"=>"ERROR");
				}
				$bOk = false;
				break;
			}
		}

		$iId=0;
		if($bOk) {
			$aFields["LID"] = $aFields["SITE_ID"];
			$aFields["CHECKWORD"] = $sCheckWord;
			$oUser=new CUser();
			if($iId = $oUser->Add($aFields)) {
				$aFields["USER_ID"] = $iId;
				$aResultMessage = array("MESSAGE"=>GetMessage("USER_REGISTER_OK"), "TYPE"=>"OK");
			} else {
				$aResultMessage = array("MESSAGE"=>"ERROR", "TYPE"=>"ERROR");
			}
		}

		if(is_array($aResultMessage)) {
			if($aResultMessage["TYPE"] == "OK") {
				if(COption::GetOptionString("main", "event_log_register", "N") === "Y") {
					$aResLog["user"] = $aFields["LOGIN"];
					CEventLog::Log("SECURITY", "USER_REGISTER", "main", $iId, serialize($aResLog));
				}
			} else {
				if(COption::GetOptionString("main", "event_log_register_fail", "N") === "Y") {
					CEventLog::Log("SECURITY", "USER_REGISTER_FAIL", "main", $iId, $aResultMessage["MESSAGE"]);
				}
			}
		}

		$aFields["RESULT_MESSAGE"] = $aResultMessage;
		foreach(GetModuleEvents("main", "OnAfterUserSimpleRegister", true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, array(&$aFields));
		}

		if ($aFields["RESULT_MESSAGE"] and isset($aFields["RESULT_MESSAGE"]['TYPE']) and $aFields["RESULT_MESSAGE"]['TYPE']=='OK') {
			return $aFields;
		} else {
			return false;
		}
	}
}