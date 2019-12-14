<?php

use \Bitrix\Main\Application as App,
    \Bitrix\Main\IO\Directory as Dir,
    \Bitrix\Main\ModuleManager,
    \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Lema_Lib extends \CModule
{
    CONST LIB_INC_PATH = '/local/php_interface/';
    CONST OLD_LIB_INC_PATH = '/bitrix/php_interface/';

    CONST COMPONENTS_INC_PATH = '/local/components/';
    CONST OLD_COMPONENTS_INC_PATH = '/bitrix/components/';

    protected $includes = array(
        'constants.php',
        'functions.php',
        'classes.php',
        'handlers.php',
        'LIBlock.php',
    );

    protected $components = array(
        'form.ajax',
        'basket',
    );

    public function __construct()
    {
        $arModuleVersion = array();

        include __DIR__ . '/version.php';

        if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'lema.lib';
        $this->MODULE_NAME = Loc::getMessage('LEMA_LIB_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('LEMA_LIB_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('LEMA_LIB_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'http://lema.agency';
    }

    public function doInstall()
    {
        $this->installFiles();
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function doUninstall()
    {
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installFiles()
    {
        //where is module?
        $moduleDir = App::getDocumentRoot() . '/local/modules/' . $this->MODULE_ID;
        if(!Dir::isDirectoryExists($moduleDir))
            $moduleDir = App::getDocumentRoot() . '/bitrix/modules/' . $this->MODULE_ID;

        $this->installLib($moduleDir);
        $this->installComponents($moduleDir);
    }

    public function unInstallFiles()
    {
        $this->unInstallLib();
        $this->unInstallComponents();
    }

    public function installLib($moduleDir)
    {
        //use directory local?
        $dir = App::getDocumentRoot() . static::LIB_INC_PATH;
        if(!Dir::isDirectoryExists($dir))
            $dir = App::getDocumentRoot() . static::OLD_LIB_INC_PATH;

        CopyDirFiles($moduleDir . '/install/include', $dir . '/include', true, true);
    }

    public function unInstallLib()
    {
        //use directory local?
        $dir = static::LIB_INC_PATH;
        if(!Dir::isDirectoryExists(App::getDocumentRoot() . $dir))
            $dir = static::OLD_LIB_INC_PATH;

        //delete copied files
        foreach($this->includes as $include)
            DeleteDirFilesEx($dir . 'include/' . $include);
        //delete include directory if it empty
        if(Dir::isDirectoryExists(App::getDocumentRoot() . $dir . 'include') && count(scandir(App::getDocumentRoot() . $dir . 'include')) === 2)
            DeleteDirFilesEx($dir . 'include');
    }

    public function installComponents($moduleDir)
    {
        //use directory local?
        $dir = App::getDocumentRoot() . static::COMPONENTS_INC_PATH;
        if(!Dir::isDirectoryExists($dir))
            $dir = App::getDocumentRoot() . static::OLD_COMPONENTS_INC_PATH;

        if(!Dir::isDirectoryExists($dir . 'lema'))
            Dir::createDirectory($dir . 'lema');

        //copy components
        foreach($this->components as $component)
        {
            CopyDirFiles(
                $moduleDir . '/install/components/lema/' . $component,
                $dir . 'lema/' . $component,
                true,
                true
            );
        }
    }

    public function unInstallComponents()
    {
        //use directory local?
        $dir = static::COMPONENTS_INC_PATH;
        if(!Dir::isDirectoryExists(App::getDocumentRoot() . $dir))
            $dir = static::OLD_COMPONENTS_INC_PATH;

        //delete installed components
        foreach($this->components as $component)
            DeleteDirFilesEx($dir . 'lema/' . $component);
        //delete lema directory if it empty
        if(Dir::isDirectoryExists(App::getDocumentRoot() . $dir . 'lema') && count(scandir(App::getDocumentRoot() . $dir . 'lema')) === 2)
            DeleteDirFilesEx($dir . 'lema');
    }
}
