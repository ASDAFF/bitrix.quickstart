<?php
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));

IncludeModuleLangFile($PathInstall."/install.php");

if(class_exists("zionec_sitemap")) return;

Class zionec_sitemap extends CModule
{
    var $MODULE_ID = "zionec.sitemap";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $PARTNER_NAME;
    var $PARTNER_URI;

    function zionec_sitemap()
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
            $this->MODULE_VERSION = SITEMAP_VERSION;
            $this->MODULE_VERSION_DATE = SITEMAP_VERSION_DATE;
        }

        $this->PARTNER_NAME = 'zionec';
        $this->PARTNER_URI = 'http://new.zionec.ru';
        $this->MODULE_NAME = GetMessage("SITEMAP_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("SITEMAP_MODULE_DESCRIPTION");
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB();
        $GLOBALS['APPLICATION']->IncludeAdminFile(GetMessage("SITEMAP_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/install/step1.php");
    }

    function InstallDB()
    {
        global $DB, $APPLICATION;

        $this->errors = false;
        if(!$DB->Query("SELECT 'x' FROM b_sitemap_property", true))
            $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/zionec.sitemap/install/db/".strtolower($DB->type)."/install.sql");

        if($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("", $this->errors));
            return false;
        }

        RegisterModule("zionec.sitemap");

        return true;
    }

    function InstallFiles()
    {
        if($_ENV["COMPUTERNAME"]!='BX')
        {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/zionec.sitemap", true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/zionec.sitemap", true, true);
        }
        return true;
    }

    function InstallEvents() { return true; }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION, $step;
        $step = IntVal($step);
        if($step<2)
        {
            $APPLICATION->IncludeAdminFile(GetMessage("SITEMAP_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/zionec.sitemap/install/unstep1.php");
        }
        elseif($step==2)
        {
            $this->UnInstallDB(array(
                "savedata" => $_REQUEST["savedata"],
            ));
            $this->UnInstallFiles();
            $APPLICATION->IncludeAdminFile(GetMessage("SITEMAP_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/zionec.sitemap/install/unstep2.php");
        }
    }

    function UnInstallDB($arParams = Array())
    {
        global $APPLICATION, $DB, $errors;
        $this->errors = false;
        if (!$arParams['savedata'])
        {
            $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/zionec.sitemap/install/db/".strtolower($DB->type)."/uninstall.sql");
        }

        if(!empty($this->errors))
        {
            $APPLICATION->ThrowException(implode("", $this->errors));
            return false;
        }

        UnRegisterModule("zionec.sitemap");

        return true;
    }

    function UnInstallFiles($arParams = array())
    {
        global $DB;
        // Delete files
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/zionec.sitemap");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zionec.sitemap/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/zionec.sitemap");
        return true;
    }

    function UnInstallEvents() { return true; }

    function GetModuleRightList()
    {
        global $MESS;
        $arr = array(
            "reference_id" => array("D","R","W"),
            "reference" => array(
                "[D] ".GetMessage("SITEMAP_DENIED"),
                "[R] ".GetMessage("SITEMAP_OPENED"),
                "[W] ".GetMessage("SITEMAP_FULL"))
        );
        return $arr;
    }
}
?>