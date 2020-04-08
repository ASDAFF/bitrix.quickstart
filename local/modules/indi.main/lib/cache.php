<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main;

use Bitrix\Main\Data\Cache as BxCache;

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
	 * @param mixed $cacheTag Код тега для тегированного кэша
	 * @return void
	 */
	public function __construct($cacheId, $cacheDir, $cacheTime = 3600, $cacheTag = '')
	{
		$this->id = serialize($cacheId);
		$this->dir = str_replace(array('/', '\\'), \DIRECTORY_SEPARATOR, $cacheDir);
		if($cacheTag) {
			$GLOBALS['CACHE_MANAGER']->StartTagCache($this->dir);
			$GLOBALS['CACHE_MANAGER']->RegisterTag($cacheTag);
			$GLOBALS['CACHE_MANAGER']->EndTagCache();
		}
		$this->time = (int) $cacheTime;
		$this->core = BxCache::createInstance();
	}
	
	/**
	 * Запускает кэширование
	 *
	 * @return boolean
	 */
	public function start()
	{
		$user = new \CUser;
		if ($_REQUEST['clear_cache'] == 'Y' && $user->IsAdmin()) {
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

	/**
	 * Устанавливет кэш для шаблона определенного элемента
	 *
	 * setTemplateCache
	 * @param $cp
	 * @param $tag
	 *
	 * @throws \Indi\Main\Exception
	 */

	public static function setTemplateCache($cacheId, $cachePath, $tag) {
		if(!strlen($cacheId) || !strlen($cachePath) || !strlen($tag)) {
			throw new Exception('Не указаны обязательные парамеры');
		}

		$cache = BxCache::createInstance();
		$cacheFile = $cache->getPath($cacheId);
		$cacheDir = $cachePath . "/" . $cacheFile;
		$GLOBALS['CACHE_MANAGER']->StartTagCache($cacheDir);
		$GLOBALS['CACHE_MANAGER']->RegisterTag($tag);
		$GLOBALS['CACHE_MANAGER']->EndTagCache();
	}
}