<?php

namespace Yandex\Market\Reference\Agent;

use Bitrix\Main;

abstract class Base
{
	public static function getClassName()
	{
		return '\\' . get_called_class();
	}

	/**
	 * Добавляем агент
	 *
	 * @param $agentParams array|null параметры агента, ключи:
	 *               method => string # название метода (необязательно)
	 *               arguments => array # параметры вызова метода (необязательно)
	 *               interval => integer, # интервал запуска, в секундах (необязательно)
	 *               sort => integer, # сортировка, по-умолчанию — 100 (необязательно)
	 *               next_exec => string, # дата в формате Y-m-d H:i:s (необязательно)
	 *
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * */
	public static function register($agentParams = null)
	{
		$className = static::getClassName();

		$agentParams = !isset($agentParams) ? static::getDefaultParams() : array_merge(
			static::getDefaultParams(),
			$agentParams
		);

		Controller::register($className, $agentParams);
	}

	/**
	 * Удаляем агент
	 *
	 * @param array|null $agentParams
	 *
	 * @throws \Bitrix\Main\NotImplementedException
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function unregister($agentParams = null)
	{
		$className = static::getClassName();

		$agentParams = !isset($agentParams) ? static::getDefaultParams() : array_merge(
			static::getDefaultParams(),
			$agentParams
		);

		Controller::unregister($className, $agentParams);
	}

	/**
	 * Обертка для вызова агента, завершает выполнение агенты при возврате false
	 *
	 * @param $method string
	 * @param $arguments array|null
	 *
	 * @return string|null
	 * */
	public static function callAgent($method, $arguments = null)
	{
		$className = static::getClassName();
		$callResult = null;
		$result = '';

		if (is_array($arguments))
		{
			$callResult = call_user_func_array(array($className, $method), $arguments);
		}
		else
		{
			$callResult = call_user_func(array($className, $method));
		}

		if ($callResult !== false)
		{
			if (is_array($callResult))
			{
				$arguments = $callResult;
			}

			$result = Controller::getAgentCall($className, $method, $arguments);
		}

		return $result;
	}

	/**
	 * @return array описания агента для выполнения по умолчанию (метод run), ключи:
	 *               method => string # название метода (необязательно)
	 *               arguments => array # параметры вызова метода (необязательно)
	 *               interval => integer, # интервал запуска, в секундах (необязательно)
	 *               sort => integer, # сортировка, по-умолчанию — 100 (необязательно)
	 *               next_exec => string, # дата в формате Y-m-d H:i:s (необязательно)
	 * */

	public static function getDefaultParams()
	{
		return array();
	}

	/**
	 *  Метод агента по умолчанию, удаляется при возврате false
	 *
	 * @return mixed|false
	 * */

	public static function run()
	{
		return false;
	}
}
