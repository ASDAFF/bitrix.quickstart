<?php
/**
 * 
 * @author dev2fun (darkfriend)
 * @copyright (c) 2016, darkfriend
 * @version 1.0.0
 * 
 */
IncludeModuleLangFile(__FILE__);

if(class_exists("dev2fun_authemail")) return;

Class dev2fun_authemail extends CModule
{
    var $MODULE_ID = "dev2fun.authemail";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    function dev2fun_authemail(){
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
                $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        } else {
                $this->MODULE_VERSION = '1.0.0';
                $this->MODULE_VERSION_DATE = '2016-05-30 15:00:00';
        }
        $this->MODULE_NAME = GetMessage("DEV2FUN_MODULE_NAME_AUTHEMAIL");
        $this->MODULE_DESCRIPTION = GetMessage("DEV2FUN_MODULE_DESCRIPTION_AUTHEMAIL");
        $this->PARTNER_NAME = "dev2fun";
        $this->PARTNER_URI = "http://dev2fun.com/";
    }

    function DoInstall(){
        global $APPLICATION;
        if(!check_bitrix_sessid()) return;
        
        $APPLICATION->IncludeAdminFile(GetMessage("STEP1"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
    }

    function DoUninstall(){
        global $APPLICATION;
        if(!check_bitrix_sessid()) return;

        $APPLICATION->IncludeAdminFile(GetMessage("UNSTEP1"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
    }
}
?>