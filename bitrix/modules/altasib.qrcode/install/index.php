<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));

IncludeModuleLangFile(__FILE__);
if(class_exists("altasib.qrcode")) return;

Class altasib_qrcode extends CModule
{
        var $MODULE_ID = "altasib.qrcode";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;

        function altasib_qrcode()
        {
                $arModuleVersion = array();

                $this->MODULE_NAME = GetMessage("ALTASIB_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_MODULE_DISCRIPTION");

                $path = str_replace("\\", "/", __FILE__);
                $path = substr($path, 0, strlen($path) - strlen("/index.php"));
                include($path."/version.php");

                if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
                    $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                    $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
                } else {
                    $this->MODULE_VERSION = '1.0.0';
                    $this->MODULE_VERSION_DATE = '2011-03-31';
                }
                $this->PARTNER_NAME = "ALTASIB";
                $this->PARTNER_URI = "http://www.altasib.ru/";
        }

        function DoInstall()
        {
            if (IsModuleInstalled("altasib.qrcode")) {
                $this->DoUninstall();
                return;
            } else {
                                global $DB, $APPLICATION, $step;
                                $RIGHT = $APPLICATION->GetGroupRight("altasib.qrcode");
                                if ($RIGHT>="W") {
                                        $step = IntVal($step);
                                        $this->InstallFiles();
                                        $this->InstallDB();
                                        $GLOBALS["errors"] = $this->errors;
                                        $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_INSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.qrcode/install/step1.php");
                                }
                        }
        }

        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->UnInstallDB();
                $this->UninstallFiles();
                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_UNINSTALL_TITLE"),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.qrcode/install/unstep1.php");
        }

        function InstallFiles()
        {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.qrcode/install/components",$_SERVER["DOCUMENT_ROOT"]."/bitrix/components",true,true);
        }

        function UninstallFiles()
        {
                        DeleteDirFilesEx("/bitrix/components/altasib/qrcode");
                        DeleteDirFilesEx("/upload/altasib/qrcode");			 
                }

        function InstallDB()
        {
            global $APPLICATION;
            $this->errors = FALSE;

            RegisterModule("altasib.qrcode");
        }

        function  UnInstallDB()
        {
            UnRegisterModule("altasib.qrcode");
                }
}
?>
