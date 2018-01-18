<?

$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");


Class b1team_smartfilter extends CModule
{
	var $MODULE_ID = "b1team.smartfilter";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	var $errors;

	function b1team_smartfilter() {
            
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
                    $this->MODULE_VERSION = IBLOCK_VERSION;
                    $this->MODULE_VERSION_DATE = IBLOCK_VERSION_DATE;
            }

            $this->MODULE_NAME = GetMessage("B1TEAM_SMART_FILTER_MODULE_NAME");
            $this->MODULE_DESCRIPTION = GetMessage("B1TEAM_SMART_FILTER_MODULE_DESCRIPTION");
            $this->PARTNER_NAME = GetMessage("B1TEAM_PARTNER");
            $this->PARTNER_URI = "http://b1team.ru/?from=bx_smartfilter_installed";
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
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/b1team.smartfilter/install/components",
                         $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/b1team", true, true, false, ".svn" );
            return true;
	}

        /**
         * Удаление файлов
         */
	function UnInstallFiles(){
            DeleteDirFilesEx("/bitrix/components/b1team/catalog.smart.filter");
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