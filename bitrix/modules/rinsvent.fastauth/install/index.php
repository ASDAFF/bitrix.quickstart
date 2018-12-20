<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\SiteTable;

Loc::loadMessages(__FILE__);

Class rinsvent_fastauth extends CModule
{
    var $MODULE_ID = "rinsvent.fastauth";
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";
    function rinsvent_fastauth()
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
        $this->MODULE_NAME = GetMessage("RINSVENT_FASTAUTH_REG_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("RINSVENT_FASTAUTH_REG_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = "RINSVENT";
        $this->PARTNER_URI = "http://www.rinsvent.ru/";
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

        $this->createUserFields();
        $this->registerEvents();

        $APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
    }

    function registerEvents(){
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            "\Rinsvent\Fastauth\Event\Functions",
            "showForm"
        );

        $eventManager->registerEventHandler(
            "main",
            "OnAfterUserLogout",
            $this->MODULE_ID,
            "\Rinsvent\Fastauth\Event\Functions",
            "afterUserLogout"
        );



    }

    function createUserFields(){
        $rsData = CUserTypeEntity::GetList( array(), array("FIELD_NAME" => "UF_RINSVENT_FA") );
        if(!$arRes = $rsData->Fetch())
        {
            $arFields = array(
                "ENTITY_ID" => "USER",
                "FIELD_NAME" => "UF_RINSVENT_FA",
                "USER_TYPE_ID" => "string",
                "XML_ID" => "",
                "SORT" => "100",
                "SHOW_FILTER" => "S",
                "SETTINGS" => array(
                    "DEFAULT_VALUE"=>"",
                    "SIZE"=>20,
                    "ROWS"=>"1",
                    "MIN_LENGTH"=>0,
                    "MAX_LENGTH"=>0,
                    "REGEXP"=>"",
                ),
                "EDIT_FORM_LABEL" => array(
                    "ru" => GetMessage("FORM_LABEL_RU"),
                    "en" => GetMessage("FORM_LABEL_EN"),
                ),
                "LIST_COLUMN_LABEL" => array(
                    "ru" => GetMessage("FORM_LABEL_RU"),
                    "en" => GetMessage("FORM_LABEL_EN"),
                ),
                "LIST_FILTER_LABEL" => array(
                    "ru" => GetMessage("FORM_LABEL_RU"),
                    "en" => GetMessage("FORM_LABEL_EN"),
                ),
                "ERROR_MESSAGE" => array(
                    "ru" => GetMessage("FORM_LABEL_RU"),
                    "en" => GetMessage("FORM_LABEL_EN"),
                ),
                "HELP_MESSAGE" => array(
                    "ru" => GetMessage("FORM_LABEL_RU"),
                    "en" => GetMessage("FORM_LABEL_EN"),
                ),
            );

            $obUserField  = new CUserTypeEntity;
            $obUserField->Add($arFields);
        }

        $rsData = CUserTypeEntity::GetList( array(), array("FIELD_NAME" => "UF_RINSVENT_FA_USE") );
        if(!$arRes = $rsData->Fetch())
        {
            $arFields = array(
                "ENTITY_ID" => "USER",
                "FIELD_NAME" => "UF_RINSVENT_FA_USE",
                "USER_TYPE_ID" => "boolean",
                "XML_ID" => "",
                "SORT" => "100",
                "SHOW_FILTER" => "I",
                "EDIT_FORM_LABEL" => array(
                    "ru" => GetMessage("FORM_LABEL_USE_RU"),
                    "en" => GetMessage("FORM_LABEL_USE_EN"),
                ),
                "LIST_COLUMN_LABEL" => array(
                    "ru" => GetMessage("FORM_LABEL_USE_RU"),
                    "en" => GetMessage("FORM_LABEL_USE_EN"),
                ),
                "LIST_FILTER_LABEL" => array(
                    "ru" => GetMessage("FORM_LABEL_USE_RU"),
                    "en" => GetMessage("FORM_LABEL_USE_EN"),
                ),
                "ERROR_MESSAGE" => array(
                    "ru" => GetMessage("FORM_LABEL_USE_RU"),
                    "en" => GetMessage("FORM_LABEL_USE_EN"),
                ),
                "HELP_MESSAGE" => array(
                    "ru" => GetMessage("FORM_LABEL_USE_RU"),
                    "en" => GetMessage("FORM_LABEL_USE_EN"),
                ),
            );

            $obUserField  = new CUserTypeEntity;
            $obUserField->Add($arFields);
        }
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

    function InstallDB(){

    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/css", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/".$this->MODULE_ID, true, true);

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
        
        return true;
    }
    function UninstallFiles()
    {
        DeleteDirFilesEx("/bitrix/themes/rinsvent/fastauth");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/css", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install", $_SERVER["DOCUMENT_ROOT"]."/bitrix");

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
    function unRegisterEvents(){
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            "\Rinsvent\Event\Functions",
            "showForm"
        );

        $eventManager->unRegisterEventHandler(
            "main",
            "OnAfterUserLogout",
            $this->MODULE_ID,
            "\Rinsvent\Fastauth\Event\Functions",
            "afterUserLogout"
        );
    }
}
?>