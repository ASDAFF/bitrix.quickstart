<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Serge                            #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2011 ALTASIB             #
#################################################
?>
<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class altasib_up extends CModule
{
        var $MODULE_ID = "altasib.up";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;

        function altasib_up()
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
                        $this->MODULE_VERSION = "1.0.0";
                        $this->MODULE_VERSION_DATE = "2011-07-10 15:47:00";
                }

                $this->MODULE_NAME = GetMessage("ALTASIB_UP_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_UP_MODULE_DESCRIPTION");

                $this->PARTNER_NAME = "ALTASIB";
                $this->PARTNER_URI = "http://www.altasib.ru/";
        }
        function DoInstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->InstallFiles();
                $this->InstallDB();

                $GLOBALS["errors"] = $this->errors;
                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_UP_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.up/install/step1.php");
        }
        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->UnInstallDB();
                $this->UnInstallFiles();
                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_UP_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.up/install/unstep1.php");
        }
        function InstallDB()
        {
                $this->errors = false;

                RegisterModule("altasib.up");
                RegisterModuleDependences("main","OnBeforeEndBufferContent","altasib.up","UP_alx","UPOnBeforeEndBufferContent", "100");
        }
        function UnInstallDB($arParams = array())
        {
                $this->errors = false;

                UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", "altasib.up", "UP_alx", "UPOnBeforeEndBufferContent");
                COption::RemoveOption("altasib_up");
                UnRegisterModule("altasib.up");

                return true;

        }

        function InstallFiles()
        {
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.up/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/altasib.up/", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.up/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/altasib.up/", true, true);
                return true;
        }

        function UnInstallFiles()
        {
                DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/altasib.up");
                DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/images/altasib.up");
                return true;
        }
}
?>
