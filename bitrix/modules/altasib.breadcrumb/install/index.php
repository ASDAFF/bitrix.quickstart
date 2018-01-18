<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Eremchenko Alexey                #
#   Site: http://www.altasib.ru                 #
#   E-mail: info@altasib.ru                     #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class altasib_breadcrumb extends CModule
{
        var $MODULE_ID = "altasib.breadcrumb";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;
        var $MODULE_GROUP_RIGHTS = "Y";

        function altasib_breadcrumb()
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
                        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
                        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
                }
                $this->MODULE_NAME = GetMessage("ALTASIB_BREADCRUMB_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_BREADCRUMB_MODULE_DESCRIPTION");
                $this->PARTNER_NAME = "ALTASIB";
                $this->PARTNER_URI = "http://www.altasib.ru/";
        }
        function DoInstall()
        {

                global $APPLICATION, $step;
                $step = IntVal($step);
                $this->InstallFiles();
                RegisterModule("altasib.breadcrumb");
                $GLOBALS["errors"] = $this->errors;
                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_BREADCRUMB_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.breadcrumb/install/step1.php");

        }
        function DoUninstall()
        {
                global $APPLICATION, $step;
                $step = IntVal($step);
                if($step<2)
                {
                    $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_BREADCRUMB_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.breadcrumb/install/unstep1.php");
                }
                elseif($step==2)
                {
                    UnRegisterModule("altasib.breadcrumb");
                    $this->UnInstallFiles();
                    $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_BREADCRUMB_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.breadcrumb/install/unstep2.php");
                }
        }
        function InstallFiles($arParams = Array())
        {
				CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.breadcrumb/install/templates", $_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/.default/components/bitrix/breadcrumb/", true, true);

                return true;
        }
        function UnInstallFiles()
        {
                $path = "/bitrix/templates/.default/components/bitrix/breadcrumb/altasib.breadcrumb_micro";

                if(is_dir($_SERVER["DOCUMENT_ROOT"].$path)) DeleteDirFilesEx($path);

                $path = "/bitrix/templates/.default/components/bitrix/breadcrumb/altasib.breadcrumb_rdf";

                if(is_dir($_SERVER["DOCUMENT_ROOT"].$path)) DeleteDirFilesEx($path);

                return true;
        }
}
?>
