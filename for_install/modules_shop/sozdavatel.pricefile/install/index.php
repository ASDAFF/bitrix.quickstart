<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class sozdavatel_pricefile extends CModule
{
	var $MODULE_ID = "sozdavatel.pricefile";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function __construct() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->MODULE_NAME = GetMessage("INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
		$this->PARTNER_URI = 'http://sozdavatel.ru';
	}


	function InstallDB($install_wizard = true)
	{
		RegisterModule("sozdavatel.pricefile");		
		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		UnRegisterModule("sozdavatel.pricefile");
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
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.pricefile/install/components/sozdavatel", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/sozdavatel", true, true);
		return true;
	}

	function InstallPublic()
	{
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.pricefile/install/components/sozdavatel", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/sozdavatel", true, true);		
		return true;
		
	}

	function DoInstall()
	{
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		
	}
}

?>