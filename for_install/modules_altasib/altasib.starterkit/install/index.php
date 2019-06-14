<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\SiteTable;

Loc::loadMessages(__FILE__);

Class altasib_starterkit extends CModule
{
    var $MODULE_ID = "altasib.starterkit";
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";
    function altasib_starterkit()
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
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_NAME = GetMessage("ALTASIB_STARTERKIT_REG_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_STARTERKIT_REG_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = "ALTASIB";
        $this->PARTNER_URI = "https://www.altasib.ru/";
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

        $APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
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

        $APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
    }

    function registerEvents(){
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler(
            "main",
            "OnPageStart",
            $this->MODULE_ID,
            '\Altasib\Starterkit\Loader\Functions',
            "start",
            1
        );
    }
    function unRegisterEvents(){
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            "main",
            "OnPageStart",
            $this->MODULE_ID,
            '\Altasib\Starterkit\Loader',
            "start"
        );
    }

    function InstallDB(){

    }

    function InstallFiles()
    {
        $arDirectory = array(
            "bitrix",
            "local",
        );

        foreach ($arDirectory as $itemDir) {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/".$itemDir."/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/".$itemDir."/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/".$itemDir."/modules/".$this->MODULE_ID."/install/tools", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools", true, true);
        }


        return true;
    }
    function UninstallFiles()
    {
        DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/" . $this->MODULE_ID);
        $arDirectory = array(
            "bitrix",
            "local",
        );

        foreach ($arDirectory as $itemDir) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/".$itemDir."/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/".$itemDir."/modules/" . $this->MODULE_ID . "/install/tools", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools");
        }

    }

    function GetModuleRightList()
    {
        $arr = array(
            "reference_id" => array("D","R","W"),
            "reference" => array(
                "[D] ".GetMessage("REL_DENIED"),
                "[R] ".GetMessage("REL_VIEW"),
                "[W] ".GetMessage("REL_ADMIN"))
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