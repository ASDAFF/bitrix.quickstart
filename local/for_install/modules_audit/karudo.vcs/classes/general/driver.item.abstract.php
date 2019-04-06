<?php

abstract class CVCSDriverItemAbstract {
	private $driver_code;
	private $orig_id;
	private $orig_id_hash;

	protected $settings;

	public final function __construct($driver_code, $orig_id, $settings = array()) {
		$this->driver_code = $driver_code;
		$this->orig_id = $orig_id;
		if ($settings instanceof CVCSArrayObject) {
			$this->settings = $settings;
		} else {
			$this->settings = new CVCSArrayObject((array) $settings);
		}
		$this->Init();
	}

	public function GetDriverCode() {
		return $this->driver_code;
	}

	public function GetID() {
		return $this->orig_id;
	}

	/**
	 * @return mixed
	 */
	public function GetIDHash() {
		if (empty($this->orig_id_hash)) {
			$this->orig_id_hash = md5($this->GetID());
		}

		return $this->orig_id_hash;
	}

	public abstract function GetSourceHash();
	public abstract function GetSource();
	public abstract function SetSource($source);

	public abstract function IsExists();

	public abstract function Delete();
	/**
	 * @param $driver_code string
	 * @param $orig_id string
	 * @return CVCSDriverItemAbstract
	 */
	public static function GetItemObject($driver_code, $orig_id) {
		$settings = CVCSMain::GetDriverByCode($driver_code);
		$c = $settings['class_item'];
		return new $c($driver_code, $orig_id, $settings['options']);
	}

	protected abstract function Init();
}