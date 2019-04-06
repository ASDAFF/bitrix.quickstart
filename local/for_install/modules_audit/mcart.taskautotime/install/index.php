<?
IncludeModuleLangFile( __FILE__);


if(class_exists("mcart_taskautotime")) 
	return;

Class mcart_taskautotime extends CModule
{
	var $MODULE_ID = "mcart.taskautotime";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	
	
	function mcart_taskautotime() 
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

        $this->MODULE_NAME = GetMessage("taskautotime_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("taskautotime_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = GetMessage("PARTNER_NAME");
        $this->PARTNER_URI  = "http://mcart.ru/";
	}
	
	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("mcart.taskautotime"))
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
		
		RegisterModule("mcart.taskautotime");	
		//RegisterModuleDependences("main", "OnBeforeUserUpdate", "mcart.taskautotime", "CmcartSocNettaskautotime", "FixUserGroups");
		RegisterModuleDependences('tasks', 'OnBeforeTaskAdd', "mcart.taskautotime", "CmcartTasks", "TaskautotimeSet");
		
		return true;
	
			
	}
	
	function UnInstallDB()
	{
		
		UnRegisterModule("mcart.taskautotime");
		return true;
	}
	
	
	
	function InstallEvents()
	{
		
		return true;
	}

	function UnInstallEvents()
	{
		//UnRegisterModuleDependences("main", "OnBeforeUserUpdate", "mcart.taskautotime", "CmcartSocNettaskautotime", "FixUserGroups");
		UnRegisterModuleDependences('tasks', 'OnBeforeTaskAdd', "mcart.taskautotime", "CmcartTasks", "TaskautotimeSet");
		return true;
	}

	function InstallFiles()
	{
	return true;
	}
	
	function UnInstallFiles()
	{	
		
		return true;
	}
	
	
} //end class
	?>	