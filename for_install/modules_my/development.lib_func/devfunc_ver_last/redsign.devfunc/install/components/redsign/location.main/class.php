<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\LoaderException;
use \Redsign\DevFunc\Sale\Location\Location;

Loc::loadMessages(__FILE__);

class RSMasterLocationsMain extends CBitrixComponent
{
    protected function checkModules()
    {
        $needModules = ['catalog', 'sale'];
        foreach ($needModules as $module) {
            if (!Loader::includeModule($module)) {
                throw new LoaderException('MODULE '.strtoupper($module).' NOT INSTALLED');
            }
        }
    }

    protected function abortDataCache()
    {
        $this->AbortResultCache();
    }

    public function readDataFromCache()
    {
        $cacheAddon = [];

        return !($this->startResultCache(false, $cacheAddon, md5(serialize($this->arParams))));
    }

    public function putDataToCache()
    {
        $this->SetResultCacheKeys(array_keys($this->arResult));
    }

    protected function getResult()
    {
        $this->arResult = Location::getMyCity();
    }

    public function executeComponent()
    {
        try {
            $this->checkModules();

            if (!$this->readDataFromCache()) {
                $this->getResult();

                $this->putDataToCache();


                $this->includeComponentTemplate();
            }
        } catch (Exception $e) {
            $this->abortDataCache();
            $this->__showError($e->getMessage(), $e->getCode());
        }
    }
}
