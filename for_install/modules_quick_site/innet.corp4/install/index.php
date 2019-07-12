<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

class innet_corp4 extends CModule {//
	
	var $MODULE_ID = "innet.corp4";//
	var $MODULE_VERSION; 
	var $MODULE_VERSION_DATE; 
	var $MODULE_NAME; 
	var $MODULE_DESCRIPTION; 
	var $MODULE_CSS; 
	var $MODULE_GROUP_RIGHTS = "Y"; 
 
	function innet_corp4(){//
	
		$arModuleVersion = array(); 
	 
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		
		$this->MODULE_VERSION = $arModuleVersion["VERSION"]; 
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"]; 
		$this->PARTNER_NAME = "INNET"; 
		$this->PARTNER_URI = "http://innet-it.ru/";
	
		$this->MODULE_NAME = GetMessage('MODULE_NAME');
		$this->MODULE_DESCRIPTION  = GetMessage('MODULE_DESCRIPTION');
	} 
 
	function InstallDB() { 
	    RegisterModule($this->MODULE_ID); 
	    return true; 
	} 
 
	function UnInstallDB() {
		UnRegisterModule($this->MODULE_ID); 
		return true; 
	} 
 
	function InstallEvents() {
		return true;
	}
	
	function UnInstallEvents() { 
		return true; 
	}
 
	function InstallFiles() {
		return true; 
	} 
 
	function UnInstallFiles() { 
	    return true; 
	} 
 
	function DoInstall() {
		global $APPLICATION;  
		
		if (!IsModuleInstalled($this->MODULE_ID)) { 
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