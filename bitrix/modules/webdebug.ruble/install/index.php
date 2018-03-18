<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class webdebug_ruble extends CModule {
	var $MODULE_ID = "webdebug.ruble";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function webdebug_ruble() {
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		} else {
			$this->MODULE_VERSION = WEBDEBUG_RUBLE_VERSION;
			$this->MODULE_VERSION_DATE = WEBDEBUG_RUBLE_DATE;
		}
		$this->PARTNER_NAME = GetMessage("WEBDEBUG_RUBLE_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("WEBDEBUG_RUBLE_PARTNER_URI");
		$this->MODULE_NAME = GetMessage("WEBDEBUG_RUBLE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("WEBDEBUG_RUBLE_MODULE_DESC");
	}

	function DoInstall() {
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webdebug.ruble/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		RegisterModule("webdebug.ruble");
		RegisterModuleDependences("main", "OnProlog","webdebug.ruble","CWebdebugRuble", "OnProlog");
		RegisterModuleDependences("currency", "CurrencyFormat","webdebug.ruble","CWebdebugRuble", "CurrencyFormat");
		$webdebug_ruble_regex_exclude = "";
		$webdebug_ruble_regex_exclude .= "#^/bitrix/admin/sale_print.php#";
		$webdebug_ruble_regex_exclude .= "\n#^/personal/order/payment/#";
		$webdebug_ruble_regex_exclude .= "\n#^/bitrix/#";
		COption::SetOptionString("webdebug.ruble", "webdebug_ruble_regex_exclude", $webdebug_ruble_regex_exclude);
		$webdebug_ruble_regex_include .= "";
		$webdebug_ruble_regex_include .= "#^/bitrix/components/.*?/ajax.php$#";
		COption::SetOptionString("webdebug.ruble", "webdebug_ruble_regex_include", $webdebug_ruble_regex_include);
		COption::SetOptionString("webdebug.ruble", "webdebug_ruble_additional_code", "if ($"."_GET[\"rub\"]==\"N\") return false;");
	}

	function DoUninstall() {
		UnRegisterModuleDependences("currency", "CurrencyFormat","webdebug.ruble","CWebdebugRuble", "CurrencyFormat");
		UnRegisterModuleDependences("main", "OnProlog","webdebug.ruble","CWebdebugRuble", "OnProlog");
		UnRegisterModule("webdebug.ruble");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webdebug.ruble/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		DeleteDirFilesEx("/bitrix/themes/.default/webdebug.ruble.font/");
		COption::RemoveOption("webdebug.ruble");
	}
}
?>