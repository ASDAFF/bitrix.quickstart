<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class webdebug_popup extends CModule {
	var $MODULE_ID = "webdebug.popup";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function __construct() {
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		$this->PARTNER_NAME = GetMessage("WEBDEBUG_POPUP_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("WEBDEBUG_POPUP_PARTNER_URI");
		$this->MODULE_NAME = GetMessage("WEBDEBUG_POPUP_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("WEBDEBUG_POPUP_MODULE_DESC");
	}
	
	function InstallFiles() {
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		return true;
	}
	
	function InstallDB() {
		global $APPLICATION;
		return true;
	}

	function DoInstall() {
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule($this->MODULE_ID);
		RegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "CWD_Popup", "Init");
		return true;
	}
	
	function UnInstallDB() {
		global $APPLICATION;
		if($DBErrors !== false) {
			$APPLICATION->ThrowException(implode("<br/>", $DBErrors));
		}
		return true;
	}
	
	function UnInstallFiles() {
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/");
		DeleteDirFilesEx("/bitrix/js/webdebug.popup/");
		DeleteDirFilesEx("/bitrix/themes/.default/webdebug.popup/");
		DeleteDirFilesEx("/bitrix/components/webdebug/popup_window");
		rmdir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/webdebug/");
		return true;
	}

	function DoUninstall() {
		UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "CWD_Popup", "Init");
		UnRegisterModule($this->MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
		return true;
	}
}
?>