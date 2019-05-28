<?php

namespace Yandex\Market\Reference\Event;

use Bitrix\Main;

abstract class Base
{
	public static function getClassName()
	{
		return '\\' . get_called_class();
	}

	/**
	 * Добавляем событие
	 *
	 * @param $handlerParams array|null параметры обработчика, ключи:
	 *               module => string # название метода
	 *               event => string, # название события
	 *               method => string, # название метода (необязательно)
	 *               sort => integer, # сортировка (необязательно)
	 *               arguments => array # аргументы (необязательно)
	 *
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * */
	public static function register($handlerParams = null)
	{
		$className = static::getClassName();

		$handlerParams = !isset($handlerParams) ? static::getDefaultParams() : array_merge(
			static::getDefaultParams(),
			$handlerParams
		);

		Controller::register($className, $handlerParams);
	}

	/**
	 * Удаляем событие
	 *
	 * @param null $handlerParams
	 */
	public static function unregister($handlerParams = null)
	{
		$className = static::getClassName();

		$handlerParams = !isset($handlerParams) ? static::getDefaultParams() : array_merge(
			static::getDefaultParams(),
			$handlerParams
		);

		Controller::unregister($className, $handlerParams);
	}

	/**
	 * @return array описания обработчика для выполнения по умолчанию, ключи:
	 *               module => string # название метода
	 *               event => string, # название события
	 *               method => string, # название метода (необязательно)
	 *               sort => integer, # сортировка (необязательно)
	 *               arguments => array # аргументы (необязательно)
	 * */

	public static function getDefaultParams()
	{
		return array();
	}
}
