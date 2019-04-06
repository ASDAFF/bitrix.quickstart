<?php
IncludeModuleLangFile(__FILE__);

class CVCSMain {

	public static function _GetDriverDocRoot($options) {
		if (empty($options['is_full_path'])) {
			$path = CSite::GetSiteDocRoot($options['site']) . $options['doc_root'];
		} else {
			$path = $options['doc_root'];
		}

		$path = rtrim($path, ' \\/');

		return $path;
	}

	public static function GetDriversArray($arParams = array()) {
		$arDrivers = array();

		$arFilter = array();
		if (empty($arParams['all'])) {
			$arFilter['ACTIVE'] = 1;
		}

		$rsSysDrivers = CVCSDriversFactory::GetList(array(), $arFilter);
		while ($arSysDriver = $rsSysDrivers->Fetch()) {
			$options = unserialize($arSysDriver['SETTINGS']);
			$options['doc_root'] = CVCSMain::_GetDriverDocRoot($options);
			$arDrivers[$arSysDriver['DRIVER_CODE']] = array(
				'code' => $arSysDriver['DRIVER_CODE'],
				'name' => $arSysDriver['NAME'],
				'class_iterator' => 'CVCSDriverIteratorFiles',
				'class_item' => 'CVCSDriverItemFiles',
				'options' => $options,
			);
		}

		return $arDrivers;
	}

	public static function GetDriverByCode($code) {
		static $arDrivers;
		if (empty($arDrivers)) {
			$arDrivers = self::GetDriversArray(array('all' => true));
		}

		return array_key_exists($code, $arDrivers) ? $arDrivers[$code] : null;
	}

