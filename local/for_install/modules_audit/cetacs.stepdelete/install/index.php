<?

IncludeModuleLangFile(__FILE__);

if (class_exists("cetacs_stepdelete"))
    return;

class cetacs_stepdelete extends CModule {

    var $MODULE_ID = "cetacs.stepdelete";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    function __construct() {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("cetacs_stepdelete_module_name");
        $this->MODULE_DESCRIPTION = GetMessage("cetacs_stepdelete_module_desc");
        $this->PARTNER_NAME = GetMessage("cetacs_stepdelete_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("cetacs_stepdelete_PARTNER_URI");
    }

    function InstallDB($arParams = array()) {
        RegisterModule($this->MODULE_ID);
        RegisterModuleDependences('main', 'OnAdminListDisplay', $this->MODULE_ID, 'CCetacs_sd', 'OnAdminListDisplay');
        return true;
    }

    function UnInstallDB($arParams = array()) {
        UnRegisterModuleDependences('main', 'OnAdminListDisplay', $this->MODULE_ID, 'CCetacs_sd', 'OnAdminListDisplay');
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    function InstallFiles($arParams = array()) {
        return true;
    }

    function UnInstallFiles() {
        return true;
    }

    function DoInstall() {
        global $DOCUMENT_ROOT, $APPLICATION, $step;
        $FM_RIGHT = $APPLICATION->GetGroupRight($this->MODULE_ID);
        if ($FM_RIGHT != "D") {
            $this->InstallDB();
            $this->InstallFiles();
        }
        $APPLICATION->IncludeAdminFile(GetMessage("SCOM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/cetacs.stepdelete/install/step.php");
    }

    function DoUninstall() {
        global $DOCUMENT_ROOT, $APPLICATION, $step;
        $FM_RIGHT = $APPLICATION->GetGroupRight($this->MODULE_ID);
        if ($FM_RIGHT != "D") {
            $this->UnInstallDB();
            $this->UnInstallFiles();
        }
        $APPLICATION->IncludeAdminFile(GetMessage("SCOM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/cetacs.stepdelete/install/unstep.php");
    }

}

?>