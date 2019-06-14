<?
IncludeModuleLangFile(__FILE__);
if(class_exists("w2a_fastermitage")) return;

class w2a_fastermitage extends CModule {
	
	var $MODULE_ID = "w2a.fastermitage";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function w2a_fastermitage()
	{
		$arModuleVersion = array();
		
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->MODULE_NAME = GetMessage("W2A_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("W2A_MODULE_DISCRIPTION");

		$this->PARTNER_NAME = GetMessage("W2A_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("W2A_PARTNER_URI");
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallEvents();
		$this->InstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("W2A_INSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/w2a.fastermitage/install/step1.php");
	}

	function DoUninstall()
	{
		global $APPLICATION;
		$this->UninstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("W2A_UNINSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/w2a.fastermitage/install/unstep1.php");
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/w2a.fastermitage/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin",true,true);
		RegisterModule($this->MODULE_ID);
	}

	function UninstallFiles()
	{
		DeleteDirFilesEx("/bitrix/admin/w2a_fastermitage.php");
		UnRegisterModule($this->MODULE_ID);
	}
}
?>
