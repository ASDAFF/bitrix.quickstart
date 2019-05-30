<?php

/*
 * Code is distributed as-is
 * the Developer may change the code at its discretion without prior notice
 * Developers: Djo 
 * Website: http://zixn.ru
 * Twitter: https://twitter.com/Zixnru
 * Email: izm@zixn.ru
 */
IncludeModuleLangFile(__FILE__);

Class zixnru_getproperties extends CModule {

    var $MODULE_ID = "zixnru.getproperties";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $COMPONENT_NAME = "zixnru.getproperties.products";

    function zixnru_getproperties() {

        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = GetMessage("ZIXNRU_GETPROP_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage('ZIXNRU_GETPROP_MODULE_DESCRIPTION') . $this->COMPONENT_NAME;
        $this->PARTNER_NAME = "Djo";
        $this->PARTNER_URI = "http://zixn.ru";
    }

    function InstallFiles($arParams = array()) {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
        return true;
    }

    function UnInstallFiles() {
        DeleteDirFilesEx("/bitrix/components/zixnru/" . $this->COMPONENT_NAME); //Папка компонента
        return true;
    }

    function DoInstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage('ZIXNRU_GETPROP_MODULE_INSTALL') . $this->MODULE_ID, $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/step.php");
    }

    function DoUninstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage('ZIXNRU_GETPROP_MODULE_UNSTALL') . $this->MODULE_ID, $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep.php");
    }

}
