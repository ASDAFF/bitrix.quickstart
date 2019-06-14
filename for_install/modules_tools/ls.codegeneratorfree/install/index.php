<?
IncludeModuleLangFile(__FILE__);
if(class_exists("ls_codegeneratorfree")) return;

class ls_codegeneratorfree extends CModule {
	
	var $MODULE_ID = "ls.codegeneratorfree";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function ls_codegeneratorfree()
	{
		$arModuleVersion = array();
		
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->MODULE_NAME = GetMessage("PTB_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("PTB_MODULE_DISCRIPTION");

		$this->PARTNER_NAME = GetMessage("PTB_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("PTB_PARTNER_URI");
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallEvents();
		$this->InstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("PTB_INSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ls.codegeneratorfree/install/step1.php");
	}

	function DoUninstall()
	{
		global $APPLICATION;
		$this->UninstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("PTB_UNINSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ls.codegeneratorfree/install/unstep1.php");
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ls.codegeneratorfree/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin",true,true);
		RegisterModule($this->MODULE_ID);
	}

	function UninstallFiles()
	{
		DeleteDirFilesEx("/bitrix/admin/ls_codegeneratorfree.php");
		UnRegisterModule($this->MODULE_ID);
	}
}
?>
