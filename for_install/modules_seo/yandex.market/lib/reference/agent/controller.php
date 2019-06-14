<?php

namespace Yandex\Market\Reference\Agent;

use Bitrix\Main;
use CAgent;
use Yandex\Market\Config;

class Controller
{
	/**
	 * Добавляем агент
	 *
	 * @param $сlassName string
	 * @param $agentParams array|null
	 *
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * */
	public static function register($className, $agentParams)
	{
		$agentDescription = static::getAgentDescription($className, $agentParams);
		$registeredAgent = static::getRegisteredAgent($agentDescription);

		static::saveAgent($agentDescription, $registeredAgent);
	}

	/**
	 * Удаляем агент
	 *
	 * @param $className
	 * @param $agentParams
	 *
	 * @throws \Bitrix\Main\NotImplementedException
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function unregister($className, $agentParams)
	{
		$agentDescription = static::getAgentDescription($className, $agentParams);
		$registeredAgent = static::getRegisteredAgent($agentDescription);

		if ($registeredAgent)
		{
			static::deleteAgent($registeredAgent);
		}
	}

	/**
	 * Обновляет привязки регулярных агентов
	 *
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * */
	public static function updateRegular()
	{
		$baseClassName = Regular::getClassName();

		$classList = static::getClassList($baseClassName);
		$agentList = static::getClassAgents($classList);
		$registeredList = static::getRegisteredAgents($baseClassName);

		static::saveAgents($agentList, $registeredList);
		static::deleteAgents($agentList, $registeredList);
	}

	/**
	 * Удаляем все агенты
	 *
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function deleteAll()
	{
		$namespace = Config::getNamespace();
		$registeredList = static::getRegisteredAgents($namespace, true);

		static::deleteAgents([], $registeredList);
	}

	/**
	 * Обходит список классов и готовит массив для записи
	 *
	 * @param $classList array список классов
	 *
	 * @return array список агентов для регистрации
	 * @throws Main\NotImplementedException
	 */
	public static function getClassAgents($classList)
	{
		$agentList = array();

		/** @var Regular $className */
		foreach ($classList as $className)
		{
			$normalizedClassName = $className::getClassName();
			$agents = $className::getAgents();

			foreach ($agents as $agent)
			{
				$agentDescription = static::getAgentDescription(
					$normalizedClassName,
					$agent
				);
				$agentKey = strtolower($agentDescription['name']);

				$agentList[$agentKey] = $agentDescription;
			}
		}

		return $agentList;
	}

	/**
	 * Возвращает описание агента для регистрации, проверяет существование метода
	 *
	 * @param $className string
	 * @param $agentParams array|null параметры агента
	 *
	 * @return array
	 * @throws Main\NotImplementedException
	 */
	public static function getAgentDescription($className, $agentParams)
	{
		$method = isset($agentParams['method']) ? $agentParams['method'] : 'run';

		if (!method_exists($className, $method))
		{
			throw new Main\NotImplementedException(
				'Method ' . $method
				. ' not defined in ' . $className
				. ' and cannot be registered as agent'
			);
		}

		$agentFnCall = static::getAgentCall(
			$className,
			$method,
			isset($agentParams['arguments']) ? $agentParams['arguments'] : null
		);

		return array(
			'name'      => $agentFnCall,
			'sort'      => isset($agentParams['sort']) ? (int)$agentParams['sort'] : 100,
			'interval'  => isset($agentParams['interval']) ? (int)$agentParams['interval'] : 86400,
			'next_exec' => isset($agentParams['next_exec']) ? $agentParams['next_exec'] : ''
		);
	}

	/**
	 * Получаем список ранее зарегистрированных агентов
	 *
	 * @param $baseClassName string название класса, наследников которого необходимо получить
	 * @param $isBaseNamespace bool первый аргумент не является классом
	 *
	 * @return array список зарегистрированных агентов
	 * */
	public static function getRegisteredAgents($baseClassName, $isBaseNamespace = false)
	{
		$registeredList = array();
		$namespaceLower = strtolower(Config::getNamespace());
		$query = CAgent::GetList(
			array(),
			array(
				'NAME' => $namespaceLower . '%'
			)
		);

		while ($agentRow = $query->fetch())
		{
			$agentCallParts = explode('::', $agentRow['NAME']);
			$agentClassName = trim($agentCallParts[0]);

			if (
				$isBaseNamespace
				|| $agentClassName === ''
				|| !class_exists($agentClassName)
				|| is_subclass_of($agentClassName, $baseClassName)
			)
			{
				$agentKey = strtolower($agentRow['NAME']);
				$registeredList[$agentKey] = $agentRow;
			}
		}

		return $registeredList;
	}

	/**
	 * Получаем зарегистрированный агент для метода класса
	 *
	 * @param $agentDescription array
	 *
	 * @return array|null зарегистрированный агент
	 * */
	public static function getRegisteredAgent($agentDescription)
	{
		$query = CAgent::GetList(
			array(),
			array(
				'NAME' => $agentDescription['name']
			)
		);

		return $query->fetch() ?: null;
	}

