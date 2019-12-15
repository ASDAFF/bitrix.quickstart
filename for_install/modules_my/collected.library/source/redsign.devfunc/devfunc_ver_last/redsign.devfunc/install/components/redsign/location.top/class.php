<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\LoaderException;
use \Bitrix\Sale;

Loc::loadMessages(__FILE__);

class RSMasterLocationTop extends CBitrixComponent
{
    protected function checkModules()
    {
        $needModules = ['sale'];
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

        $locationIterator = Sale\Location\LocationTable::getList([
            'order' => ['SORT' => 'asc'],
            'select' => [
                'ID',
                'LNAME' => 'NAME.NAME',
                'CODE' => 'CODE'
            ],
            'filter' => [
                'NAME.LANGUAGE_ID' => LANGUAGE_ID,
                'TYPE.CODE' => 'CITY'
            ],
            'limit' => (isset($this->arParams['COUNT_ITEMS']) ? $this->arParams['COUNT_ITEMS'] : 15)
        ]);

        $this->arResult['ITEMS'] = $locationIterator->fetchAll();
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
