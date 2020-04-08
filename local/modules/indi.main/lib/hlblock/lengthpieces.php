<?php
namespace Indi\Main\Hlblock;

use Indi\Main\Util;
use Indi\Main\Hlblock\Prototype;
use Bitrix\Main\ArgumentException;
use Indi\Main as Main;

class Lengthpieces extends Prototype
{
    /**
     * ID инфоблока
     *
     * @var integer
     */
    protected $id = 33;

    /**
     * Конструктор
     *
     * @param integer $id ID highload-блока
     *
     * @throws \Indi\Main\Exception
     */
    public function __construct()
    {
        if (!$this->id) {
            throw new Main\Exception('Hlblock ID is undefined.');
        }
    }

    /**
     * Возвращает hlblock "Пример класса highload-блока"
     *
     * @return Example
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }
}