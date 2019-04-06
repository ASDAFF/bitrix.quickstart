<?
global $MESS;

$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
@require_once(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
IncludeModuleLangFile($strPath2Lang."/install/index.php");

if(class_exists("unisender_integration")) return;

Class unisender_integration extends CModule
{
    var $MODULE_ID = "unisender.integration";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "N";

	var $modulePath;

    function unisender_integration()
    {
	    $this->modulePath = str_replace("\\", "/", __FILE__);
	    $this->modulePath = substr($this->modulePath, 0, strlen($this->modulePath) - strlen("install/index.php"));
		require_once($this->modulePath."install/version.php");
	    $this->PARTNER_NAME = "Unisender Inc";
	    $this->PARTNER_URI = "http://unisender.com/";

	    if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
		    $this->MODULE_VERSION = $arModuleVersion["VERSION"];
		    $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		    $this->MODULE_NAME = GetMessage("UNISENDER_MODULE_NAME");
		    $this->MODULE_DESCRIPTION = GetMessage("UNISENDER_MODULE_DESCRIPTION");
	    } else {
		    $this->MODULE_VERSION = '1.7.0';
		    $this->MODULE_VERSION_DATE = '2014-06-23 00:00:00';
		    $this->MODULE_NAME = GetMessage("UNISENDER_MODULE_NAME");
		    $this->MODULE_DESCRIPTION = GetMessage("UNISENDER_MODULE_DESCRIPTION");
	    }


    }

    function DoInstall()
    {
        global $DB, $APPLICATION, $step;
        if ($this->InstallFiles()) {
			RegisterModule($this->MODULE_ID);
			$APPLICATION->IncludeAdminFile(
				GetMessage("UNISENDER_INSTALL_TITLE"),
				$this->modulePath."install/step1.php"
			);
		}
    }

    function DoUninstall()
    {
        global $DB, $APPLICATION, $step;
		if ($this->UnInstallFiles()) {
			UnRegisterModule($this->MODULE_ID);
			$APPLICATION->IncludeAdminFile(
				GetMessage("UNISENDER_UNINSTALL_TITLE"),
				$this->modulePath."install/unstep1.php"
			);
		}
    }

	function InstallFiles()
	{
		CopyDirFiles(
			$this->modulePath."install/admin",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin",
			true
		);
		CopyDirFiles(
			$this->modulePath."install/themes/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default",
			true,
			true
		);
		CopyDirFiles(
			$this->modulePath."install/js",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID,
			true,
			true
		);
		CopyDirFiles(
			$this->modulePath."install/images/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID,
			true
		);
		return true;
	}
	
	function UnInstallFiles($arParams = array())
	{
		DeleteDirFiles(
			$this->modulePath."install/admin/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin"
		);
		DeleteDirFiles(
			$this->modulePath."install/themes/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default"
		);
		DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID);
		DeleteDirFiles(
			$this->modulePath."install/images/",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID
		);
		return true;
	}
}
?>