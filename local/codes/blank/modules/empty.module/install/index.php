<?

$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");


Class b1team_smartfilter extends CModule
{
	var $MODULE_ID = "....."; // ID модуля
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	var $errors;




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
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/empty.module/install/components",
                         $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/empty.module", true, true, false, ".svn" );
            return true;
	}

        /**
         * Удаление файлов
         */
	function UnInstallFiles(){
            DeleteDirFilesEx("/bitrix/components/empty.module");
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
