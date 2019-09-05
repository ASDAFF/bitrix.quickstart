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
 * Выводит сообщения в консоль браузера
 */
class Console
{
	/**
	 * Тип записи - лог
	 */
	const ITEM_TYPE_LOG = 0;
	
	/**
	 * Тип записи - ошибка
	 */
	const ITEM_TYPE_ERROR = 1;
	
	/**
	 * Тип записи - таблица
	 */
	const ITEM_TYPE_TABLE = 2;
	
	/**
	 * Обработчик на рендер консоли добавлен
	 *
	 * @var boolean
	 */
	protected static $handlerAdded = false;
	
	/**
	 * Данные для вывода в консоль
	 *
	 * @var array
	 */
	protected static $items = array();
	
	/**
	 * Добавляет данные в лог сообщений
	 *
	 * @param mixed $data Данные
	 * @param boolean $delayed Отложенный режим (после генерации всей страницы)
	 * @return void
	 */
	public static function log($data, $delayed = false)
	{
		self::append($data, self::ITEM_TYPE_LOG, $delayed);
	}
	
	/**
	 * Добавляет данные в лог ошибок
	 *
	 * @param mixed $data Данные
	 * @param boolean $delayed Отложенный режим (после генерации всей страницы)
	 * @return void
	 */
	public static function error($data, $delayed = false)
	{
		self::append($data, self::ITEM_TYPE_ERROR, $delayed);
	}
	
	/**
	 * Добавляет данные в лог таблиц
	 *
	 * @param mixed $data Данные
	 * @param boolean $delayed Отложенный режим (после генерации всей страницы)
	 * @return void
	 */
	public static function table($data, $delayed = false)
	{
		self::append($data, self::ITEM_TYPE_TABLE, $delayed);
	}
	
	/**
	 * Добавляет элемент в лог
	 *
	 * @param mixed $data Данные
	 * @param integer $type Тип элемента
	 * @param boolean $delayed Отложенный режим (после генерации всей страницы)
	 * @return void
	 */
	protected static function append($data, $type = self::ITEM_TYPE_LOG, $delayed = false)
	{
		if (defined('SITE_CHARSET') && SITE_CHARSET != 'UTF-8') {
			$data = Util::convertCharset($data, SITE_CHARSET, 'UTF-8');
		}
		
		if ($delayed) {
			self::$items[] = array($data, $type);
			self::addHandler();
		} else {
			self::render(array(array($data, $type)));
		}
	}
	
	/**
	 * Добавляет обработчик на рендер консоли
	 *
	 * @return void
	 */
	protected static function addHandler()
	{
		if (!self::$handlerAdded) {
			self::$handlerAdded = true;
			\Bitrix\Main\EventManager::getInstance()->addEventHandler(
				'main',
				'OnAfterEpilog',
				array(__CLASS__, 'render')
			);
		}
	}
	
	/**
	 * Выводит HTML-код, добавляющий данные в консоль браузера
	 *
	 * @param array|null $items Данные
	 * @return void
	 */
	public static function render($items = null)
	{
		if ($items === null) {
			$items = &self::$items;
		}
		if (!$items) {
			return;
		}
		
		?>
		<script role="console">
			if (typeof console != 'undefined') {
				<?foreach ($items as $item) {
					switch ($item[1]) {
						case self::ITEM_TYPE_LOG:
							?>console.log(<?=json_encode($item[0])?>);<?
							break;
						
						case self::ITEM_TYPE_ERROR:
							?>console.error(<?=json_encode($item[0])?>);<?
							break;
						
						case self::ITEM_TYPE_TABLE:
							?>console.table ? console.table(<?=json_encode($item[0])?>) : console.error("Table mode isn't supported.");<?
							break;
					}
				}?>
			}
		</script>
		<?
	}
}