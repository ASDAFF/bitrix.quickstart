<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

use \Bitrix\Main\Application as App,
    \Bitrix\Main\IO\Directory as Dir,
    \Bitrix\Main\ModuleManager,
    \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Collected_Library extends \CModule
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

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'collected.library';
        $this->MODULE_NAME = Loc::getMessage('COLLECT_LIB_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('COLLECT_LIB_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('COLLECT_LIB_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://asdaff.github.io/';
    }

    public function doInstall()
    {
        if (!IsModuleInstalled($this->MODULE_ID)) {
//            RegisterModule($this->MODULE_ID);
            ModuleManager::registerModule($this->MODULE_ID);

            RegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'loadCollectedLibrary', 1);
            function loadCollectedLibrary()
            {
                \Bitrix\Main\Loader::includeModule('collected.library');
            }

//        RegisterModuleDependences('main', 'OnPageStart', self::MODULE_ID, 'loadCollectedLibrary', 'loadCollectedLibrary', 1);

            /**
             * @TODO make all handlers
             */


            //BASKET
            //basket add
            RegisterModuleDependences('sale', 'OnBeforeBasketAdd', $this->MODULE_ID, 'Collected\Handlers\Basket', 'beforeAdd');
            RegisterModuleDependences('sale', 'OnBasketAdd', $this->MODULE_ID, 'Collected\Handlers\Basket', 'afterAdd');

            //basket update
            RegisterModuleDependences('sale', 'OnBeforeBasketUpdate', $this->MODULE_ID, 'Collected\Handlers\Basket', 'beforeUpdate');
            RegisterModuleDependences('sale', 'OnBasketUpdate', $this->MODULE_ID, 'Collected\Handlers\Basket', 'afterUpdate');

            // basket delete
            RegisterModuleDependences('sale', 'OnBeforeBasketDelete', $this->MODULE_ID, 'Collected\Handlers\Basket', 'beforeDelete');
            RegisterModuleDependences('sale', 'OnBasketDelete', $this->MODULE_ID, 'Collected\Handlers\Basket', 'afterDelete');

            //order
            RegisterModuleDependences('sale', 'OnOrderAdd', $this->MODULE_ID, 'Collected\Handlers\Order', 'afterAdd');
            RegisterModuleDependences('sale', 'OnOrderUpdate', $this->MODULE_ID, 'Collected\Handlers\Order', 'afterUpdate');

            //user
            RegisterModuleDependences('main', 'OnBeforeUserRegister', $this->MODULE_ID, '\Collected\Handlers\User', 'beforeAdd');
            RegisterModuleDependences('main', 'OnBeforeUserUpdate', $this->MODULE_ID, '\Collected\Handlers\User', 'beforeUpdate');

            //highload blocks
            RegisterModuleDependences('', 'UserDataOnUpdate', $this->MODULE_ID, '\Collected\Handlers\UserData', 'afterUpdate');
            RegisterModuleDependences('', 'UserDataOnAdd', $this->MODULE_ID, '\Collected\Handlers\UserData', 'afterAdd');

        }

        $this->installFiles();

    }

    public function doUninstall()
    {
        UnRegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, 'loadCollectedLibrary', 1);
        function loadCollectedLibrary()
        {
            \Bitrix\Main\Loader::includeModule('collected.library');
        }

