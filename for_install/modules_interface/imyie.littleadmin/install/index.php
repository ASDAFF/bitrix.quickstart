<?
global $MESS;
IncludeModuleLangFile(__FILE__);

Class imyie_littleadmin extends CModule
{
    var $MODULE_ID = "imyie.littleadmin";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function imyie_littleadmin()
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

		$this->MODULE_NAME = GetMessage("IMYIE_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("IMYIE_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("IMYIE_INSTALL_COPMPANY_NAME");
        $this->PARTNER_URI  = "http://imyie.ru/";
	}

	// Install functions
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		RegisterModule("imyie.littleadmin");
		return TRUE;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.littleadmin/install/copyfiles/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		return TRUE;
	}

	function InstallPublic()
	{
		return TRUE;
	}

	function InstallEvents()
	{
		RegisterModuleDependences("main", "OnPageStart", "imyie.littleadmin", "CIMYIELittleAdmin", "OnPageStartHandler", "500");
		RegisterModuleDependences("main", "OnAdminContextMenuShow", "imyie.littleadmin", "CIMYIELittleAdmin", "OnAdminContextMenuShowHandler", "500");
		return TRUE;
	}
	
	function CantInstall()
	{
		UnRegisterModule("imyie.littleadmin");
		return TRUE;
	}

	// UnInstal functions
	function UnInstallDB($arParams = Array())
	{
		
		global $DB, $DBType, $APPLICATION;
		UnRegisterModule("imyie.littleadmin");
		return TRUE;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/themes/.default/imyie.littleadmin.css");
		return TRUE;
	}

	function UnInstallPublic()
	{
		return TRUE;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences("main", "OnPageStart", "imyie.littleadmin", "CIMYIELittleAdmin", "OnPageStartHandler");
		UnRegisterModuleDependences("main", "OnAdminContextMenuShow", "imyie.littleadmin", "CIMYIELittleAdmin", "OnAdminContextMenuShowHandler");
		return TRUE;
	}

    function DoInstall()
    {
		global $APPLICATION, $step;
		$keyGoodDB = $this->InstallDB();
		$keyGoodEvents = $this->InstallEvents();
		if($keyGoodEvents)
		{
			$keyGoodFiles = $this->InstallFiles();
			$keyGoodPublic = $this->InstallPublic();
			$APPLICATION->IncludeAdminFile(GetMessage("SPER_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.littleadmin/install/install.php");
		} else {
			$this->CantInstall();
			$APPLICATION->IncludeAdminFile(GetMessage("SPER_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.littleadmin/install/badinstall.php");
		}
    }

    function DoUninstall()
    {
		global $APPLICATION, $step;
		$keyGoodFiles = $this->UnInstallFiles();
		$keyGoodEvents = $this->UnInstallEvents();
		$keyGoodDB = $this->UnInstallDB();
		$keyGoodPublic = $this->UnInstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage("SPER_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imyie.littleadmin/install/uninstall.php");
    }
}
?>