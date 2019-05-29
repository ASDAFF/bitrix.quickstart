<?
IncludeModuleLangFile(__FILE__);

if(class_exists("routdesign_menuext")) return;

class routdesign_menuext extends CModule {
	
	var $MODULE_ID = "routdesign.menuext";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "N";
	
	var $errors;
	
	function routdesign_menuext()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("COMMENTS_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("COMMENTS_INSTALL_DESCRIPTION");
        
        $this->PARTNER_NAME = "Rout Design Studio";
        $this->PARTNER_URI = "http://www.rout.ru/";
	}
	
	function InstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        RegisterModule($this->MODULE_ID);
        return true;
    }
	
	function UnInstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        $this->errors = false;
        UnRegisterModule($this->MODULE_ID);
        return true;
    }
	
	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}
	
	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/routdesign.menuext/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/rout/rout.menu.sections/");
		return true;
	}
	
	function DoInstall()
	{
		global $APPLICATION, $step;

        $this->InstallDB();
		$this->InstallEvents();
		$this->InstallFiles();
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
	}
}
?>