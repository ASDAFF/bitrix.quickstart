<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class slobel_connectjq extends CModule
{
		const MODULE_ID = 'slobel.socialicons';
        var $MODULE_ID = "slobel.connectjq";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;

	function __construct()
		{
			$arModuleVersion = array();
			include(dirname(__FILE__)."/version.php");
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->MODULE_NAME = GetMessage("slobel.socialicons_MODULE_NAME");
			$this->MODULE_DESCRIPTION = GetMessage("slobel.socialicons_MODULE_DESC");

			$this->PARTNER_NAME = GetMessage("slobel.socialicons_PARTNER_NAME");
			$this->PARTNER_URI = GetMessage("slobel.socialicons_PARTNER_URI");
		}
	
        function DoInstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->InstallFiles();
                $this->InstallDB();
                $this->InstallEvents();

                $GLOBALS["errors"] = $this->errors;
                $APPLICATION->IncludeAdminFile(GetMessage("SLOBEL_CONNECTJQ_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/slobel.connectjq/install/step1.php");
        }
        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);
                $this->UnInstallDB();
                $this->UnInstallEvents();
                $this->UnInstallFiles();
                $APPLICATION->IncludeAdminFile(GetMessage("SLOBEL_CONNECTJQ_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/slobel.connectjq/install/unstep1.php");
        }
        function InstallDB()
        {
                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                RegisterModule("slobel.connectjq");
                RegisterModuleDependences("main","OnBeforeEndBufferContent","slobel.connectjq","CSlobelConnectjq","JQOnBeforeEndBufferContent", "100");
        }
        function UnInstallDB($arParams = array())
        {
                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", "slobel.connectjq", "CSlobelConnectjq", "JQOnBeforeEndBufferContent");
                COption::RemoveOption("slobel.connectjq");
                UnRegisterModule("slobel.connectjq");

                return true;

        }
        Function InstallEvents()
        {

        }

        Function UnInstallEvents()
        {
        }

        function InstallFiles()
        {
        }

        function UnInstallFiles()
        {
        }
}
?>
