<?
/**
 * Copyright (c) 28/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");


Class Local_Lib extends CModule
{
//	var $MODULE_ID = "local.lib";
//	var $MODULE_VERSION;
//	var $MODULE_VERSION_DATE;
//	var $MODULE_NAME;
//	var $MODULE_DESCRIPTION;
//	var $MODULE_CSS;
//
//	var $errors;

    const MODULE_ID = 'local.lib'; // ID модуля
    var $MODULE_ID = 'local.lib';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("LIB_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("LIB_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("LIB_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("LIB_PARTNER_URI");
    }



        /**
         * Установка БД
         */
	function InstallDB() {
            RegisterModule($this->MODULE_ID);
            return true;
	}

        /**
         * Удаление БД
         */
	function UnInstallDB($arParams = array()){
            UnRegisterModule($this->MODULE_ID);
            return true;
	}

        /**
         * Установка обработчиков событий
         */
	function InstallEvents() {
            return true;
	}

        /**
         * Удаление обработчиков событий
         */
	function UnInstallEvents()
	{
            return true;
	}

        /**
         * Установка файлов
         */
	function InstallFiles() {
//            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/local.lib/install/components",
//                         $_SERVER["DOCUMENT_ROOT"]."/local/components/local.lib", true, true, false, ".svn" );
            return true;
	}

        /**
         * Удаление файлов
         */
	function UnInstallFiles(){
//            DeleteDirFilesEx("/local/components/local.lib");
            return true;
	}

        /**
         * Установка модуля
         */
	function DoInstall() {
            if (!IsModuleInstalled($this->MODULE_ID)) {
                $this->InstallFiles();
                $this->InstallDB();
                $this->InstallEvents();
            }
	}

        /**
         * Удаление модуля
         */
	function DoUninstall() {
            $this->UnInstallDB();
            $this->UnInstallEvents();
            $this->UnInstallFiles();
	}
}
?>
