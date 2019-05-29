<?

IncludeModuleLangFile(__FILE__);

if (class_exists("cetacs_multiedit"))
    return;

class cetacs_multiedit extends CModule
{

    var $MODULE_ID = "cetacs.multiedit";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("cetacs_mulltiedit_module_name");
        $this->MODULE_DESCRIPTION = GetMessage("cetacs_mulltiedit_module_desc");
        $this->PARTNER_NAME = GetMessage("cetacs_mulltiedit_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("cetacs_mulltiedit_PARTNER_URI");
    }

    function InstallDB($arParams = array())
    {
        RegisterModule($this->MODULE_ID);
        RegisterModuleDependences('main', 'OnAdminListDisplay', $this->MODULE_ID, '\Cetacs\MultiEdit\Events', 'onAdminListDisplay');
        RegisterModuleDependences('main', 'OnAdminSubListDisplay', $this->MODULE_ID, '\Cetacs\MultiEdit\Events', 'onAdminListDisplay');
        return true;
    }

    function UnInstallDB($arParams = array())
    {
        UnRegisterModuleDependences('main', 'OnAdminListDisplay', $this->MODULE_ID, '\Cetacs\MultiEdit\Events', 'onAdminListDisplay');
        UnRegisterModuleDependences('main', 'OnAdminSubListDisplay', $this->MODULE_ID, '\Cetacs\MultiEdit\Events', 'onAdminListDisplay');
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    function InstallFiles($arParams = array())
    {
        $src = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/tools";
        $dst = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools/" . $this->MODULE_ID;
        CopyDirFiles($src, $dst, true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/tools/" . $this->MODULE_ID);
        return true;
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION, $step;
        $FM_RIGHT = $APPLICATION->GetGroupRight($this->MODULE_ID);
        if ($FM_RIGHT != "D") {
            $this->InstallDB();
            $this->InstallFiles();
        }
        return true;
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION, $step;
        $FM_RIGHT = $APPLICATION->GetGroupRight($this->MODULE_ID);
        if ($FM_RIGHT != "D") {
            $this->UnInstallDB();
            $this->UnInstallFiles();
        }
    }
}