<?php
namespace Wizard\Additional;

/**
 * Class Errors
 * @package Wizard\Additional
 */
Class Errors
{
	protected static $instance;
	protected $error;
	protected $obError;
	protected $Application;

	/**
	 * Errors constructor.
	 */
	private function __construct(){
		$this->error = false;
		global $APPLICATION;
		$this->Application = $APPLICATION;
		$this->obError = new \CAdminException([]);
	}

	private function __clone(){}

	/**
	 * @return mixed
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			$c = __CLASS__;
			self::$instance = new $c();
		}

		return self::$instance;
	}

	public function clearErrors(){
		$this->error = false;
		$this->obError = new \CAdminException([]);
	}

	/**
	 * @param $msg
	 */
	public function setError ($msg)
	{
		$this->error = true;
		$this->obError->AddMessage(
			[
				"text" => $msg
			]
		);
	}

	/**
	 * @param bool $bShowError
	 * @return bool
	 */
	public function setErrors ($bShowError = false)
	{
		if ($this->error && !empty($this->obError)) {
			$this->Application->ThrowException($this->obError);
			if ($bShowError && $err = $this->Application->GetException())
				ShowError($err->GetString());

			return false;
		}

		return true;
	}

	/*public function getObError(){
		return $this->obError;
	}*/
}