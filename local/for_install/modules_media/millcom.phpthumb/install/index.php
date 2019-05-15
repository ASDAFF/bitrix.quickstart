<?
IncludeModuleLangFile(__FILE__);

class millcom_phpthumb extends CModule {
	var $MODULE_ID = "millcom.phpthumb";		
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;	
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	
	function millcom_phpthumb() {
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("MILLCOM_PHPTHUMB_PARTNER_NAME");
		$this->PARTNER_URI = "http://millcom.by";

		$this->MODULE_NAME = GetMessage("MILLCOM_PHPTHUMB_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MILLCOM_PHPTHUMB_MODULE_DESCRIPTION");	
	}

	function DoInstall() {
		global $APPLICATION, $DB;
		RegisterModule($this->MODULE_ID);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/images', $_SERVER["DOCUMENT_ROOT"].'/bitrix/images', true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER["DOCUMENT_ROOT"].'/bitrix/admin', true, true);

		$DBErrors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/db/'.strtolower($DB->type).'/install.sql');
		if($DBErrors !== false) {
			$APPLICATION->ThrowException(implode("<br/>", $DBErrors));
		}


		return true;
	}
	
	function DoUninstall() {
		global $APPLICATION, $DB;		
		UnRegisterModule($this->MODULE_ID); 
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/images/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/');

		$DBErrors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$this->MODULE_ID.'/install/db/'.strtolower($DB->type).'/uninstall.sql');
		if($DBErrors !== false) {
			$APPLICATION->ThrowException(implode("<br/>", $DBErrors));
		}
		
		return true;
	}
}
?>