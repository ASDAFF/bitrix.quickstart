<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class yandexparser extends CModule
{
	var $MODULE_ID = "yandexparser";
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
       //     global $DB, $APPLICATION, $DBType;
        //    $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/db/".$DBType."/install.sql");
            return true;		 
	}
	
	function UnInstallDB($arParams = array())
	{
        //    global $DB, $APPLICATION, $DBType;
        //    $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/db/".$DBType."/uninstall.sql");
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
         //   	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
	//	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
         //       CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
                return true;		
	}

	function UnInstallFiles($arParams = array())
	{	
		return true;
	}

	function DoInstall()
	{
	//	global $DB, $DBType, $DOCUMENT_ROOT, $APPLICATION;
	//	$this->InstallDB();
         //       $this->InstallFiles();
                RegisterModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
	//	global $DB, $DOCUMENT_ROOT, $APPLICATION, $DBType;
         //       $this->UnInstallDB(); 
		UnRegisterModule($this->MODULE_ID);
	}
}
?>