<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @package		MVC
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main\Mvc\View;

use Indi\Main as Main;

/**
 * Абстрактный MVC view
 * 
 * @category	Individ
 * @package		MVC
 */
class Prototype
{
	/**
	 * Каталог по умолчанию для файлов view
	 *
	 * @var string
	 */
	protected $baseDir = '';

	/**
	 * Имя view
	 *
	 * @var string
	 */
	protected $name = '';
	
	/**
	 * Данные view
	 *
	 * @var mixed
	 */
	protected $data = array();
	
	/**
	 * Создает новый MVC view
	 *
	 * @param string $name Название шаблона view
	 * @param mixed $data Данные view
	 * @return void
	 */
	public function __construct($name = '', $data = array())
	{
		if (!$this->baseDir) {
			$this->baseDir = \Indi\Main\BASE_DIR . '/views/';
		}
		$this->name = $name;
		$this->data = $data;
	}
	
	/**
	 * Отсылает http-заголовки для view
	 *
	 * @return void
	 */
	public function sendHeaders()
	{
	}
	
	/**
	 * Формирует view
	 *
	 * @return string
	 */
	public function render()
	{
		throw new Main\Exception("Abstract view can't be rendered.");
	}
	
	/**
	 * Устанавливает данные
	 *
	 * @param mixed $data Данные
	 * @return void
	 */
	public function setData($data)
	{
		$this->data = $data;
	}
	
	/**
	 * Устанавливает базовый каталог
	 *
	 * @param string $dir Базовый каталог
	 * @return void
	 */
	public function setBaseDir($dir)
	{
		$this->baseDir = $dir;
	}
}