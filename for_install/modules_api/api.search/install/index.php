<?
IncludeModuleLangFile(__FILE__);
Class api_search extends CModule
{
	const MODULE_ID = 'api.search';
	var $MODULE_ID = 'api.search'; 
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
		$this->MODULE_NAME = GetMessage("api.search_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("api.search_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.search_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("api.search_PARTNER_URI");
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
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/". $this->MODULE_ID ."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", True, True);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/api/search.title/");
		DeleteDirFilesEx("/bitrix/components/api/search.page/");

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if ($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->InstallFiles();
		$this->InstallDB();

		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		$this->UnInstallDB();
		$this->UnInstallFiles();

		UnRegisterModule(self::MODULE_ID);
	}
}
?>