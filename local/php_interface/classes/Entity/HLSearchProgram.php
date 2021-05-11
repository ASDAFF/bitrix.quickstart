<?php
/**
 * Created by PhpStorm.
 * User: anastasiya.zayarnaya
 * Date: 14.08.2018
 * Time: 9:27
 */

namespace Entity;

use Bitrix\Highloadblock\HighloadBlockTable as HLBT,
    Bitrix\Main\Entity,
    Bitrix\Main\Loader;

class HLSearchProgram
{
    /** @var int */
    private $intHlID = 1;
    /** @var string */
    private $strDateTimeFormat = 'd.m.Y H:i:s';
    /** @var Entity\Base|null */
    private $entity = null;
    /** @var Entity\DataManager|null */
    private $objHL = null;
    /** @var HLSearchProgram */
    protected static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone() {}

    private function __wakeup() {}

    private function __construct()
    {
        Loader::IncludeModule('highloadblock');
        $arHlBlock = HLBT::getById($this->intHlID)->fetch();
        $this->entity = HLBT::compileEntity($arHlBlock);
        $this->objHL = $this->entity->getDataClass();
    }

    public function add($arFields)
    {
        $entityClass = $this->objHL;
        $arFields['UF_QUERY'] = trim($arFields['UF_QUERY']);
        $arFields['UF_COUNTER'] = 1;

        $arElement = $this->getList(['filter' => ['UF_QUERY' => $arFields['UF_QUERY']]])->Fetch();

        if($arElement){
            $arFields = ['UF_COUNTER' => $arFields['UF_COUNTER'] + $arElement['UF_COUNTER']];
            return $this->update($arElement['ID'], $arFields);
        }

        $arFields['UF_DATE_CREATE'] = date($this->strDateTimeFormat);
        $arFields['UF_DATE_UPDATE'] = date($this->strDateTimeFormat);

        return $entityClass::add($arFields);
    }

    public function delete($intId)
    {
        $entityClass = $this->objHL;
        return $entityClass::delete($intId);
    }

    public function update($intId, $arFields)
    {
        $entityClass = $this->objHL;
        $arFields['UF_DATE_UPDATE'] = date($this->strDateTimeFormat);
        return $entityClass::update($intId, $arFields);
    }

    public function getList($arParams)
    {
        $entityClass = $this->objHL;
        return $entityClass::getList($arParams);
    }
}