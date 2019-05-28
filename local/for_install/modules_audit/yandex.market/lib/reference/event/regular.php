<?php

namespace Yandex\Market\Reference\Event;

abstract class Regular extends Base
{
	/**
	 * @return array список обработчиков, ключи:
	 *               module => string # название метода
	 *               event => string, # название события
	 *               method => string, # название метода (необязательно)
	 *               sort => integer, # сортировка (необязательно)
	 *               arguments => array # аргументы (необязательно)
	 * */

	public static function getHandlers()
	{
		return array();
	}
}