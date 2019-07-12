<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
class webrover_salon extends CModule {
	
	var $MODULE_ID = "webrover.salon";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function webrover_salon(){
	
		$arModuleVersion = array(); 
	 
		$path = dirname(__FILE__);
		include($path . "/version.php");
	 
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = "Webrover Internet Solutions";
		$this->PARTNER_URI = "http://www.webrover.ru/";

		$this->MODULE_NAME = GetMessage("MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("MODULE_DESCRIPTION");
	} 
 
	function InstallDB() {
		RegisterModule("webrover.salon");
		return true;
	}

	function UnInstallDB() {
		
		UnRegisterModule("webrover.salon");
		return true;
	}

	function InstallEvents() {
		
		return true;
	}
	
	function UnInstallEvents() {
		return true; 
	}

	function InstallFiles() {
/*		CopyDirFiles( 
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webrover.salon/install/wizards", 
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards", 
			true, true 
		); */
	
		return true;
	} 
 
	function UnInstallFiles() {

//		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webrover.salon/install/wizards", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards");
		return true;
	}

	function DoInstall() {
	
		global $APPLICATION;
		
		if (!IsModuleInstalled("webrover.salon")) {
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
		}
	}

	function DoUninstall() {
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
	}
}