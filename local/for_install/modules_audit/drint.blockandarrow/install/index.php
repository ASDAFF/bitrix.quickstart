<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class drint_blockandarrow extends CModule
{
        var $MODULE_ID = "drint.blockandarrow";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;

        function drint_blockandarrow()
        {
                $this->MODULE_VERSION = '1.0.0';
                $this->MODULE_VERSION_DATE = '2014-06-22';
                $this->MODULE_NAME = GetMessage("MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
				$this->PARTNER_NAME = GetMessage("MODULE_AUTHOR");
				$this->PARTNER_URI = GetMessage("MODULE_URI");
        }
		
        function DoInstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->InstallFiles();
                $this->InstallDB();

                $GLOBALS["errors"] = $this->errors;
                $APPLICATION->IncludeAdminFile(GetMessage("INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/drint.blockandarrow/install/step1.php");
        }
        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->UnInstallDB();
                $this->UnInstallFiles();
                $APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/drint.blockandarrow/install/unstep1.php");
        }
        function InstallDB()
        {
                $this->errors = false;

                RegisterModule("drint.blockandarrow");
                RegisterModuleDependences("main","OnBeforeEndBufferContent","drint.blockandarrow","drint_class","blockAndUp", "100");
        }
        function UnInstallDB($arParams = array())
        {
                $this->errors = false;

                UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", "drint.blockandarrow", "drint_class", "blockAndUp");
                COption::RemoveOption("drint_blockandarrow");
                UnRegisterModule("drint.blockandarrow");

                return true;

        }

        function InstallFiles()
        {
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/drint.blockandarrow/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/drint.blockandarrow/", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/drint.blockandarrow/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/drint.blockandarrow/", true, true);
                return true;
        }

        function UnInstallFiles()
        {
                DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/drint.blockandarrow");
                DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/images/drint.blockandarrow");
                return true;
        }
}
?>
