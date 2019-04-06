<?php
/**
 * Created by Artmix.
 * User: Oleg Maksimenko <oleg.39style@gmail.com>
 * Date: 27.10.2014. Time: 12:51
 */

IncludeModuleLangFile(__FILE__);

class artmix_noticefixcore extends CModule
{
    const MODULE_ID = 'artmix.noticefixcore';
    var $MODULE_ID = 'artmix.noticefixcore';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function artmix_noticefixcore()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("ARTMIX_NOTICEFIXCORE_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("ARTMIX_NOTICEFIXCORE_MODULE_DESCRIPTION");

        $this->PARTNER_NAME = GetMessage("ARTMIX_NOTICEFIXCORE_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("ARTMIX_NOTICEFIXCORE_PARTNER_URI");
    }

    function GetModuleTasks()
    {
        return array();
    }

    function InstallDB($arParams = array())
    {
        global $DB, $DBType, $APPLICATION;

        $this->InstallTasks();
        RegisterModule($this->MODULE_ID);
        CModule::IncludeModule($this->MODULE_ID);

        RegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, '\Artmix\NoticeFixCore\AdminMessage', 'OnEndBufferContent');

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        UnRegisterModule($this->MODULE_ID);
        UnRegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, '\Artmix\NoticeFixCore\AdminMessage', 'OnEndBufferContent');

        if ($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }
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

    function InstallFiles($arParams = array())
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    function DoInstall()
    {
        global $USER, $APPLICATION;

        if ($USER->IsAdmin())
        {
            if ($this->InstallDB())
            {
                $this->InstallEvents();
                $this->InstallFiles();
            }
            $GLOBALS["errors"] = $this->errors;
        }
    }

    function DoUninstall()
    {
        global $DB, $USER, $DOCUMENT_ROOT, $APPLICATION, $step;

        if ($USER->IsAdmin())
        {
            if ($this->UnInstallDB())
            {
                $this->UnInstallEvents();
                $this->UnInstallFiles();
            }
            $GLOBALS["errors"] = $this->errors;
        }
    }
}
?>