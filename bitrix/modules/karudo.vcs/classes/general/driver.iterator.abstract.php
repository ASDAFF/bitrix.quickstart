<?php

abstract class CVCSDriverIteratorAbstract {
	private $driver_code;
	protected $items = array();
	protected $settings;

	public final function __construct($options = array()) {
		$this->driver_code = $options['code'];
		$this->settings = new CVCSArrayObject(empty($options['options']) ? array() : $options['options']);

		$this->Init();

		$obCache = new CPHPCache();
		if ($obCache->StartDataCache(3600, $this->GetCacheUniqStr(), CVCSConfig::CACHE_DIR)) {
			$this->collect();
			$obCache->EndDataCache($this->items);
		} else {
			$this->items = $obCache->GetVars();
		}
	}

	public final function GetDriverCode() {
		return $this->driver_code;
	}

	/**
	 * @return string
	 */
	protected function GetCacheUniqStr() {
		return get_class($this).serialize($this->settings);
	}


	/**
	 * Init settings
	 * @abstract
	 * return null
	 */
	protected abstract function Init();

	/**
	 * Collect items
	 * @abstract
	 *
	 */
	protected abstract function collect();

	/**
	 * @abstract
	 * @return CVCSDriverItemAbstract|bool
	 */
	public abstract function GetNextItem();
	/**
	 * @abstract
	 * @param $last_item mixed
	 */
	public abstract function SetLastItemOrigID($last_item_orig_id);

	public abstract function GetItemsCount();
	public abstract function GetCurPosition();
}