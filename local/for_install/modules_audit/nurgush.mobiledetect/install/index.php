<?php
IncludeModuleLangFile(__FILE__);
 
if(class_exists("nurgush.mobiledetect")) return;
 
Class nurgush_mobiledetect extends CModule
{
    var $MODULE_ID = "nurgush.mobiledetect";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    var $errors;

    function nurgush_mobiledetect()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__)."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("nurgush_mobiledetect_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("nurgush_mobiledetect_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("nurgush_mobiledetect_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("nurgush_mobiledetect_PARTNER_URI");
    }

    function DoInstall()
    {
        $this->InstallFiles();
        RegisterModule("nurgush.mobiledetect");
    }

    function DoUninstall()
    {
        $this->UnInstallFiles();
        UnRegisterModule("nurgush.mobiledetect");
    }
}
?>