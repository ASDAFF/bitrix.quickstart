<?
namespace Indi\Main\Hlblock;

use Indi\Main\Util;
use Indi\Main\Hlblock\Prototype;
use Bitrix\Main\ArgumentException;
use Indi\Main as Main;

/**
 * Класс "Пример класса highload-блока"
 *
 * @category    Individ
 * @package    Hlblock
 */
class City extends Prototype
{
    /**
     * ID инфоблока
     *
     * @var integer
     */
    protected $id = 19;

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

    /**
     * Возвращает массив с данными из hlblock
     *
     *
     * @return array {array[]|false[]}
     * @throws ArgumentException
     */
    /*public static function getCities(){
        //Возвращает highload - блок по его ID или символьному коду
        //$prototype = Prototype::getInstance((int)self::getInstance());
        $res = $this->getElements();
        return $res;
    }*/

}