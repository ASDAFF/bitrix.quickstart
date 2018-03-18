<?php
global $MESS,$APPLICATION;
if (!defined('SHEEPLA_DIR'))
{
    define('SHEEPLA_DIR', substr(dirname(__FILE__), 0, strlen(dirname(__FILE__))-strlen("/install")));
}

IncludeModuleLangFile(__FILE__);

require_once SHEEPLA_DIR . DIRECTORY_SEPARATOR . 'include.php';

/**
 * Simple wrapper function for CSheepla::WriteSheeplaLog()
 */
function __log($m)
{
    $sl = new CSheepla();
    $sl->WriteSheeplaLog('install/index.php', '', $m);
    unset($sl);
}

class sheepla_delivery extends CModule
{
    var $MODULE_ID = "sheepla.delivery";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function sheepla_delivery() {
        global $MESS;
        $arModuleVersion = array();
        include(SHEEPLA_DIR. '/install/' ."version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->PARTNER_NAME = "http://sheepla.ru";
        $this->PARTNER_URI = "http://sheepla.ru";
        $this->MODULE_NAME = GetMessage('SHEEPLA_SHEEPLA');
        $this->MODULE_DESCRIPTION = GetMessage('SHEEPLA_DESCRIPTION');

    }

    function DoInstall() {
        global $APPLICATION;
        include(SHEEPLA_DIR. '/' ."test.php");
        if(class_exists('TestSheeplaInstall')){
            $Test = new TestSheeplaInstall(true,SHEEPLA_DIR);
            $res =  $Test->RunTest();
            if($res['error']!=''){
                __log('During installation error was occured: ' . $res['error']);
                $APPLICATION->ThrowException($res['error']);
                return false;
            }else{
                __log('Start installation of module files');
                $this->InstallFiles();
                __log('Start installation database tables');
                $this->InstallDB();
                @mail('logs@sheepla.com', 'Bitrix installation '.$_SERVER['SERVER_NAME'].'', 'The module was installed on: HTTP_HOST:'.$_SERVER['HTTP_HOST'].' SERVER_NAME:'.$_SERVER['SERVER_NAME'].' SERVER_ADDR:'.$_SERVER['SERVER_ADDR'] . ' REMOTE_ADDR:'. $_SERVER['REMOTE_ADDR']);
                RegisterModule("sheepla.delivery");
                __log('Module was installed');
            }
        }else{
            __log("Can't find TestSheeplaInstall class. Please check module files.");
            $APPLICATION->ThrowException("Can't find TestSheeplaInstall class. Please check module files.");
            return false;
        }

    }

    function DoUninstall() {
            $this->UnInstallFiles();
            $this->UnInstallDB();
            UnRegisterModule($this->MODULE_ID);
    }

    function InstallDB() {
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        $this->errors = $DB->RunSQLBatch(SHEEPLA_DIR . DIRECTORY_SEPARATOR . 'install'.DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.$DBType.DIRECTORY_SEPARATOR."install.sql");
        if($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("", $this->errors));
            return false;
        }
        RegisterModuleDependences("main", "OnPageStart", "sheepla.delivery", "CSheepla", "OnPageStartAddHeaders");
        RegisterModuleDependences("sale", "OnOrderAdd", "sheepla.delivery", "CSheepla", "OnOrderAdd");
        RegisterModuleDependences("sale", "OnOrderUpdate", "sheepla.delivery", "CSheepla", "OnOrderUpdate");


        return true;
    }

    function UnInstallDB($arParams = array()) {
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        $this->errors = $DB->RunSQLBatch(SHEEPLA_DIR . DIRECTORY_SEPARATOR . 'install'.DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.$DBType.DIRECTORY_SEPARATOR."uninstall.sql");
        if($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("", $this->errors));
            return false;
        }
        UnRegisterModuleDependences("main", "OnPageStart", "sheepla.delivery", "CSheepla", "OnPageStartAddHeaders");
        UnRegisterModuleDependences("sale", "OnOrderAdd", "sheepla.delivery", "CSheepla", "OnOrderAdd");
        UnRegisterModuleDependences("sale", "OnOrderUpdate", "sheepla.delivery", "CSheepla", "OnOrderUpdate");