	/**
	 * Возвращает строку для вызова метода callAgent класса через eval
	 *
	 * @param $className string
	 * @param $method string
	 * @param $arguments array|null
	 *
	 * @return string вызов метода
	 * */
	public static function getAgentCall($className, $method, $arguments = null)
	{
		return static::getFunctionCall(
			$className,
			'callAgent',
			isset($arguments)
				? array( $method, $arguments )
				: array( $method )
		);
	}

	/**
	 * Возвращает строку для вызова метод класс через eval
	 *
	 * @param $className string
	 * @param $method string
	 * @param $arguments array|null
	 *
	 * @return string вызов метода
	 * */
	public static function getFunctionCall($className, $method, $arguments = null)
	{
		$argumentsString = '';

		if (is_array($arguments))
		{
			$isFirstArgument = true;

			foreach ($arguments as $argument)
			{
				if (!$isFirstArgument)
				{
					$argumentsString .= ', ';
				}

				$argumentsString .= var_export($argument, true);

				$isFirstArgument = false;
			}
		}

		return $className . '::' . $method . '(' . $argumentsString . ');';
	}

	/**
	 * Возвращает список всех подклассов неймспейса Agent
	 *
	 * @param string название класса, наследники которого надо вернуть
	 *
	 * @return array список имён классов для обхода
	 * */
	protected static function getClassList($baseClassName)
	{
		$baseDir = Config::getModulePath();
		$baseNamespace = Config::getNamespace();
		$directory = new \RecursiveDirectoryIterator($baseDir);
		$iterator = new \RecursiveIteratorIterator($directory);
		$result = [];

		/** @var \DirectoryIterator $entry */
		foreach ($iterator as $entry)
		{
			if (
				$entry->isFile()
				&& $entry->getExtension() == 'php'
			)
			{
				$relativePath = str_replace($baseDir, '', $entry->getPath());
				$className = $baseNamespace . str_replace('/', '\\', $relativePath) . '\\' . $entry->getBasename('.php');
				$tableClassName = $className . 'Table';

				if (
					!empty($relativePath)
					&& !class_exists($tableClassName)
					&& class_exists($className)
					&& is_subclass_of($className, $baseClassName)
				)
				{
					$result[] = $className;
				}
			}
		}

		return $result;
	}

	/**
	 * Регистрирует все агенты в базе данных
	 *
	 * @params $agentList array список агентов для регистрации
	 * @params $registeredList array список ранее зарегистрированных агентов
	 *
	 * @throws Main\SystemException
	 * */
	protected static function saveAgents($agentList, $registeredList)
	{
		foreach ($agentList as $agentKey => $agent)
		{
			static::saveAgent(
				$agent,
				isset($registeredList[$agentKey]) ? $registeredList[$agentKey] : null
			);
		}
	}

	/**
	 * Регистрируем агент в базе данных
	 *
	 * @params $agent array агент для регистрации
	 * @params $registeredAgent array ранее зарегистрированный агент
	 *
	 * @throws Main\SystemException
	 * */
	protected static function saveAgent($agent, $registeredAgent)
	{
		global $APPLICATION;

		$agentData = array(
			'NAME'           => $agent['name'],
			'MODULE_ID'      => Config::getModuleName(),
			'SORT'           => $agent['sort'],
			'ACTIVE'         => 'Y',
			'AGENT_INTERVAL' => $agent['interval'],
			'IS_PERIOD'      => 'N',
			'USER_ID'        => 0
		);

		if (!empty($agent['next_exec']))
		{
			$agentData['NEXT_EXEC'] = $agent['next_exec'];
		}

		if (!isset($registeredAgent)) // добавляем агент, если отсутствует
		{
			$saveResult = CAgent::Add($agentData);
		}
		else
		{
			$saveResult = CAgent::Update($registeredAgent['ID'], $agentData);
		}

		if (!$saveResult)
		{
			$exception = $APPLICATION->GetException();

			throw new Main\SystemException(
				'agent '
				. $agent['name']
				. ' register error'
				. ($exception ? ': ' . $exception->GetString() : '')
			);
		}
	}

	/**
	 * Удаляет неиспользуемые агенты из базы данных
	 *
	 * @params $agentList array список агентов для регистрации
	 * @params $registeredList array список ранее зарегистрированных агентов
	 *
	 * @throws Main\SystemException
	 * */
	protected static function deleteAgents($agentList, $registeredList)
	{
		foreach ($registeredList as $agentKey => $agentRow)
		{
			if (!isset($agentList[$agentKey]))
			{
				static::deleteAgent($agentRow);
			}
		}
	}

	/**
	 * Удаляет агент из базы данных
	 *
	 * @params $registeredRow array ранее зарегистрированный агент
	 *
	 * @throws Main\SystemException
	 * */
	protected static function deleteAgent($registeredRow)
	{
		$deleteResult = CAgent::Delete($registeredRow['ID']);

		if (!$deleteResult)
		{
			throw new Main\SystemException('agent ' . $registeredRow['NAME'] . ' not deleted');
		}
	}
}

