<?
IncludeModuleLangFile(__FILE__);

Class webdebug_pageprops extends CModule {
	var $MODULE_ID = 'webdebug.pageprops';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function __construct() {
		include(dirname(__FILE__).'/version.php');
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->PARTNER_NAME = GetMessage('WEBDEBUG_PAGEPROPS_PARTNER_NAME');
		$this->PARTNER_URI = GetMessage('WEBDEBUG_PAGEPROPS_PARTNER_URI');
		$this->MODULE_NAME = GetMessage('WEBDEBUG_PAGEPROPS_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('WEBDEBUG_PAGEPROPS_MODULE_DESC');
	}
	
	function InstallDB() {
		global $APPLICATION, $DB, $DBType;
		$DBErrors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/db/{$DBType}/install.sql");
		if($DBErrors !== false) {
			$APPLICATION->ThrowException(implode('<br/>', $DBErrors));
		}
		return true;
	}
	
	function UnInstallDB() {
		global $APPLICATION, $DB, $DBType;
		$DBErrors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/db/{$DBType}/uninstall.sql");
		if($DBErrors !== false) {
			$APPLICATION->ThrowException(implode('<br/>', $DBErrors));
		}
		return true;
	}
	
	function InstallFiles() {
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/admin/", $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true, true);
		return true;
	}
	
	function UnInstallFiles() {
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js");
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/admin/", $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFilesEx("/bitrix/themes/.default/icons/{$this->MODULE_ID}");
		return true;
	}

	function DoInstall() {
		$this->InstallDB();
		$this->InstallFiles();
		RegisterModule($this->MODULE_ID);
		RegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'CWD_Pageprops', 'OnEndBufferContent');
		RegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'CWD_Pageprops', 'OnPageStart');
		return true;
	}

	function DoUninstall() {
		UnRegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'CWD_Pageprops', 'OnEndBufferContent');
		UnRegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'CWD_Pageprops', 'OnPageStart');
		UnRegisterModule($this->MODULE_ID);
		$this->UnInstallFiles();
		$this->UnInstallDB();
		return true;
	}
}
?>