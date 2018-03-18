<?
IncludeModuleLangFile( __FILE__);

Class mcart_softmebel extends CModule
{
	var $MODULE_ID = "mcart.softmebel";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";
	
	
	function mcart_softmebel() 
	{
		$arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }else{
            $this->MODULE_VERSION=MS_MODULE_VERSION;
            $this->MODULE_VERSION_DATE=MS_MODULE_VERSION_DATE;
        }

        $this->MODULE_NAME = GetMessage("MSOFTMEB_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("MSOFTMEB_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = GetMessage("MSOFTMEB_PARTNER_NAME");
        $this->PARTNER_URI  = "http://mcart.ru/";
	}
	
	
	
	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("mcart.softmebel"))
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
		
		RegisterModule("mcart.softmebel");	
		return true;
	
			
	}
	
	function UnInstallDB()
	{
		
		UnRegisterModule("mcart.softmebel");
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
	{CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.softmebel/install/mcart",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/mcart", true, True);
	return true;
	}
	
	function UnInstallFiles()
	{	DeleteDirFilesEx("/bitrix/wizards/mcart/");
		DeleteDirFilesEx("/bitrix/templates/softmebel_/");
		return true;
	}	


} //end class
	?>	