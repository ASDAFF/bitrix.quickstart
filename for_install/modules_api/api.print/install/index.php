<?
IncludeModuleLangFile(__FILE__);
Class api_print extends CModule
{
	const MODULE_ID = 'api.print';
	var $MODULE_ID = 'api.print';
	var $CP_DIRNAME = 'api';
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
		$this->MODULE_NAME = GetMessage("api.print_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("api.print_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.print_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("api.print_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		//RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CApiPrint', 'OnBuildGlobalMenu');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		//UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CApiPrint', 'OnBuildGlobalMenu');
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
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/". $this->MODULE_ID ."/install/files/", $_SERVER["DOCUMENT_ROOT"], True, True);

		return true;
	}

	function UnInstallFiles()
	{
		//@unlink($_SERVER['DOCUMENT_ROOT'] . '/ts_print.css');
		@unlink($_SERVER['DOCUMENT_ROOT'] . '/ts_print.php');

		DeleteDirFilesEx("/bitrix/components/". $this->CP_DIRNAME ."/print.mini/");
		DeleteDirFilesEx("/bitrix/components/". $this->CP_DIRNAME ."/print.full/");

		return true;
	}

	function DoInstall()
	{
		$this->InstallFiles();
		//$this->InstallDB();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		UnRegisterModule(self::MODULE_ID);
		//$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}