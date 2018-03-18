<?
global $DOCUMENT_ROOT, $MESS;

IncludeModuleLangFile(__FILE__);

Class span_fblb extends CModule
{
	var $MODULE_ID = "span.fblb";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
	var $MODULE_GROUP_RIGHTS = "N";

	function span_fblb()
	{
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("DSSSTATUS_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("DSSSTATUS_PARTNER_URI"); 
		$this->MODULE_NAME = GetMessage("DSSSTATUS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("DSSSTATUS_MODULE_DESCRIPTION");
	}

	function InstallDB()
	{
		RegisterModule("span.fblb");
		return true;
	}

	function UnInstallDB()
	{
		UnRegisterModule("span.fblb");
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
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/span.fblb/install/components/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/components",
			true, true
		);
		return true;
	}

	function UnInstallFiles()
	{
	    DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/span.fblb");
	    DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/span/fblb");
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("span.fblb"))
		{
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
		}
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
	}
}
?>