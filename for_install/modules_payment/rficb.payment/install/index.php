<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");

Class rficb_payment extends CModule
{
    var $MODULE_ID = "rficb.payment";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $PARTNER_NAME;
    var $PARTNER_URI;

    function rficb_payment()
    {
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("RFICB.PAYMENT_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("RFICB.PAYMENT_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage("RFICB.PAYMENT_PARTNER_NAME");
        $this->PARTNER_URI = "http://www.rficb.ru";
    }

    function DoInstall()
    {
        global $APPLICATION, $step, $errors;

        $step = IntVal($step);
        if($step<2)
            $APPLICATION->IncludeAdminFile(GetMessage("RFICB.PAYMENT_INSTALL_TITLE"),
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rficb.payment/install/step1.php");
        elseif($step==2){
            $errors = $this->errors;
            $this->InstallFiles();
            $this->InstallDB();            
            $APPLICATION->IncludeAdminFile(GetMessage("RFICB.PAYMENT_INSTALL_TITLE"),
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rficb.payment/install/step2.php");
        }
    }

    function DoUninstall()
    {
        global $APPLICATION, $step;

        $step = IntVal($step);
        if($step<2)
            $APPLICATION->IncludeAdminFile(GetMessage("RFICB.PAYMENT_UNINSTALL_TITLE"),
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rficb.payment/install/unstep1.php");
        elseif($step==2){
            $errors = $this->errors;
            $this->UnInstallFiles();
            $this->UnInstallDB();
            $APPLICATION->IncludeAdminFile(GetMessage("RFICB.PAYMENT_UNINSTALL_TITLE"),
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rficb.payment/install/unstep2.php");
        }
    }

    function InstallDB()
    {
            global $DB, $DBType, $APPLICATION;
            $this->errors = false;
            RegisterModule("rficb.payment");
			$arOptions = array("key", "secret_key","holdstatus");
            foreach($arOptions as $name) {
                    COption::SetOptionString("rficb.payment", $name, $_REQUEST[$name], "");
			}
            return true;
    }

    function UnInstallDB($arParams = array())
    {
            global $DB, $DBType, $APPLICATION;
            $this->errors = false;
            UnRegisterModule("rficb.payment");
            COption::RemoveOption("rficb.payment", "");
            return true;
    }


    function InstallFiles()
    {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rficb.payment/install/payment/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/rficb.payment/");
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rficb.payment/install/tools/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/rficb.payment/");
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rficb.payment/install/themes/.default/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/");
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rficb.payment/install/themes/.default/icons/rficb/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/icons/rficb/");
            return true;
    }

    function UnInstallFiles()
    {
            DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/rficb.payment");
            DeleteDirFilesEx("/bitrix/tools/rficb.payment/");
            DeleteDirFilesEx("/bitrix/themes/.default/icons/rficb/");
            return true;
    }
}
