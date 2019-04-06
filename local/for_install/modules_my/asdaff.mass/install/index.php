<?
global $MESS;
IncludeModuleLangFile(__FILE__);

class asdaff_mass extends CModule {
	var $MODULE_ID = 'asdaff.mass';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $Errors;

	function __construct() {
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
//		$this->PARTNER_URI = GetMessage("WDA_PARTNER_URI");
		$this->MODULE_NAME = GetMessage("WDA_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("WDA_MODULE_DESCR");
	}

	function InstallDB() {
		global $DB, $DBType;
		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/db/{$DBType}/install.sql");
		if($this->errors!==false) {
			return false;
		}
		return true;
	}

	function UnInstallDB() {
		global $DB, $DBType;
		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/db/{$DBType}/uninstall.sql");
		if($this->errors!==false) {
			return false;
		}
		return true;
	}
	
	function InstallFiles() {
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		return true;
	}
	
	function UnInstallFiles($SaveTemplate=true) {
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		return true;
	}

	function DoInstall() {
		if (!check_bitrix_sessid()) return false;
		if ($this->InstallDB()) {
			RegisterModule($this->MODULE_ID);
			$this->InstallFiles();
		} else {
			$APPLICATION->ThrowException(implode('', $this->errors));
			return false;
		}
		return true;
	}

	function DoUninstall() {
		global $DB;
		if (!check_bitrix_sessid()) return false;
		if ($this->UnInstallDB()) {
			$this->UnInstallFiles();
			UnRegisterModule($this->MODULE_ID);
		} else {
			$APPLICATION->ThrowException(implode('', $this->errors));
			return false;
		}
		return true;
	}
	
}
?>