<?php

namespace Yandex\Market\Result;

use Yandex\Market;

class StepCount extends Base
{
	protected $countList;
	protected $countWarnings;
	protected $countErrors;

	public function __construct()
	{
		parent::__construct();

		$this->countList = [];
		$this->countWarnings = [];
		$this->countErrors = [];
	}

	public function setCount($key, $value)
	{
		$this->countList[$key] = $value;
	}

	public function hasCount($key)
	{
		return isset($this->countList[$key]);
	}

	public function getCount($key)
	{
		return isset($this->countList[$key]) ? $this->countList[$key] : null;
	}

	public function addCountWarning($key, Market\Error\Base $warning)
	{
		$this->countWarnings[$key] = $warning;

		$this->addWarning($warning);
	}

	public function getCountWarning($key)
	{
		return isset($this->countWarnings[$key]) ? $this->countWarnings[$key] : null;
	}

	public function addCountError($key, Market\Error\Base $error)
	{
		$this->countErrors[$key] = $error;

		$this->addError($error);
	}

	public function getCountError($key)
	{
		return isset($this->countErrors[$key]) ? $this->countErrors[$key] : null;
	}

	public function getSum()
	{
		return array_sum($this->countList);
	}
}