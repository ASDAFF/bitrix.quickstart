<?
global $MESS;
IncludeModuleLangFile(__FILE__);

Class gsa_modul extends CModule
{
    var $MODULE_ID = "gsa.modul";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;


    function gsa_modul()
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
        $this->MODULE_NAME = GetMessage("M_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("M_DESC");
        $this->PARTNER_NAME = "GetShopApp";
        $this->PARTNER_URI = "http://www.getshopapp.com";
    }

	function InstallFiles()
    {
        if($_ENV["COMPUTERNAME"]!='BX')
        {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
			//копирую файл для хуков
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/hook_files", $_SERVER["DOCUMENT_ROOT"], true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/ajax_files", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
			//копирую классы
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/classes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/classes", true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/profile", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/catalog_export", true, true);
        }
        return true;
    }

    function InstallDB()
    {
        global $APPLICATION;
        global $DB;
        global $DBType;
        global $errors;

        //регистрируем обработчки
        RegisterModule($this->MODULE_ID);
        RegisterModuleDependences("sale", "OnOrderNewSendEmail", $this->MODULE_ID, "cGsa", "OrderCreateBitrix");
        RegisterModuleDependences("sale", "OnOrderUpdate", $this->MODULE_ID, "cGsa", "OrderUpdateBitrix");
        RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID,"cGsa", "LaunchpadSetter");
        RegisterModuleDependences("main", "OnAfterUserUpdate", $this->MODULE_ID,"cGsa", "UserUpdatedBitrix");

        

        return true;
    }




	function UnInstallFiles($arParams = array())
    {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
        DeleteDirFilesEx("/bitrix/themes/.default/icons/gsa.modul/");//icons
        DeleteDirFilesEx(BX_PERSONAL_ROOT."/tmp/gsa.modul/");
		//gsa_block.php
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/hook_files", $_SERVER["DOCUMENT_ROOT"]);
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/ajax_files", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools");
		//копирую классы
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/classes/general", $_SERVER["DOCUMENT_ROOT"]."/bitrix/classes/general");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/gsa.modul/install/profile", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/catalog_export");

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $APPLICATION, $DB, $errors;
        //удаляем обработчки
        COption::RemoveOption($this->MODULE_ID);
        UnRegisterModuleDependences("sale", "OnOrderNewSendEmail", $this->MODULE_ID, "cGsa", "OrderCreateBitrix");
        UnRegisterModuleDependences("sale", "OnOrderUpdate", $this->MODULE_ID, "cGsa", "OrderUpdateBitrix");
        UnRegisterModuleDependences("sale", "OnBeforeProlog", $this->MODULE_ID,"cGsa","LaunchpadSetter");
        UnRegisterModuleDependences("main", "OnAfterUserUpdate", $this->MODULE_ID,"cGsa", "UserUpdatedBitrix");
        UnRegisterModule($this->MODULE_ID);
        return true;
    }


    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        // Install events
        $this->InstallFiles();
        $this->InstallDB();
        $APPLICATION->IncludeAdminFile(GetMessage("M_DOINST"), $DOCUMENT_ROOT."/bitrix/modules/gsa.modul/install/step.php");
        return true;
    }



    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        $this->UnInstallDB();
        $APPLICATION->IncludeAdminFile(GetMessage("M_DOUNINST"), $DOCUMENT_ROOT."/bitrix/modules/gsa.modul/install/unstep.php");
        return true;
    }

}