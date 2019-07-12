<?
namespace Citrus\Realty;

/**
 * Класс для работы с избранным
 * @package Citrus\Realty
 */
class Favourites
{
	// nook use cookies or session storage to store favourites for longer period
	// nook spread current state to all open tabs
	static private $list = null;

	protected static function init()
	{
		if (!isset(self::$list))
		{
			if (!(isset($_SESSION["citrus.realestate.favourites"]) && is_array($_SESSION["citrus.realestate.favourites"])))
				$_SESSION["citrus.realestate.favourites"] = array();
			self::$list = & $_SESSION["citrus.realestate.favourites"];
		}
	}

	protected static function check($element)
	{
		$element = intval($element);
		if ($element <= 0)
			throw new \Exception("element must be a positive int");
	}

	/**
	 * Добавление в избранное
	 * @param int $element ID предложения (элемента инфоблока)
	 */
	public static function add($element)
	{
		self::init();
		self::check($element);
		if (!self::isInList($element))
			self::$list[$element] = true;
	}

	/**
	 * Удаление из избранного
	 * @param int $element ID предложения (элемента инфоблока)
	 */
	public static function remove($element)
	{
		self::init();
		self::check($element);
		if (self::isInList($element))
			unset(self::$list[$element]);
	}

	/**
	 * Возвращает количество элементов в избранном
	 * @return int
	 */
	public static function getCount()
	{
		self::init();
		return count(self::$list);
	}

	/**
	 * Возвращает true, если указанное предложение уже добавлено в избранное
	 * @param int $element ID предложения (элемента инфоблока)
	 * @return bool
	 */
	public static function isInList($element)
	{
		self::init();
		return array_key_exists($element, self::$list);
	}

	/**
	 * Возвращает список элементов, добавленных в избранное
	 * @return array Список избранного
	 */
	public static function getList()
	{
		self::init();
		return self::$list;
	}

}

