<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));


Class design2u_stindustrial extends CModule
{
	var $MODULE_ID = "design2u.stindustrial";
	//var $MODULE_ID = "design2u";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";



         
		 //function design2u()
		 function design2u_stindustrial()
        {

                                $arModuleVersion = array();

				$path = str_replace("\\", "/", __FILE__);

				$path = substr($path, 0, strlen($path) - strlen("/index.php"));

				include($path."/version.php");

				$this->MODULE_VERSION = $arModuleVersion["VERSION"];

				$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

				$this->PARTNER_NAME = "design2u";

				$this->PARTNER_URI = "http://design2u.ru/";

				$this->MODULE_NAME = GetMessage("SCOM_INSTALL_NAME");

				$this->MODULE_DESCRIPTION = GetMessage("SCOM_INSTALL_DESCRIPTION");

			return true;

        }




	function InstallDB($install_wizard = true)
	{
		global $DB, $DBType, $APPLICATION;

		RegisterModule($this->MODULE_ID);
		
		RegisterModuleDependences("main", "OnBeforeProlog", "stindustrial", "CStindustrial", "ShowPanel");
		
		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;

        UnRegisterModuleDependences("main", "OnBeforeProlog", "stindustrial", "CStindustrial", "ShowPanel");
		UnRegisterModule($this->MODULE_ID);

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
		
		
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/design2u.stindustrial/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/design2u.stindustrial/install/wizards", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards", true, true);
		return true;
		
		
		/*
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/design2u/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/design2u/install/wizards", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards", true, true);
		return true;
		*/

	}

	function InstallPublic()
	{
	}

	function UnInstallFiles()
	{
		return true;
	}
	
	
	function DoInstall()
	{
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();

         
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/design2u.stindustrial/install/step.php");
		
		
		/*
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/design2u/install/step.php");
		*/
	
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/design2u.stindustrial/install/unstep.php");
		
		/*
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/design2u/install/unstep.php");
		*/
		
	}
}
?>