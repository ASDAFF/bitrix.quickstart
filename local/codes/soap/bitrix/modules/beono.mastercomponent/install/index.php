<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class beono_mastercomponent extends CModule
{
    var $MODULE_ID = "beono.mastercomponent";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function beono_mastercomponent()
    {
        $arModuleVersion = array();
        include("version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = GetMessage("BEONO_MODULE_MASTERCOMP_INSTALL_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("BEONO_MODULE_MASTERCOMP_INSTALL_DESCRIPTION");
        
        $this->PARTNER_NAME = "beono";
		$this->PARTNER_URI = "http://dev.1c-bitrix.ru/community/webdev/user/14039/";
    }


    function InstallDB($install_wizard = true)
    {
        global $DB, $DBType, $APPLICATION;
        
        RegisterModule($this->MODULE_ID);
        RegisterModuleDependences("main", "OnPanelCreate", $this->MODULE_ID, "BeonoMasterComponent", "ShowCreateButton");

        return true;
    }

    function UnInstallDB($arParams = Array())
    {
        global $DB, $DBType, $APPLICATION;
        
        UnRegisterModuleDependences("main", "OnPanelCreate", $this->MODULE_ID, "BeonoMasterComponent", "ShowCreateButton"); 
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
    	return CopyDirFiles(dirname(__FILE__)."/wizards/beono/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/beono/", true, true);
    }

    function InstallPublic()
    {
    }

    function UnInstallFiles()
    {	
		DeleteDirFilesEx("/bitrix/wizards/beono/component/"); // Удаляем только мастер, а не всю папку beono
        return true;
    }

    function DoInstall()
    {
        global $APPLICATION, $step;

        $this->InstallFiles();
        $this->InstallDB(false);
        $this->InstallEvents();
        $this->InstallPublic();

        $APPLICATION->IncludeAdminFile(GetMessage("BEONO_MODULE_MASTERCOMP_INSTALL_TITLE"), dirname(__FILE__)."/step.php");
      
    }

    function DoUninstall()
    {
        global $APPLICATION, $step;

        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $APPLICATION->IncludeAdminFile(GetMessage("BEONO_MODULE_MASTERCOMP_UNINSTALL_TITLE"), dirname(__FILE__)."/unstep.php");
    }
}
?>