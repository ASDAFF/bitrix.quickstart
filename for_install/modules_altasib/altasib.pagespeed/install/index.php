<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\SiteTable;

Loc::loadMessages(__FILE__);

Class altasib_pagespeed extends CModule
{
    var $MODULE_ID = "altasib.pagespeed";
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function altasib_pagespeed()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_NAME = GetMessage("ALTASIB_PAGESPEED_REG_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_PAGESPEED_REG_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = "ALTASIB";
        $this->PARTNER_URI = "https://altasib.ru";
    }

    function DoInstall()
    {
        global $APPLICATION, $step;
        $step = IntVal($step);
        $this->InstallFiles();
        $this->InstallDB();
        $this->InstallIblock();
        $GLOBALS["errors"] = $this->errors;
        RegisterModule($this->MODULE_ID);

        $this->registerEvents();

        $APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/step1.php");
    }

    function registerEvents(){
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler(
            "main",
            "OnPageStart",
            $this->MODULE_ID,
            '\Altasib\Pagespeed\Events',
            "onPageStartHandler",
            1
        );
    }
    function unRegisterEvents(){
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            "main",
            "OnPageStart",
            $this->MODULE_ID,
            '\Altasib\Pagespeed\Events',
            "onPageStartHandler"
        );
    }

    function DoUninstall()
    {
        global $APPLICATION, $step;
        $step = IntVal($step);

        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        UnRegisterModule($this->MODULE_ID);

        $this->unRegisterEvents();

        $APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep1.php");
    }

    function InstallDB()
    {

    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/" . $this->MODULE_ID, true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/css", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/css/" . $this->MODULE_ID, true, true);

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/tools", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/themes", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes", true, true);

        return true;
    }

    function UninstallFiles()
    {
        DeleteDirFilesEx("/bitrix/themes/rinsvent/" . $this->MODULE_ID);
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/css", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/css");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install", $_SERVER["DOCUMENT_ROOT"] . "/bitrix");

    }

    function GetModuleRightList()
    {
        $arr = array(
            "reference_id" => array("D", "R", "W"),
            "reference" => array(
                "[D] " . GetMessage("REL_DENIED"),
                "[R] " . GetMessage("REL_VIEW"),
                "[W] " . GetMessage("REL_ADMIN"))
        );
        return $arr;
    }

    function InstallIblock()
    {

    }

    function UnInstallEvents()
    {

    }
}

?>