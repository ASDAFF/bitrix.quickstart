<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class imyie_loginbyemail extends CModule
{
    var $MODULE_ID = "imyie.loginbyemail";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function imyie_loginbyemail()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
	
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = "1.0.0";
            $this->MODULE_VERSION_DATE = "2012.01.01";
        }

		$this->MODULE_NAME = GetMessage("IMYIE_MODULE_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("IMYIE_MODULE_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("IMYIE_MODULE_INSTALL_COPMPANY_NAME");
        $this->PARTNER_URI  = "http://imyie.ru/";
	}

	// Install functions
	function InstallDB()
	{
		RegisterModule("imyie.loginbyemail");
		return TRUE;
	}

	function InstallFiles()
	{
		return TRUE;
	}

	function InstallPublic()
	{
		return TRUE;
	}

	function InstallEvents()
	{
		COption::SetOptionString("imyie.loginbyemail", "auth_by_email", "Y" );
		COption::SetOptionString("imyie.loginbyemail", "loginbylink_onoff", "N" );
		COption::SetOptionString("imyie.loginbyemail", "loginbylink_link", "/auth/" );
		RegisterModuleDependences("main", "OnBeforeUserLogin", "imyie.loginbyemail", "CIMYIELoginByEmail", "OnBeforeUserLoginHandler");
		return TRUE;
	}

	// UnInstal functions
	function UnInstallDB($arParams = Array())
	{
		UnRegisterModule("imyie.loginbyemail");
		return TRUE;
	}

	function UnInstallFiles()
	{
		return TRUE;
	}

	function UnInstallPublic()
	{
		return TRUE;
	}

	function UnInstallEvents()
	{
		COption::RemoveOption("imyie.loginbyemail");
		UnRegisterModuleDependences("main", "OnBeforeUserLogin", "imyie.loginbyemail", "CIMYIELoginByEmail", "OnBeforeUserLoginHandler");
		return TRUE;
	}

    function DoInstall()
    {
		global $APPLICATION, $step;
		$keyGoodFiles = $this->InstallFiles();
		$keyGoodDB = $this->InstallDB();
		$keyGoodEvents = $this->InstallEvents();
		$keyGoodPublic = $this->InstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage("SPER_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.loginbyemail/install/install.php");
    }

    function DoUninstall()
    {
		global $APPLICATION, $step;
		$keyGoodFiles = $this->UnInstallFiles();
		$keyGoodDB = $this->UnInstallDB();
		$keyGoodEvents = $this->UnInstallEvents();
		$keyGoodPublic = $this->UnInstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage("SPER_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.loginbyemail/install/uninstall.php");
    }
}
?>