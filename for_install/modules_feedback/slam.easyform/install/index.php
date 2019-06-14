<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\IO\File;
use \Bitrix\Main\IO\Directory;
use \Bitrix\Main\Entity\Base;
use \Slam\Easyform\Main;
use \Bitrix\Main\Application;
use \Bitrix\Main\Config as Conf;


global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));


if (!class_exists("slam_easyform")) {

    class slam_easyform extends CModule
    {
        const MODULE_ID = 'slam.easyform';
        var $MODULE_ID = "slam.easyform";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $PARTNER_NAME;
        var $PARTNER_URI;
        var $MODULE_DESCRIPTION;

        function __construct()
        {
            $arModuleVersion = array();
            include($this->GetPath() . "/install/version.php");
            if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
                $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            }

            $this->MODULE_NAME = GetMessage("SLAM_EASYFORM_MODULE_NAME");
            $this->MODULE_DESCRIPTION = GetMessage("SLAM_EASYFORM_MODULE_DESCRIPTION");
            $this->PARTNER_NAME = GetMessage("SLAM_EASYFORM_PARTNER_NAME");
            $this->PARTNER_URI = GetMessage("SLAM_EASYFORM_PARTNER_URI");
            $this->MODULE_SORT = 1;
        }

        /**
         * @param bool $notDocumentRoot
         * @return mixed|string
         */
        public function GetPath($notDocumentRoot = false)
        {
            if ($notDocumentRoot)
                return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
            else
                return dirname(__DIR__); 
        }

        /**
         * @return bool
         */
        public function isVersionD7()
        {
            return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
        }

        function InstallDB()
        {
            Loader::includeModule($this->MODULE_ID);

            if (!Application::getConnection(\Slam\Easyform\EasyformTable::getConnectionName())->isTableExists(Base::getInstance('\Slam\Easyform\EasyformTable')->getDBTableName())) {
                Base::getInstance('\Slam\Easyform\EasyformTable')->createDbTable();
            }
            return true;
        }

        function UnInstallDB()
        {
            Loader::includeModule($this->MODULE_ID);
            // Drop PersonTable
            Application::getConnection(\Slam\Easyform\EasyformTable::getConnectionName())->queryExecute('drop table if exists ' . Base::getInstance('\Slam\Easyform\EasyformTable')->getDBTableName());

            Option::delete($this->MODULE_ID);
        }

        function InstallEvents()
        {
            return true;
        }

        function UnInstallEvents()
        {
            return true;
        }

        function InstallFiles($arParams = array())
        {

            $path = $this->GetPath() . "/install/components";

            if (Directory::isDirectoryExists($path)) {
				CopyDirFiles($path, $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
            } else {
                throw new \Bitrix\Main\IO\InvalidPathException($path);
            }

            if (Directory::isDirectoryExists($path = $this->GetPath() . '/install/admin')) {
                CopyDirFiles($this->GetPath() . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"); //если есть файлы для копирования
            }

            return true;
        }

        function UnInstallFiles()
        {
            Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/slam/easyform');
       
            if (Directory::isDirectoryExists($path = $this->GetPath() . '/install/admin')) { // удаляем административные файлы
                DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/install/admin/', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
            }

            return true;
        }

        function DoInstall()
        {

            global $APPLICATION;
          
			if ($this->isVersionD7()) {

				ModuleManager::registerModule($this->MODULE_ID);
				$this->InstallDB();
				$this->InstallEvents();
				$this->InstallFiles();
				
				$dbSites = \Bitrix\Main\SiteTable::getList(array(
					'filter' => array('ACTIVE' => 'Y')
				));
				$aSitesTabs = $arOptionsSite = array();
				while ($site = $dbSites->fetch()) {
					\Bitrix\Main\Config\Option::set($this->MODULE_ID, "SHOW_MESSAGE", 'Y', $site['LID']);
					\Bitrix\Main\Config\Option::set($this->MODULE_ID, "EMAIL", \Bitrix\Main\Config\Option::get("main", "email_from", ""), $site['LID']);
					\Bitrix\Main\Config\Option::set($this->MODULE_ID, "MESSAGE_TEXT", Loc::getMessage('SLAM_OPTION_MESSAGE_TEXT_DEFAULT'), $site['LID']);
				}

			} else {
				$APPLICATION->ThrowException(GetMessage("SLAM_EASYFORM_INSTALL_ERROR_VERSION"));
			}
			$APPLICATION->IncludeAdminFile(GetMessage("SLAM_EASYFORM_INSTALL_TITLE"), $this->GetPath() . "/install/step1.php");
            
        }

        function DoUninstall()
        {
            global $APPLICATION;
            $context = Application::getInstance()->getContext();
            $request = $context->getRequest();
            if ($request["step"] < 2) {
                $APPLICATION->IncludeAdminFile(GetMessage("ADELSHIN_PERSONE_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep1.php");
            } elseif ($request["step"] == 2) {
                $this->UnInstallFiles();
                $this->UnInstallEvents();

                if ($request['savedata'] != 'Y')
                    $this->UnInstallDB();

                ModuleManager::unRegisterModule($this->MODULE_ID);

                $APPLICATION->IncludeAdminFile(GetMessage("ADELSHIN_PERSONE_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep2.php");
            }
        }

    }

}
?>