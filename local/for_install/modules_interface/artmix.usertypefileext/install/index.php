<?php
/**
 * Created by Artmix.
 * User: Oleg Maksimenko <oleg.39style@gmail.com>
 * Date: 02.06.2016
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class artmix_usertypefileext extends CModule
{
    var $MODULE_ID = 'artmix.usertypefileext';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';
    var $errors = array();

    function artmix_usertypefileext()
    {
        $arModuleVersion = array();
        $path = str_replace('\\', '/', __FILE__);
        $path = substr($path, 0, strlen($path) - strlen('/index.php'));
        include($path . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('ARTMIX_USERTYPEFILEEXT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ARTMIX_USERTYPEFILEEXT_MODULE_DESCRIPTION');

        $this->PARTNER_NAME = GetMessage('ARTMIX_USERTYPEFILEEXT_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('ARTMIX_USERTYPEFILEEXT_PARTNER_URI');
    }

    function GetModuleTasks()
    {
        return array();
    }

    function DoInstall()
    {
        global $USER, $APPLICATION;

        if ($USER->IsAdmin()) {
            if ($this->InstallDB()) {
                $this->InstallEvents();
                $this->InstallFiles();
            }
            $GLOBALS['errors'] = $this->errors;
        }
    }

    function InstallDB($arParams = array())
    {
        global $DB, $DBType, $APPLICATION;

        ModuleManager::registerModule($this->MODULE_ID);

        Loader::includeModule($this->MODULE_ID);

        return true;
    }

    function InstallEvents()
    {

        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler('main', 'OnUserTypeBuildList', $this->MODULE_ID, '\Artmix\UserTypeFileExt\UserTypeFileExt', 'getUserTypeDescription');

        return true;
    }

    function InstallFiles($arParams = array())
    {
        return true;
    }

    function DoUninstall()
    {
        global $DB, $USER, $DOCUMENT_ROOT, $APPLICATION, $step;

        if ($USER->IsAdmin()) {
            if ($this->UnInstallDB()) {
                $this->UnInstallEvents();
                $this->UnInstallFiles();
            }
            $GLOBALS['errors'] = $this->errors;
        }
    }

    function UnInstallDB($arParams = array())
    {
        global $DB, $DBType, $APPLICATION;

        $this->errors = false;

        ModuleManager::unRegisterModule($this->MODULE_ID);

        if ($this->errors !== false) {
            $APPLICATION->ThrowException(implode('<br>', $this->errors));
            return false;
        }
        return true;
    }

    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler('main', 'OnUserTypeBuildList', $this->MODULE_ID, '\Artmix\UserTypeFileExt\UserTypeFileExt', 'getUserTypeDescription');

        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }
}
