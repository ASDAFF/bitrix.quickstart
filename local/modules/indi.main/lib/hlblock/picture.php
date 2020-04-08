<?php
/**
 * Created by PhpStorm.
 * User: osobolev
 * Date: 23.01.2018
 * Time: 15:29
 */

namespace Indi\Main\Hlblock;


class Picture extends Prototype
{
    /**
     * ID инфоблока
     *
     * @var integer
     */
    protected $id = 6;

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