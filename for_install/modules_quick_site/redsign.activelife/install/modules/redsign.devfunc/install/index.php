<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class redsign_devfunc extends CModule
{
	var $MODULE_ID = "redsign.devfunc";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function redsign_devfunc()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SCOM_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SCOM_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("SPER_PARTNER");
		$this->PARTNER_URI = GetMessage("PARTNER_URI");
	}

	// Install functions
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		RegisterModule("redsign.devfunc");
		return TRUE;
	}

	function InstallEvents()
	{
		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "redsign.devfunc", "RSDevFuncOffersExtension", "OnAfterIBlockElementAddHandler",10000);
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "redsign.devfunc", "RSDevFuncOffersExtension", "OnAfterIBlockElementUpdateHandler",10000);
		RegisterModuleDependences("catalog", "OnPriceAdd", "redsign.devfunc", "RSDevFuncOffersExtension", "OnPriceUpdateAddHandler",10000);
		RegisterModuleDependences("catalog", "OnPriceUpdate", "redsign.devfunc", "RSDevFuncOffersExtension", "OnPriceUpdateAddHandler",10000);
		return TRUE;
	}

	function InstallOptions()
	{
		COption::SetOptionString("redsign.devfunc", "fakeprice_active", "Y" );
		COption::SetOptionString("redsign.devfunc", "propcode_cml2link", "CML2_LINK" );
		COption::SetOptionString("redsign.devfunc", "propcode_fakeprice", "PROD_PRICE_FALSE" );
		return TRUE;
	}

	function InstallFiles()
	{
		COption::SetOptionString("redsign.devfunc", "no_photo_path", "/bitrix/modules/redsign.devfunc/img/no-photo.png");
		$arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/img/no-photo.png");
		$fid = CFile::SaveFile($arFile, "redsign_devfunc_nophoto");
		COption::SetOptionInt("redsign.devfunc", "no_photo_fileid", $fid);
		
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		return TRUE;
	}

	function InstallPublic()
	{
		return TRUE;
	}

	// UnInstal functions
	function UnInstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		UnRegisterModule("redsign.devfunc");
		return TRUE;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "redsign.devfunc", "RSDevFuncOffersExtension", "OnAfterIBlockElementAddHandler");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "redsign.devfunc", "RSDevFuncOffersExtension", "OnAfterIBlockElementUpdateHandler");
		UnRegisterModuleDependences("catalog", "OnPriceAdd", "redsign.devfunc", "RSDevFuncOffersExtension", "OnPriceUpdateAddHandler");
		UnRegisterModuleDependences("catalog", "OnPriceUpdate", "redsign.devfunc", "RSDevFuncOffersExtension", "OnPriceUpdateAddHandler");
		return TRUE;
	}

	function UnInstallOptions()
	{
		COption::RemoveOption("redsign.devfunc");
		return TRUE;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/js/redsign.devfunc");
		return TRUE;
	}

	function UnInstallPublic()
	{
		return TRUE;
	}

    function DoInstall()
    {
		global $APPLICATION, $step;
		$keyGoodDB = $this->InstallDB();
		$keyGoodEvents = $this->InstallEvents();
		$keyGoodOptions = $this->InstallOptions();
		$keyGoodFiles = $this->InstallFiles();
		$keyGoodPublic = $this->InstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/install.php");
    }

    function DoUninstall()
    {
		global $APPLICATION, $step;
		$keyGoodFiles = $this->UnInstallFiles();
		$keyGoodEvents = $this->UnInstallEvents();
		$keyGoodOptions = $this->UnInstallOptions();
		$keyGoodDB = $this->UnInstallDB();
		$keyGoodPublic = $this->UnInstallPublic();
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/redsign.devfunc/install/uninstall.php");
    }
}