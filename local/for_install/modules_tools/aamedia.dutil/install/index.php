<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

class aamedia_dutil extends CModule
{
    var $MODULE_ID = "aamedia.dutil";

    public function __construct()
    {
        if(file_exists(__DIR__."/version.php")){
            $arModuleVersion = array();

            include (__DIR__."/version.php");

            $this->MODULE_ID = "aamedia.dutil";
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME = Loc::GetMessage("AAM_DUTIL_NAME");
            $this->MODULE_DESCRIPTION = Loc::GetMessage("AAM_DUTIL_DESCRIPTION");
            $this->PARTNER_NAME = "AAM";
            $this->PARTNER_URI = "http://www.2amedia.ru";
        }
    }

    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot)
            return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
        else
            return dirname(__DIR__);
    }

    public function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

    public  function InstallDB()
    {
        return true;
    }

    public function InstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler(
            "main",
            "OnAfterSetOption_update_devsrv",
            "aamedia.dutil",
            "CHandlers",
            "Handler_update_devsrv"
        );

        \Bitrix\Main\EventManager::getInstance()->registerEventHandler(
            "main",
            "OnAfterUserAuthorize",
            "aamedia.dutil",
            "CHandlers",
            "HandlerUserAuthorize"
        );

        return true;
    }

    public  function InstallFiles()
    {
        return true;
    }

    public  function UnInstallDB()
    {
        Option::delete($this->MODULE_ID);
    }

    public function UnInstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnAfterSetOption_update_devsrv",
            "aamedia.dutil",
            "CHandlers",
            "Handler_update_devsrv"
        );

        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnAfterUserAuthorize",
            "aamedia.dutil",
            "CHandlers",
            "HandlerUserAuthorize"
        );

        return true;
    }

    public  function UnInstallFiles()
    {
        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION;

        if($this->isVersionD7())
        {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

            ModuleManager::registerModule($this->MODULE_ID);
        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage("AAM_DUTIL_ERROR_VERSION"));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("AAM_DUTIL_INSTALL_TITLE"), $this->GetPath()."/install/step.php");
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/aamedia.dutil/include.php");

        \CAdminNotify::DeleteByTag
        (
            'phpdebugon_notify'
        );

        \CHandlers::DeleteRobotsTXT();

        $this->UnInstallFiles();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $this->UnInstallDB();

        $APPLICATION->IncludeAdminFile(Loc::getMessage("AAM_DUTIL_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep.php");
    }
}
?>