	public static function GetScriptTag($script_name) {
		$kdm = CVCSConfig::KarudoDevelMode();
		if ($kdm) {
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . CVCSConfig::GetPublicJSPath($script_name . '.coffee'))) {
				$kdm = false;
			}
		}
		return '<script type="text/'.($kdm ? 'coffeescript' : 'javascript').'" src="'
			. CVCSConfig::GetPublicJSPath($script_name . ($kdm ? '.coffee' : '.js')) . '"></script>';
	}

	public static function InitJS() {
		static $inited = false;
		if ($inited) {
			return;
		}
		$inited = true;
		$APPLICATION = self::GetAPPLICATION();

		CJSCore::Init('core');

		$pathCSS = '/bitrix/themes/.default/'.CVCSConfig::MODULE_ID . '/';
		$APPLICATION->SetAdditionalCSS($pathCSS . 'bootstrap.css');
		$APPLICATION->SetAdditionalCSS($pathCSS . 'main.css');

		$arMess = __IncludeLang($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/'.CVCSConfig::MODULE_ID.'/lang/'.LANGUAGE_ID . '/js.php', true);
		$arMess['KARUDO_AJAX_PATH'] = CVCSConfig::GetPublicAjaxPath();
		$arMess['KARUDO_ADMIN_SECTION'] = defined('ADMIN_SECTION') && ADMIN_SECTION === true;
		$arMess['KARUDO_EX_TIME'] = ini_get('max_execution_time');
		$arMess['KARUDO_EX_TIME_SET'] = CVCSConfig::GetStepExecutionTime();

		$APPLICATION->AddHeadString('<script type="text/javascript">BX.message('.CUtil::PhpToJSObject($arMess, false).')</script>', true);

		if (CVCSConfig::KarudoDevelMode()) {
			$APPLICATION->AddHeadString(self::GetScriptTag('coffee-script'));
			foreach (CVCSConfig::GetJavascripts() as $i) {
				$APPLICATION->AddHeadString(self::GetScriptTag($i));
			}
		} else {
			$APPLICATION->AddHeadString(self::GetScriptTag('compiled'));
		}
	}

	public static function GetLastCheckTimeText() {
		$last_check_time = CVCSConfig::GetLastCheckTime();
		return $last_check_time ?
			GetMessage('VCS_LAST_CHECK_TIME', array('#DATETIME#' => ConvertTimeStamp($last_check_time, 'FULL')))
			:
			GetMessage('VCS_LAST_CHECK_NEVER');
	}

	/**
	 * @static
	 * @return CDatabase
	 */
	public static function GetDB() {
		return $GLOBALS['DB'];
	}

	/**
	 * @static
	 * @return CMain
	 */
	public static function GetAPPLICATION() {
		return $GLOBALS['APPLICATION'];
	}

	/**
	 * @static
	 * @return CUser
	 */
	public static function GetUSER() {
		return $GLOBALS['USER'];
	}

	public static function DBForSql($str) {
		return '\'' . CVCSMain::GetDB()->ForSql($str) . '\'';
	}

	public static function diff($old, $new) {
		$maxlen = 0;
		foreach ($old as $oindex => $ovalue) {
			$nkeys = array_keys($new, $ovalue);
			foreach ($nkeys as $nindex) {
				$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
					$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
				if ($matrix[$oindex][$nindex] > $maxlen) {
					$maxlen = $matrix[$oindex][$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}
		}
		if ($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
		return array_merge(
			self::diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
			array_slice($new, $nmax, $maxlen),
			self::diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen))
		);
	}

	public static function GetStrForJSShow($x) {
		return htmlspecialchars(str_replace("\r", " ", $x));
	}

	public static function GetDiffArray($str_old, $str_new) {
		$arDiff = self::diff(explode("\n", $str_old), explode("\n", $str_new));
		$arResult = array();
		foreach ($arDiff as $diffItem) {
			if (is_array($diffItem)) {
				if (!empty($diffItem['d']) && is_array($diffItem['d'])) {
					foreach ($diffItem['d'] as $i) {
						$arResult[] = array('str' => self::GetStrForJSShow($i), 'type' => 'del');
					}
				}
				if (!empty($diffItem['i']) && is_array($diffItem['i'])) {
					foreach ($diffItem['i'] as $i) {
						$arResult[] = array('str' => self::GetStrForJSShow($i), 'type' => 'ins');
					}
				}
			} else {
				$arResult[] = array('str' => self::GetStrForJSShow($diffItem), 'type' => 'norm');
			}
		}

		return $arResult;
	}

	public static function AddPanelButtons() {
		if (!CVCSMain::GetUSER()->IsAdmin()) {
			return;
		}
		self::InitJS();
		self::GetAPPLICATION()->AddPanelButton(array(
			"HREF" => "javascript:Karudo.vcs.CheckAndCommit()",
			"ICON" => "bx-panel-vcs-icon",
			"ALT" => GetMessage("KARUDO_VCS_TOCKA_VOSSTANOVLENIA"),
			"TEXT" => GetMessage("KARUDO_VCS_TOCKA_VOSSTANOVLENIA"),
			"MAIN_SORT" => 1000,
			"HINT" => array(
				"TITLE" => GetMessage("KARUDO_VCS_TOCKA_VOSSTANOVLENIA1"),
				"TEXT" => GetMessage("KARUDO_VCS_SOZDANIE_NOVOY_TOCKI"),
			),
			"MENU" => array(
				array(
					"TEXT" => GetMessage("KARUDO_VCS_SOZDANIE_TOCKI_VOSST"),
					"TITLE" => GetMessage("KARUDO_VCS_SOZDANIE_NOVOY_TOCKI1"),
					"ACTION" => "javascript:Karudo.vcs.CheckAndCommit()",
					"DEFAULT" => true,
				),
				array(
					"TEXT" => GetMessage("KARUDO_VCS_OTKAT_K_PREDYDUSEY_T"),
					"TITLE" => GetMessage("KARUDO_VCS_OTKAT_K_PREDYDUSEY_T1"),
					"ACTION"=>"javascript:Karudo.vcs.RestoreFromLastRevision()",
				)
			),
		));
	}
}
