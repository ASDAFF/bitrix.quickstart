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
 * Модель кэша
 */
class Cache
{
	/**
	 * Экземпляр кэша
	 *
	 * @var \Bitrix\Main\Data\Cache
	 */
	protected $core = null;
	
	/**
	 * Идентификатор кэша
	 *
	 * @var string
	 */
	protected $id = '';
	
	/**
	 * Каталог кэша
	 *
	 * @var string
	 */
	protected $dir = '';
	
	/**
	 * Время жизни кэша
	 *
	 * @var integer
	 */
	protected $time = 0;
	
	/**
	 * Конструктор
	 *
	 * @param mixed $cacheId Идентификатор кэша
	 * @param mixed $cacheDir Каталог кэша
	 * @param mixed $cacheTime Время жизни кэша
	 * @return void
	 */
	public function __construct($cacheId, $cacheDir, $cacheTime = 3600)
	{
		$this->id = serialize($cacheId);
		$this->dir = str_replace(array('/', '\\'), \DIRECTORY_SEPARATOR, $cacheDir);
		$this->time = (int) $cacheTime;
		$this->core = \Bitrix\Main\Data\Cache::createInstance();
	}
	
	/**
	 * Запускает кэширование
	 *
	 * @return boolean
	 */
	public function start()
	{
		if ($_REQUEST['clear_cache'] == 'Y' && \CUser::IsAdmin()) {
			$this->core->clean($this->id, $this->dir);
		}
		
		return $this->core->startDataCache($this->time, $this->id, $this->dir);
	}
	
	/**
	 * Сохраняет данные в кэш
	 *
	 * @param mixed $data Данные для кэширования
	 * @return void
	 */
	public function end($data)
	{
		return $this->core->endDataCache($data);
	}
	
	/**
	 * Возвращает ранее закэшированные данные
	 *
	 * @return mixed
	 */
	public function getVars()
	{
		return $this->core->getVars();
	}
	
	/**
	 * Удаляет ранее проинициализированный кэш
	 *
	 * @return void
	 */
	public function abort()
	{
		return $this->core->abortDataCache();
	}
}