<?

IncludeModuleLangFile(__FILE__);

class millcom_menu extends CModule {
	var $MODULE_ID = "millcom.menu";		
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;

	function millcom_menu() {
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("MILLCOM_MENU_PARTNER_NAME");
		$this->PARTNER_URI = "http://millcom.by";

		$this->MODULE_NAME = GetMessage("MILLCOM_MENU_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MILLCOM_MENU_MODULE_DESCRIPTION");	
	}
	
	function DoInstall() {
		RegisterModule($this->MODULE_ID);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/millcom.menu/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		return true;
	}

	function DoUninstall() {		
		UnRegisterModule($this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/components/millcom/menu");
		rmdir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/millcom");
		return true;
	}	
}	
?>