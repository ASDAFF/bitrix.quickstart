<?php
/**
 *  module
 * 
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main;

/**
 * Глобальный реестр объектов
 */
class Registry
{
	/**
	 * Объекты реестра
	 *
	 * @var array
	 */
	protected static $objects = array();
	
	/**
	 * Возвращает объект из реестра
	 *
	 * @param string $code Код объекта
	 * @return mixed
	 */
	public static function get($code)
	{
		return array_key_exists($code, self::$objects) ? self::$objects[$code] : null;
	}
	
	/**
	 * Сохраняет объект в реестр
	 *
	 * @param string $code Код объекта
	 * @param mixed $object Объект
	 * @return void
	 */
	public static function set($code, $object)
	{
		self::$objects[$code] = $object;
	}
}