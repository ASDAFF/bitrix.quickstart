<?
IncludeModuleLangFile(__FILE__);

Class api_uncachedarea extends CModule
{
	const MODULE_ID = 'api.uncachedarea';
	var $MODULE_ID = 'api.uncachedarea';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError  = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION      = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME         = GetMessage("api.uncachedarea_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.uncachedarea_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.uncachedarea_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.uncachedarea_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		RegisterModuleDependences('main', 'OnEpilog', self::MODULE_ID, 'CAPIUncachedArea', 'OnEpilog', 1000);
		//RegisterModuleDependences('main', 'OnEndBufferContent', self::MODULE_ID, 'CAPIUncachedArea', 'OnEndBufferContent', 1000);
		RegisterModuleDependences('main', 'OnBeforeEndBufferContent', self::MODULE_ID, 'CAPIUncachedArea', 'OnBeforeEndBufferContent', 1000);

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnEpilog', self::MODULE_ID, 'CAPIUncachedArea', 'OnEpilog');
		//UnRegisterModuleDependences('main', 'OnEndBufferContent', self::MODULE_ID, 'CAPIUncachedArea', 'OnEndBufferContent');
		UnRegisterModuleDependences('main', 'OnBeforeEndBufferContent', self::MODULE_ID, 'CAPIUncachedArea', 'OnBeforeEndBufferContent');

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

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}

?>