//        UnRegisterModuleDependences('main', 'OnPageStart', self::MODULE_ID, 'loadCollectedLibrary', 'loadCollectedLibrary', 1);

        /**
         * @TODO make all handlers
         */


        //BASKET
        //basket add
        UnRegisterModuleDependences('sale', 'OnBeforeBasketAdd', $this->MODULE_ID, 'Collected\Handlers\Basket', 'beforeAdd');
        UnRegisterModuleDependences('sale', 'OnBasketAdd', $this->MODULE_ID, 'Collected\Handlers\Basket', 'afterAdd');

        //basket update
        UnRegisterModuleDependences('sale', 'OnBeforeBasketUpdate', $this->MODULE_ID, 'Collected\Handlers\Basket', 'beforeUpdate');
        UnRegisterModuleDependences('sale', 'OnBasketUpdate', $this->MODULE_ID, 'Collected\Handlers\Basket', 'afterUpdate');

        // basket delete
        UnRegisterModuleDependences('sale', 'OnBeforeBasketDelete', $this->MODULE_ID, 'Collected\Handlers\Basket', 'beforeDelete');
        UnRegisterModuleDependences('sale', 'OnBasketDelete', $this->MODULE_ID, 'Collected\Handlers\Basket', 'afterDelete');

        //order
        UnRegisterModuleDependences('sale', 'OnOrderAdd', $this->MODULE_ID, 'Collected\Handlers\Order', 'afterAdd');
        UnRegisterModuleDependences('sale', 'OnOrderUpdate', $this->MODULE_ID, 'Collected\Handlers\Order', 'afterUpdate');

        //user
        UnRegisterModuleDependences('main', 'OnBeforeUserRegister', $this->MODULE_ID, '\Collected\Handlers\User', 'beforeAdd');
        UnRegisterModuleDependences('main', 'OnBeforeUserUpdate', $this->MODULE_ID, '\Collected\Handlers\User', 'beforeUpdate');

        //highload blocks
        UnRegisterModuleDependences('', 'UserDataOnUpdate', $this->MODULE_ID, '\Collected\Handlers\UserData', 'afterUpdate');
        UnRegisterModuleDependences('', 'UserDataOnAdd', $this->MODULE_ID, '\Collected\Handlers\UserData', 'afterAdd');

//        UnRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installFiles()
    {
        //where is module?
        $moduleDir = App::getDocumentRoot() . '/local/modules/' . $this->MODULE_ID;
        if (!Dir::isDirectoryExists($moduleDir))
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
        if (!Dir::isDirectoryExists($dir))
            $dir = App::getDocumentRoot() . static::OLD_LIB_INC_PATH;

        CopyDirFiles($moduleDir . '/install/helper', $dir . '/helper', true, true);
    }

    public function unInstallLib()
    {
        //use directory local?
        $dir = static::LIB_INC_PATH;
        if (!Dir::isDirectoryExists(App::getDocumentRoot() . $dir))
            $dir = static::OLD_LIB_INC_PATH;

        //delete copied files
        foreach ($this->includes as $include)
            DeleteDirFilesEx($dir . 'helper/' . $include);
        //delete include directory if it empty
        if (Dir::isDirectoryExists(App::getDocumentRoot() . $dir . 'helper') && count(scandir(App::getDocumentRoot() . $dir . 'helper')) === 2)
            DeleteDirFilesEx($dir . 'helper');
    }

    public function installComponents($moduleDir)
    {
        //use directory local?
        $dir = App::getDocumentRoot() . static::COMPONENTS_INC_PATH;
        if (!Dir::isDirectoryExists($dir))
            $dir = App::getDocumentRoot() . static::OLD_COMPONENTS_INC_PATH;

        if (!Dir::isDirectoryExists($dir . 'lm'))
            Dir::createDirectory($dir . 'lm');

        //copy components
        foreach ($this->components as $component) {
            CopyDirFiles(
                $moduleDir . '/install/components/lm/' . $component,
                $dir . 'lm/' . $component,
                true,
                true
            );
        }
    }

    public function unInstallComponents()
    {
        //use directory local?
        $dir = static::COMPONENTS_INC_PATH;
        if (!Dir::isDirectoryExists(App::getDocumentRoot() . $dir))
            $dir = static::OLD_COMPONENTS_INC_PATH;

        //delete installed components
        foreach ($this->components as $component)
            DeleteDirFilesEx($dir . 'lm/' . $component);
        //delete lm directory if it empty
        if (Dir::isDirectoryExists(App::getDocumentRoot() . $dir . 'lm') && count(scandir(App::getDocumentRoot() . $dir . 'lm')) === 2)
            DeleteDirFilesEx($dir . 'lm');
    }
}
