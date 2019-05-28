<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\ModuleManager;

if (class_exists('discount_updater')) {
    return;
}

class discount_updater extends CModule
{
    public $MODULE_ID;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_DIR;


    public function __construct ()
    {
        $this->MODULE_ID          = basename(dirname(dirname(__FILE__)));
        $this->MODULE_NAME        = 'Обновление скидочных карт';
        $this->MODULE_DESCRIPTION = 'Обновляет данные из xml-файла по скидочным картам клиентов сайта moshoztorg.ru';
        $this->PARTNER_NAME       = 'ITsfera';
        $this->PARTNER_URI        = 'http://web.it-sfera.ru';
        $this->MODULE_DIR         = rtrim(preg_replace('~[\\\\/]+~', '/', dirname(dirname(__FILE__))), '/');

        // Получаем версию модуля из файла
        $arModuleVersion = array();
        include($this->MODULE_DIR . '/install/version.php');

        $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

    }

    public function doInstall ()
    {
        $this->installRule();
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function doUninstall ()
    {
        $this->uninstallRule();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    public function installRule()
    {
        if (!CUrlRewriter::GetList(array("ID" => "local:".$this->MODULE_ID))){

            CUrlRewriter::Add(array(
                "CONDITION" => "#^/bitrix/admin/discount_cards_updater.php#",
                "PATH" => '/local/modules/'.$this->MODULE_ID.'/admin/index.php',
                "ID" => 'local:'.$this->MODULE_ID,
            ));
        }
    }

    public function uninstallRule()
    {
       if (CUrlRewriter::GetList(array("ID" => "local:".$this->MODULE_ID))){
            CUrlRewriter::Delete(array(
                "ID" => 'local:'.$this->MODULE_ID,
            ));
       }
    }

}