        return true;
    }

    function InstallFiles() {
        global $APPLICATION;

        CopyDirFiles(SHEEPLA_DIR . "/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
        CopyDirFiles(SHEEPLA_DIR . "/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
        CopyDirFiles(SHEEPLA_DIR . "/install/delivery/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_delivery/", true, true);

        __log("Trying to create gateway file");
        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/gateway.php')) {
            __log('Gateway does not exist at '.$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/gateway.php');
            if (mkdir($_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID)) {
                if (!copy(SHEEPLA_DIR."/install/tools/gateway.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/gateway.php')) {
                    __log("Can't copy file: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/gateway.php');
                    $APPLICATION->ThrowException("Can't copy file: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/gateway.php');
                    return false;
                }
                __log("Gateway file created at ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/gateway.php');
                if (!copy(SHEEPLA_DIR."/install/tools/ajax.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/ajax.php')) {
                    __log("Can't copy file: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/ajax.php');
                    $APPLICATION->ThrowException("Can't copy file: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/ajax.php');
                    return false;
                }
                __log("Ajax controller created at ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID.'/ajax.php');
            } else {
                __log("Can't create folder: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID);
                $APPLICATION->ThrowException("Can't create folder: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID);
                return false;
            }
        }
//TODO
/** Delete copying js and css files*/
        __log("Trying to install module");
        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID.'/css')) {
            __log('Folder in js folder does not exist');
            if (!mkdir($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID.'/css', 0755, true)) {
                __log("Can't create folder: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID."/css");
                $APPLICATION->ThrowException("Can't create folder: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID.'/css');
                return false;
            }
            __log("Created folder: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID."/css");
        }
//TODO
/** Delete copying js and css files*/
        __log("Trying to copy js and css files");
        $files = array('/css/sheepla.css', '/sheepla.admin.js', '/sheepla.checkout.js');
        foreach ($files as $file) {
            __log("File: " . $file);
            if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID.$file))
                __log('File does not exist: '.$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID.$file);
                if (!copy(SHEEPLA_DIR."/install".$file, $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID.$file)) {
                    __log("Can't copy file: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID.$file);
                    $APPLICATION->ThrowException("Can't copy file: ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID.$file);
                    return false;
                }
        }

        __log("Trying to copy delivery_sheepla.php");
        if (!file_exists(SHEEPLA_DIR."/delivery/delivery_sheepla.php")) {
            __log('File does not exist: '.SHEEPLA_DIR."/delivery/delivery_sheepla.php");
            if (!copy(SHEEPLA_DIR."/install/delivery/delivery_sheepla.php", SHEEPLA_DIR."/delivery/delivery_sheepla.php")) {
                __log("Can't copy file: ".SHEEPLA_DIR."/delivery/delivery_sheepla.php");
                $APPLICATION->ThrowException("Can't copy file: ".SHEEPLA_DIR."/delivery/delivery_sheepla.php");
                return false;
            }
            __log("Created file: ".SHEEPLA_DIR."/delivery/delivery_sheepla.php");
            @chmod(SHEEPLA_DIR."/delivery", 0755);
            @chmod(SHEEPLA_DIR."/delivery/delivery_sheepla.php", 0755);
        }

        return true;
    }

    function UnInstallFiles() {
        DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID);
        DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".$this->MODULE_ID);
        DeleteDirFiles(SHEEPLA_DIR . "/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        DeleteDirFiles(SHEEPLA_DIR . "/install/delivery/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_delivery");
        DeleteDirFiles(SHEEPLA_DIR . "/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
        return true;
    }

}