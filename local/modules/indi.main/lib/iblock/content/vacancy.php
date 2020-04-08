<?php
/**
 * Individ module
 *
 * @category	Individ
 * @package		Iblock
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main\Iblock\Content;

use Indi\Main\Iblock;
/**
 * Инфоблок новостей
 *
 * @category	Individ
 * @package		Iblock
 */
class Vacancy extends Iblock\Prototype
{
    /**
     * ID инфоблока
     *
     * @var integer
     */
    protected $id = 5;

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
            throw new Main\Exception('Iblock ID is undefined.');
        }
    }

    /**
     * Возвращает инфоблок новостей
     *
     * @return News
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }
}