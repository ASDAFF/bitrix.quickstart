<?php

namespace Yandex\Market\Component\Base;

use Bitrix\Main;

abstract class AbstractProvider
{
	protected $component;

	public function __construct(\CBitrixComponent $component)
	{
		$this->component = $component;
	}

	public function prepareComponentParams($params)
	{
		return $params;
	}

	/**
	 * @return String[]
	 */
	public function getRequiredParams()
	{
		return [];
	}

	/**
	 * @return String[]
	 */
	public function getRequiredModules()
	{
		return [];
	}

	public function getComponentResult($key)
	{
		return isset($this->component->arResult[$key])
			? $this->component->arResult[$key]
			: null;
	}

	public function getComponentParam($key)
	{
		return isset($this->component->arParams[$key])
			? $this->component->arParams[$key]
			: null;
	}

	public function getComponentLang($key, $replaces = null)
	{
		return $this->component->getLang($key, $replaces);
	}

	/**
	 * @param $action
	 * @param $data
	 *
	 * @return array
	 * @throws \Bitrix\Main\SystemException
	 */
	public function processAjaxAction($action, $data)
	{
		throw new Main\SystemException('ACTION_NOT_FOUND');
	}
}