<?php

namespace Yandex\Market\Reference\Agent;

abstract class Regular extends Base
{
	/**
	 * @return array список описаний агентов, ключи:
	 *               method => string, # название метода (необязательно)
	 *               arguments => array|null # параметры вызова метода (необязательно)
	 *               interval => integer, # интервал запуска, в секундах (необязательно)
	 *               sort => integer, # сортировка, по-умолчанию — 100 (необязательно)
	 *               next_exec => string, # дата в формате Y-m-d H:i:s (необязательно)
	 * */
	public static function getAgents()
	{
		return array(
			static::getDefaultParams()
		);
	}
}
