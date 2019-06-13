<?
global $MESS;
IncludeModuleLangFile(__FILE__);

Class uniplug_sqladminer extends CModule {
	const MODULE_ID = "uniplug.sqladminer";
	var $MODULE_ID = "uniplug.sqladminer";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function uniplug_sqladminer() {
		$arModuleVersion = array();

		include(dirname(__FILE__) . "/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("UNIPLUG_SQLADMINER_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("UNIPLUG_SQLADMINER_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("UNIPLUG_SQLADMINER_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("UNIPLUG_SQLADMINER_PARTNER_URI");

	}

	function InstallDB() {
		RegisterModule(self::MODULE_ID);

		return true;
	}

	function InstallFiles() {
		CopyDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
		CopyDirFiles(__DIR__ . '/themes', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes', true, true);

		return true;
	}

	function InstallPublic() {
		return true;
	}

	function InstallEvents() {
		CModule::IncludeModule(self::MODULE_ID);

		return true;
	}

	function UnInstallDB($arParams = Array()) {
		UnRegisterModule(self::MODULE_ID);

		return true;
	}

	function UnInstallFiles() {
		DeleteDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
		DeleteDirFiles(__DIR__ . '/themes', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes');

		return true;
	}

	function UnInstallPublic() {
		return true;
	}

	function UnInstallEvents() {
		return true;
	}

	function DoInstall() {
		global $APPLICATION, $step;
		$keyGoodFiles = $this->InstallFiles();
		$keyGoodDB = $this->InstallDB();
		$keyGoodEvents = $this->InstallEvents();
		$keyGoodPublic = $this->InstallPublic();
	}

	function DoUninstall() {
		global $APPLICATION, $step;
		$keyGoodFiles = $this->UnInstallFiles();
		$keyGoodEvents = $this->UnInstallEvents();
		$keyGoodDB = $this->UnInstallDB();
		$keyGoodPublic = $this->UnInstallPublic();
	}
}
