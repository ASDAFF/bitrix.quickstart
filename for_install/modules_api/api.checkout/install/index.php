<?
IncludeModuleLangFile(__FILE__);

Class api_checkout extends CModule
{
	const MODULE_ID = 'api.checkout';
	var $MODULE_ID = 'api.checkout';
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
		$this->MODULE_NAME         = GetMessage("api.checkout_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.checkout_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.checkout_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.checkout_PARTNER_URI");
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx('/bitrix/components/api/checkout/');

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
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallFiles();
	}
}
?>