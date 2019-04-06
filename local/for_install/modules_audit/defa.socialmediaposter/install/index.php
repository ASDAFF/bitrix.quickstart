<?
IncludeModuleLangFile(__FILE__);

if(class_exists("defa_socialmediaposter")) return;

class defa_socialmediaposter extends CModule {

	var $MODULE_ID = "defa.socialmediaposter";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "N";

	function defa_socialmediaposter()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SOCIALMEDIAPOSTER_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SOCIALMEDIAPOSTER_INSTALL_DESCRIPTION");
        
        $this->PARTNER_NAME = "Defa Interaktiv";
        $this->PARTNER_URI = "http://www.idefa.ru";
	}

    function InstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        $this->errors = false;

        RegisterModule($this->MODULE_ID);
        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        $this->errors = false;

        UnRegisterModule($this->MODULE_ID);

        return true;
    }

	function InstallEvents()
	{
		RegisterModuleDependences("main", "OnEventLogGetAuditTypes", "defa.socialmediaposter", "DSocialMediaPosterEvent", "OnEventLogGetAuditTypes");
		RegisterModuleDependences("main", "OnProlog", "defa.socialmediaposter", "DSocialMediaPosterAJAX", "OnProlog");

		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", "defa.socialmediaposter", "DSocialMediaPosterCIBlockProperty", "GetUserTypeDescription");

		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "defa.socialmediaposter", "DSocialMediaPosterCIBlockEvent", "OnAfterIBlockElementAdd");

		RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyAdd", "defa.socialmediaposter", "DSocialMediaPosterCIBlockEvent", "OnBeforeIBlockPropertyAddUpdate");
		RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", "defa.socialmediaposter", "DSocialMediaPosterCIBlockEvent", "OnBeforeIBlockPropertyAddUpdate");

		return true;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences("main", "OnEventLogGetAuditTypes", "defa.socialmediaposter", "DSocialMediaPosterEvent", "OnEventLogGetAuditTypes");
		UnRegisterModuleDependences("main", "OnProlog", "defa.socialmediaposter", "DSocialMediaPosterAJAX", "OnProlog");

		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", "defa.socialmediaposter", "DSocialMediaPosterCIBlockProperty", "GetUserTypeDescription");

		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "defa.socialmediaposter", "DSocialMediaPosterCIBlockEvent", "OnAfterIBlockElementAdd");

		UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyAdd", "defa.socialmediaposter", "DSocialMediaPosterCIBlockEvent", "OnBeforeIBlockPropertyAddUpdate");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", "defa.socialmediaposter", "DSocialMediaPosterCIBlockEvent", "OnBeforeIBlockPropertyAddUpdate");

		return true;
	}

    function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/defa.socialmediaposter", true, true);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/js/defa.socialmediaposter");
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $step;

        $this->InstallDB();
		$this->InstallEvents();
		$this->InstallFiles();
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
