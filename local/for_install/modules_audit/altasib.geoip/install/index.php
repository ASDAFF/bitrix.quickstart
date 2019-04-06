<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Andrew N. Popov                  #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class altasib_geoip extends CModule
{
        var $MODULE_ID = "altasib.geoip";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;
//        var $MODULE_GROUP_RIGHTS = "Y";

        function altasib_geoip()
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
                        $this->MODULE_VERSION = "1.0";
                        $this->MODULE_VERSION_DATE = "2010-11-01 00:00:00";
                }

                $this->MODULE_NAME = GetMessage("ALTASIB_GEOIP_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("ALTASIB_GEOIP_MODULE_DESCRIPTION");

                $this->PARTNER_NAME = "ALTASIB";
                $this->PARTNER_URI = "http://www.altasib.ru/";
        }
        function DoInstall()
        {
                global $DB, $APPLICATION, $step;

                $step = IntVal($step);
                if($step<2)
				{					$GLOBALS["install_step"] = 1;
	                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_GEOIP_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geoip/install/step1.php");
				}
				elseif($step==2)
				{
					if($this->InstallDB()){

						$this->InstallFiles();

		          	}

					$GLOBALS["errors"] = $this->errors;
					$GLOBALS["install_step"] = 2;
	                $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_GEOIP_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geoip/install/step1.php");
				}

        }
        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);

                if($step<2)
                {
                        $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_GEOIP_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geoip/install/unstep1.php");
                }
                elseif($step==2)
                {
                        $this->UnInstallDB(array(
                                "savedata" => $_REQUEST["savedata"],
                        ));
                        $this->UnInstallFiles();
                        $APPLICATION->IncludeAdminFile(GetMessage("ALTASIB_GEOIP_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geoip/install/unstep2.php");
                }

        }
        function InstallDB()
        {

                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                RegisterModule("altasib.geoip");
				return true;

        }
        function UnInstallDB($arParams = array())
        {

                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                if (!$arParams['savedata']) COption::RemoveOption("altasib_geoip");
                UnRegisterModule("altasib.geoip");

                return true;

        }
        function InstallFiles()
        {       			global $DB;

                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geoip/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geoip/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geoip/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);

                return true;
        }
        function UnInstallFiles()
        {

                DeleteDirFilesEx("/bitrix/components/altasib/altasib.geoip");
                return true;

        }

}
?>