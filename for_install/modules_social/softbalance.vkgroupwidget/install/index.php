<?
IncludeModuleLangFile(__FILE__);
Class softbalance_vkgroupwidget extends CModule
{
	const MODULE_ID = 'softbalance.vkgroupwidget';
	var $MODULE_ID = 'softbalance.vkgroupwidget'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("PARTNER_URI");
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
		global $DOCUMENT_ROOT;
		CopyDirFiles($DOCUMENT_ROOT."/bitrix/modules/".self::MODULE_ID."/install/components", $DOCUMENT_ROOT."/bitrix/components", true, true);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/softbalance/vkgroupwidget");
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		$this->UnInstallFiles();
		UnRegisterModule(self::MODULE_ID);
	}
}
?>
