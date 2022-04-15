<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class tc extends CModule
{
	var $MODULE_ID = "tc";
        var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
        var $MODULE_GROUP_RIGHTS;
	var $errors = array();
 
	function __construct() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
                
		$this->MODULE_NAME = GetMessage("MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
	}
	
	function InstallDB($arParams = array())
	{
            return true;		 
	}
	
	function UnInstallDB($arParams = array())
	{
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
            return true;		
	}

	function UnInstallFiles($arParams = array())
	{	
		return true;
	}

	function DoInstall()
	{
		 
                RegisterModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
		 
		UnRegisterModule($this->MODULE_ID);
	}
}
?>