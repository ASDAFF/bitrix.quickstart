<?
global $MESS; 
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class grain_customsettings extends CModule
{
	var $MODULE_ID = "grain.customsettings";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $MODULE_GROUP_RIGHTS = "Y";

	function grain_customsettings() 
	{

		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");


		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("GCUSTOMSETTINGS_MODULE_NAME"); 
		$this->MODULE_DESCRIPTION = GetMessage("GCUSTOMSETTINGS_MODULE_DESC"); 
		$this->PARTNER_URI = GetMessage("GCUSTOMSETTINGS_PARTNER_URL");
		$this->PARTNER_NAME = GetMessage("GCUSTOMSETTINGS_PARTNER_NAME");
	}

	function DoInstall() 
	{
		/*patchinstallmutatormark1*/
		$this->InstallFiles();

		RegisterModule("grain.customsettings");
		/*patchinstallmutatormark2*/
	}

	function DoUninstall()
	{

		global $APPLICATION;

		UnRegisterModule("grain.customsettings");

		$this->UnInstallFiles();
			
		$GLOBALS["errors"] = $this->errors;

		COption::RemoveOption("grain.customsettings");

		$APPLICATION->IncludeAdminFile(GetMessage("GCUSTOMSETTINGS_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/install/unstep2.php");

	}


	function InstallFiles()
	{

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/install/images",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/grain.customsettings", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);

		// copy settings files, because of marketplace no converting charset in files not in lang directory 
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/lang/ru/default_settings", $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin", true, true);

		return true;
	}	

	function UnInstallFiles()
	{

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		DeleteDirFilesEx("/bitrix/themes/.default/icons/grain.customsettings/");//icons
		DeleteDirFilesEx("/bitrix/themes/.default/start_menu/grain.customsettings/");//start menu icons
		DeleteDirFilesEx("/bitrix/images/grain.customsettings/");//images

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/lang/ru/default_settings/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin"); // default settings files

		return true;
	}


	function GetModuleRightList()
	{
		$arr = Array(
			"reference_id" => array("D","R","S","W"),
			"reference" => array(
				"[D] ".GetMessage("GCUSTOMSETTINGS_RIGHTS_D"),
				"[R] ".GetMessage("GCUSTOMSETTINGS_RIGHTS_R"),
				"[S] ".GetMessage("GCUSTOMSETTINGS_RIGHTS_S"),
				"[W] ".GetMessage("GCUSTOMSETTINGS_RIGHTS_W"),
			)
		);
		return $arr;
	}

} 

?>