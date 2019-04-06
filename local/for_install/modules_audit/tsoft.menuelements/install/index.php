<?
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
IncludeModuleLangFile($strPath2Lang."/install.php");

Class tsoft_menuelements extends CModule
{
    var $MODULE_ID = "tsoft.menuelements";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	var $errors;

    function tsoft_menuelements()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");


        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("IBLOCK_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("IBLOCK_INSTALL_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage("SPER_PARTNER");
		$this->PARTNER_URI = GetMessage("PARTNER_URI");
	}

    function InstallFiles()
	{
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tsoft.menuelements/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		return true;
	}

    function UnInstallFiles()
	{
		return true;
	}

    function DoInstall()
	{
		global $APPLICATION, $step, $obModule;
        RegisterModule("tsoft.menuelements");
        $this->InstallFiles();

        return true;
	}

	function DoUninstall()
	{
		global $APPLICATION, $step, $obModule;
        UnRegisterModule("tsoft.menuelements");

        return true;
	}
}