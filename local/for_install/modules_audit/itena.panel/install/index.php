<?
IncludeModuleLangFile(__FILE__);

if(class_exists("itena.panel")) return;
class itena_panel extends CModule
{
	var $MODULE_ID = "itena.panel";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	var $errors;

	function itena_panel()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = PANEL_VERSION;
			$this->MODULE_VERSION_DATE = PANEL_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("PANEL_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("PANEL_MODULE_DESC");
    $this->PARTNER_NAME = "Компания Айтена"; 
    $this->PARTNER_URI = "http://itena.ru";

	}

	function DoInstall()
	{
    global $DB, $APPLICATION, $step;
    $step = IntVal($step);
    $this->InstallDB();
    $this->InstallFiles();
    $GLOBALS["errors"] = $this->errors;
    $APPLICATION->IncludeAdminFile(GetMessage("PANEL_INST_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/install/step.php");
	}
        
	function DoUninstall()
	{
    global $DB, $APPLICATION, $step;
    $this->UnInstallDB();
    $this->UnInstallFiles();
    $APPLICATION->IncludeAdminFile(GetMessage("PANEL_UNINST_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/install/unstep.php");
	}
        
	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

    RegisterModule("itena.panel");
	}
  
	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		UnRegisterModule("itena.panel");
	}

	function InstallFiles($arParams = array())
	{
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/itena.panel", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		DeleteDirFilesEx("/bitrix/themes/.default/icons/itena.panel/");
		DeleteDirFilesEx("/bitrix/images/itena.panel/");
	}
}
?>