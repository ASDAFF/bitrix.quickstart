<?
IncludeModuleLangFile(__FILE__); 

if(class_exists("ipol_mailorder")) 
    return;
	
Class ipol_mailorder extends CModule
{
    var $MODULE_ID = "ipol.mailorder";
    var $MODULE_NAME;
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "N";
        var $errors;
		var $mine = "0";
	
	function ipol_mailorder()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("IPOLMO_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("IPOLMO_INSTALL_DESCRIPTION");
        
        $this->PARTNER_NAME = "Ipol";
        $this->PARTNER_URI = "http://www.ipolh.com";

	}
	function InstallDB()
	{
        return true;
	}
	function UnInstallDB()
	{
        return true;
	}
	function InstallEvents(){
        RegisterModuleDependences("main", "OnBeforeEventAdd", $this->MODULE_ID, "mailorderdriver", "insertMacrosData");
		return true;
	}
	function UnInstallEvents(){
		UnRegisterModuleDependences("main", "OnBeforeEventAdd", $this->MODULE_ID, "mailorderdriver", "insertMacrosData");
		return true;
	}
	function InstallFiles(){
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID, true, true);
		return true;
	}
	function UnInstallFiles(){
		DeleteDirFilesEx("/bitrix/images/".$this->MODULE_ID);
		return true;
	}
    function DoInstall()
    {
        global $DB, $APPLICATION, $step;
		$this->errors = false;
		
		$this->InstallDB();
		$this->InstallEvents();
		$this->InstallFiles();
		
		RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage("IPOLMO_INSTALLING").$this->MODULE_ID, $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
    }

    function DoUninstall()
    {
        global $DB, $APPLICATION, $step;
		$this->errors = false;
		
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		
		UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage("IPOLMO_UNINSTALLING").$this->MODULE_ID, $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
    }
}
?>
