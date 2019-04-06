<?php

namespace Sprint\Migration\Helpers;

use Sprint\Migration\Helper;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;


class HlblockHelper extends Helper
{

    public function __construct() {
        Loader::includeModule('highloadblock');
    }

    public function getHlblocks($filter = array()) {
        $dbres = HL\HighloadBlockTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );

        $result = [];
        while ($hlblock = $dbres->fetch()) {
            $hlblock['LANG'] = $this->getHblockLangs($hlblock['ID']);
            $result[] = $hlblock;
        }

        return $result;
    }

    public function getHlblock($name) {
        if (is_array($name)) {
            $filter = $name;
        } elseif (is_numeric($name)) {
            $filter = array('ID' => $name);
        } else {
            $filter = array('NAME' => $name);
        }

        $result = HL\HighloadBlockTable::getList(
            array(
                'select' => array('*'),
                'filter' => $filter,
            )
        );

        $hlblock = $result->fetch();
        if ($hlblock) {
            $hlblock['LANG'] = $this->getHblockLangs($hlblock['ID']);
        }

        return $hlblock;
    }

    public function getHlblockId($name) {
        $item = $this->getHlblock($name);
        return ($item && isset($item['ID'])) ? $item['ID'] : 0;
    }

    public function addHlblock($fields) {
        $this->checkRequiredKeys(__METHOD__, $fields, array('NAME', 'TABLE_NAME'));

        $lang = array();
        if (isset($fields['LANG'])) {
            $lang = $fields['LANG'];
            unset($fields['LANG']);
        }

        $fields['NAME'] = ucfirst($fields['NAME']);

        $result = HL\HighloadBlockTable::add($fields);
        if ($result->isSuccess()) {
            $this->replaceHblockLangs($result->getId(), $lang);
            return $result->getId();
        }

        $this->throwException(__METHOD__, implode(', ', $result->getErrors()));
    }

    public function addHlblockIfNotExists($fields) {
        $this->checkRequiredKeys(__METHOD__, $fields, array('NAME'));

        $item = $this->getHlblock($fields['NAME']);
        if ($item) {
            return $item['ID'];
        }

        return $this->addHlblock($fields);
    }

    public function updateHlblock($hlblockId, $fields) {
        $lang = array();
        if (isset($fields['LANG'])) {
            $lang = $fields['LANG'];
            unset($fields['LANG']);
        }

        $result = HL\HighloadBlockTable::update($hlblockId, $fields);
        if ($result->isSuccess()) {
            $this->replaceHblockLangs($hlblockId, $lang);
            return $hlblockId;
        }

        $this->throwException(__METHOD__, implode(', ', $result->getErrors()));
    }

    public function updateHlblockIfExists($name, $fields) {
        $item = $this->getHlblock($name);
        if (!$item) {
            return false;
        }

        return $this->updateHlblock($item['ID'], $fields);
    }

    public function deleteHlblock($hlblockId) {
        $result = HL\HighloadBlockTable::delete($hlblockId);
        if ($result->isSuccess()) {
            return true;
        }

        $this->throwException(__METHOD__, implode(', ', $result->getErrors()));
    }

    public function deleteHlblockIfExists($name) {
        $item = $this->getHlblock($name);
        if (!$item) {
            return false;
        }

        return $this->deleteHlblock($item['ID']);
    }

    protected function getHblockLangs($hlblockId) {
        $result = array();

        if (!class_exists('Bitrix\Highloadblock\HighloadBlockLangTable')) {
            return $result;
        }

        $dbres = HL\HighloadBlockLangTable::getList(array(
            'filter' => array('ID' => $hlblockId)
        ));


        while ($item = $dbres->fetch()) {
            $result[$item['LID']] = array(
                'NAME' => $item['NAME']
            );
        }

        return $result;
    }

    protected function deleteHblockLangs($hlblockId) {
        $del = 0;

        if (!class_exists('Bitrix\Highloadblock\HighloadBlockLangTable')) {
            return $del;
        }

        $res = HL\HighloadBlockLangTable::getList(array(
            'filter' => array('ID' => $hlblockId)
        ));


        while ($row = $res->fetch()) {
            HL\HighloadBlockLangTable::delete($row['ID']);
            $del++;
        }

        return $del;
    }

    protected function addHblockLangs($hlblockId, $lang = array()) {
        $add = 0;

        if (!class_exists('Bitrix\Highloadblock\HighloadBlockLangTable')) {
            return $add;
        }

        foreach ($lang as $lid => $item) {
            if (!empty($item['NAME'])) {
                HL\HighloadBlockLangTable::add(array(
                    'ID' => $hlblockId,
                    'LID' => $lid,
                    'NAME' => $item['NAME']
                ));

                $add++;
            }
        }

        return $add;
    }

    protected function replaceHblockLangs($hlblockId, $lang = array()) {
        if (!empty($lang) && is_array($lang)) {
            $this->deleteHblockLangs($hlblockId);
            $this->addHblockLangs($hlblockId, $lang);
        }
    }


    //version 2


    public function saveHlblock($fields) {
        $this->checkRequiredKeys(__METHOD__, $fields, array('NAME'));

        $item = $this->getHlblock($fields['NAME']);
        if ($item) {
            return $this->updateHlblock($item['ID'], $fields);
        } else {
            return $this->addHlblock($fields);
        }
    }

}