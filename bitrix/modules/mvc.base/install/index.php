<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (class_exists('digitalwand_mvc')) return;

class digitalwand_mvc extends CModule
{
    var $MODULE_ID = 'digitalwand.mvc';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = 'Y';
    var $MODULE_CSS;
    var $PARTNER_NAME = 'DigitalWand';
    var $PARTNER_URI = '';

    function digitalwand_mvc()
    {
        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = MVC_VERSION;
        $this->MODULE_VERSION_DATE = MVC_VERSION_DATE;
        $this->MODULE_NAME = Loc::getMessage('MVC_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MVC_INSTALL_DESCRIPTION');
    }

    function DoInstall()
    {
        CopyDirFiles(__DIR__ . '/wizard', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/wizards/digitalwand/', true, true);
        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
        DeleteDirFiles(__DIR__ . '/wizard', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/wizards/digitalwand/mvc');
    }
}