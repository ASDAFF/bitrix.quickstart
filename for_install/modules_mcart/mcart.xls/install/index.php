<?
IncludeModuleLangFile( __FILE__);


if(class_exists("mcart_xls")) 
	return;

Class mcart_xls extends CModule
{
	var $MODULE_ID = "mcart.xls";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	
	
	function mcart_xls() 
	{
		$arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }else{
            $this->MODULE_VERSION=TASKFROMEMAIL_MODULE_VERSION;
            $this->MODULE_VERSION_DATE=TASKFROMEMAIL_MODULE_VERSION_DATE;
        }

        $this->MODULE_NAME = GetMessage("xls_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("xls_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = GetMessage("PARTNER_NAME");
        $this->PARTNER_URI  = "http://mcart.ru/";
	}
	
	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("mcart.xls"))
		{
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
			
		}
		return true;
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
		
		return true;
	}

	
	function InstallDB() {
		global $DB;
		$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/mcart.xls/install/createtable.sql");
		RegisterModule("mcart.xls");	
		return true;
	
			
	}
	
	function UnInstallDB()
	{
		global $DB;
		$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/mcart.xls/install/droptable.sql");
		UnRegisterModule("mcart.xls");
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

	function InstallFiles()
	{
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
	return true;
	}
	
	function UnInstallFiles()
	{	
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.xls/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		return true;
	}
	
	

	
} //end class
	?>