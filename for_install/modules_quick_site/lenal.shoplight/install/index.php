<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class lenal_shoplight extends CModule {

    var $MODULE_ID = "lenal.shoplight";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function lenal_shoplight() {
        
        $this->MODULE_ID = "lenal.shoplight";
        $this->MODULE_NAME = GetMessage("LLL_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("LLL_MODULE_DESCR");
        $this->PARTNER_NAME = GetMessage("LLL_MODULE_COMPANY");
        $this->PARTNER_URI = "http://lenal.biz";
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
    }

    function InstallDB($install_wizard = true) {
        global $DB, $DBType, $APPLICATION;

        RegisterModule("lenal.shoplight");

        return true;
    }

    function UnInstallDB($arParams = Array()) {
        global $DB, $DBType, $APPLICATION;

        UnRegisterModule("lenal.shoplight");

        return true;
    }

    function InstallEvents() {
        return true;
    }

    function UnInstallEvents() {
        return true;
    }

    function InstallFiles() {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/lenal.shoplight/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/lenal.shoplight/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/lenal.shoplight", true, true);

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/lenal.shoplight/install/wizards/bitrix/eshop.mobile", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/wizards/bitrix/eshop.mobile", true, true);

        return true;
    }

    function InstallPublic() {
        
    }

    function UnInstallFiles() {
        return true;
    }

    function DoInstall() {
        global $APPLICATION, $step;

        $this->InstallFiles();
        $this->InstallDB(false);
        $this->InstallEvents();
        $this->InstallPublic();
        return true;
    }

    function DoUninstall() {
        global $APPLICATION, $step;

        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        return true;
    }

}

?>