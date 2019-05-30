<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if (class_exists('lastworld_props'))
{
    return;
}

class lastworld_props extends CModule
{
    var $MODULE_ID = 'lastworld.props';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    public $errors;

    public function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->PARTNER_NAME = "DoctorBooooom";
        $this->PARTNER_URI = 'https://www.lastworld.ru/';
        $this->MODULE_NAME = Loc::getMessage('LW_PROPS_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('LW_PROPS_DESC');
    }

    function InstallDB($arParams = array())
    {

        return true;
    }

    function InstallFiles()
    {
        //JS
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lastworld.props/install/data/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/lastworld.props", true, true);
        //CSS
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lastworld.props/install/data/css", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/lastworld.props", true, true);
        //Images
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/lastworld.props/install/data/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/lastworld.props", true, true);

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        return true;
    }

    function UnInstallFiles()
    {
        //JS
        DeleteDirFilesEx("/bitrix/js/lastworld.props");
        //CSS
        DeleteDirFilesEx("/bitrix/css/lastworld.props");
        //Images
        DeleteDirFilesEx("/bitrix/images/lastworld.props");

        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;

        $this->InstallDB();
        $this->InstallFiles();

        RegisterModule($this->MODULE_ID);
        CModule::IncludeModule($this->MODULE_ID);

        //Register properties
        RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "LastWorld\\Property\\CYouTubeProperty", "GetUserTypeDescription");
        RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "LastWorld\\Property\\CColorProperty", "GetUserTypeDescription");

        //Register Yandex.Disk cloud
        //RegisterModuleDependences("clouds", "OnGetStorageService", $this->MODULE_ID, "LastWorld\\Cloud\\CYandexDiskCloud", "GetObject");

    }

    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallFiles();

        //UnRegister properties
        UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "LastWorld\\Property\\CYouTubeProperty", "GetUserTypeDescription");
        UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "LastWorld\\Property\\CColorProperty", "GetUserTypeDescription");

        //UnRegister Yandex.Disk cloud
        //UnRegisterModuleDependences("clouds", "OnGetStorageService", $this->MODULE_ID, "LastWorld\\Cloud\\CYandexDiskCloud", "GetObject");

        UnRegisterModule($this->MODULE_ID);
    }
}