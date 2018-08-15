<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config as Conf;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

Class soobwa_comments extends CModule
{
    var $MODULE_ID = "soobwa.comments";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function __construct()
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

        $this->MODULE_NAME = Loc::getMessage('SOOBWA_COMMENTS_INSTALL_INDEX_CONSTRUCT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('SOOBWA_COMMENTS_INSTALL_INDEX_CONSTRUCT_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('SOOBWA_COMMENTS_INSTALL_INDEX_CONSTRUCT_PARTNER_NAME');
        $this->PARTNER_URI = "https://soobwa.ru";
    }

    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        Base::getInstance('\Soobwa\Comments\CommentsTable')->createDbTable();

        /*
         * Сохранил путь к модулю
         * */
        Option::set("soobwa_comments", "path", $this->GetPath(true));
        return true;
    }

    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        Application::getConnection(\Soobwa\Comments\CommentsTable::getConnectionName())->queryExecute('drop table if exists '.Base::getInstance('\Soobwa\Comments\CommentsTable')->getDbTableName());

        /*
         * Удаляем путь к модулю
         * */
        Option::delete("soobwa_comments", "path");
        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallFiles()
    {
        /*
         * Копируем компоненты
         * */
        CopyDirFiles($this->GetPath()."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);

        /*
         * Копируем админские файлы
         * */
        CopyDirFiles($this->GetPath()."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        /*
         * Удоляем компоненты
         * */
        DeleteDirFiles($this->GetPath()."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components");

        /*
         * Удоляем админские файлы
         * */
        DeleteDirFiles($this->GetPath()."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        return true;
    }

    function DoInstall()
    {
        Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();

    }

    function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $this->UnInstallDB();

        Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * Определяем место размешения модуля
     * @return string
     */
    public function GetPath($notDocRoot = false)
    {
        if($notDocRoot){
            return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
        }else{
            return dirname(__DIR__);
        }
    }
}