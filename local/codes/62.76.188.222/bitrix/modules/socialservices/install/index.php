<?
global $MESS;
include(GetLangFileName(substr(__FILE__, 0, -18)."/lang/", "/install/index.php"));

class socialservices extends CModule
{
	var $MODULE_ID = "socialservices";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function socialservices()
	{
		$arModuleVersion = array();

		include(substr(__FILE__, 0,  -10)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("socialservices_install_name");
		$this->MODULE_DESCRIPTION = GetMessage("socialservices_install_desc");
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$errors = false;
		if(!$DB->Query("SELECT 'x' FROM b_socialservices_user", true))
		{
			$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/install/db/".$DBType."/install.sql");
		}

		if ($errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}

		RegisterModule("socialservices");

		RegisterModuleDependences("main", "OnUserDelete", "socialservices", "CSocServAuthDB", "OnUserDelete");

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $APPLICATION, $DB, $DBType;
		$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/install/db/".$DBType."/uninstall.sql");
		if ($errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}
		
		UnRegisterModuleDependences("main", "OnUserDelete", "socialservices", "CSocServAuthDB", "OnUserDelete");

		UnRegisterModule("socialservices");

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
		if($_ENV["COMPUTERNAME"]!='BX')
		{
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
		}
		return true;
	}

	function UnInstallFiles()
	{
		if($_ENV["COMPUTERNAME"]!='BX')
		{
			DeleteDirFilesEx("/bitrix/js/socialservices/");
			DeleteDirFilesEx("/bitrix/images/socialservices/");
			DeleteDirFilesEx("/bitrix/tools/oauth/");
		}
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallDB();
		$this->InstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("socialservices_install_title_inst"), $DOCUMENT_ROOT."/bitrix/modules/socialservices/install/step.php");
	}

	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallFiles();
		$this->UnInstallDB();
		$APPLICATION->IncludeAdminFile(GetMessage("socialservices_install_title_unitst"), $DOCUMENT_ROOT."/bitrix/modules/socialservices/install/unstep.php");
	}
}
?>